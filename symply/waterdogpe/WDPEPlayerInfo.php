<?php

namespace symply\waterdogpe;

use pocketmine\entity\Skin;
use pocketmine\player\PlayerInfo;
use Ramsey\Uuid\UuidInterface;

class WDPEPlayerInfo  extends PlayerInfo{
	private string $xuid;
	private string $realIp;

	public function __construct(string $xuid, string $username, string $realIp, UuidInterface $uuid, Skin $skin, string $locale, array $extraData = []){
		parent::__construct($username, $uuid, $skin, $locale, $extraData);
		$this->xuid = $xuid;
		$this->realIp = $realIp;
	}

	public function getXuid() : string{
		return $this->xuid;
	}

	public function getRealIp(): string{
		return $this->realIp;
	}

	/**
	 * Returns a new PlayerInfo with XBL player info stripped. This is used to ensure that non-XBL players can't spoof
	 * XBL data.
	 */
	public function withoutXboxData() : PlayerInfo{
		return new PlayerInfo(
			$this->getUsername(),
			$this->getUuid(),
			$this->getSkin(),
			$this->getLocale(),
			$this->getExtraData()
		);
	}
}