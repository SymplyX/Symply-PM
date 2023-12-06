<?php

namespace symply\utils;

use pmmp\thread\ThreadSafeArray;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockTypeIds;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use WeakMap;

class BlockUtils
{
    public static function calculateBreakProgress(Player $player, Block $block) : float{
        if(!$block->getBreakInfo()->isBreakable()){
            return 0.0;
        }
        $breakTimePerTick = ceil($block->getBreakInfo()->getBreakTime($player->getInventory()->getItemInHand()) * 20);
        $breakTimePerTick *= 1 - (0.2 * $player->getEffects()->get(VanillaEffects::HASTE())?->getAmplifier() ?? 0);
        $breakTimePerTick *= 1 - (0.3 * $player->getEffects()->get(VanillaEffects::MINING_FATIGUE())?->getAmplifier() ?? 0);

        $breakTimePerTick *= match ($player->getWorld()->getBlock($player->getLocation())->getTypeId()) {
            BlockTypeIds::LADDER, BlockTypeIds::VINES => true,
            default => false
        } || !$player->isOnGround() ? BlockBreakInfo::INCOMPATIBLE_TOOL_MULTIPLIER : 1;
        $breakTimePerTick -= 1;
        return ceil($breakTimePerTick);
    }
}