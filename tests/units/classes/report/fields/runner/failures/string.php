<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\failures;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\runner\failures
{
	public function testClassConstants()
	{
		$this->assert
			->string(runner\failures\string::titlePrompt)->isEqualTo('> ')
			->string(runner\failures\string::methodPrompt)->isEqualTo('=> ')
		;
	}

	public function test__construct()
	{
		$failures = new runner\failures\string();

		$this->assert
			->object($failures)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($failures->getRunner())->isNull()
			->object($failures->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithRunner()
	{
		$failures = new runner\failures\string();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();

		$this->assert
			->object($failures->setWithRunner($runner))->isIdenticalTo($failures)
			->object($failures->getRunner())->isIdenticalTo($runner)
			->object($failures->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($failures)
			->object($failures->getRunner())->isIdenticalTo($runner)
			->object($failures->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($failures)
			->object($failures->getRunner())->isIdenticalTo($runner)
		;
	}

	public function test__toString()
	{
		$failures = new runner\failures\string($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getErrors = function() { return array(); };

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = function() use ($score) { return $score; };

		$this->assert
			->castToString($failures)->isEmpty()
			->castToString($failures->setWithRunner($runner))->isEmpty()
			->castToString($failures->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($failures->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$fails = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => $line = uniqid(),
				'asserter' => $asserter = uniqid(),
				'fail' => $fail = uniqid()
			),
			array(
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'file' => $otherFile = uniqid(),
				'line' => $otherLine = uniqid(),
				'asserter' => $otherAsserter = uniqid(),
				'fail' => $otherFail = uniqid()
			)
		);

		$score->getMockController()->getFailAssertions = function() use ($fails) { return $fails; };

		$failures = new runner\failures\string($locale = new atoum\locale());

		$this->assert
			->castToString($failures)->isEmpty()
			->castToString($failures->setWithRunner($runner))->isEqualTo(runner\failures\string::titlePrompt . sprintf($locale->__('There is %d failure:', 'There are %d failures:', sizeof($fails)), sizeof($fails)) . PHP_EOL .
				runner\failures\string::methodPrompt . $class . '::' . $method . '():' . PHP_EOL .
				sprintf($locale->_('In file %s on line %d, %s failed : %s'), $file, $line, $asserter, $fail) . PHP_EOL .
				runner\failures\string::methodPrompt . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				sprintf($locale->_('In file %s on line %d, %s failed : %s'), $otherFile, $otherLine, $otherAsserter, $otherFail) . PHP_EOL
			)
		;
	}
}

?>
