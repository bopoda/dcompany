<?php

class Helper_Order
{
	public static function calculateFZP(array $order)
	{
		$fzp = $order['sale_price'] - $order['purchase_price'] - $order['assembly_price'] - $order['delivery_price'];

		return $fzp;
	}

	public static function getLabelByStatus($status)
	{
		$statusId = $status;
		if (!is_numeric($status)) {
			$statusId = Table_Orders::me()->getStatusIdByStatus($status);
		}

		if ($statusId == Table_Orders::STATUS_PRIVATE) {
			return 'danger';
		}
		if ($statusId == Table_Orders::STATUS_PENDING) {
			return 'primary';
		}
		elseif ($statusId == Table_Orders::STATUS_CLOSED) {
			return 'success';
		}
	}

	public static function getStatusLabelById($statusId)
	{
		if ($statusId == Table_Orders::STATUS_PRIVATE) {
			return "<span data-rel='tooltip' data-original-title='" . Helper_Order::getStatusDescriptionById($statusId) . "' class='label label-inverse arrowed'>private</span>";
		}
		elseif ($statusId == Table_Orders::STATUS_PENDING) {
			return "<span data-rel='tooltip' data-original-title='" . Helper_Order::getStatusDescriptionById($statusId) . "' class='label label-warning arrowed-in arrowed-in-right'>pending</span>";
		}
		elseif ($statusId == Table_Orders::STATUS_CLOSED) {
			return "<span data-rel='tooltip' data-original-title='" . Helper_Order::getStatusDescriptionById($statusId) . "' class='label label-success'>closed</span>";
		}
	}

	public static function getAllowedUserStatuses()
	{
		$user = Auth::instance()->getUser();

		if ($user['role_id'] == Table_Users::ROLE_MANAGER) {
			return self::getManagerAllowedStatuses();
		}
		elseif ($user['role_id'] == Table_Users::ROLE_SERVICE) {
			return self::getServiceManagerAllowedStatuses();
		}
	}

	public static function getManagerAllowedStatuses()
	{
		return array(
			Table_Orders::STATUS_PRIVATE => Table_Orders::me()->getStatusByStatusId(Table_Orders::STATUS_PRIVATE),
			Table_Orders::STATUS_PENDING => Table_Orders::me()->getStatusByStatusId(Table_Orders::STATUS_PENDING),
		);
	}

	public static function getServiceManagerAllowedStatuses()
	{
		return array(
			Table_Orders::STATUS_PENDING => Table_Orders::me()->getStatusByStatusId(Table_Orders::STATUS_PENDING),
			Table_Orders::STATUS_CLOSED => Table_Orders::me()->getStatusByStatusId(Table_Orders::STATUS_CLOSED),
		);
	}

	public static function getStatusDescriptionById($statusId)
	{
		if ($statusId == Table_Orders::STATUS_PRIVATE) {
			return 'private - заказы видны только менеджеру, который их добавил, не видны сервисному центру.';
		}
		if ($statusId == Table_Orders::STATUS_PENDING) {
			return 'pending - заказы ждут доставки клиенту, видны сервисному центру.';
		}
		elseif ($statusId == Table_Orders::STATUS_CLOSED) {
			return 'closed - заказы уже доставлены клиенту.';
		}
	}
}
