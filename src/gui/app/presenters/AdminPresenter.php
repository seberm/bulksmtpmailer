<?php

/**
 *  All admin modules has to inherits this class because of their safety!
 *  (except the Sign presenter)
 */


class AdminPresenter extends BasePresenter {

    public function startup() {

        parent::startup();

         if (!$this->getUser()->isLoggedIn()) {

            $this->flashMessage(tr("Please login to view this page"), "warning");
           
            $key = $this->getApplication()->storeRequest();
            $this->redirect('Sign:in', array('backlink' => $key));
        }       
    }
};


?>
