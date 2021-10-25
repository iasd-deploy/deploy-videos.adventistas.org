<?php

 // @codeCoverageIgnoreStart

class IASD_ResultPrinter extends PHPUnit_TextUI_ResultPrinter {
	public function endTest(PHPUnit_Framework_Test $test, $time) {
		$timestr = str_pad(substr($time, 0, strpos($time, '.')), 3, ' ', STR_PAD_LEFT);
		echo '   [', $timestr, 's] - ', (($this->lastTestFailed) ? ' F ' : 'OK '), $test->getName(), "\r\n";
//    if (! $this->lastTestFailed) {
	//    $this->writeProgress('.');
		//}
		
		$this->lastEvent = self::EVENT_TEST_END;
		$this->lastTestFailed = FALSE;
	}
	public function startTest(PHPUnit_Framework_Test $test) {
		//echo '-> ' . $test->getName(), "\r\n";
		parent::startTest($test);
	}
	public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {
		echo 'Suite: ' . $suite->getName(), "\r\n";
		parent::startTestSuite($suite);
	}
	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
		//$this->writeProgress('F');
		$this->lastTestFailed = TRUE;
	}
}

// @codeCoverageIgnoreEnd