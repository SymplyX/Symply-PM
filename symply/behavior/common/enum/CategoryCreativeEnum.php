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

namespace symply\behavior\common\enum;

enum CategoryCreativeEnum : string
{
	case CATEGORY_ALL = "all";
	case CATEGORY_COMMANDS = "commands";
	case CATEGORY_CONSTRUCTION = "construction";
	case CATEGORY_EQUIPMENT = "equipment";
	case CATEGORY_ITEMS = "items";
	case CATEGORY_NATURE = "nature";

	public function toItemCategory() : int{
		return match ($this){
			self::CATEGORY_CONSTRUCTION => 1,
			self::CATEGORY_NATURE => 2,
			self::CATEGORY_EQUIPMENT => 3,
			self::CATEGORY_ITEMS => 4,
			default => 0
		};
	}
}
