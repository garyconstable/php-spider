<?php

namespace App\Utils;

abstract class DomainAbstract
{
    protected $parts = [];
    protected $name = "";
    protected $prefix = "";
    protected $suffix = "";

    public function d($data = [], $die = true)
    {
        echo '<pre>' . print_r($data, true) . '</pre>';
        if ($die) {
            die();
        }
    }

    public function getMaterials()
    {
        return [
            'name' => $this->name,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
        ];
    }

    public function __construct($domain)
    {
        $this->extract($domain);
        $this->assign();
    }

    public function assign()
    {
        $this->prefix = $this->parts->getSubdomain();
        $this->name = $this->parts->getHostname();
        $this->suffix = $this->parts->getSuffix();
    }
}
