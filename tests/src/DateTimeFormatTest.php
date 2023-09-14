<?php declare(strict_types=1);

namespace h4kuna\DateFilter\Tests;

use h4kuna\DateFilter\DatetimeFormatterFactory;
use h4kuna\DateFilter\DI\DateFilterExtension;
use IntlDateFormatter;
use Nette\Bridges\ApplicationDI\LatteExtension;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\DI;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class DateTimeFormatTest extends \Tester\TestCase
{

	public function testCs_CZ(): void
	{
		setlocale(LC_TIME, 'cs_CZ.utf8');
		$container = $this->createContainer();
		$factory = $container->getService('formats.factory');
		assert($factory instanceof DatetimeFormatterFactory);
		$factory->addLocale('en_US.utf8', 'en_US');
		Assert::same('30. prosince 1986', $factory->get('date')->format(new \DateTime('1986-12-30')));

		$latteFactory = $container->getService('latte.latteFactory');
		assert($latteFactory instanceof LatteFactory);
		$latte = $latteFactory->create();
		Assert::same('30. prosince 1986', $latte->invokeFilter('date', [new \DateTime('1986-12-30')]));

		Assert::same('7:45', $factory->get('time')->format(new \DateTime('1986-12-30 07:45:51')));
		Assert::same('30.12.86 7:45', $factory->get('dateMinute')->format(new \DateTime('1986-12-30 07:45')));
		Assert::same('30. 12.', $factory->get('month')->format(new \DateTime('1986-12-30 07:45:51')));
	}


	public function testAlias(): void
	{
		$container = $this->createContainer();
		$factory = $container->getService('formats.factory');
		assert($factory instanceof DatetimeFormatterFactory);
		$factory->addLocale('en_US.utf8', 'en_US');
		$factory->addLocale('en_US.utf8', '1');

		Assert::same($factory->get('date', 'en_US.utf8'), $factory->get('date', 'en_US'));
		Assert::same($factory->get('date', 'en_US.utf8'), $factory->get('date', '1'));
	}


	public function testEn_US(): void
	{
		setlocale(LC_TIME, 'en_US.utf8');
		$container = $this->createContainer();
		$factory = $container->getService('formats.factory');

		Assert::same('12/30/86, 7:45 AM', $factory->get('dateMinute')->format(new \DateTime('1986-12-30 07:45:51')));

		Assert::same('30/12', $factory->get('month')->format(new \DateTime('1986-12-30 07:45:51')));
	}


	public function testSwitch(): void
	{
		setlocale(LC_TIME, 'cs_CZ.utf8');
		$container = $this->createContainer();
		$factory = $container->getService('formats.factory');
		Assert::same('30. 9.', $factory->get('month')->format(new \DateTime('1986-09-30 07:45:51')));

		setlocale(LC_TIME, 'en_US.utf8');
		Assert::same('30/09', $factory->get('month')->format(new \DateTime('1986-09-30 07:45:51')));

		setlocale(LC_TIME, 'cs_CZ.utf8');
		Assert::same('30. 9.', $factory->get('month')->format(new \DateTime('1986-09-30 07:45:51')));
	}


	private function createContainer(): DI\Container
	{
		$loader = new DI\ContainerLoader(TEMP_DIR, true);
		$class = $loader->load(function (DI\Compiler $compiler): void {
			$compiler->addExtension('formats', new DateFilterExtension());
			$compiler->addExtension('latte', new LatteExtension(TEMP_DIR));

			$compiler->addConfig([
				'formats' => [
					'dates' => [
						'date' => [
							'date' => IntlDateFormatter::LONG,
							'time' => IntlDateFormatter::NONE,
						],
						'time' => [
							'date' => IntlDateFormatter::NONE,
							'time' => IntlDateFormatter::SHORT,
						],
						'month' => [
							'pattern' => [
								'cs_CZ.utf8' => 'd. M.',
								'en_US.utf8' => 'dd/MM',
							],
						],
						'dateMinute' => [
							'date' => IntlDateFormatter::SHORT,
							'time' => IntlDateFormatter::SHORT,
						],
					],
				],
			],
			);
		}, __FILE__);

		$container = new $class();
		assert($container instanceof DI\Container);

		return $container;
	}

}

(new DateTimeFormatTest())->run();
