<?php

/**
 * @author: Juris Lukjanovs
 * @descriptin: Solution to homework given by potential employer
 * @date: 13.12.2021.
 */

spl_autoload_register(function($class) {
	require_once $class . '.php';
});

use Controller\ScriptController;
use Exception\ErrorException;

try {
	(new ScriptController())->Execute();
} catch (ErrorException $e) {
	exit($e->getCode());
}
