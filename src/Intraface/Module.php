<?php

class FakeModule {

    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }


    public function getPath()
    {
        return PATH_WWW.'modules/'.$this->module.'/';
    }

}
