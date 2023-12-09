<?php

namespace symply\events\session;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;
use Throwable;

class SessionErrorEvent extends Event implements Cancellable
{
	use CancellableTrait;

	/**
	 * @param Player $player
	 * @param Throwable $throwable
	 * @param string $errorMessage
	 */
	public function __construct(
		protected Player $player,
		protected Throwable $throwable,
		protected string $errorMessage
	) {}

	/**
	 * @return Player
	 */
	public function getPlayer(): Player
	{
		return $this->player;
	}

	/**
	 * @return Throwable
	 */
	public function getThrowable(): Throwable
	{
		return $this->throwable;
	}

	/**
	 * @return string
	 */
	public function getErrorMessage(): string
	{
		return $this->errorMessage;
	}

	/**
	 * @param string $message
	 * @return void
	 */
	public function setErrorMessage(string $message): void
	{
		$this->errorMessage = $message;
	}
}