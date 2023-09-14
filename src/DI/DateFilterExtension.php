<?php declare(strict_types=1);

namespace h4kuna\DateFilter\DI;

use h4kuna\DateFilter\DatetimeFormatterFactory;
use h4kuna\DateFilter\Intl\DateFormatterFactory;
use Nette\DI;
use Nette\Schema;

/**
 * @property-read Config $config
 */
class DateFilterExtension extends DI\CompilerExtension
{
	/**
	 * @var array<string, string>
	 */
	private array $services = [];


	public function getConfigSchema(): Schema\Schema
	{
		return Schema\Expect::from(new Config);
	}


	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		foreach ($this->config->dates as $name => $date) {
			$date = (new Schema\Processor())->process(Schema\Expect::from(new DateFormatterFactory()), $date);
			assert($date instanceof DateFormatterFactory);

			$this->services[$name] = [
				'dateType' => $date->date,
				'timeType' => $date->time,
				'pattern' => $date->pattern,
			];
		}

		$builder->addDefinition($this->prefix('factory'))
			->setFactory(DatetimeFormatterFactory::class, [$this->services]);
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		if ($builder->hasDefinition('latte.latteFactory')) {
			$latteFactory = $builder->getDefinition('latte.latteFactory')
				->getResultDefinition();

			foreach ($this->services as $name => $service) {
				$latteFactory->addSetup('addFilter', [
					$name,
					new DI\Definitions\Statement('fn ($date) => $this->container->getService(?)->get(?)->format($date)', [
						$this->prefix('factory'),
						$name,
					]),
				]);
			}
		}
	}

}
