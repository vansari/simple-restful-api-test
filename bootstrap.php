<?php

use Api\System\Database\MySqlConnection;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$connection = new MySqlConnection();