<?php declare(strict_types=1);

namespace h4kuna\DateFilter;

use IntlDateFormatter;

interface DateFormats
{

	function get(string $name): IntlDateFormatter;

}
