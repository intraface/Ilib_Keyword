<?php
/**
 *
 *
 *
 * PHP version 5
 *
 * @category   Keyword
 * @package    Ilib_Keyword
 * @author     Lars Olesen <lars@intraface.dk>
 * @copyright
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version
 * @filesource
 * @link
 *
 */
class Ilib_Keyword_Appender extends Ilib_Keyword
{
    protected $object;
    protected $type;
    protected $extra_conditions;
    protected $belong_to_id = 0;

    function __construct($object)
    {
        $this->object = $object;
        $this->kernel = $this->object->getKernel();
        $this->type = get_class($this->object);

        $this->extra_conditions = array('intranet_id' => $this->object->getKernel()->intranet->get('id'));
    }

    /**
     * Gets belong to id
     *
     * @return integer
     */
    function getBelongToId()
    {
        return $this->belong_to_id;
    }

    /**
     * Appends a keyword to an object
     *
     * @param object $keyword
     *
     * @return boolean
     */
    function addKeyword($keyword)
    {
        $condition = $this->extra_conditions;
        $condition['keyword_x_object.keyword_id'] = $keyword->getId();
        $condition['keyword_x_object.belong_to'] = $this->getBelongToId();

        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }

        $db = new DB_Sql;
        $db->query("SELECT * FROM keyword_x_object
            WHERE " . implode(' AND ', $c));

        if (!$db->nextRecord()) {
            $db->query("INSERT INTO keyword_x_object
                SET " . implode(', ', $c));

        }
        return true;
    }

    /**
     * Add keywords from an array
     *
     * @param array $keywords Keyword object
     *
     * @return boolean
     */
    function addKeywords($keywords)
    {
        if (is_array($keywords) AND count($keywords) > 0) {
            foreach ($keywords AS $keyword) {
                $this->addKeyword($keyword);
            }
        }
        return true;
    }

    /**
     * Returns the used keywords
     *
     * @return array
     */
    function getUsedKeywords()
    {
        $keywords = array();

        //$condition = $this->extra_conditions;
        $condition['keyword.intranet_id'] = $this->kernel->intranet->get('id');
        $condition['keyword.type'] = $this->type;
        $condition['keyword.active'] = 1;

        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }

        $db = new DB_Sql;
        $db->query("SELECT DISTINCT(keyword.id), keyword.keyword
            FROM keyword_x_object x
            INNER JOIN keyword keyword
                ON x.keyword_id = keyword.id
            WHERE " . implode(' AND ', $c) . "
            ORDER BY keyword ASC");

        $i = 0;
        while ($db->nextRecord()) {
            $keywords[$i]['id'] = $db->f('id');
            $keywords[$i]['keyword'] = $db->f('keyword');
            $i++;
        }

        return $keywords;
    }

    /**
     * Returns the keywords which has been connected to objects
     *
     * @return array
     */
    function getConnectedKeywords()
    {
        $keywords = array();
        //$condition = $this->extra_conditions;
        $condition['keyword.active '] = 1;
        $condition['keyword.type '] = $this->type;
        $condition['keyword.intranet_id '] = $this->kernel->intranet->get('id');

        $condition['keyword_x_object.intranet_id '] = $this->kernel->intranet->get('id');
        $condition['keyword_x_object.belong_to'] = $this->getBelongToId();

        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }

        $db = new DB_Sql;
        $db->query("SELECT DISTINCT(keyword.id) AS id, keyword.keyword
            FROM keyword_x_object
            INNER JOIN keyword
            ON keyword_x_object.keyword_id = keyword.id
            WHERE " . implode(' AND ', $c) . " AND keyword.keyword != ''
            ORDER BY keyword.keyword");

        $i = 0;
        while ($db->nextRecord()) {
            $keywords[$i]['id'] = $db->f('id');
            $keywords[$i]['keyword'] = $db->f('keyword');
            $i++;
        }

        return $keywords;
    }

    /**
     * Delete all connected keywords to an object
     *
     * @return boolean
     */
    function deleteConnectedKeywords()
    {
        if ($this->object->get('id') == 0) {
            return false;
        }

        //$condition = $this->extra_conditions;
        $condition['keyword.intranet_id'] = $this->kernel->intranet->get('id');
        $condition['keyword.type '] = $this->type;

        $condition['keyword_x_object.belong_to'] = $this->getBelongToId();

        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }


        $db = new DB_Sql;
        $db->query("DELETE keyword_x_object FROM keyword_x_object
            INNER JOIN keyword ON keyword_x_object.keyword_id = keyword.id
            WHERE " . implode(' AND ', $c));

        return true;
    }

    /**
     * Returns the connected keywords as a string
     *
     * @return string
     */
    function getConnectedKeywordsAsString()
    {
        $keywords = $this->getConnectedKeywords();
        $arr = array();

        foreach ($keywords AS $keyword) {
            $arr[] = $keyword['keyword'];
        }
        $string = implode(', ', $arr);

        return trim($string);
    }
}
