<?php

class Restful {

	const STATUS_OK = 200;
	const STATUS_CRIADO = 201;
	const STATUS_NO_CONTENT = 204;
	const STATUS_BAD_REQUEST = 400;
	const STATUS_SEM_AUTORIZACAO = 401;
	const STATUS_NAO_PERMITIDO = 403;
	const STATUS_NAO_ENCONTRADO = 404;
	const STATUS_METODO_NAO_PERMITIDO = 405;
	const STATUS_ERRO_INTERNO_SERVIDOR = 500;
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';
	const OUTPUTMETHOD_XML = 'xml';
	const OUTPUTMETHOD_JSON = 'json';

	private $tipo_saida;

	function __construct() {
		$this->escolheTipoSaida();
	}

	/**
	 * Retorna os valores requisitados do cliente
	 * @return array[key] = value
	 */
	public function getREQUEST() {
		foreach ($_REQUEST as $key => $value) {
			$array[$key] = $value;
		}
		foreach ($_SERVER as $key => $value) {
			if (strpos($key, 'HTTP_') === FALSE) {
				continue;
			}
			$key = str_replace('HTTP_', '', $key);
			$array[strtolower($key)] = $value;
		}
		return $array;
	}

	/**
	 * 
	 * @return array
	 */
	public function getRequestHeaders() {
		return apache_request_headers();
	}

	/**
	 *  Retorna o metodo requisitado pelo cliente
	 * @return string
	 */
	public function getMethod() {
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Usada para converter ISO para UTF
	 * @param string $item
	 */
	public static function formatUTF8(&$item) {
		if (!is_numeric($item)) {
			$item = utf8_encode($item);
		}
	}

	/**
	 *
	 * @param array $data
	 * @param integer $status
	 */
	public function printREST($data, $status = Restful::STATUS_OK) {
		$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
		if ((strpos($origin, 'ueg.br') !== false) || (Util::isLocalIp() || Util::isBeta())) {
			header("Access-Control-Allow-Origin: {$origin}");
			header('Access-Control-Allow-Credentials: true');
		}
		header("Access-Control-Allow-Methods: *");
		header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));

		if ($this->tipo_saida == 'json') {
			header("Content-Type: application/{$this->tipo_saida};");
			echo json_encode($data);
			die();
		}

		if ($this->tipo_saida == 'xml') {
			header("Content-Type: text/{$this->tipo_saida};charset=utf-8");
			echo $this->xml_encode($data);
			die();
		}
	}

	public function setTipoSaida($tipoSaida) {
		$this->tipo_saida = $tipoSaida;
	}

	private function xml_encode($data) {
		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		if (isset($data[0])) {
			$xml .= '<registros>';
		}
		foreach ($data as $key => $value) {
			if (is_string($value)) {
				$xml .= "<$key>$value</$key>";
			} else if (is_array($value)) {
				$xml .= "<registro>";
				foreach ($value as $key1 => $value1) {
					if (is_string($value1)) {
						$xml .= "<$key1>$value1</$key1>";
					} elseif (is_array($value1)) {
						$xml .= "<$key1>";
						foreach ($value1 as $key2 => $value2) {
							$xml .= "<$key2>$value2</$key2>";
						}
						$xml .= "</$key1>";
					}
				}
				$xml .= "</registro>";
			}
		}
		if (isset($data[0])) {
			$xml .= '</registros>';
		}
		return $xml;
	}

	private function requestStatus($code) {
		$status = array(
			self::STATUS_OK => 'OK',
			self::STATUS_CRIADO => 'Criado',
			self::STATUS_NO_CONTENT => 'No Content',
			self::STATUS_BAD_REQUEST => 'Bad Request',
			self::STATUS_SEM_AUTORIZACAO => 'Sem Autorizacao',
			self::STATUS_NAO_PERMITIDO => 'Nao Permitido',
			self::STATUS_NAO_ENCONTRADO => 'Nao Encontrado',
			self::STATUS_METODO_NAO_PERMITIDO => 'Metodo nao permitido',
			self::STATUS_ERRO_INTERNO_SERVIDOR => 'Erro Interno do Servidor',
		);
		return (isset($status[$code])) ? $status[$code] : $status[500];
	}

	public function escolheTipoSaida() {
		if (!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] == '') {
			$this->setTipoSaida('json');
			return;
		}
		$content = $_SERVER['CONTENT_TYPE'];
		if (!(strpos($content, self::OUTPUTMETHOD_XML) === FALSE)) {
			$this->setTipoSaida('xml');
		} else if (strpos($content, self::OUTPUTMETHOD_JSON)) {
			$this->setTipoSaida('json');
		} else {
			$this->setTipoSaida('json');
		}
	}

}

?>