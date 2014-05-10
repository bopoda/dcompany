<?php

class Controller_Orders
{
	const COUNT_PER_PAGE = 10;

	public function main(Http_Request $request)
	{
		if (!Auth::instance()->isLogged()) {
			return new Http_Response_Redirect(Helper_Url::routeUrl('user_auth'));
		}

		$user = Auth::instance()->getUser();
		if ($user['role_id'] == Table_Users::ROLE_MANAGER) {
			return $this->viewMainPageByManager($user, $request);
		}
		elseif ($user['role_id'] == Table_Users::ROLE_SERVICE) {
			return $this->viewMainPageByServiceCenter($user, $request);
		}
	}

	private function viewMainPageByManager(array $user, Http_Request $request)
	{
		$totalOrdersCnt = Table_Orders::me()->fetchCountByUserId($user['id']);

		$pagination = Pagination::factory(array(
			'total_items'    => $totalOrdersCnt,
			'items_per_page' => self::COUNT_PER_PAGE,
		), $request);

		$orders = Table_Orders::me()->fetchByUserId(
			$user['id'],
			$pagination->getOffset(),
			$pagination->getItemsPerPage()
		);

		return new Http_Response_View(
			'manager/order/main.html',
			array(
				'user' => $user,
				'orders' => $orders,
				'pages' => $pagination->render(),
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
			$orderRow = $this->getOrderFromPostData($request->getPost());

			$error = Order::getNewOrderErrors($orderRow);
			if ($error) {
				Messages::put($error, Messages::DANGER);
			}
			else {
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

		$user = Auth::instance()->getUser();

		return new Http_Response_View(
			'manager/order/add.html',
			array(
				'user' => $user,
			)
		);
	}

	public function ordersByStatus(Http_Request $request, $status)
	{
		if (!Auth::instance()->isLogged()) {
			return new Http_Response_Redirect(Helper_Url::routeUrl('user_auth'));
		}

		$statusId = Table_Orders::me()->getStatusIdByStatus($status);
		$user = Auth::instance()->getUser();

		if ($user['role_id'] == Table_Users::ROLE_MANAGER) {
			$totalOrdersCnt = Table_Orders::me()->fetchCountByUserId($user['id'], $statusId);
		}
		else {
			$totalOrdersCnt = Table_Orders::me()->fetchCountByStatusId($statusId);
		}

		$pagination = Pagination::factory(array(
			'total_items'    => $totalOrdersCnt,
			'items_per_page' => self::COUNT_PER_PAGE,
		), $request);

		if ($user['role_id'] == Table_Users::ROLE_MANAGER) {
			$orders = Table_Orders::me()->fetchByUserIdAndStatusId(
				$user['id'],
				$statusId,
				$pagination->getOffset(),
				$pagination->getItemsPerPage()
			);
		}
		else {
			$orders = Table_Orders::me()->fetchByStatusIds(
				array($statusId),
				$pagination->getOffset(),
				$pagination->getItemsPerPage()
			);
		}

		return new Http_Response_View(
			'manager/order/by-status.html',
			array(
				'user' => $user,
				'orders' => $orders,
				'status' => $status,
				'statusId' => $statusId,
				'pages' => $pagination->render(),
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

	public function ajaxOrderUpdate(Http_Request $request)
	{
		if ($request->getMethod() == 'POST') {
			$data = $request->getPost();

			$order = $this->makeOrderFromData($data);
			$purchasePrice = 0;
			foreach ($order as $orderPosition) {
				$purchasePrice += $orderPosition['purchase-price'];
			}
			$purchasePrice = (int)$purchasePrice;

			$orderRow = array(
				'id' => $data['id'],
				'delivery_date' => $data['delivery_date'],
				'updated_at' => date('Y-m-d H:i:s'),
				'status_id' => Arr::get($data, 'status_id', Table_Orders::STATUS_PRIVATE),
				'order' => serialize($order),
				'purchase_price' => $purchasePrice,
				'delivery_time' => Arr::get($data, 'delivery_time'),
				'contacts' => Arr::get($data, 'contacts'),
				'notes' => Arr::get($data, 'notes'),
				'delivery_address' => Arr::get($data, 'delivery_address'),
				'sale_price' => Arr::get($data, 'sale_price'),
				'assembly_price' => Arr::get($data, 'assembly_price'),
				'delivery_price' => Arr::get($data, 'delivery_price'),
			);
			$orderRow['fzp'] = Helper_Order::calculateFZP($orderRow);

			$orderRow = Table_Orders::me()->updateOrder($orderRow);

			$orderHtmlView = Core_View::create('manager/order/parts/part-order-row.html', array(
				'order' => $orderRow,
			));

			return new Http_Response_Json(array(
				'success' => true,
				'order' => $orderRow,
				'orderHtmlRow' => $orderHtmlView->render(),
			));
		}

		return new Http_Response_Json(array(
			'success' => false,
		));
	}

	private function getOrderFromPostData(array $data)
	{
//		if (!array_filter($data)) {
//			Messages::put('Нужно заполнить поля', Messages::DANGER);
//		}
//		else {
			$order = $this->makeOrderFromData($data);
			$purchasePrice = 0;
			foreach ($order as $orderPosition) {
				$purchasePrice += $orderPosition['purchase-price'];
			}
			$purchasePrice = (int)$purchasePrice;

			$orderRow = array(
				'delivery_date' => @$data['delivery_date'] ? : NULL,
				'added_at' => date('Y-m-d H:i:s'),
				'user_id' => Auth::instance()->getUser()['id'],
				'status_id' => Arr::get($data, 'status_id', Table_Orders::STATUS_PRIVATE),
				'order' => serialize($order),
				'purchase_price' => $purchasePrice,
				'delivery_time' => Arr::get($data, 'delivery_time'),
				'contacts' => Arr::get($data, 'contacts'),
				'notes' => Arr::get($data, 'notes'),
				'delivery_address' => Arr::get($data, 'delivery_address'),
				'sale_price' => Arr::get($data, 'sale_price'),
				'assembly_price' => Arr::get($data, 'assembly_price'),
				'delivery_price' => Arr::get($data, 'delivery_price'),
			);
			$orderRow['fzp'] = Helper_Order::calculateFZP($orderRow);


//		}

		return $orderRow;
	}

	private function makeOrderFromData(array $orderRow)
	{
		Assert::hasIndex($orderRow, 'order-hardware');
		Assert::hasIndex($orderRow, 'order-purchase-price');
		Assert::hasIndex($orderRow, 'order-supplier');

		$order = array();

		foreach ($orderRow['order-hardware'] as $index => $hardware) {
			$hardware = trim($hardware);
			if ($hardware) {
				$order[] = array(
					'hardware' => $hardware,
					'purchase-price' => Arr::get($orderRow['order-purchase-price'], $index, ''),
					'supplier' => Arr::get($orderRow['order-supplier'], $index, ''),
				);
			}
		}

		return $order;
	}

}
