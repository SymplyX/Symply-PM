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

use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionWithBlockInfo;
use symply\plugin\player\Player;
use function count;

class ServerBreakListener implements Listener
{

	private readonly string $playerString;

	public function __construct()
	{
		$this->playerString = Player::class;
	}

	public function onDataReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		$origin = $event->getOrigin();
		$player = $origin->getPlayer();
		if (!($player instanceof Player && $packet->pid() === PlayerAuthInputPacket::NETWORK_ID)){
			return;
		}
		$blockActions = $packet->getBlockActions();
		if($blockActions !== null){
			if(count($blockActions) > 100){
				$origin->disconnectWithError("Too many block actions in PlayerAuthInputPacket");
				return;
			}
			foreach($blockActions as $i => $blockAction){
				if($blockAction instanceof PlayerBlockActionWithBlockInfo){
					if ($blockAction->getActionType() === PlayerAction::CONTINUE_DESTROY_BLOCK){
						if (!$player->attackBlock($vector3 = $this->BlockPositionToVector3($blockAction->getBlockPosition()), $blockAction->getFace())){
							$player->onFailedBlockAction($vector3, $blockAction->getFace());
						}
					}elseif ($blockAction->getActionType() === PlayerAction::PREDICT_DESTROY_BLOCK){
						if (!$player->breakBlock($vector3 = $this->BlockPositionToVector3($blockAction->getBlockPosition()))){
							$player->onFailedBlockAction($vector3, $blockAction->getFace());
						}
					}elseif($blockAction->getActionType() === PlayerAction::CRACK_BREAK){
						unset($blockActions[$i]);
					}
				}
			}
			(function() use($blockActions){
				$this->blockActions = $blockActions;
			})->call($packet);
		}
	}

	public function BlockPositionToVector3(BlockPosition $blockPosition) : Vector3{
		return new Vector3($blockPosition->getX(), $blockPosition->getY(), $blockPosition->getZ());
	}

	/**
	 * @priority LOWEST
	 */
	public function onCreation(PlayerCreationEvent $event) : void{
		$event->setBaseClass($this->playerString);
		$event->setPlayerClass($this->playerString);
	}

	/**
	 * @priority MONITOR
	 * @throws Exception
	 */
	public function onCreationTesterFinal(PlayerCreationEvent $event) : void{
		if ($this->playerString != $event->getPlayerClass() && !(new \ReflectionClass($event->getPlayerClass()))->isSubclassOf($this->playerString)){
			throw new Exception("No cant create playerclass {$event->getPlayerClass()} because is not children of {$this->playerString}");
		}
	}
}
