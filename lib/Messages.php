<?php

class Messages
{
	const SUCCESS = 'success';
	const INFO = 'info';
	const WARNING = 'warning';
	const DANGER = 'danger';

	private static $_buffer;

	private static function getBuffer()
	{
		if (!isset($_SESSION['_messages'])) {
			$_SESSION['_messages'] = array();
		}

		Messages::$_buffer =& $_SESSION['_messages'];
	}

	public static function clear()
	{
		Messages::getBuffer();
		Messages::$_buffer = array();
	}

	/**
	 * @param $message
	 * @param string $type
	 * @return bool
	 * @throws Exception
	 */
	public static function put($message, $type = self::SUCCESS)
	{
		if (!in_array($type, array(
			self::SUCCESS,
			self::INFO,
			self::WARNING,
			self::DANGER,
		))) {
			throw new Exception("Unexpected message type given: $type");
		}

		Messages::getBuffer();
		if ($message === false) {
			return false;
		}

		Messages::$_buffer[$type][] = $message;
		return true;
	}

	/**
	 * Get all messages array
	 *
	 * @return array
	 */
	public static function getAll()
	{
		Messages::getBuffer();
		return Messages::$_buffer;
	}

	/**
	 * Using at frontend
	 * @return string
	 */
	public static function view()
	{
		Messages::getBuffer();
		$html = '';
		if (count(Messages::$_buffer)) {
			foreach (Messages::$_buffer as $type => $messages) {
				$html .= Core_View::create('messages/list.html', array(
					'type' => $type,
					'messages' => $messages,
				))
					->render();
			}

			Messages::clear();
		}

		return $html;
	}

	public static function getCount()
	{
		Messages::getBuffer();
		return count(Messages::$_buffer);
	}

}
