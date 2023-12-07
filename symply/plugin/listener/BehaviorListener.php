<?php

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

	public function __construct(private bool $serverBreakSide)
	{
	}

	/**
	 * @priority LOWEST
	 * @param DataPacketSendEvent $event
	 * @return void
	 */
	public function onSend(DataPacketSendEvent $event): void
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
			} else if ($packet instanceof ResourcePackStackPacket) {
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
			} else if ($packet instanceof BiomeDefinitionListPacket) {
				foreach ($targets as $target) {
					$target->sendDataPacket(SymplyItemFactory::getInstance()->getItemsComponentPacket());
				}
			}
		}
	}
}