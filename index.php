<?php
echo '<pre>';
print_r($_SERVER);die;
session_start();
include_once('./config/requires.php');
Util::debug($_REQUEST);
ini_set('display_errors', 'on');
error_reporting(E_ALL);
(new Controller())->start();