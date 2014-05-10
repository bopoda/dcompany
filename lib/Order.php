<?php

class Order
{
	public static function getNewOrderErrors($orderRow)
	{
		if (!count($orderRow['order'])) {
			return 'Нужно заполнить хотя бы одну позицию.';
		}
		if ($orderRow['delivery_date'] && !preg_match('/\d\d\d\d\-\d\d\-\d\d/', $orderRow['delivery_date'])) {
			return 'Формат даты доставки указан неверно, нужно: yyyy-mm-dd';
		}

		if ($orderRow['status_id'] > Table_Orders::STATUS_PRIVATE) {
			if ($orderRow['purchase_price'] > $orderRow['sale_price']) {
				return 'Цена продажи не может быть ниже закупочной.';
			}

			if (!$orderRow['delivery_date']) {
				return 'Обязательно укажите дату доставки.';
			}

			if ($orderRow['fzp'] < 0) {
				return 'ФЗП не может быть меньше нуля.';
			}
		}
	}
}