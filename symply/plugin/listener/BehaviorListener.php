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
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\PlayerMovementSettings;
use symply\behavior\SymplyBlockFactory;
use symply\behavior\SymplyItemFactory;

class BehaviorListener implements Listener
{

	public function __construct(private readonly bool $serverBreakSide)
	{
	}

	/**
	 * @priority LOWEST
	 */
	public function onSend(DataPacketSendEvent $event) : void
	{
		$packets = $event->getPackets();
		$targets = $event->getTargets();
		foreach ($packets as $packet) {
			if ($packet instanceof StartGamePacket) {
				$packet->playerMovementSettings = new PlayerMovementSettings($packet->playerMovementSettings->getMovementType(), $packet->playerMovementSettings->getRewindHistorySize() , $this->serverBreakSide);
				$packet->levelSettings->experiments = new Experiments([
					"wild_update" => true,
					"vanilla_experiments" => true,
					"data_driven_items" => true,
					"data_driven_biomes" => true,
					"scripting" => true,
					"upcoming_creator_features" => true,
					"gametest" => true,
					"experimental_molang_features" => true,
				], true);
				$packet->blockPalette = SymplyBlockFactory::getInstance()->getBlockPaletteEntries();
			} elseif ($packet instanceof ResourcePackStackPacket) {
				$packet->experiments = new Experiments([
					"wild_update" => true,
					"vanilla_experiments" => true,
					"data_driven_items" => true,
					"data_driven_biomes" => true,
					"scripting" => true,
					"upcoming_creator_features" => true,
					"gametest" => true,
					"experimental_molang_features" => true,
				], true);
			} elseif ($packet instanceof BiomeDefinitionListPacket) {
				foreach ($targets as $target) {
					$target->sendDataPacket(SymplyItemFactory::getInstance()->getItemsComponentPacket());
				}
			}
		}
	}
}
