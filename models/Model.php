<?php
require 'vendor/autoload.php';

use Medoo\Medoo;
use PDO;

include_once 'models/Model.php';
class Model
{
    protected $database;
    public function __construct()
    {
        $this->database  = new Medoo([
            // [required]
            'type' => 'mysql',
            'host' => 'localhost',
            'database' => 'sgm',
            'username' => 'root',
            'password' => '',

            // [optional]
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'port' => 3306,

            'logging' => true,
            'error' => PDO::ERRMODE_SILENT,

            // [optional]
            // The driver_option for connection.
            // Read more from http://www.php.net/manual/en/pdo.setattribute.php.
            'option' => [
                PDO::ATTR_CASE => PDO::CASE_NATURAL
            ],

            // [optional] Medoo will execute those commands after the database is connected.
            'command' => [
                'SET SQL_MODE=ANSI_QUOTES'
            ]
        ]);
    }
}
