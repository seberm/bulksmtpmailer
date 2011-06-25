<?php

/**
 * My Application bootstrap file.
 */


use Nette\Debug;
use Nette\Environment;
use Nette\Application\Route;


// Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require LIBS_DIR . '/Nette/loader.php';
require LIBS_DIR . '/gettext-translator/GettextTranslator.php';

// Enable Nette\Debug for error visualisation & logging
Debug::enable();


// Load configuration from config.neon file
Environment::loadConfig();


// Configure application
$application = Environment::getApplication();
$application->errorPresenter = 'Error';
//$application->catchExceptions = TRUE;

$application->onStartup[] = function() {
	
	Model::initialize(Environment::getConfig('application'));
    Model::initDB(Environment::getConfig('database'));
    Model::initLocalization(Environment::getConfig('translation'));
};

// My implementation of TR function (like in Qt4) because of simplification
function tr($text, $count = 1) {

    return Model::$translator->translate($text, $count);
}


// Setup router
$application->onStartup[] = function() use ($application) {

	$router = $application->getRouter();
	$router[] = new Route('index.php', 'Sign:in', Route::ONE_WAY);
	$router[] = new Route('<presenter>/<action>[/<id>]', 'Sign:in');
};


// Run the application!
$application->run();
