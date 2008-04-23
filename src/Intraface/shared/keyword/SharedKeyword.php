<?php
/**
 *
 * @package Ilib_Keyword
 * @author	<Sune>
 * @since	1.0
 * @version	1.0
 *
 */
class SharedKeyword extends Shared
{

    function __construct()
    {
        $this->shared_name = 'keyword'; // Navn på på mappen med modullet
        $this->active = 1; // Er shared aktivt

        $this->addPreloadFile('Keyword.php');
    }
}