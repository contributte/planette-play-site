<?php

namespace App\Security;

use App\Model\UserManager,
	Nette,
	Nette\Utils\Json;


class NetteOrgAuthenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
	/** @var string */
	private $authKey;

	/** @var UserNamager */
	private $userManager;


	public function __construct($authKey, UserManager $userManager)
	{
		if (!extension_loaded('mcrypt')) {
			throw new \LogicException("PHP extension 'mcrypt' is missing.");
		}

		$this->authKey = $authKey;
		$this->userManager = $userManager;
	}


	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$mcrypt = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($mcrypt), MCRYPT_DEV_RANDOM);
		mcrypt_generic_init($mcrypt, $this->authKey, $iv);

		$url = $this->buildAuthUrl($username, $password, $mcrypt, $iv);
		list($code, $body) = $this->httpGet($url);

		if ($code === 404) {
			throw new Nette\Security\AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
		} elseif ($code === 403) {
			throw new Nette\Security\AuthenticationException('Invalid password.', self::INVALID_CREDENTIAL);
		} elseif ($code !== 200) {
			throw new Nette\Security\AuthenticationException("Nette.org endpoint hung with code $code.");
		}

		$json = Json::decode(trim(mdecrypt_generic($mcrypt, $body)));

		$user = $this->userManager->signInUpdate($json->id, [
			'username' => $username,
			'email' => $json->email,
			'name' => $json->realname,
		]);

		if (!$user) {
			$user = $this->userManager->create([
				'id' => $json->id,
				'username' => $username,
				'password' => '',
				'email' => $json->email,
				'role' => 'user',
				'active' => TRUE,
				'name' => $json->realname,
				'avatar' => '',
			]);
		}

		return new Nette\Security\Identity($user->id, $user->role, [
			'username' => $user->username,
			'name' => $user->name,
			'email' => $user->email,
		]);
	}


	/**
	 * @return string
	 */
	private function buildAuthUrl($username, $password, $mcrypt, $iv)
	{
		return 'http://nette.org/loginpoint.php?' . http_build_query([
			'name' => $username,
			'password' => base64_encode(mcrypt_generic($mcrypt, $password)),
			'iv' => base64_encode($iv),
		]);
	}


	private function httpGet($url)
	{
		$context = stream_context_create([
			'http' => [
				'method' => 'GET',
				'follow_location' => 1,
				'protocol_version' => 1.1,
				'ignore_errors' => TRUE,
			],
		]);

		$e = NULL;
		set_error_handler(function($severity, $message, $file, $line) use (& $e) {
			$e = new \ErrorException($message, 0, $severity, $file, $line, $e);
		}, E_WARNING);

		$body = file_get_contents($url, FALSE, $context);
		restore_error_handler();

		if (!isset($http_response_header)) {
			throw new \RuntimeException('Missing HTTP headers, request failed.', 0, $e);
		}

		if (!isset($http_response_header[0]) || !preg_match('~^HTTP/1[.]. (\d{3})~i', $http_response_header[0], $m)) {
			throw new \RuntimeException('HTTP status code is missing.', 0, $e);
		}

		return [(int) $m[1], $body];
	}

}
