<?php

namespace symply\behavior\entities\component\generic;

use pocketmine\entity\EntitySizeInfo;

/**
 * Sets the width and height of the Entity's collision box.
 * @package symply\behavior\entities\component
 */
class CollisionBoxGeneric
{
	private EntitySizeInfo $entitySizeInfo;

	public function __construct(float $height, float $width, ?float $eyeHeight = null)
	{
		$this->entitySizeInfo = new EntitySizeInfo($width, $height, $eyeHeight);
	}

	public function getName(): string
	{
		return "minecraft:collision_box";
	}

	public function getEntitySizeInfo(): EntitySizeInfo
	{
		return $this->entitySizeInfo;
	}
}