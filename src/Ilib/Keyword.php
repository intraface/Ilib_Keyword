<?php
/**
 * Keyword
 *
 * @author Lars Olesen <lars@legestue.net>
 * @package Ilib_Keyword
 */
require_once 'Ilib/Keyword/functions.php';

class Ilib_Keyword
{
    /**
     * @var object
     */
    protected $adapter;

    /**
     * @var object
     */
    protected $db;

    /**
     * @var object
     */
    protected $error;

    /**
     * @var array
     */
    protected $extra_conditions = array();

    /**
     * Constructor
     *
     * @param object  $db
     * @param object  $error
     * @param object  $adapter
     * @param array   $extra_condition
     * @param integer $id
     *
     * @return void
     */
    function __construct(DB_Sql $db, Ilib_Error $error, Ilib_KeywordTypeAdapter $adapter, $extra_condition, $id = 0)
    {
        $this->adapter = $adapter;
        $this->db = $db;
        $this->error = $error;
        $this->extra_conditions = $extra_condition;
    }

    /**
     * Loads the object
     *
     * @return boolean
     */
    protected function load()
    {
        $condition = $this->extra_conditions;
        $condition['id'] = $this->id;
        $condition['keyword.type'] = $this->adapter->identify();

        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }

        $this->db->query("SELECT id, keyword FROM keyword
            WHERE " . implode(' AND ', $c));

        if (!$this->db->nextRecord()) {
            return false;
        }
        $this->value['id'] = $this->db->f('id');
        $this->value['keyword'] = $this->db->f('keyword');
        return true;
    }

    /**
     * Validates
     *
     * @param array $var
     *
     * @return boolean
     */
    protected function validate($var)
    {
        $validator = new Ilib_Validator($this->error);

        if (!empty($var['id'])) {
            $validator->isNumeric($var['id'], 'id', 'allow_empty');
        }
        if (empty($var['keyword'])) {
            $this->error->set("Du har ikke skrevet et nøgleord");
        }

        if ($this->error->isError()) {
            return false;
        }
        return true;
    }

    /**
     * Saves a keyword
     *
     * @param array $var
     *
     * @return integer
     */
    public function save($var)
    {
        $var['keyword'] = str_replace('"', '', $var['keyword']);
        $var = safeToDb($var);
        $var = array_map('strip_tags', $var);

        if (!$this->validate($var)) {
            return false;
        }
        $c = array();
        $condition = $this->extra_conditions;
        $condition['type'] = $this->adapter->identify();
        $condition['keyword'] = $var['keyword'];
        $condition['active'] = 1;

        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }

        $this->db->query("SELECT id, active FROM keyword
            WHERE " . implode(' AND ', $c));

        if ($this->db->nextRecord()) {
            $this->id = $this->db->f('id');
            return $this->id;
        }

        if ($this->id > 0) {
            $c = array();
            $condition = array();
            $condition = $this->extra_conditions;
            $condition['id'] = $this->id;
            $condition['type'] = $this->adapter->identify();

            foreach ($condition as $column => $value) {
                $c[] = $column . " = '" . $value . "'";
            }

            $sql_type = 'UPDATE ';
            $sql_end = ' WHERE ' . implode(' AND ', $c);

        } else {
            $c = array();
            $condition = array();
            $condition = $this->extra_conditions;
            $condition['type'] = $this->adapter->identify();

            foreach ($condition as $column => $value) {
                $c[] = $column . " = '" . $value . "'";
            }

            $sql_type = "INSERT INTO ";
            $sql_end = ", " . implode(', ', $c);
        }

        $sql = $sql_type . "keyword SET keyword = '".$var['keyword']."'" . $sql_end;
        $this->db->query($sql);

        if ($this->id == 0) {
            $this->id = $this->db->insertedId();
        }
        $this->load();
        return $this->id;
    }

    /**
     * Deletes a keyword
     *
     * @return boolean
     */
    function delete()
    {
        $condition = $this->extra_conditions;
        $condition['id'] = $this->id;
        $condition['type'] = $this->adapter->identify();
        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }

        $this->db->query("UPDATE keyword SET active = 0
            WHERE " . implode(' AND ', $c));

        return true;
    }
}
