<?php

namespace symply\behavior;

use Closure;
use pocketmine\block\tile\TileFactory;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\cache\StaticPacketCache;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use pocketmine\entity\EntityFactory as PMEntityFactory;

class SymplyEntityFactory
{
	use SingletonTrait;

	private static int $ID = 400;

	/**
	 * @param class-string<Entity> $entityClass
	 * @param Closure|null $customClosure
	 * @param bool $registerAvailableActorIdentifiers
	 * @return void
	 */
	public function registerEntity(string $entityClass, ?Closure $customClosure = null, bool $isCustomEntity = true): void
	{
		$identifier = $entityClass::getNetworkTypeId();
		$customClosure ??= function (World $world, CompoundTag $nbt) use ($entityClass): Entity {
			return new $entityClass(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		};
		PMEntityFactory::getInstance()->register($entityClass, $customClosure, [$identifier]);
		if($isCustomEntity){
			$this->registerAvailableActorIdentifiers($identifier);
		}
	}

	public function registerAvailableActorIdentifiers(string $networkId) :void{
		StaticPacketCache::getInstance()->getAvailableActorIdentifiers()->identifiers->getRoot()->getListTag("idlist")->push(CompoundTag::create()
			->setByte("hasspawnegg", 1)
			->setString("id", $networkId)
			->setInt("rid", self::$ID++)
			->setByte("summonable", 1));
	}
}