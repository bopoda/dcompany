<?php

final class Table_Orders extends Dao_Table_MySQL
{
	/**
	 * @return Table_Orders
	 */
	static public function me()
	{
		return self::getInstance(__CLASS__);
	}

	public function getTableName()
	{
		return 'orders';
	}

	public function fetchRowById($id)
	{
		return $this->fetchRowBy('id', $id);
	}

	public function fetchByUserId($userId, $offset = 0, $limit = 20)
	{
		return $this->getAdapter()->select(
			'SELECT * FROM ?#
				WHERE user_id = ?d
				ORDER BY id DESC
				LIMIT ?d,?d',
			$this->getTableName(),
			$userId,
			$offset, $limit
		);
	}
}
