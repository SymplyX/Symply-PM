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

namespace symply\camera\instruction;

use pocketmine\network\mcpe\protocol\CameraInstructionPacket;
use pocketmine\player\Player;

final class ClearCameraInstruction extends CameraInstruction
{
	private ?bool $clear = null;

	public function setClear(bool $clear): void
	{
		$this->clear = $clear;
	}

	public function send(Player $player): void
	{
		$player->getNetworkSession()->sendDataPacket(CameraInstructionPacket::create(null, $this->clear, null));
	}
}