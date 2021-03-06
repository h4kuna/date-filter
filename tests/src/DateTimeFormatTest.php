<?php declare(strict_types=1);

namespace h4kuna\DateFilter;

use Tester\Assert;

$container = require __DIR__ . '/../bootsrap.php';

/**
 * @testCase
 */
final class DateTimeFormatTest extends \Tester\TestCase
{

	/** @var DateTimeFormat */
	private $dateTimeFormat;


	public function __construct(DateTimeFormat $dateTimeFormat)
	{
		$this->dateTimeFormat = $dateTimeFormat;
	}


	public function testBasic(): void
	{
		Assert::same('Thursday Čt Leden Led l D F M', $this->dateTimeFormat->format('all', '1987-01-01'));
		Assert::same('Tuesday Út December Pro l D F M', $this->dateTimeFormat->format('all', '1986-12-30'));
		Assert::same('Pondělí Po December Pro l D F M', $this->dateTimeFormat->format('all', '1986-12-29'));

		Assert::same('30. 12.', $this->dateTimeFormat->format('dayMonth', '1986-12-30'));
		$this->dateTimeFormat->setFormatsGroup('uk');
		Assert::same('30/12', $this->dateTimeFormat->format('dayMonth', '1986-12-30'));

		$this->dateTimeFormat->setDayMonthGroup('en');
		Assert::same('Thursday Thu January Jan', $this->dateTimeFormat->format('all', '1987-01-01'));

		Assert::same('d/m', $this->dateTimeFormat->getFormat('dayMonth'));
	}


	/**
	 * @throws \InvalidArgumentException
	 */
	public function testFail(): void
	{
		Assert::same('d/m', $this->dateTimeFormat->getFormat('foo'));
	}

}

$filter = $container->getService('dateFilterExtension.dateTimeFormat');
(new DateTimeFormatTest($filter))->run();
