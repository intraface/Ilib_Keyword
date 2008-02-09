<?php
/**
 *
 */


class FakeKernel {

    private $translation;
    public $setting;
    public $intranet;
    public $user;
    public $sesion_id;

    public function __construct()
    {
        $this->session_id = session_id();
    }

    /**
     * We should actually return an object, but lets see how this works
     */
    public function module($module) {
        switch($module) {
            case 'filemanager':
                require_once 'Intraface/modules/filemanager/FileManager.php';
                require_once 'Intraface/shared/filehandler/FileHandler.php';
                require_once 'Intraface/shared/keyword/Keyword.php';

                break;
            default:
                trigger_error('The module is not installed!', E_USER_ERROR);

        }

        require_once 'Intraface/Module.php';
        return New FakeModule($module);

    }

    public function useShared($shared)
    {
        switch($shared) {
            case 'filehandler':
                require_once 'Intraface/shared/filehandler/FileHandler.php';
                break;
            case 'keyword':
                require_once 'Intraface/shared/keyword/Keyword.php';
                break;

            default:
                trigger_error('Shared '.$shared.' is not installed!', E_USER_ERROR);
                exit;
        }
        require_once 'Intraface/Shared.php';
        return new FakeShared($shared);
    }

    public function getTranslation($page_id)
    {

        $dbinfo = array(
            'hostspec' => DB_HOST,
            'database' => DB_NAME,
            'phptype'  => 'mysql',
            'username' => DB_USER,
            'password' => DB_PASSWORD
        );

        if (!defined('LANGUAGE_TABLE_PREFIX')) {
            define('LANGUAGE_TABLE_PREFIX', 'core_translation_');
        }

        $params = array(
            'langs_avail_table' => LANGUAGE_TABLE_PREFIX.'langs',
            'strings_default_table' => LANGUAGE_TABLE_PREFIX.'i18n'
        );

        require_once 'Translation2.php';

        $translation = Translation2::factory('MDB2', $dbinfo, $params);
        //always check for errors. In this examples, error checking is omitted
        //to make the example concise.
        if (PEAR::isError($translation)) {
            trigger_error('Could not start Translation ' . $translation->getMessage(), E_USER_ERROR);
        }

        // set primary language
        $set_language = $translation->setLang('dk');
        if (PEAR::isError($set_language)) {
            trigger_error($set_language->getMessage(), E_USER_ERROR);
        }

        // set the group of strings you want to fetch from
        // $translation->setPageID($page_id);

        // add a Lang decorator to provide a fallback language
        $translation = $translation->getDecorator('Lang');
        $translation->setOption('fallbackLang', 'uk');
        // $translation = $translation->getDecorator('LogMissingTranslation');
        // require_once("ErrorHandler/Observer/File.php");
        // $translation->setOption('logger', array(new ErrorHandler_Observer_File(ERROR_LOG), 'update'));
        $translation = $translation->getDecorator('DefaultText');

        return $translation;
    }


    public function getSessionId() {
        return $this->session_id;
    }

    /**
     * Function to make a random key - e.g. for passwords
     * This functions don't return any characters whick can be mistaken.
     * Won't return 0 (zero) or o (as in Ole) or 1 (one) or l (lars), because they can be mistaken on print.
     *
     * @param $count (integer) how many characters to return?
     *
     * @return  random key (string) only letters
     */
    function randomKey($length = 1)
    {
        // Legal characters
        $chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789';
        $how_many = strlen($chars);
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;

        while ($i < $length) {
            $num = rand() % $how_many;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }
        return $pass;
    }
}
