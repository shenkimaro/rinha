<?php

/**
 * Classe responsável por manter as configurações de banco globais às aplicações.
 *
 * Também faz o tratamento do banco que o usuário seleciona tela de login (em ambiente de desenvolvimento)
 * para que as demais aplicações possam utilizar o mesmo banco.
 *
 * @version 1.0
 */
class DatabaseConfig {

	const LOCALHOST = '127.0.0.1';
	const DNS_PRODUCAO = 'www.adms.ueg.br';
	const PORTA_DEFAULT = '5432';

	//DESENVOLVIMENTO
	const HOST_DEV_DEFAULT = '10.20.60.33';
	// const BANCO_DEV_DEFAULT = "ueg_central_20200830";
	const BANCO_DEV_DEFAULT = "ueg_central_24h";
	const USUARIO_DEV = 'postgres';
	const SENHA_DEV = 'Hitokiri_Battousai';
	
	//Homologacao
	const HOST_HOMOLOG_DEFAULT = '10.20.3.37';
	const BANCO_HOMOLOG_DEFAULT = "ueg_central_20180131";
	const USUARIO_HOMOLOG = 'sistema';
	const SENHA_HOMOLOG = 'mteste123';

	//PRODUCAO
	const HOST_PROD = 'pgsql.ueg.br';
	const BANCO_PROD = 'ueg_central';

	//LOGS
	const HOST_LOGS = "pgsql.ueg.br";
	const BANCO_LOGS = "ueg_central_logs";
	const USUARIO_LOGS = "user_logs";
	const SENHA_LOGS = "user_logs753";

	/**
	 * Obtém o nome do banco que a aplicação deve utilizar, dando prioridade para o banco que o usuário escolher na tela de login.
	 * Caso o usuário não escolha nenhum banco, o banco default será utilizado
	 *
	 * @see DatabaseConfig::BANCO_DEV_DEFAULT
	 *
	 * @return string Nome do banco
	 */
	public static function getBancoDev() {
		if (isset($_SESSION["auth"]["_banco"]) && $_SESSION["auth"]["_banco"] != '') {
			return $_SESSION["auth"]["_banco"];
		} else {
			return self::BANCO_DEV_DEFAULT;
		}
	}
	

	/**
	 * Obtém a url do servidor de banco de dados, dando prioridade para o host que o usuário escolher na tela de login.
	 * Caso o usuário não escolha nenhum host, o host default será utilizado
	 *
	 * @see DatabaseConfig::HOST_DEV_DEFAULT
	 *
	 * @return string Url do servidor de banco de dados
	 */
	public static function getHostDev() {
		if (isset($_SESSION["auth"]["_host"]) && $_SESSION["auth"]["_host"] != '') {
			return $_SESSION["auth"]["_host"];
		} else {
			return self::HOST_DEV_DEFAULT;
		}
	}


}
