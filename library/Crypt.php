<?php

/**
 * Utilitario de criptografia
 *
 * @author ibanez
 */
class Crypt {

	const HASH_WHIRLPOOL = 'whirlpool';
	const HASH_MD5 = 'md5';
	const HASH_SHA256 = 'sha256';
	const HASH_SHA512 = 'sha512';

	public static function getPublickey($res) {
		$keyArray = openssl_pkey_get_details($res);
		$pubKey = $keyArray["key"];
		return $pubKey;
	}

	/**
	 * Assina um texto retornando a assinatura e o texto
	 * @param string $cleartext
	 * @param string $private_key
	 * @return string
	 */
	public static function sign($cleartext, $private_key) {
		$msg_hash = self::hash($cleartext, self::HASH_WHIRLPOOL);
		openssl_private_encrypt($msg_hash, $sig, $private_key);
		$signed_data = $sig . "----ASSINATURA:----" . $cleartext;
		return $signed_data;
	}

	/**
	 * Retorna um texto previamente assinado com Crypt::sign()
	 * @param type $signedData
	 * @param type $publicKey
	 * @return string
	 */
	public static function getSignText($signedData, $publicKey) {
		list($old_sig, $plain_data) = explode("----ASSINATURA:----", $signedData);
		openssl_public_decrypt($old_sig, $decrypted_sig, $publicKey);
		$data_hash = self::hash($plain_data, self::HASH_WHIRLPOOL);
		if ($decrypted_sig == $data_hash && strlen($data_hash) > 0) {
			return $plain_data;
		}
		return "ERRO -- Assinatura quebrada";
	}

	/**
	 * Realiza funcao de hash use uma constante da classe Crypt
	 * @param string $data
	 * @param const $algoritmo
	 * @return string
	 */
	public static function hash($data, $algoritmo) {
		$r = hash($algoritmo, $data, false);
		return $r;
	}

	/**
	 * Lista possiveis algoritmos nativos de hash
	 * @param string $data
	 */
	public static function listaAlgoritmosResumo($data) {
		foreach (hash_algos() as $v) {
			$r = hash($v, $data, false);
			printf("%-12s %3d %s\n", $v, strlen($r), $r);
			echo "<br>";
		}
	}

	public static function encrypt($message, $password, $limit = true, $salt = '') {
		if ($limit && strlen($message) > 256) {
			throw new Exception("O valor foi perdido.");
		}
		$iv = random_bytes(16);
		$key = self::getKey($password, $salt);
		$result = self::assinar(openssl_encrypt($message, 'aes-256-ctr', $key, OPENSSL_RAW_DATA, $iv), $key);
		return bin2hex($iv) . bin2hex($result);
	}

	public static function decrypt($hash, $password, $salt = '') {
		$iv = hex2bin(substr($hash, 0, 32));
		$hashValue = substr($hash, 32);
		if (strlen($hashValue) % 2 != 0) { //precisa ter uma quantidade par
			throw new Exception("O dado foi corrompido na comunicação.");
		}
		$data = hex2bin($hashValue);
		$key = self::getKey($password, $salt);
		if (!self::verify($data, $key)) {
			throw new Exception("O dado foi perdido na comunicação.");
		}
		return openssl_decrypt(mb_substr($data, 64, null, '8bit'), 'aes-256-ctr', $key, OPENSSL_RAW_DATA, $iv);
	}

	private static function assinar($message, $key) {
		return hash_hmac('sha256', $message, $key) . $message;
	}

	private static function verify($bundle, $key) {
		return hash_equals(
				hash_hmac('sha256', mb_substr($bundle, 64, null, '8bit'), $key),
				mb_substr($bundle, 0, 64, '8bit')
		);
	}

	private static function getKey($password, $salt = '', $keysize = 16) {
		if ($salt == '') {
			$salt = date('W') . 'library' . date('W.Y'); //salt muda 1 vez por semana
		}
		return hash_pbkdf2('sha256', $password, $salt, 100000, $keysize, true);
	}

}
