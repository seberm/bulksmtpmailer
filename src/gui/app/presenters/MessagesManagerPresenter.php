<?php

use Nette\Application\AppForm;
use Nette\Forms\Form;
use Nette\Security\AuthenticationException;


final class MessagesManagerPresenter extends AdminPresenter {
    

	public function renderDefault() {
       
        $this->template->moduleName = tr("Messages manager");

        $messages = Model::getMessages();

        if (count($messages) == 0)
            $this->flashMessage(tr("No messages in database"), "warning");

        $this->template->messages = $messages;
	}


    public function handleRemoveMessage($id) {

        if (Model::removeMessage($id))
            $this->flashMessage(tr("Message was removed"));
        else
            $this->flashMessage(tr("Error in message deletion"), "warning");
        

        $this->redirect("this");
    }


    public function createComponentMessageForm() {

        $form = new AppForm;
        $form->setTranslator(Model::$translator);
        
        $form->addGroup(tr("Message"));

        $form->addText("subject", tr("Subject"))
             ->setRequired(tr("You have to fill subject"));
             
        $form->addTextArea("text", tr("Text"))
             ->setRequired(tr("You have to fill message text"));

        $form->addSubmit("save", tr("Save"));
        
        $form->onSubmit[] = callback($this, "messageFormSubmitted");
     
        return $form;
    }


    public function messageFormSubmitted($form) {

        try {

            $values = $form->getValues();

            if ($this->getParam("id")) {
                
                if (Model::updateMessage($values->subject, $values->text, $this->getParam("id"))) {

                    $this->flashMessage(tr("Message updated"));
                    $this->redirect("MessagesManager:");
                } else
                    $this->flashMessage(tr("Cannot update message"));
            } else {
                
                if (Model::addMessage($values->subject, $values->text)) {

                    $this->flashMessage(tr("Message added"));
                    $this->redirect("MessagesManager:");
                } else
                    $this->flashMessage(tr("Cannot add message"));
            }

        } catch (AuthenticationException $e) {

            $form->addError($e->getMessage());
        }
    }


    public function renderMessage($id = NULL) {

        if (!$id)
            return;
        
        $form = $this->getComponent("messageForm"); 

        $message = Model::getMessage($id);
        $defaults = array("subject" => $message->subject,
                          "text" => $message->text);

        $form->setDefaults($defaults);
    }
}

?>