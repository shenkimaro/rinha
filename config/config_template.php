<?php

define('_TEMPLATE_MANAGER', View::ENGINE_JSONVIEW);
define('_TEMPLATE_PAGINATION_MAX_ROWS', '20');

$template['blockPrefix'] = "";
$template['extension'] = ".html";
$template['templateDefault'] = "interface/modulos/error/index";
$template['css'] = 'css'; //Nome do css padrao
define('_templateIndex', $template['templateDefault']);
