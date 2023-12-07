<?php

namespace symply\behavior\entities\component\generic;

/**
 * Specifies how much life an entity has when spawned.
 * @package symply\behavior\entities\component
 */
class HealthGeneric
{
	public function getName(): string
	{
		return "minecraft:health";
	}
}