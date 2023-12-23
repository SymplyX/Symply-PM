<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace symply\camera;

use pocketmine\network\mcpe\protocol\types\camera\CameraPreset;
use pocketmine\utils\RegistryTrait;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static CameraPreset FREE()
 * @method static CameraPreset FIRST_PERSON()
 * @method static CameraPreset THIRD_PERSON()
 * @method static CameraPreset THIRD_PERSON_FRONT()
 */

final class VanillaCameraPresets{
	use RegistryTrait;

	protected static function setup(): void{
		self::register("free", new CameraPreset("minecraft:free", "", 0, 0, 0, 0, 0, CameraPreset::AUDIO_LISTENER_TYPE_CAMERA, false));
		self::register("first_person", new CameraPreset("minecraft:first_person", "", 0, 0, 0, 0, 0, CameraPreset::AUDIO_LISTENER_TYPE_PLAYER, false));
		self::register("third_person", new CameraPreset("minecraft:third_person", "", 0, 0, 0, 0, 0, CameraPreset::AUDIO_LISTENER_TYPE_PLAYER, false));
		self::register("third_person_front", new CameraPreset("minecraft:third_person_front", "", 0, 0, 0, 0, 0, CameraPreset::AUDIO_LISTENER_TYPE_PLAYER, false));
	}

	protected static function register(string $name, CameraPreset $member): void{
		self::_registryRegister($name, $member);
	}

	public static function getAll(): array{
		return [self::FREE(), self::FIRST_PERSON(), self::THIRD_PERSON(), self::THIRD_PERSON_FRONT()];
	}
}