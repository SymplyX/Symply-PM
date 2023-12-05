<?php

declare(strict_types=1);

namespace pocketmine\world\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;

final class CampfireSound implements Sound{

    public function encode(Vector3 $pos) : array{
        return [LevelSoundEventPacket::nonActorSound(LevelSoundEvent::BLOCK_CAMPFIRE_CRACKLE, $pos, false)];
    }
}