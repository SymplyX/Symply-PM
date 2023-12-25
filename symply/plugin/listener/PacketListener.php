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

namespace symply\plugin\listener;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketDecodeEvent;
use symply\utils\PacketUtils;
use function in_array;

class PacketListener implements Listener
{

	public function onDataPacketDecode(DataPacketDecodeEvent $event) : void
	{
		$packetId = $event->getPacketId();
		if(in_array($packetId, PacketUtils::$disabledPackets, true)) {
			$event->cancel();
		}
	}
}
