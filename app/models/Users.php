<?php

final class Table_Users extends Dao_Table_MySQL
{
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
