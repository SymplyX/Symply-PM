<?php

namespace symply\behavior\entities\component;

/**
 * Determines whether this entity can be ridden. Allows specifying the different seat positions and quantity.
 * @package symply\behavior\entities\component
 */
class RideableComponent
{
	public function getName(): string
	{
		return "minecraft:rideable";
	}
}