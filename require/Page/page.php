<?php

namespace Page;
use Rain\Tpl;

class Page
{

    private $tpl;
    private $options;
    private  $defaults = [
        "data" => []
    ];

    public function __construct($options = [], $tpl_dir = "/views/"){

        $this->options = array_merge($this->defaults, $options);

        $config = array(
            "tpl_dir"     => $_SERVER['DOCUMENT_ROOT'].$tpl_dir,
            "cache_dir"   => $_SERVER['DOCUMENT_ROOT']."/views-cache/",
            "debug"       => false
        );

        Tpl::configure($config);
        $this->tpl = new Tpl();

        $this->setData($this->options["data"]);
    }

    private function setData($data = []){
        foreach($data as $indice => $valor){
            $this->tpl->assign($indice, $valor);
        }
    }

    public function setTpl($name, $data = [], $returnHTML = false){
        $this->setData($data);

        return $this->tpl->draw($name, $returnHTML);
    }

}