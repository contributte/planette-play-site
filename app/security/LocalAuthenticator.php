<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\Security;

use Nette,
	Nette\Database\Context,
	Nette\Security,
	Nette\Security\Passwords;

class LocalAuthenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
	/**
	 * @var \Nette\Database\Context
	 */
	protected $database;

	/**
	 * User roles
	 *
	 * @var array
	 */
	protected $roles = array(
		"user" => "user",
		"admin" => "admin",
		"root" => "super user",
	);


	/**
	 * @param \Nette\Database\Context $database
	 */
	public function __construct(Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication
	 *
	 * @param array $credentials
	 *
	 * @return Security\Identity|Security\IIdentity
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$row = $this->database->table('users')
			->where('username ? OR email ?', $username, $username)
			->where('active', '1')
			->fetch();

		if (!$row) {
			throw new Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row->password)) {
			throw new Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row->password)) {
			$row->update(array(
				'password' => Passwords::hash($password),
			));
		}

		$arr = $row->toArray();
		unset($arr['password']);

		return new Security\Identity($row->id, $row->role, $arr);
	}

}
