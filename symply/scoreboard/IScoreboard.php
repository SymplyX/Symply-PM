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

namespace symply\scoreboard;

use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use symply\plugin\player\Player;

interface IScoreboard
{
	public const OBJECTIVE_NAME = "objective";
	public const CRITERIA_NAME = "dummy";
	public const MIN_LINES = 1;
	public const MAX_LINES = 15;
	public const SORT_ASCENDING = 0;
	public const SLOT_LIST = "list";
	public const SLOT_SIDEBAR = "sidebar";
	public const SLOT_BELOW_NAME = "belowname";

	public function setScore(Player $player, string $displayName, string $objectiveName = self::OBJECTIVE_NAME) : void;

	public function removeScore(Player $player) : void;

	public function getScoreboards() : array;

	public static function hasScore(Player $player) : bool;

	public function setScoreLine(Player $player, int $line, string $message, int $type = ScorePacketEntry::TYPE_FAKE_PLAYER) : void;

	public function setMultipleScoreLines(Player $player, array $lines) : void;
}
