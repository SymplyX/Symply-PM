<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace symply\command\arguments\list;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use symply\command\arguments\CommandArgument;
use function array_keys;
use function array_map;
use function implode;
use function preg_match;
use function strtolower;

abstract class StringEnumCommandArgument extends CommandArgument
{
	/**
	 * @var array
	 */
	protected const VALUES = [];

	public function __construct(string $name, bool $optional = false){
		parent::__construct($name, $optional);
		$this->parameter->enum = new CommandEnum("", $this->getEnumValues());
	}

	public function getNetworkType() : int{
		return -1;
	}

	public function isValid(string $testString, CommandSender $sender) : bool{
		return (bool) preg_match(
			"/^(" . implode("|", array_map("\\strtolower", $this->getEnumValues())) . ")$/iu",
			$testString
		);
	}

	public function getValue(string $string) : mixed{
		return self::VALUES[strtolower($string)];
	}

	public function getEnumValues() : array{
		return array_keys(self::VALUES);
	}
}
