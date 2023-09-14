<?php declare(strict_types=1);

namespace h4kuna\DateFilter\Tests;

use Tester;
use Tracy;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Prague');

Tester\Environment::setup();

define('TEMP_DIR', __DIR__ . '/temp');
Tracy\Debugger::enable(false, TEMP_DIR);
