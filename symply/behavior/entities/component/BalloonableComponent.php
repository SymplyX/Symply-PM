<?php

namespace symply\behavior\entities\component;

/**
 * Allows the entity to have a balloon attached and defines the conditions and events for the entity when is ballooned.
 * @package symply\behavior\entities\component
 */
class BalloonableComponent
{
	public function getName(): string
	{
		return "minecraft:balloonable";
	}
}