<?php

include __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();

// 2# Create Nette Configurator
$configurator = new Nette\Configurator;

$tmp = __DIR__ . '/temp/' . getmypid();
@mkdir($tmp, 0755, true);
$configurator->enableDebugger($tmp . '/..');
$configurator->setTempDirectory($tmp);
$configurator->setDebugMode(false);
$configurator->addConfig(__DIR__ . '/config/test.neon');
$local = __DIR__ . '/config/test.local.neon';
if (is_file($local)) {
	$configurator->addConfig($local);
}

Tracy\Debugger::enable(false);
$container = $configurator->createContainer();

return $container;
