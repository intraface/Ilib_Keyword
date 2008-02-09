<?php

class FakeIntranet {


    public function get($key) {
        // if id should be other than 0 you need to add options to the use of Ilib classes with 'intranet_id = ?'
        $values = array(
            'id' => 0,
            'public_key' => 'vih');

        return $values[$key];
    }
}
