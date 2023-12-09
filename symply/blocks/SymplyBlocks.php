<?php

namespace symply\blocks;

use pocketmine\block\Block;
use pocketmine\utils\CloningRegistryTrait;

class SymplyBlocks
{
	use CloningRegistryTrait;

	private function __construct()
	{
		//NOOP
	}

	protected static function register(string $name, Block $block): void
	{
		self::_registryRegister($name, $block);
	}

	/**
	 * @return Block[]
	 * @phpstan-return array<string, Block>
	 */
	public static function getAll(): array
	{
		/** @var Block[] $result */
		$result = self::_registryGetAll();
		return $result;
	}

	protected static function setup(): void
	{
		// TODO: Add blocks here
	}
}