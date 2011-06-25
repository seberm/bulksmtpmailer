<?php

abstract class BasePresenter extends Nette\Application\Presenter {

    
    public function beforeRender() {

        $this->template->user = $this->getUser();
        $this->template->setTranslator(Model::$translator);
        $this->template->moduleName = tr("Bulk administration"); // It must be defined
    }



    public function handleCronScript() {

        $this->redirectUri(CRON_SCRIPT_URI);
    }


    public function handleLogout() {

        $this->getUser()->logout(true);
        $this->flashMessage(tr("You've been signed out"));

        $this->redirect("Sign:in");
    }
}
