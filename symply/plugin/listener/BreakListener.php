<?php

namespace symply\plugin\listener;

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
use symply\utils\BlockUtils;
use WeakMap;

class BreakListener implements Listener
{

	/**
	 * @var WeakMap
	 * @phpstan-var WeakMap<NetworkSession, array>
	 */
	private WeakMap $breaks;

	const MAX_DISTANCE_BREAK = 16 ** 2;

	public function __construct()
	{
		$this->breaks = new WeakMap();
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
			foreach ($blockActions as $k => $blockAction) {
				$action = $blockAction->getActionType();
				if ($blockAction instanceof PlayerBlockActionWithBlockInfo) {
					$vector3 = new Vector3($blockAction->getBlockPosition()->getX(), $blockAction->getBlockPosition()->getY(), $blockAction->getBlockPosition()->getZ());
					$block = $player->getWorld()->getBlock($vector3);
					if ($action === PlayerAction::START_BREAK) {
						if ($block->getBreakInfo()->breaksInstantly()) continue;
						$this->breaks->offsetSet($session, [$vector3, ceil(microtime(true) * 20), BlockUtils::calculateBreakProgress($player, $block)]);
					} elseif ($action === PlayerAction::CRACK_BREAK) {
						if ($this->breaks->offsetExists($session)) {
							[$OriginalVector3, $startTime, $endTime] = $this->breaks->offsetGet($session);
							if ($vector3->distanceSquared($OriginalVector3) > self::MAX_DISTANCE_BREAK) {
								$this->breaks->offsetUnset($session);
								continue;
							}
							if ((ceil(microtime(true) * 20) - $startTime) >= $endTime) {
								$player->breakBlock($vector3);
								$this->breaks->offsetUnset($session);
							}

						}
					} else if ($blockAction->getActionType() === PlayerAction::ABORT_BREAK){
						if ($this->breaks->offsetExists($session)) {
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