<?php

namespace Page;

class PageAdmin extends Page
{

    public function __construct($options = [], $tpl_dir = "/views/admin/")
    {
        parent::__construct($options, $tpl_dir);
    }

}