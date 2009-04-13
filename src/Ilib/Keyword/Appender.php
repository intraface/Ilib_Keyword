<?php
class Ilib_Keyword_Appender extends Ilib_Keyword
{
    protected $object;
    protected $type;
    protected $extra_conditions;
    protected $belong_to_id = 0;
    public $error;

    function __construct($object)
    {
        /*
        if (get_class($object) == 'FakeKeywordAppendObject') {
            $this->type = 'contact';
            $this->object = $object;
            $this->kernel = $object->getKernel();
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
                case 'ilib_filehandler_gateway':
                    $this->type = 'file_handler';
                    $this->object = $object;
                    break;
                case 'vih_news':
                    $this->type = 'vih_news';
                    $this->object = $object;
                    break;
                default:
                    trigger_error(get_class($this) . ' kræver enten Customer, CMSPage, Product eller FileManager som object. Fik ' . get_class($object), E_USER_ERROR);
                    break;
            }
        }
        */

        $this->object = $object;
        $this->kernel = $this->object->getKernel();
        $this->type = get_class($this->object);

        if (method_exists($this->object, 'getId')) {
            $this->belong_to_id = $this->object->getId();
        }
        $this->error = new Ilib_Error;

        $this->extra_conditions = array('intranet_id' => $this->object->getKernel()->intranet->get('id'));
    }

    function getBelongToId()
    {
        return $this->belong_to_id;
    }

    /**
     * Denne funktion tilføjer et nøgleord til et objekt
     *
     * @param integer $keyword_id
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
     * @param array $keywords
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
     * Returnerer de keywords der bliver brugt på nogle poster
     * Især anvendelig til søgeoversigter
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
     * Returnerer de keywords, der er tilføjet til et objekt
     *
     * Det er meget mærkeligt, men den her funktion returnerer alle keywords på et intranet?
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

    ///////////////////////////////////////////////////////////////////////////
    // INGEN DB I DE FOLGENDE
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Returnerer de vedhæftede keywords som en streng
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
