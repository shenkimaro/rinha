<?php

//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\///\\//\\//
//Log Desenvolvimento
$configLog['db'] = "rinha";
$configLog['login'] = "postgres";
$configLog['password'] = "teste";
//Log Producao
$configLog['_db'] = "rinha";
$configLog['_login'] = "postgres";
$configLog['_password'] = "teste";

$configLog['status'] = FALSE;

$configLog['table'] = "logs";
$configLogVar['ip_cliente'] = isset($HTTP_SERVER_VARS) && trim(@$HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]) ? @$HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"] : $_SERVER['REMOTE_ADDR'];
if ($configLog['status']) {
	$usuario_logado = PermissoesService::getUsuarioLogado();
	if (is_object($usuario_logado)) {
		$configLogVar['fk_usuario'] = $usuario_logado->getId();
		$configLogVar['nome_usuario'] = $usuario_logado->getNome();
	}
}
$configLogVar['data'] = date("Y-m-d");
$configLogVar['hora'] = date("H:i:s");
