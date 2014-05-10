<?php

class Helper_Manager 
{
	private static $fzpIndex = 0.25;

	public static function getFzpIndex()
	{
		return self::$fzpIndex;
	}

	public static function calculateMonthlyZp(array $user)
	{
		$minZp = 100;

		$monthlyFzp = Table_Orders::me()->fetchMonthlyFzpByUserId($user['id']);

		if ($monthlyFzp >= 2000) {
			$minZp += 200;
		}
		elseif ($monthlyFzp >= 1000) {
			$minZp += 100;
		}

		$additionalZp = ceil($monthlyFzp * self::getFzpIndex());

		return $minZp + $additionalZp;
	}

}