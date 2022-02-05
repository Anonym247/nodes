<?php

require "../vendor/autoload.php";

use Dotenv\Dotenv;
use Src\Core\DatabaseConnector;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$databaseConnection = (new DatabaseConnector())->getConnection();