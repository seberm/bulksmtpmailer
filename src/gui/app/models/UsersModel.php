<?php
use Nette\Security;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Security\IAuthenticator;
use Nette\Object;
use Nette\Debug;

class UsersModel extends Object implements IAuthenticator {

	public function authenticate(array $credentials) {

        list($login, $password) = $credentials;
        
		$user = Model::getUser($login);//$database->query('SELECT id, realName, password FROM Users WHERE login=?', $username)->fetch();

		if (!$user) 
			throw new AuthenticationException("User '$login' not found.", self::IDENTITY_NOT_FOUND);

		if ($user->password !== $this->calculateHash($password))
			throw new AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);

		unset($user->password);

        $data = array('realName' => $user->realName,
                      'login' => $login);

        $identity = new Identity($user->id, NULL, $data);

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
