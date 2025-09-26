<?php

class ContrucstionController extends Controller
{

    public function getAll()
    {
        $contructionModel =  new ContructionModel();
        $data = $contructionModel->getAll();
        echo json_encode($data);
    }

    public function index()
    {
        $contructionModel =  new ContructionModel();
        $data = $contructionModel->getAll();
        return $this->view('index', $data);
    }

    public function insert()
    {
        $contructionModel =  new ContructionModel();
        $data = $contructionModel->insert();
        echo "OK";
    }
}
