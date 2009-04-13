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
require_once 'Ilib/Keyword/functions.php';

abstract class Ilib_Abstract_Keyword
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $types = array();

    /**
     * @var array
     */
    protected $value = array();

    /**
     * Constructor
     *
     * @return void
     */
    function __construct($type, $extra_conditions = array(), $id = 0)
    {
        $this->id = (int)$id;
        $this->error = new Ilib_Error;
        $this->extra_conditions = $extra_conditions;
        $this->type = $type;
        // @todo before this is changed we need to change all the data in the database
        //$this->type = $this->getTypeKey($this->type);

        if ($this->id > 0) {
            $this->load();
        }
    }

    /**
     * Gets a type for a type key
     *
     * @param integer $key The key for a type
     *
     * @return string
     */
    function getType($key)
    {
        return $this->types[$key];
    }

    /**
     * Gets a type key
     *
     * @param string  $identifier Identifier for a type
     *
     * @return integer
     */
    function getTypeKey($identifier)
    {
        if (!$key = array_search($identifier, $this->types)) {
            throw new Exception('No type registered with this identifier ' . $identifier);
        }
        return $key;
    }

    /**
     * Register a type
     *
     * @param integer $key        The key for a type
     * @param string  $identifier Identifier for a type
     *
     * @return void
     */
    function registerType($key, $identifier)
    {
        $this->types[$key] = $identifier;
    }

    /**
     * Gets the keyword
     *
     * @return string
     */
    function getKeyword()
    {
        return $this->value['keyword'];
    }

    /**
     * Gets the id for a keyword
     *
     * @return integer
     */
    function getId()
    {
        return $this->id;
    }

}

class Ilib_Keyword extends Ilib_Abstract_Keyword
{
    /**
     * @var object
     */
    protected $object;

    /**
     * @var object
     */
    public $error;

    /**
     * @var array
     */
    protected $extra_conditions = array();

    /**
     * Constructor
     *
     * @param object  $object
     * @param integer $id
     *
     * @return void
     */
    function __construct($object, $id = 0)
    {
        /*
        //@todo type gaar igen som fast parameter
        $this->registerType(0, '_invalid_');
        $this->registerType(1, 'contact');
        $this->registerType(2, 'product');
        $this->registerType(3, 'cms_page');
        $this->registerType(4, 'ilib_filehandler');
        $this->registerType(5, 'cms_template');
        $this->registerType(6, 'vih_news');


        if (get_class($object) == 'FakeKeywordObject') {
            $this->type = 'contact';
            $this->object = $object;
            $this->kernel = $object->kernel;
        } else {

            switch (strtolower(get_class($object))) {
                case 'contact':
                    $this->type = 'contact';
                    $this->object = $object;
                    break;
                case 'product':
                    $this->type = 'product';
                    $this->object = $object;
                    $this->object->load();
                    break;
                case 'cms_page':
                    $this->type = 'cms_page';
                    $this->object = $object;
                    break;
                case 'cms_template':
                    $this->type = 'cms_template';
                    $this->object = $object;
                    break;
                case 'filemanager':
                    $this->type = 'file_handler';
                    $this->object = $object;
                    break;
                case 'ilib_filehandler':
                    $this->type = 'file_handler';
                    $this->object = $object;
                    break;
                case 'ilib_filehandler_manager':
                    $this->type = 'file_handler';
                    $this->object = $object;
                    break;
                case 'vih_news':
                    $this->type = 'vih_news';
                    $this->object = $object;
                    break;
                default:
                    trigger_error('Keyword got ' . get_class($object), E_USER_ERROR);
                    break;
            }
        }
        */

        $this->object = $object;

        if (!method_exists($this->object, 'getKernel')) {
            throw new Exception('The object has to implement getKernel()');
        }

        $this->kernel = $this->object->getKernel();
        $this->type = get_class($this->object);

        $extra_conditions = array('intranet_id' => $this->kernel->intranet->get('id'));

        parent::__construct($this->type, $extra_conditions, $id);

        //$object_id = $this->object->get('id');

    }

