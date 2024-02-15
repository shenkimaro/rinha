<?php
//date_default_timezone_set('America/Sao_Paulo');

define('_CONTROLLER_PREFIX', 'Control');
define('_SYSNAMELABEL', 'Rinha');
define('_SYSNAME', 'rinha');
processaURL();
$modulo = Request::getString('modulo','');

function exception_error_handler($errno, $errstr, $errfile, $errline, $errcontext=[]) {
	$mensagem = 'Descrição: ' . $errstr . ' <br>Linha: ' . $errline . ' <br>Arquivo: ' . $errfile;
	$mensagem .= '<br>Detalhes:<br><pre>';
	$mensagem .= print_r($errcontext, true);
	$mensagem .= '</pre>';
	$exception = new ErrorException($errstr, 0, $errno, $errfile, $errline);
	$mensagem .= '<br>Stack:<br><pre>';
	$mensagem .= $exception->getTraceAsString();
	$mensagem .= '</pre>';
	if (Util::isLocalIp()) {
		Util::debug($mensagem);
	}
}


function processaURL() {
    $request = Request::getString('request');
    if($request == null){
        $rest = new Restful();
        $rest->printREST('Url inválida', Restful::STATUS_BAD_REQUEST);
    }
    $parts = explode('/', $request);
    if(count($parts) != 3){
        $rest = new Restful();
        $rest->printREST('Url inválida', Restful::STATUS_BAD_REQUEST);
    }
    $_REQUEST['modulo'] = $parts[0];
    $_REQUEST['id'] = $parts[1];
    $_REQUEST['acao'] = $parts[2];
}

set_error_handler("exception_error_handler");

$labels['op'] = "acao";
$labels['module'] = "modulo";