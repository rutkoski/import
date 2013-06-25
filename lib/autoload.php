<?php

if (! defined('SY_DIR')) {
  define('SY_DIR', dirname(__file__));

  require_once('functions.php');

  require_once ('Simplify' . DIRECTORY_SEPARATOR . 'Autoload.php');

  sy_autoload_register(array('Simplify_Autoload', 'autoload'));
}

Simplify_Autoload::registerPath(dirname(__file__));
