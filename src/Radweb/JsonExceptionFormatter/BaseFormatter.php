<?php namespace Radweb\JsonExceptionFormatter;

use Exception;

class BaseFormatter implements FormatterInterface {

	function formatDebug(Exception $exception)
	{
		return array(
			'error' => array(
				'type' => get_class($exception),
				'message' => $exception->getMessage(),
				'file' => $exception->getFile(),
				'line' => $exception->getLine(),
			)
		);
	}

	function formatPlain(Exception $exception)
	{
		return array(
			'error' => array(
				'type' => get_class($exception),
				'message' => $exception->getMessage(),
			)
		);
	}

}
