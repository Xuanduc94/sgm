<?php

namespace controllers;

class Controller
{
    protected $baseUrl = "http://sgm.test";
    protected function view($view, $data = [])
    {
        if ($data != null) {
            extract($data);
        }
        require "views/$view.php";
    }
}
