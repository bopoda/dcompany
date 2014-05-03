<?php

class Controller_Orders
{
	public function main(Http_Request $request)
	{
		if (!Auth::instance()->isLogged()) {
			return new Http_Response_Redirect(Helper_Url::routeUrl('user_auth'));
		}

		$user = Auth::instance()->getUser();
		if ($user['role_id'] == Table_Users::ROLE_MANAGER) {
			return $this->viewMainPageByManager($user);
		}
		elseif ($user['role_id'] == Table_Users::ROLE_SERVICE) {
			return $this->viewMainPageByServiceCenter($user);
		}
	}

	private function viewMainPageByManager(array $user)
	{
		$orders = Table_Orders::me()->fetchByUserId($user['id']);

		return new Http_Response_View(
			'manager/order/main.html',
			array(
				'user' => $user,
				'orders' => $orders,
			)
		);
	}

	private function viewMainPageByServiceCenter(array $user)
	{
		$orders = Table_Orders::me()->fetchByStatusIds(array(Table_Orders::STATUS_PENDING, Table_Orders::STATUS_CLOSED));

		return new Http_Response_View(
			'manager/order/main.html',
			array(
				'user' => $user,
				'orders' => $orders,
			)
		);
	}

	public function orderAdd(Http_Request $request)
	{
		if (!Auth::instance()->isLogged()) {
			return new Http_Response_Redirect(Helper_Url::routeUrl('user_auth'));
		}

		if ($request->getMethod() == 'POST') {
			$this->addOrder($request->getPost());
		}

		$user = Auth::instance()->getUser();

		return new Http_Response_View(
			'manager/order/add.html',
			array(
				'user' => $user,
			)
		);
	}

	public function ordersByStatus($status)
	{
		if (!Auth::instance()->isLogged()) {
			return new Http_Response_Redirect(Helper_Url::routeUrl('user_auth'));
		}

		$statusId = Table_Orders::me()->getStatusIdByStatus($status);
		$user = Auth::instance()->getUser();

		if ($user['role_id'] == Table_Users::ROLE_MANAGER) {
			$orders = Table_Orders::me()->fetchByUserIdAndStatusId($user['id'], $statusId);
		}
		else {
			$orders = Table_Orders::me()->fetchByStatusIds(array(Table_Orders::me()->getStatusIdByStatus($status)));
		}

		return new Http_Response_View(
			'manager/order/list-by-status.html',
			array(
				'user' => $user,
				'orders' => $orders,
				'status' => $status,
			)
		);
	}

	// ???
	public function ajaxOrderEditField(Http_Request $request)
	{
		$orderId = $request->getPostVar('orderId');
		$fieldName = $request->getPostVar('fieldName');
		$fieldValue = trim($request->getPostVar('fieldValue'));

		$order = Table_Orders::me()->fetchRowById($orderId);

		//TODO: add allowEdit check

		// isset не прокатывает, если индекс есть, но значение NULL !!!
		if (/*isset($order[$fieldName]) &&*/ $order[$fieldName] != $fieldValue) {
			Table_Orders::me()->updateField($orderId, $fieldName, $fieldValue);
		}

		return new Http_Response_Json(array(
			'success' => true,
			'fieldEscapedValue' => Content_Text::escape($fieldValue),
		));
	}

	public function ajaxAddOrderHtml(Http_Request $request)
	{
		$view = Core_View::create('manager/order/parts/part-add-order.html');

		return new Http_Response_Json(array(
			'success' => true,
			'html' => $view->render(),
		));
	}

	private function addOrder($orderRow)
	{
		$orderRow = array_map(function($value){return trim($value);}, $orderRow);

		if (!array_filter($orderRow)) {
			Messages::put('Нужно заполнить поля', Messages::DANGER);
		}
		else {
			$orderRow['user_id'] = Auth::instance()->getUser()['id'];
			$result = Table_Orders::me()->addOrder($orderRow);
			if ($result) {
				$statusId = Arr::get($orderRow, 'status_id', Table_Orders::STATUS_PRIVATE);
				$status = Table_Orders::me()->getStatusByStatusId($statusId);
				Messages::put('Заказ успешно добавлен. Перейти к <a href="'.Helper_Url::routeUrl('ordersByStatus', array('status' => $status)).'" class="alert-link">списку</a>.');
			}
			else {
				Messages::put('Были ошибки. Заказ не добавлен.', Messages::DANGER);
			}
		}
	}

}
