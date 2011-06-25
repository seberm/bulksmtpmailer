<?php

use Nette\Application\AppForm;
use Nette\Forms\Form;
use Nette\Security\AuthenticationException;


final class MailsManagerPresenter extends AdminPresenter {

	public function renderDefault() {
       
        $this->template->moduleName = tr("E-mails manager");

        $mails = Model::getMails();

        if (count($mails) == 0)
            $this->flashMessage(tr("No e-mails in database"), "warning");

        $this->template->mails = $mails;
	}


    public function handleRemoveMail($id) {

        if (Model::removeMail($id))
            $this->flashMessage(tr("E-mail was removed"));
        else 
            $this->flashMessage(tr("Error in e-mail deletion"), "warning");

        $this->redirect("this");
    }


    public function createComponentMailForm() {

        $form = new AppForm;
        $form->setTranslator(Model::$translator);
        
        $form->addGroup(tr("E-mail"));

        $form->addText("name", tr("Name"))
             ->setRequired(tr("You have to fill name"));
             
        $form->addText("email", tr("E-mail"))
             ->setRequired(tr("You have to fill email"))
             ->addRule(Form::EMAIL, tr("Bad e-mail format"));

        $form->addSubmit("save", tr("Save"));
        
        $form->onSubmit[] = callback($this, "mailFormSubmitted");
     
        return $form;
    }


    public function mailFormSubmitted($form) {

        try {

            $values = $form->getValues();

            if ($this->getParam("id")) {
                
                if (Model::updateMail($values->name, $values->email, $this->getParam("id"))) {
                    $this->flashMessage(tr("E-mail updated"));
                    $this->redirect("MailsManager:");
                } else
                    $this->flashMessage(tr("Cannot update e-mail"), "warning");
                
            } else {
                
                if (Model::addMail($values->name, $values->email)) {
                    $this->flashMessage(tr("E-mail added"));
                    $this->redirect("MailsManager:");
                } else
                    $this->flashMessage(tr("Cannot update e-mail"), "warning");
            }
            

        } catch (AuthenticationException $e) {

            $form->addError($e->getMessage());
        }
    }


    public function renderMail($id = NULL) {

        if (!$id)
            return;
        
        $form = $this->getComponent("mailForm"); 

        $mail = Model::getMail($id);
        $defaults = array("name" => $mail->name,
                          "email" => $mail->email);

        $form->setDefaults($defaults);
    }
}

?>