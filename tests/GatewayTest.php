<?php
require_once dirname(__FILE__) . '/config.test.php';
require_once 'PHPUnit/Framework.php';
require_once 'Ilib/ClassLoader.php';

set_include_path(PATH_INCLUDE_PATH);

if (!class_exists('FakeKeywordIntranet')) {
class FakeKeywordIntranet
{
    function get()
    {
        return 1;
    }
}
}

if (!class_exists('FakeKeywordKernel')) {
class FakeKeywordKernel
{
    public $intranet;

    function __construct()
    {
        $this->intranet = new FakeKeywordIntranet;
    }

    function useModule()
    {
        return true;
    }
}
}

if (!class_exists('FakeKeywordObject')) {
class FakeKeywordObject
{
    public $kernel;

    function __construct()
    {
        $this->kernel = new FakeKeywordKernel;
    }

    function get()
    {
        return 1;
    }

    function getKernel()
    {
    	return $this->kernel;
    }
}
}

if (!class_exists('MyKeyword')) {

class MyKeyword extends Ilib_Keyword
{
    function __construct($object, $id = 0)
    {
        $this->registerType(1, 'cms');
        $this->registerType(2, 'contact');
        parent::__construct($object, $id);
    }
}
}
if (!class_exists('FakeKeywordKeyword')) {

class FakeKeywordKeyword
{
    public $id;
    public $keyword;

    function __construct($id = 1, $keyword = 'test')
    {
        $this->id = $id;
        $this->keyword = $keyword;
    }

    function getId()
    {
        return $this->id;
    }

    function getKeyword()
    {
        return $this->keyword;
    }
}
}
class GatewayTest extends PHPUnit_Framework_TestCase
{
    protected $backupGlobals = false;

    private $gateway;

    function setUp()
    {
        $this->gateway = $this->getGateway();
    }

    function _tearDown()
    {
        $db = MDB2::factory(DB_DSN);
        $db->query('TRUNCATE keyword');
        $db->query('TRUNCATE keyword_x_object');
    }

    function saveKeyword($keyword = 'test')
    {
        $data = array('keyword' => $keyword);
        $keyword = $this->createKeyword();
        return $keyword->save($data);
    }

    function createKeyword($id = 0)
    {
        return new MyKeyword(new FakeKeywordObject, $id);
    }

    function getGateway()
    {
        return new Ilib_Keyword_Gateway();
    }

    function testGetAllKeywords()
    {
        if (!$id = $this->saveKeyword()) {
        	$this->assertFalse(true, 'Could not save keyword');
        }
        $keywords = $this->gateway->getAllKeywordsFromType('FakeKeywordObject');
        $this->assertEquals($id, $keywords[0]['id']);
        $this->assertEquals('test', $keywords[0]['keyword']);
    }
}

