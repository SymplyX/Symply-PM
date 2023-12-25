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

namespace symply\events\session;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;
use Throwable;

class SessionErrorEvent extends Event implements Cancellable
{
	use CancellableTrait;

	public function __construct(
		protected Player $player,
		protected Throwable $throwable,
		protected string $errorMessage
	) {}

	public function getPlayer() : Player
	{
		return $this->player;
	}

	public function getThrowable() : Throwable
	{
		return $this->throwable;
	}

	public function getErrorMessage() : string
	{
		return $this->errorMessage;
	}

	public function setErrorMessage(string $message) : void
	{
		$this->errorMessage = $message;
	}
}
