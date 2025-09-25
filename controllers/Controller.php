<?php
class Controller
{
    protected function view($view, $data = [])
    {
        if ($data != null) {
            extract($data);
        }
        require "views/$view.php";
    }
}
