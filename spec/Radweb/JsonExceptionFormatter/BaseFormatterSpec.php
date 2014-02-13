<?php namespace spec\Radweb\JsonExceptionFormatter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RuntimeException;

class BaseFormatterSpec extends ObjectBehavior {

	function it_is_initializable()
	{
		$this->shouldHaveType('Radweb\JsonExceptionFormatter\BaseFormatter');
		$this->shouldImplement('Radweb\JsonExceptionFormatter\FormatterInterface');
	}

	function it_formats_a_response_from_an_exception()
	{
		$exception = new RuntimeException('Foo Bar!');
		$response = $this->formatDebug($exception);

		// testing it this was as doing something like:
		//
		//   $response->shouldBe([
		//     'error' => [
		//       ...
		//     ]
		//   ]);
		//
		// results in a poor error message like:
		//   "expected [array:1], but got [array:1]."
		//
		// that's not too useful...

		$response->shouldBeArray();

		$response->shouldHaveKey('error');

		$response->shouldHaveNestedKey('error.type');
		$response->shouldHaveNestedKeyBe('error.type', 'RuntimeException');

		$response->shouldHaveNestedKey('error.message');
		$response->shouldHaveNestedKeyBe('error.message', 'Foo Bar!');

		$response->shouldHaveNestedKey('error.file');
		$response->shouldHaveNestedKeyBe('error.file', $exception->getFile());

		$response->shouldHaveNestedKey('error.line');
		$response->shouldHaveNestedKeyBe('error.line', $exception->getLine());
	}

	function it_does_not_include_debug_info_in_the_plain_matcher()
	{
		$exception = new RuntimeException('Foo Bar!');
		$response = $this->formatPlain($exception);

		$response->shouldBeArray();
		$response->shouldHaveKey('error');
		$response->shouldNotHaveNestedKey('error.file');
		$response->shouldNotHaveNestedKey('error.line');
	}

	function getMatchers()
	{
		return array(
			'haveKey' => function($subject, $key) {
				return array_key_exists($key, $subject);
			},
			'haveNestedKey' => function($subject, $key) {
				// based on Illuminate\Support helpers
				foreach (explode('.', $key) as $segment)
				{
					if ( ! is_array($subject) || ! array_key_exists($segment, $subject))
					{
						return false;
					}

					$subject = $subject[$segment];
				}

				return true;
			},
			'haveNestedKeyBe' => function($subject, $key, $value) {
				foreach (explode('.', $key) as $segment)
				{
					if ( ! is_array($subject) || ! array_key_exists($segment, $subject))
					{
						return false;
					}

					$subject = $subject[$segment];
				}

				return $subject === $value;
			}
		);
	}

}
