<?php

namespace symply\behavior\entities\component;

/**
 * Allows this entity to be named (e.g. using a name tag).
 * @package symply\behavior\entities\component
 */
class NameableComponent
{
	public function getName(): string
	{
		return "minecraft:nameable";
	}
}