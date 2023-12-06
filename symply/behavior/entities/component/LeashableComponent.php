<?php

namespace symply\behavior\entities\component;

/**
 * Allows this entity to be leashed and defines the conditions and events for this entity when is leashed.
 * @package symply\behavior\entities\component
 */
class LeashableComponent
{
	public function getName(): string
	{
		return "minecraft:leashable";
	}
}