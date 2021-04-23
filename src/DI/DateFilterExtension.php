<?php declare(strict_types=1);

namespace h4kuna\DateFilter\DI;

use h4kuna\DateFilter\DateTimeFormat;
use Nette\DI;

class DateFilterExtension extends DI\CompilerExtension
{
	private $defaults = [
		'dayMonth' => [],
		'formats' => [],
	];


	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config + $this->defaults;
		$prepareData = new PrepareFormats((array) $config['formats'], (array) $config['dayMonth']);

		// Filter
		$filter = $builder->addDefinition($this->prefix('dateTimeFormat'))
			->setFactory(DateTimeFormat::class, [$prepareData->validDateFormats()]);

		if ($config['dayMonth']) {
			$filter->addSetup('setDayMonth', [$prepareData->validDayMonth(), $prepareData->getHelperFormat()]);
		}

		$latteFactory = $builder->getDefinition('latte.latteFactory');
		foreach (current($config['formats']) as $name => $none) {
			$latteFactory->getResultDefinition()->addSetup('addFilter', [
				$name,
				new DI\Definitions\Statement('function($date) {
					return ?->format(?, $date);
				}', [$filter, $name]),
			]);
		}
	}

}
