<?php

namespace symply\plugin\listener;

use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\network\mcpe\protocol\types\PlayerBlockAction;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionStopBreak;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionWithBlockInfo;
use symply\plugin\player\BlockBreakRequest;
use symply\utils\BlockUtils;
use WeakMap;

class ClientBreakListener implements Listener
{

	/**
	 * @var WeakMap
	 * @phpstan-var WeakMap<NetworkSession, BlockBreakRequest>
	 */
	private WeakMap $breaks;

	/**
	 * @var WeakMap
	 * @phpstan-var WeakMap<Block, float>
	 */
	private WeakMap $blockSpeed;

	const MAX_DISTANCE_BREAK = 16 ** 2;

	public function __construct()
	{
		$this->breaks = new WeakMap();
		$this->blockSpeed = new WeakMap();
	}

	public function onSend(DataPacketSendEvent $event): void
	{
		$packets = $event->getPackets();
		$targets = $event->getTargets();
		foreach ($packets as $packet) {
			if ($packet instanceof LevelEventPacket) {
				if ($packet->eventId === LevelEvent::BLOCK_START_BREAK && $packet->position !== null) {
					$block = $targets[array_key_first($targets)]->getPlayer()->getWorld()->getBlock($packet->position);
					if (!isset($this->blockSpeed[$block])) break;
					$packet->eventData = (int)(floor(65535 * $this->blockSpeed[$block]));
					$this->blockSpeed->offsetUnset($block);
				}
			}
		}
	}


	public function onDataReceive(DataPacketReceiveEvent $event): void
	{
		$player = ($session = $event->getOrigin())->getPlayer();
		if ($player === null) return;
		$packet = $event->getPacket();
		if(!$packet instanceof PlayerAuthInputPacket) return;

		$blockActions = $packet->getBlockActions();
		if ($blockActions !== null) {
			if (count($blockActions) > 100) {
				$session->getLogger()->debug("PlayerAuthInputPacket contains " . count($blockActions) . " block actions, dropping");
				return;
			}
			/**
			 * @var int $k
			 * @var PlayerBlockAction $blockAction
			 */
			$blockActions = array_filter($blockActions, fn(PlayerBlockAction $blockAction) =>
				$blockAction->getActionType() === PlayerAction::START_BREAK ||
				$blockAction->getActionType() === PlayerAction::CRACK_BREAK ||
				$blockAction->getActionType() === PlayerAction::ABORT_BREAK ||
				$blockAction instanceof PlayerBlockActionStopBreak);
			foreach ($blockActions as $blockAction) {
				$action = $blockAction->getActionType();
				if ($blockAction instanceof PlayerBlockActionWithBlockInfo) {
					if ($action === PlayerAction::START_BREAK) {
						$vector3 = new Vector3($blockAction->getBlockPosition()->getX(), $blockAction->getBlockPosition()->getY(), $blockAction->getBlockPosition()->getZ());
						$block = $player->getWorld()->getBlock($vector3);
						if ($block->getBreakInfo()->breaksInstantly()) continue;
						$speed = BlockUtils::getDestroyRate($player, $block);
						$this->breaks->offsetSet($session, new BlockBreakRequest($player->getWorld(), $vector3, $speed));
						$this->blockSpeed[$block] = $speed;
					} elseif ($action === PlayerAction::CRACK_BREAK) {
						if ($this->breaks->offsetExists($session)) {
							$vector3 = new Vector3($blockAction->getBlockPosition()->getX(), $blockAction->getBlockPosition()->getY(), $blockAction->getBlockPosition()->getZ());
							$block = $player->getWorld()->getBlock($vector3);
							$breakRequest = $this->breaks->offsetGet($session);
							if ($vector3->distanceSquared($breakRequest->getOrigin()) > self::MAX_DISTANCE_BREAK) {
								$this->breaks->offsetUnset($session);
								continue;
							}
							if ($breakRequest->addTick(BlockUtils::getDestroyRate($player, $block)) >= 1) {
								$player->breakBlock($vector3);
								$this->breaks->offsetUnset($session);
							}
						}
					} else if ($blockAction->getActionType() === PlayerAction::ABORT_BREAK){
						$vector3 = new Vector3($blockAction->getBlockPosition()->getX(), $blockAction->getBlockPosition()->getY(), $blockAction->getBlockPosition()->getZ());
						if ($this->breaks->offsetExists($session)) {
							$player->stopBreakBlock($vector3);
							$this->breaks->offsetUnset($session);
						}
					}
				} elseif ($blockAction instanceof PlayerBlockActionStopBreak) {
					if ($this->breaks->offsetExists($session)) {
						$this->breaks->offsetUnset($session);
					}
				}
			}
		}
	}
}

