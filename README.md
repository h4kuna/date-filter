Date filter
==========
[![Build Status](https://travis-ci.org/h4kuna/date-filter.svg?branch=master)](https://travis-ci.org/h4kuna/date-filter)
[![Downloads this Month](https://img.shields.io/packagist/dm/h4kuna/date-filter.svg)](https://packagist.org/packages/h4kuna/date-filter)
[![Latest stable](https://img.shields.io/packagist/v/h4kuna/date-filter.svg)](https://packagist.org/packages/h4kuna/date-filter)
[![Coverage Status](https://coveralls.io/repos/github/h4kuna/date-filter/badge.svg?branch=master)](https://coveralls.io/github/h4kuna/date-filter?branch=master)

----

## Náhrada

Koukněte na knihovnu Format konkrétně na [formátování datumů](https://github.com/h4kuna/number-format/blob/master/doc/date.md). Doplněk by vás měl odstínit od manuálního vypisování a skládání objektů, na to momentálně nemám kapacitu, tak vám ukážu jak to dělat ručně ve třech krocích.

1. nainstalovat knihovnu `composer require h4kuna/number-format`
2. sepsat si vlastní sadu formátů v neonu

```neon
services:
	format.date:
		factory: h4kuna\Format\Date\Formatters\DateTimeFormatter('j. n. Y')
		autowired: false
	format.time:
		factory: h4kuna\Format\Date\Formatters\DateTimeFormatter('H:i:s')
		autowired: false

	# budete-li potřebovat formátovat kdekoliv v projektu, použijte tento Accessor
	number.formats: h4kuna\Format\Date\FormatsAccessor(
		date: @format.date
		time: @format.time
	)

	latte.latteFactory:
		setup:
			- addFilter('date', @format.date)
			- addFilter('time', @format.time)
```
3. v šabloně pak bude fungovat
```latte
{=(new DateTime())|date}<br>
{=(new DateTime())|time}
```

----

Require PHP 5.4+.

This extension is for php [Nette framework](//github.com/nette/nette).

Installation to project
-----------------------
The best way to install h4kuna/date-filter is using composer:
```sh
$ composer require h4kuna/date-filter
```

How to use
-----------
Register extension for Nette in neon config file.
```sh
extensions:
    dateFilterExtension: h4kuna\DateFilter\DI\DateFilterExtension
```
Now show you how set new date filters and other variants.

> You can overwrite default latte filter **date**!

For define format use letters from table [date function](http://php.net/manual/en/function.date.php#refsect1-function.date-parameters).

You can set own filters groups like:
```sh
dateFilterExtension:
	formats:
		cze: # name of group is optional (i choose country)
			# filter name: format
			day: D
			date: j. n. Y # overwrite default latte filter
			dateTime: j. n. Y H:i:s
		uk:
			day: D
			date: d/m/Y
			dateTime: d/m/Y H:i:s
```
First group is default. Now in latte you can use:
```
{$date|dateTime}
{$date|day}
{$date|date}
```

You can forget on this and duplicate on every place where you need.
```
{$date|date:'j. n. Y H:i:s'}
```

For different country you can change date format. And change it by:
```php
/* @var $dateTimeFormat h4kuna\DateFilter\DateTimeFormat */
$dateTimeFormat = $container->getService('dateFilterExtension.dateTimeFormat');
$dateTimeFormat->setFormatsGroup('uk');
$dateTimeFormat->format('dateTime', 'now'); // internaly call filter from latte
```

If you don't change locale, then date letters (D, l (lower L), F, M) are everytime in english language, ok here is example how change it.

```sh
	dayMonth:
		cs: # name of group is optional (i choose language) it isn't same group above
			# days:
			l: # lower L long
				# original name: translate
				Monday: Pondělí
				Tuesday: Úterý
				Wednesday: Středa
				Thursday: Čtvrtek
				Friday: Pátek
				Saturday: Sobota
				Sunday: Neděle

			D: # short
				Mon: Po
				Tue: Út
				Wed: St
				Thu: Čt
				Fri: Pá
				Sat: So
				Sun: Ne

			# months:
			F: # long
				January: Leden
				February: Únor
				March: Březen
				April: Duben
				May: Květen
				June: Červen
				July: Červenec
				August: Srpen
				September: Září
				October: Říjen
				November: Listopad
				December: Prosinec

			M: # short
				Jan: Led
				Feb: Úno
				Mar: Bře
				Apr: Dub
				May: Kvě
				Jun: Čvn
				Jul: Čvc
				Aug: Srp
				Sep: Zář
				Oct: Říj
				Nov: Lis
				Dec: Pro
		en: [] # we don't need translate
```
First group is default. Change group like:
```php
/* @var $dateTimeFormat h4kuna\DateFilter\DateTimeFormat */
$dateTimeFormat = $container->getService('dateFilterExtension.dateTimeFormat');
$dateTimeFormat->setDayMonthGroup('en');
$dateTimeFormat->format('day', 'now()');
```
