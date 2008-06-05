<?php
class Intraface_Keyword_StringAppender
{
    private $keyword_class;
    private $appender;

    function __construct($keyword, $appender)
    {
        $this->keyword = $keyword;
        $this->appender = $appender;
    }

    function cloneKeyword()
    {
        return clone $this->keyword;
    }

    /**
     * Add keywords by string
     *
     * @param string $string
     *
     * @return boolean
     */
    function addKeywordsByString($string)
    {
        $this->appender->deleteConnectedKeywords();

        $keywords = self::quotesplit(stripslashes($string), ",");

        if (is_array($keywords) AND count($keywords) > 0) {
            foreach ($keywords AS $key => $value) {
                $keyword = $this->cloneKeyword();
                if ($add_keyword_id = $keyword->save(array('id' => '', 'keyword' => $value))) {
                    $this->appender->addKeyword($keyword);
                }
            }
        }
        return true;
    }

    /****************************************************************************
     * Tools
     ***************************************************************************/

    /**
     * Funktionen er en hjælpefunktion, så man bare kan skrive nøgleordene i et inputfelt
     *
     * @param string $s        The string to split
     * @param string $splitter What splitter to use to split the string
     *
     * @return array med nøgleordene
     */
    public static function quotesplit($s, $splitter=',')
    {
        //First step is to split it up into the bits that are surrounded by quotes and the bits that aren't. Adding the delimiter to the ends simplifies the logic further down
        $getstrings = split('\"', $splitter.$s.$splitter);
        //$instring toggles so we know if we are in a quoted string or not
        $delimlen = strlen($splitter);
        $instring = 0;
        $result = array();

        while (list($arg, $val) = each($getstrings)) {
            if ($instring==1) {
                //Add the whole string, untouched to the result array.
                if (!empty($val)) {
                    $result[] = $val;
                    $instring = 0;
                }
            } else {
                //Break up the string according to the delimiter character
                //Each string has extraneous delimiters around it (inc the ones we added above), so they need to be stripped off
                $temparray = split($splitter, substr($val, $delimlen, strlen($val)-$delimlen-$delimlen ) );

                while(list($iarg, $ival) = each($temparray)) {
                    if (!empty($ival)) $result[] = trim($ival);
                }
                $instring = 1;
            }
        }
        return $result;
    }

}
