<?php

namespace pocketmine\ranks;

use pocketmine\ranks\exception\RankAlreadyExistsException;
use pocketmine\ranks\exception\RankDoesNotExistsException;

class RankHandler{
	/** @var Rank[] */
	private array $ranks = array();

	/**
	 * @throws RankAlreadyExistsException
	 */
	public function addRank(Rank $rank): void
	{
		if ($this->exists($rank->getToken())) {
			throw new RankAlreadyExistsException("The rank with token " . $rank->getToken() . " already exists.");
		}
		$this->ranks[$rank->getToken()] = $rank;
	}

	/**
	 * @throws RankDoesNotExistsException
	 */
	public function removeRank(string $token): void
	{
		if (!$this->exists($token)) {
			throw new RankDoesNotExistsException("The rank with token " . $token . " does not exists.");
		}
		unset($this->ranks[$token]);
	}

	public function getRank(string $token): ?Rank
	{
		if ($this->exists($token)) return $this->ranks[$token];
		return null;
	}

	public function exists(string $token): bool
	{
		return isset($this->ranks[$token]);
	}

	/**
	 * @return array
	 */
	public function getRanks() : array{
		return $this->ranks;
	}
}