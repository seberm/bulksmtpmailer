<?php

final class MainPresenter extends AdminPresenter {

	public function renderDefault() {
        
       $this->template->moduleName = tr("Welcome");
	}

}
