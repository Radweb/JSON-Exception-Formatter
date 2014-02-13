<?php namespace spec\Radweb\JsonExceptionFormatter;

use Exception;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Radweb\JsonExceptionFormatter\FormatterInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DisplayerSpec extends ObjectBehavior {

	function let(FormatterInterface $formatter)
	{
		/** @noinspection PhpParamsInspection */
		$this->beConstructedWith($formatter);
	}

	function it_is_initializable()
	{
		$this->shouldHaveType('Radweb\JsonExceptionFormatter\Displayer');
	}

	function it_returns_a_JsonResponse(Exception $exception)
	{
		$this->display($exception)->shouldReturnAnInstanceOf('Illuminate\Http\JsonResponse');
	}

	function it_uses_formatDebug_when_in_debug_mode(Exception $exception, $formatter)
	{
		$formatter->formatDebug($exception)->shouldBeCalled();
		$formatter->formatPlain($exception)->shouldNotBeCalled();

		$this->display($exception, $debugMode = true);
	}

	function it_uses_formatPlain_when_not_in_debug_mode(Exception $exception, $formatter)
	{
		$formatter->formatDebug($exception)->shouldNotBeCalled();
		$formatter->formatPlain($exception)->shouldBeCalled();

		$this->display($exception, $debugMode = false);
	}

	function it_uses_formatDebug_by_default(Exception $exception, $formatter)
	{
		$formatter->formatDebug($exception)->shouldBeCalled();
		$formatter->formatPlain($exception)->shouldNotBeCalled();

		$this->display($exception);
	}

	function it_wraps_the_result_from_formatter(Exception $exception, $formatter)
	{
		$formatter->formatDebug($exception)->willReturn(array('error' => 'foo bar'));

		$this->display($exception)->getContent()->shouldBe('{"error":"foo bar"}');
	}

	function it_returns_a_status_code_of_500_for_normal_exceptions(Exception $exception)
	{
		$this->display($exception)->getStatusCode()->shouldBe(500);
	}

	function it_uses_the_status_code_from_an_HttpExceptionInterface(HttpException $exception)
	{
		$exception->getStatusCode()->willReturn(401);
		$exception->getHeaders()->willReturn(array());

		$this->display($exception)->getStatusCode()->shouldBe(401);
	}

	function it_uses_the_headers_from_an_HttpExceptionInterface(HttpException $exception)
	{
		$exception->getStatusCode()->willReturn(401);
		$exception->getHeaders()->willReturn(array('Foo' => 'Bar'));

		$this->display($exception)->headers->get('Foo')->shouldBe('Bar');
	}

}
