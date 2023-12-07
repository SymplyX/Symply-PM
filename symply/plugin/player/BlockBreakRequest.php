<?php

namespace symply\plugin\player;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\world\World;

class BlockBreakRequest
{
    public function __construct(private readonly World $world, private readonly Vector3 $origin, private float $start)
    {
    }

    /**
     * @return Vector3
     */
    public function getOrigin(): Vector3
    {
        return $this->origin;
    }

    /**
     * @return float
     */
    public function getStart(): float
    {
        return $this->start;
    }

    public function addTick(float $tick = 1.0): float
    {
        return $this->start += $tick;
    }

    public function __destruct()
    {
        if ($this->world->isInLoadedTerrain($this->origin)) {
            $this->world->broadcastPacketToViewers(
                $this->origin,
                LevelEventPacket::create(LevelEvent::BLOCK_STOP_BREAK, 0, $this->origin)
            );
        }
    }
}