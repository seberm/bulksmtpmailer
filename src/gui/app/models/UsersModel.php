<?php
use Nette\Security;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Security\IAuthenticator;
use Nette\Object;
use Nette\Debug;

class UsersModel extends Object implements IAuthenticator {

	public function authenticate(array $credentials) {

        list($username, $password) = $credentials;
        
		$row = Model::$database->query('SELECT id, realName, password FROM Users WHERE login=?', $username)->fetch();

		if (!$row) 
			throw new AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);

		if ($row->password !== $this->calculateHash($password))
			throw new AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);

		unset($row->password);

        $data = array('realName' => $row->realName,
                      'login' => $username);

        $identity = new Identity($row->id, NULL, $data);

        return $identity; 
	}



	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public function calculateHash($password) {

		return sha1($password /*. str_repeat('*enter any random salt here*', 10*/);
	}

}
