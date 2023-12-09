<?php

namespace symply\events\session;

use pocketmine\event\Event;
use pocketmine\network\mcpe\NetworkSession;

class SessionPingUpdateEvent extends Event
{
	public function __construct(
		protected NetworkSession $networkSession,
		protected float $ping
	) {}

	/**
	 * @return NetworkSession
	 */
	public function getNetworkSession(): NetworkSession
	{
		return $this->networkSession;
	}

	/**
	 * @return float
	 */
	public function getPing(): float
	{
		return $this->ping;
	}
}