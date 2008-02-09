<?php


class FakeUser {


    public function get($key) {

        $values = array('id' => 1);

        return $values[$key];
    }

    public function hasModuleAccess() {
        return true;
    }

}
