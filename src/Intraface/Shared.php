<?php

class FakeShared {

    private $shared;

    public function __construct($shared) {
        $this->shared = $shared;
    }

    public function getPath()
    {
        return PATH_WWW.'shared/'.$this->shared.'/';
    }

    public function includeFile($file) {
        require_once 'Intraface/shared/'.$this->shared.'/'.$file;
    }
}
