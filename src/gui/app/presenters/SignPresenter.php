<?php

use Nette\Application\AppForm;
use Nette\Security as NS;
use Nette\Debug;
use Nette\Environment;


final class SignPresenter extends BasePresenter {

    public function startup() {
        
        parent::startup();

        if ($this->getUser()->isLoggedIn()) {
            
            $this->flashMessage("Already logged in.");
            $this->redirect("Admin:");
        }
    }

    
    protected function createComponentSignInForm() {

		$form = new AppForm;
        $form->addGroup("Web administration");
		$form->addText('username', 'Username:')
			->setRequired('Please provide a username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please provide a password.');

		$form->addCheckbox('remember', 'Remember me on this computer');

        $form->addHidden('redirectKey', $this->getParam('backlink'));

		$form->addSubmit('send', 'Sign in');

		$form->onSubmit[] = callback($this, 'signInFormSubmitted');
		return $form;
	}



	public function signInFormSubmitted($form) {

        try {

			$values = $form->getValues();
            
            if ($values->remember)
				$this->getUser()->setExpiration('+ 14 days', false);
            else
				$this->getUser()->setExpiration('+ 20 minutes', true);

		    $this->getUser()->login($values->username, $values->password);
            $this->flashMessage("You've signed in.");
            
            $redirectKey = $values->redirectKey;

            if ($redirectKey)
                $this->getApplication()->restoreRequest($redirectKey);
            else  $this->redirect("Admin:");

        } catch (NS\AuthenticationException $e) {

			$form->addError($e->getMessage());
		}
	}

}
