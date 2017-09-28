<?php

namespace h4kuna\DateFilter\DI;

use Nette\DI\CompilerExtension;

class DateFilterExtension extends CompilerExtension
{

	private $defaults = [
		'dayMonth' => [],
		'formats' => [],
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->config + $this->defaults;
		$prepareData = new PrepareFormats((array) $config['formats'], (array) $config['dayMonth']);

		// Filter
		$filter = $builder->addDefinition($this->prefix('dateTimeFormat'))
			->setClass('h4kuna\DateFilter\DateTimeFormat', [$prepareData->validDateFormats()]);

		if ($config['dayMonth']) {
			$filter->addSetup('setDayMonth', [$prepareData->validDayMonth(), $prepareData->getHelperFormat()]);
		}

		$latteFactory = $builder->getDefinition('latte.latteFactory');
		foreach (current($config['formats']) as $name => $none) {
			$latteFactory->addSetup('addFilter', [
				$name,
				new \Nette\DI\Statement('function($date) {
					return ?->format(?, $date);
				}', [$filter, $name])
			]);
		}
	}

}
