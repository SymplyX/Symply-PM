<?php

namespace symply\behavior\entities\component;

/**
 * Defines physics properties of an actor, including if it is affected by gravity or if it collides with objects.
 * @package symply\behavior\entities\component
 */
class PhysicsComponent
{
	public function getName(): string
	{
		return "minecraft:physics";
	}
}