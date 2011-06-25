<?php


use Nette\Application\AppForm;
use Nette\Security\AuthenticationException;


final class BulkAdminPresenter extends AdminPresenter {

	public function renderDefault() {
       
       $queues = Model::getQueues();
       
       if (count($queues) == 0)
            $this->flashMessage(tr("No queues in the database"), "warning");
           
       $this->template->queues = $queues;
	}


    public function handleRemoveQueue($id) {

        if (Model::removeQueue($id))
            $this->flashMessage(tr("Queue was removed"));
        else
            $this->flashMessage(tr("Error in queue deletion"), "warning");

        $this->redirect("this");
    }


    public function handleStartSending($id) {

        if (Model::startSending($id))
            $this->flashMessage(tr("Sending has started"));
        else
            $this->flashMessage(tr("Cannot start sending"), "warning");

        $this->redirect("this");
    }


    public function createComponentQueueForm() {

        $form = new AppForm;
        $form->setTranslator(Model::$translator);
        
        $form->addGroup(tr("Add queue"));
        $form->addText("name", tr("Queue name"))
             ->setRequired(tr("Please provide name of the queue"));

        $messagesIDs = array();
        foreach (Model::getMessages() as $message)
            $messagesIDs[$message->id] = $message->subject;
                
        $form->addSelect("messageID", tr("Message"), $messagesIDs);
        $form->addCheckbox("startSending", tr("Start sending immediately"));
        $form->setDefaults(array("startSending" => true)); // this checkbox is checked by default
        
        $form->addSubmit("save", tr("Save"));

        $form->onSubmit[] = callback($this, "queueFormSubmitted");
        
        return $form;
    }


    public function queueFormSubmitted($form) {

        try {

            $values = $form->getValues();
            
            if (Model::addQueue($values->name, $values->messageID, $values->startSending)) {

                $this->flashMessage(tr("Queue added"));
                $this->redirect("BulkAdmin:");
            } else
                $this->flashMessage(tr("Cannot add queue"), "warning");


        } catch (AuthenticationException $e) {

            $form->addError($e->getMessage());
        }

    }
}

?>