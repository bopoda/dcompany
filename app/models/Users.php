<?php

final class Table_Users extends Dao_Table_MySQL
{
	const ROLE_MANAGER = 1;
	const ROLE_SERVICE = 2;
	const ROLE_ADMIN = 3;

	/**
	 * @return Table_Users
	 */
	static public function me()
	{
		return self::getInstance(__CLASS__);
	}

	public function getTableName()
	{
		return 'users';
	}

	public function fetchRowById($id)
	{
		return $this->fetchRowBy('id', $id);
	}

	public function fetchRowByEmail($id)
	{
		return $this->fetchRowBy('email', $id);
	}

}
