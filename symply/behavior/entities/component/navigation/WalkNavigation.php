<?php

namespace symply\behavior\entities\component\navigation;

/**
 * Allows this entity to generate paths by walking around and jumping up and down a block like regular mobs.
 * @package symply\behavior\entities\component\navigation
 */
class WalkNavigation
{
	public function getName(): string
	{
		return "minecraft:navigation.walk";
	}
}