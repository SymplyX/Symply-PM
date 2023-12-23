<?php

namespace symply\plugin\listener;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketDecodeEvent;
use symply\utils\PacketUtils;

class PacketListener implements Listener
{
	/**
	 * @param DataPacketDecodeEvent $event
	 * @return void
	 */
	public function onDataPacketDecode(DataPacketDecodeEvent $event): void
	{
		$packetId = $event->getPacketId();
		if(in_array($packetId, PacketUtils::$disabledPackets)) {
			$event->cancel();
		}
	}
}