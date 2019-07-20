<?php

namespace App\Utils;

abstract class DomainAbstract
{
    public function d($data = [], $die = true)
    {
        echo '<pre>' . print_r($data, true) . '</pre>';
        if ($die) {
            die();
        }
    }
}
