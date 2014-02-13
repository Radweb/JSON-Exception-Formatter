<?php namespace Radweb\JsonExceptionFormatter;

use Exception;

interface FormatterInterface {

	function formatDebug(Exception $exception);

	function formatPlain(Exception $exception);

} 