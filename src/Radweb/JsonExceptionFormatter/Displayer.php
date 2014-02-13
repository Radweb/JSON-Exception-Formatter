<?php

namespace Radweb\JsonExceptionFormatter;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Displayer
{

	public function __construct(FormatterInterface $formatter)
	{
		$this->formatter = $formatter;
	}

	public function display(Exception $exception, $debugMode = true)
	{
		$status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
		$headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : array();

		if ($debugMode)
		{
			$response = $this->formatter->formatDebug($exception);
		}
		else
		{
			$response = $this->formatter->formatPlain($exception);
		}

		return new JsonResponse($response, $status, $headers);
	}

}
