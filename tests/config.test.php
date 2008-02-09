<?php
require_once 'MDB2.php';
define('DB_HOST', 'localhost');
define('DB_PASS', '');
define('DB_USER', 'root');
define('DB_NAME', 'intraface');
define('DB_DSN', 'mysql://root:@localhost/intraface');
define('PATH_ROOT', 'c:\Users/Lars Olesen\workspace\intraface\\');
define('PATH_INCLUDE_CONFIG', PATH_ROOT . 'Intraface\config\\');
define('XMLRPC_PATH', PATH_ROOT . 'intraface.dk/xmlrpc/');
define('PATH_INCLUDE_MODULE', 'c:/Users/Lars Olesen/workspace/intraface/Intraface/modules/');
define('PATH_INCLUDE_SHARED', 'c:/Users/Lars Olesen/workspace/intraface/Intraface/shared/');
define('CONNECTION_INTERNET', 'ONLINE');
define('PATH_UPLOAD', realpath(PATH_ROOT . 'upload/'));
define('PATH_UPLOAD_TEMPORARY', 'tempdir\\');
define('FILE_VIEWER', '');
define('PATH_WWW', '');
define('IMAGE_LIBRARY', 'GD');
define('TEST_PATH_TEMP', './');


$db = MDB2::singleton(DB_DSN);
$db->setOption('debug', 0);
$db->setOption('portability', MDB2_PORTABILITY_NONE);

if ($db->getOption('debug')) {
    $db->setOption('log_line_break', "\n\n\n\n\t");

    require_once 'MDB2/Debug/ExplainQueries.php';

    $my_debug_handler = new MDB2_Debug_ExplainQueries($db);
    $db->setOption('debug_handler', array($my_debug_handler, 'collectInfo'));

    register_shutdown_function(array($my_debug_handler, 'executeAndExplain'));
    register_shutdown_function(array($my_debug_handler, 'dumpInfo'));
}

define('PATH_INCLUDE_PATH', 'c:/Users/Lars Olesen/workspace/ilib/Redirect/src/' . PATH_SEPARATOR . 'c:/Users/Lars Olesen/workspace/ilib/Error/src/' . PATH_SEPARATOR . 'c:/Users/Lars Olesen/workspace/ilib/Random/src/' . PATH_SEPARATOR . 'c:/Users/Lars Olesen/workspace/intrafacelibraries/DBQuery/src/' . PATH_SEPARATOR . 'c:/Users/Lars Olesen/workspace/intrafacelibraries/Error/src/' . PATH_SEPARATOR . 'c:/Users/Lars Olesen/workspace/intrafacelibraries/Redirect/src/' . PATH_SEPARATOR . 'c:/Users/Lars Olesen/workspace/intrafacelibraries/ErrorHandler/src/' .  PATH_SEPARATOR . get_include_path());

set_include_path(dirname(__FILE__) . '/../src/' . PATH_SEPARATOR . PATH_INCLUDE_PATH);
?>