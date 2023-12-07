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

namespace symply\plugin\player;

use pocketmine\block\BlockTypeTags;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player as PMPlayer;
use pocketmine\timings\Timings;
use pocketmine\world\sound\FireExtinguishSound;
use pocketmine\world\sound\ItemBreakSound;
use function array_push;
use function array_shift;
use function count;

class Player extends PMPlayer
{
	private const MAX_REACH_DISTANCE_CREATIVE = 13;
	private const MAX_REACH_DISTANCE_SURVIVAL = 7;

	public ?SurvivalBlockBreakHandler $blockBreakHandlerModded = null;

	public function attackBlock(Vector3 $pos, int $face) : bool
	{
		if($pos->distanceSquared($this->location) > 10000){
			return false; //TODO: maybe this should throw an exception instead?
		}

		$target = $this->getWorld()->getBlock($pos);

		$ev = new PlayerInteractEvent($this, $this->inventory->getItemInHand(), $target, null, $face, PlayerInteractEvent::LEFT_CLICK_BLOCK);
		if($this->isSpectator()){
			$ev->cancel();
		}
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}
		$this->broadcastAnimation(new ArmSwingAnimation($this), $this->getViewers());
		if($target->onAttack($this->inventory->getItemInHand(), $face, $this)){
			return true;
		}

		$block = $target->getSide($face);
		if($block->hasTypeTag(BlockTypeTags::FIRE)){
			$this->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
			$this->getWorld()->addSound($block->getPosition()->add(0.5, 0.5, 0.5), new FireExtinguishSound());
			return true;
		}

		if(!$this->isCreative() && !$block->getBreakInfo()->breaksInstantly()){
			if ($this->blockBreakHandlerModded !== null && $this->blockBreakHandlerModded->getBlockPos()->distanceSquared($pos) < 0.0001)
				return true;
			$this->blockBreakHandlerModded = new SurvivalBlockBreakHandler($this, $pos, $target, $face, 16);
		}

