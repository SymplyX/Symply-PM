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

use BadFunctionCallException;
use OutOfBoundsException;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\utils\SingletonTrait;
use symply\plugin\player\Player;

class Scoreboard implements IScoreboard
{
	use SingletonTrait;

	private static array $scoreboards = [];

	/**
	 * Set a scoreboard for a player.
	 */
	public function setScore(Player $player, string $displayName, string $objectiveName = self::OBJECTIVE_NAME) : void
	{
		if (isset(self::$scoreboards[$player->getName()])) {
			self::removeScore($player);
		}

		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = self::SLOT_SIDEBAR;
		$pk->objectiveName = $objectiveName;
		$pk->displayName = $displayName;
		$pk->criteriaName = self::CRITERIA_NAME;
		$pk->sortOrder = self::SORT_ASCENDING;
		$player->getNetworkSession()->sendDataPacket($pk);
		self::$scoreboards[$player->getName()] = $objectiveName;
	}

	/**
	 * Remove the scoreboard for a player.
	 */
	public function removeScore(Player $player) : void
	{
		$objectiveName = self::$scoreboards[$player->getName()] ?? self::OBJECTIVE_NAME;

		$pk = new RemoveObjectivePacket();
		$pk->objectiveName = $objectiveName;
		$player->getNetworkSession()->sendDataPacket($pk);

		unset(self::$scoreboards[$player->getName()]);
	}

	/**
	 * Get all active scoreboards.
	 */
	public function getScoreboards() : array
	{
		return self::$scoreboards;
	}

	/**
	 * Check if a player has a scoreboard.
	 */
	public static function hasScore(Player $player) : bool
	{
		return isset(self::$scoreboards[$player->getName()]);
	}

	/**
	 * Set a score line on the player's scoreboard.
	 */
	public function setScoreLine(Player $player, int $line, string $message, int $type = ScorePacketEntry::TYPE_FAKE_PLAYER) : void
	{
		if (!isset(self::$scoreboards[$player->getName()])) {
			throw new BadFunctionCallException("Cannot set a score to a player without a scoreboard");
		}

		if ($line < self::MIN_LINES || $line > self::MAX_LINES) {
			throw new OutOfBoundsException("$line is out of range, expected value between " . self::MIN_LINES . " and " . self::MAX_LINES);
		}

		$entry = new ScorePacketEntry();
		$entry->objectiveName = self::$scoreboards[$player->getName()] ?? self::OBJECTIVE_NAME;
		$entry->type = $type;
		$entry->customName = $message;
		$entry->score = $line;
		$entry->scoreboardId = $line;

		$pk = new SetScorePacket();
		$pk->type = $pk::TYPE_CHANGE;
		$pk->entries[] = $entry;
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	/**
	 * Set multiple score lines on the player's scoreboard.
	 */
	public function setMultipleScoreLines(Player $player, array $lines) : void
	{
		foreach ($lines as $line => $message) {
			$this->setScoreLine($player, $line, $message);
		}
	}
}
