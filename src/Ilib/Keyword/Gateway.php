<?php
/**
 * Nøgleord
 *
 * @todo Gruppere nøgleord
 * @todo Systemnøgleord
 *
 * @author Lars Olesen <lars@legestue.net>
 * @package Ilib_Keyword
 */
class Ilib_Keyword_Gateway
{
    private $extra_conditions;

    function __construct($extra_conditions = array())
    {
        $this->extra_conditions = $extra_conditions;
    }

    function setExtraConditions($conditions)
    {
    	$this->extra_conditions = $conditions;
    }

    /**
     * Returns all keywords
     *
     * @return array
     */
    function getAllKeywordsFromType($type)
    {
        $keywords = array();

        $condition = $this->extra_conditions;
        $condition['keyword.type'] = $type;
        $condition['keyword.active'] = 1;
        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }

        $db = new DB_Sql;
        $db->query("SELECT * FROM keyword
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
}