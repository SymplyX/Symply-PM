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

namespace symply\command\arguments;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

abstract class CommandArgument
{

	protected CommandParameter $parameter;

	/** @param string $name */
	private string $name;

	/** @param bool $optional */
	private string $optional;

	public function __construct(string $name, bool $optional = false)
	{
		$this->name = $name;
		$this->optional = $optional;

		$this->parameter = new CommandParameter();
		$this->parameter->paramName = $name;
		$this->parameter->paramType = AvailableCommandsPacket::ARG_FLAG_VALID; $this->parameter->paramType |= $this->getNetworkType();
		$this->parameter->isOptional = $this->isOptional();
	}

	abstract public function canExecute(string $testString, CommandSender $sender) : bool;

	abstract public function execute(string $argument, CommandSender $sender) : mixed;

	abstract public function getNetworkType() : int;

	abstract public function getTypeName() : string;

	public function getName() : string{
		return $this->name;
	}

	public function isOptional() : bool{
		return $this->optional;
	}

	public function getCommandParameter() : CommandParameter{
		return $this->parameter;
	}
}