		return true;
	}

	public function onUpdate(int $currentTick) : bool{
		$tickDiff = $currentTick - $this->lastUpdate;

		if($tickDiff <= 0){
			return true;
		}

		$this->messageCounter = 2;

		$this->lastUpdate = $currentTick;

		if($this->justCreated){
			$this->onFirstUpdate($currentTick);
		}

		if(!$this->isAlive() && $this->spawned){
			$this->onDeathUpdate($tickDiff);
			return true;
		}

		$this->timings->startTiming();

		if($this->spawned){
			Timings::$playerMove->startTiming();
			$this->processMostRecentMovements();
			$this->motion = Vector3::zero(); //TODO: HACK! (Fixes player knockback being messed up)
			if($this->onGround){
				$this->inAirTicks = 0;
			}else{
				$this->inAirTicks += $tickDiff;
			}
			Timings::$playerMove->stopTiming();

			Timings::$entityBaseTick->startTiming();
			$this->entityBaseTick($tickDiff);
			Timings::$entityBaseTick->stopTiming();

			if($this->isCreative() && $this->fireTicks > 1){
				$this->fireTicks = 1;
			}

			if(!$this->isSpectator() && $this->isAlive()){
				Timings::$playerCheckNearEntities->startTiming();
				$this->checkNearEntities();
				Timings::$playerCheckNearEntities->stopTiming();
			}

			if($this->blockBreakHandlerModded !== null && !$this->blockBreakHandlerModded->update()){

				$this->blockBreakHandlerModded = null;
			}
		}

		$this->timings->stopTiming();

		return true;
	}

	public function breakBlock(Vector3 $pos) : bool{
		$this->removeCurrentWindow();
		if($this->canInteract($pos->add(0.5, 0.5, 0.5), $this->isCreative() ? self::MAX_REACH_DISTANCE_CREATIVE : self::MAX_REACH_DISTANCE_SURVIVAL)){
			if ($this->isSurvival()){
				if ($this->blockBreakHandlerModded !== null && $this->blockBreakHandlerModded->getBreakProgress() < 1) return false;
			}
			$this->broadcastAnimation(new ArmSwingAnimation($this), $this->getViewers());
			$this->stopBreakBlock($pos);
			$item = $this->inventory->getItemInHand();
			$oldItem = clone $item;
			$returnedItems = [];
			if($this->getWorld()->useBreakOn($pos, $item, $this, true, $returnedItems)){
				$this->returnItemsFromAction($oldItem, $item, $returnedItems);
				$this->hungerManager->exhaust(0.005, PlayerExhaustEvent::CAUSE_MINING);
				return true;
			}
		}else{
			$this->logger->debug("Cancelled block break at $pos due to not currently being interactable");
		}

		return false;
	}

	public function continueBreakBlock(Vector3 $pos, int $face) : void{
		if($this->blockBreakHandlerModded !== null && $this->blockBreakHandlerModded->getBlockPos()->distanceSquared($pos) < 0.0001){
			$this->blockBreakHandlerModded->setTargetedFace($face);
		}
	}

	public function stopBreakBlock(Vector3 $pos) : void{
		if($this->blockBreakHandlerModded !== null && $this->blockBreakHandlerModded->getBlockPos()->distanceSquared($pos) < 0.0001){
			$this->blockBreakHandlerModded = null;
		}
	}

	/**
	 * @param Item[] $extraReturnedItems
	 */
	private function returnItemsFromAction(Item $oldHeldItem, Item $newHeldItem, array $extraReturnedItems) : void{
		$heldItemChanged = false;

		if(!$newHeldItem->equalsExact($oldHeldItem) && $oldHeldItem->equalsExact($this->inventory->getItemInHand())){
			//determine if the item was changed in some meaningful way, or just damaged/changed count
			//if it was really changed we always need to set it, whether we have finite resources or not
			$newReplica = clone $oldHeldItem;
			$newReplica->setCount($newHeldItem->getCount());
			if($newReplica instanceof Durable && $newHeldItem instanceof Durable){
				$newReplica->setDamage($newHeldItem->getDamage());
			}
			$damagedOrDeducted = $newReplica->equalsExact($newHeldItem);

			if(!$damagedOrDeducted || $this->hasFiniteResources()){
				if($newHeldItem instanceof Durable && $newHeldItem->isBroken()){
					$this->broadcastSound(new ItemBreakSound());
				}
				$this->inventory->setItemInHand($newHeldItem);
				$heldItemChanged = true;
			}
		}

		if(!$heldItemChanged){
			$newHeldItem = $oldHeldItem;
		}

		if($heldItemChanged && count($extraReturnedItems) > 0 && $newHeldItem->isNull()){
			$this->inventory->setItemInHand(array_shift($extraReturnedItems));
		}
		foreach($this->inventory->addItem(...$extraReturnedItems) as $drop){
			//TODO: we can't generate a transaction for this since the items aren't coming from an inventory :(
			$ev = new PlayerDropItemEvent($this, $drop);
			if($this->isSpectator()){
				$ev->cancel();
			}
			$ev->call();
			if(!$ev->isCancelled()){
				$this->dropItem($drop);
			}
		}
	}

	/**
	 * Internal function used to execute rollbacks when an action fails on a block.
	 */
	public function onFailedBlockAction(Vector3 $blockPos, ?int $face) : void{
		if($blockPos->distanceSquared($this->getLocation()) < 10000){
			$blocks = $blockPos->sidesArray();
			if($face !== null){
				$sidePos = $blockPos->getSide($face);

				/** @var Vector3[] $blocks */
				array_push($blocks, ...$sidePos->sidesArray()); //getAllSides() on each of these will include $blockPos and $sidePos because they are next to each other
			}else{
				$blocks[] = $blockPos;
			}
			foreach($this->getWorld()->createBlockUpdatePackets($blocks) as $packet){
				$this->getNetworkSession()->sendDataPacket($packet);
			}
		}
	}

}
