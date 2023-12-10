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
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use symply\command\arguments\CommandArgument;
use function count;
use function explode;
use function preg_match;
use function substr;

class PositionCommandArgument extends CommandArgument
{

	public function getNetworkType() : int{
		return AvailableCommandsPacket::ARG_TYPE_POSITION;
	}

	public function getTypeName() : string{
		return "x y z";
	}

	public function canExecute(string $testString, CommandSender $sender) : bool{
		$position = explode(" ", $testString);
		if (count($position) === 3) {
			foreach($position as $vector) {
				if ($this->isValid($vector, $sender instanceof Vector3)) {
					return false;
				}
			}
			return true;
		}
		return false;
	}

	public function execute(string $argument, CommandSender $sender) : Vector3{
		$values = [];
		$position = explode(" ", $argument);

		foreach ($position as $key => $vector) {
			$offset = 0;
			if  (preg_match("/^(?:~-|~\+)|~/", $vector) && $sender instanceof Entity) {
				$offset = substr($vector, 1);
				$pos = $sender->getPosition();

				$vector = match ($key) {
					0 => $pos->getX(),
					1 => $pos->getY(),
					2 => $pos->getZ(),
				};
			}

			$values[] = $vector + $offset;
		}

		return new Vector3(...$values);
	}

	public function isValid(string $coordinate, bool $locatable) : bool {
		return (bool) preg_match("/^(?:" . ($locatable ? "(?:~-|~\+)?" : "") . "-?(?:\d+|\d*\.\d+))" . ($locatable ? "|~" : "") . "$/", $coordinate);
	}
}
