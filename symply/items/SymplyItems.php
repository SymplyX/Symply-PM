<?php

namespace symply\items;

use pocketmine\item\Item;
use pocketmine\utils\CloningRegistryTrait;

class SymplyItems
{
	use CloningRegistryTrait;

	private function __construct()
	{
		//NOOP
	}

	protected static function register(string $name, Item $item): void
	{
		self::_registryRegister($name, $item);
	}

	/**
	 * @return Item[]
	 * @phpstan-return array<string, Item>
	 */
	public static function getAll(): array
	{
		//phpstan doesn't support generic traits yet :(
		/** @var Item[] $result */
		$result = self::_registryGetAll();
		return $result;
	}

	protected static function setup(): void
	{
		//TODO: Add items here
	}
}