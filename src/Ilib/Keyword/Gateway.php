<?php
/**
 *
 *
 *
 * PHP version 5
 *
 * @category
 * @package
 * @author     lsolesen
 * @copyright
 * @license   http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version
 * @filesource
 * @link
 */
class Ilib_Keyword_Gateway
{
    /**
     * @var array
     */
    private $extra_conditions;

    /**
     * Constructor
     *
     * @param array $extra_conditions
     *
     * @return void
     */
    function __construct($extra_conditions = array())
    {
        $this->extra_conditions = $extra_conditions;
    }

    /**
     * Set conditions
     *
     * @param array $conditions
     *
     * @return void
     */
    function setExtraConditions($conditions)
    {
    	$this->extra_conditions = $conditions;
    }

    /**
     * Returns all keywords
     *
     * @param string $type
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

    function findByTypeAndId($type, $id)
    {
        return new Ilib_Keyword($type, $this->extra_conditions, $id);
    }
}