    /**
     * Skal factory bare tage en kernel og en id og så selv lave objektet,
     * eller skal det være omvendt at factory bruges til at smide et objekt ind i
     * klassen - og at Keyword selv laver objektet?
     *
     * @param object  $kernel
     * @param integer $id
     *
     * @return object
     */
    /*
    public function factory($kernel, $id)
    {
        $id = (int)$id;

        $db = new DB_Sql;
        $db->query("SELECT id, type FROM keyword WHERE id = " . $id . " AND intranet_id=" . $kernel->intranet->get('id'));
        if (!$db->nextRecord()) {
            return 0;
        }

        $class = $db->f('type');

        if (strtolower(get_class($kernel)) == 'fakekeywordkernel') {
            return new Keyword(new FakeKeywordObject(), $db->f('id'));
        }
        $kernel->useModule($class);
        return new Keyword(new $class($kernel), $db->f('id'));
    }
    */

    /**
     * Loader det enkelte keyword
     *
     * @return boolean
     */
    protected function load()
    {
        $condition = $this->extra_conditions;
        $condition['id'] = $this->id;
        $condition['keyword.type'] = $this->type;

        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }

        $db = new DB_Sql;
        $db->query("SELECT id, keyword FROM keyword
            WHERE " . implode(' AND ', $c));

        if (!$db->nextRecord()) {
            return false;
        }
        $this->value['id'] = $db->f('id');
        $this->value['keyword'] = $db->f('keyword');
        //$this->value['type'] = $db->f('type');
        return true;
    }

    /**
     * Validerer
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
     * Gemmer et keyword
     *
     * @param array $var
     *
     * @return integer
     */
    public function save($var)
    {
        settype($var['keyword'], 'string');

        $var['keyword'] = str_replace('"', '', $var['keyword']);
        $var = safeToDb($var);
        $var = array_map('strip_tags', $var);

        if (!$this->validate($var)) {
            return false;
        }
        $c = array();
        $condition = $this->extra_conditions;
        $condition['type'] = $this->type;
        $condition['keyword'] = $var['keyword'];
        $condition['active'] = 1;

        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }

        $db = new DB_Sql;
        $db->query("SELECT id, active FROM keyword
            WHERE " . implode(' AND ', $c));

        if ($db->nextRecord()) {
            $this->id = $db->f('id');
            return $db->f('id');
        }

        if ($this->id > 0) {
            $c = array();
            $condition = array();
            $condition = $this->extra_conditions;
            $condition['id'] = $this->id;
            $condition['type'] = $this->type;

            foreach ($condition as $column => $value) {
                $c[] = $column . " = '" . $value . "'";
            }

            $sql_type = 'UPDATE ';
            $sql_end = ' WHERE ' . implode(' AND ', $c);

        } else {
            $c = array();
            $condition = array();
            $condition = $this->extra_conditions;
            $condition['type'] = $this->type;

            foreach ($condition as $column => $value) {
                $c[] = $column . " = '" . $value . "'";
            }

            $sql_type = "INSERT INTO ";
            $sql_end = ", " . implode(', ', $c);
        }

        $sql = $sql_type . "keyword SET keyword = '".$var['keyword']."'" . $sql_end;
        $db->query($sql);

        if ($this->id == 0) {
            $this->id = $db->insertedId();
        }
        $this->load();
        return $this->id;
    }

    /**
     * Denne metode sletter et nøgleord i nøgleordsdatabasen
     *
     * @return boolean
     */
    function delete()
    {
        if ($this->id == 0) {
            return false;
        }

        $condition = $this->extra_conditions;
        $condition['id'] = $this->id;
        $condition['type'] = $this->type;
        foreach ($condition as $column => $value) {
            $c[] = $column . " = '" . $value . "'";
        }

        $db = new DB_Sql;
        $db->query("UPDATE keyword SET active = 0
            WHERE " . implode(' AND ', $c));

        return true;
    }

    /**
     * Egentlig en slags getList i keywords
     *
     * @return array
     */
    function getAllKeywords()
    {
        $gateway = new Ilib_Keyword_Gateway($this->extra_conditions);
        return $gateway->getAllKeywordsFromType($this->type);

        /*
        $keywords = array();

        $condition = $this->extra_conditions;
        $condition['keyword.type'] = $this->type;
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
        */
    }
}
