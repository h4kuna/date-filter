<?php declare(strict_types=1);

namespace h4kuna\DateFilter\DI;

use h4kuna\DateFilter\Intl\DateFormatterFactory;

final class Config
{
	/**
	 * @var array<string, DateFormatterFactory>
	 */
	public array $dates = [];

}
