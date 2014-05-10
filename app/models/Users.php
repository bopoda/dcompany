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

	public function fetchAll()
	{
		return $this->getAdapter()->query(
			'SELECT * FROM ?#',
			$this->getTableName()
		);
	}

	public function getRoleTitleByRoleId($roleId)
	{
		switch ($roleId) {
			case self::ROLE_MANAGER:
				return 'менеджер';
				break;

			case self::ROLE_SERVICE:
				return 'сервисный центр';
				break;

			case self::ROLE_ADMIN:
				return 'admin';
				break;

			default:
				return NULL;
		}
	}

}
