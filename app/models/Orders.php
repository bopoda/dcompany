<?php

final class Table_Orders extends Dao_Table_MySQL
{
	const STATUS_PRIVATE = 0;
	const STATUS_PENDING = 1;
	const STATUS_CLOSED = 2;

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

	public function addOrder(array $orderRow)
	{
		return $this->getAdapter()->query(
			'INSERT INTO ?# (?#)
				VALUES(?a)',
			$this->getTableName(),
			array_keys($orderRow),
			array_values($orderRow)
		);
	}

	public function fetchByUserIdAndStatusId($userId, $statusId, $offset = 0, $limit = 20)
	{
		return $this->getAdapter()->select(
			'SELECT * FROM ?#
				WHERE user_id=?d AND status_id=?d
				ORDER BY id DESC
				LIMIT ?d,?d',
			$this->getTableName(),
			$userId, $statusId,
			$offset, $limit
		);
	}

	public function fetchByStatusIds(array $statusIds, $offset = 0, $limit = 100)
	{
 		return $this->getAdapter()->select(
			'SELECT * FROM ?#
				WHERE status_id IN (?a)
				ORDER BY id DESC
				LIMIT ?d,?d',
			$this->getTableName(),
			$statusIds
		);
	}

	public function fetchMonthlyFzpByUserId($userId)
	{
		return $this->getAdapter()->selectCell(
			'SELECT SUM(fzp) FROM ?# 
				WHERE user_id = ?d
				AND delivery_date >= ?
				AND status_id = ?d',
			$this->getTableName(),
			$userId,
			date('Y-m-') . '01', // начало текущего месяца
			self::STATUS_CLOSED
		);
	}

	public function fetchCountByUserId($userId, $statusId = NULL)
	{
		return $this->getAdapter()->selectCell(
			'SELECT COUNT(*) FROM ?#
				WHERE user_id = ?d
				{AND status_id = ?d}',
			$this->getTableName(),
			$userId,
			$statusId ? : DBSIMPLE_SKIP
		);
	}

	public function fetchCountByStatusId($statusId)
	{
		return $this->getAdapter()->selectCell(
			'SELECT COUNT(*) FROM ?#
				WHERE status_id = ?d',
			$this->getTableName(),
			$statusId
		);
	}

	public function updateField($id, $fieldName, $fieldValue)
	{
		return $this->getAdapter()->query(
			'UPDATE ?#
				SET ?# = ?,
				updated_at = ?
				WHERE id = ?d',
			$this->getTableName(),
			$fieldName, $fieldValue,
			date('Y-m-d H:i:s'),
			$id
		);
	}

	public function updateOrder(array $orderRow)
	{
		return $this->replaceRow('id', $orderRow);
	}

	public function getStatusByStatusId($statusId)
	{
		if ($statusId == self::STATUS_PRIVATE) {
			return 'private';
		}
		if ($statusId == self::STATUS_PENDING) {
			return 'pending';
		}
		elseif ($statusId == self::STATUS_CLOSED) {
			return 'closed';
		}
	}

	public function getStatusIdByStatus($status)
	{
		if ($status == 'private') {
			return self::STATUS_PRIVATE;
		}
		if ($status == 'pending') {
			return self::STATUS_PENDING;
		}
		elseif ($status == 'closed') {
			return self::STATUS_CLOSED;
		}
	}

}
