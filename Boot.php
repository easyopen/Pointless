<?php
// Core
define('CORE', ROOT . 'Core' . SEPARATOR);
define('CORE_LIBRARY', CORE . 'Library' . SEPARATOR);
define('CORE_PLUGIN', CORE . 'Plugin' . SEPARATOR);

/**
 * Load NanoCLI and Setting
 */
require_once CORE_PLUGIN . 'NanoCLI/NanoCLI.php';
require_once CORE_PLUGIN . 'NanoCLI/NanoIO.php';
require_once CORE_PLUGIN . 'NanoCLI/NanoLoader.php';

// Default Setting
define('NANOCLI_COMMAND', ROOT . 'Command' . SEPARATOR);
define('NANOCLI_PREFIX', 'pointless');

// Register NanoCLI Autoloader
NanoLoader::Register();

// Load First Command and Init
require_once NANOCLI_COMMAND . 'pointless.php';

$NanoCLI = new pointless();
$NanoCLI->Init();
