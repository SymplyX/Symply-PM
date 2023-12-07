<?php

namespace symply\behavior\entities\component\generic;

/**
 * Despawns the Actor when the despawn rules or optional filters evaluate to true.
 * @package symply\behavior\entities\component
 */
class DespawnGeneric
{
	public function getName(): string
	{
		return "minecraft:despawn";
	}
}