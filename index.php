<?php
session_start();
include_once('./config/requires.php');
ini_set('display_errors', 'on');
error_reporting(E_ALL);
(new Controller())->start();