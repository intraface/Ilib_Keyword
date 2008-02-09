<?php
require_once 'Intraface/Kernel.php';
$kernel = new FakeKernel;

require_once 'Intraface/Setting.php';
$kernel->setting = new FakeSetting;

require_once 'Intraface/Intranet.php';
$kernel->intranet = new FakeIntranet;

require_once 'Intraface/User.php';
$kernel->user = new FakeUser;

if(!defined('PATH_UPLOAD_TEMPORARY')) {
    define('PATH_UPLOAD_TEMPORARY', 'tempdir/');
}

