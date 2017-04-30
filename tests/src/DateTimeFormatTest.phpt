<?php

namespace h4kuna\DateFilter;

use Tester\Assert;

$container = require __DIR__ . '/../bootsrap.php';

class DateTimeFormatTest extends \Tester\TestCase
{

	/** @var DateTimeFormat */
	private $dateTimeFormat;

	public function __construct(DateTimeFormat $dateTimeFormat)
	{
		$this->dateTimeFormat = $dateTimeFormat;
	}

	public function testBasic()
	{
		Assert::same('Thursday Čt Leden Led l D F M', $this->dateTimeFormat->format('all', '1987-01-01'));
		Assert::same('Tuesday Út December Pro l D F M', $this->dateTimeFormat->format('all', '1986-12-30'));
		Assert::same('Pondělí Po December Pro l D F M', $this->dateTimeFormat->format('all', '1986-12-29'));

		Assert::same('30. 12.', $this->dateTimeFormat->format('dayMonth', '1986-12-30'));
		$this->dateTimeFormat->setFormatsGroup('uk');
		Assert::same('30/12', $this->dateTimeFormat->format('dayMonth', '1986-12-30'));

		$this->dateTimeFormat->setDayMonthGroup('en');
		Assert::same('Thursday Thu January Jan', $this->dateTimeFormat->format('all', '1987-01-01'));
	}

}

$filter = $container->getService('dateFilterExtension.dateTimeFormat');
(new DateTimeFormatTest($filter))->run();
