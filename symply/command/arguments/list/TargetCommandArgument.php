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
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use symply\command\arguments\CommandArgument;
use function preg_match;
use function strtolower;

class TargetCommandArgument extends CommandArgument
{

	public function getNetworkType() : int {
		return AvailableCommandsPacket::ARG_TYPE_TARGET;
	}

	public function getTypeName() : string {
		return "target";
	}

	public function isValid(string $testString, CommandSender $sender) : bool {
		return (bool) preg_match("/^(?!rcon|console)[a-zA-Z0-9_ ]{1,16}$/i", $testString);
	}

	public function execute(string $argument, CommandSender $sender) : string|Player {
		$target = strtolower($argument);

		return Server::getInstance()->getPlayerExact($target) ?? $target;
	}
}
