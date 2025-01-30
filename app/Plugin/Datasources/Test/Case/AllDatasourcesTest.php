<?php
/**
 * All Datasources plugin tests
 *
 */
namespace Datasources\Test\Case;

class AllDatasourcesTest extends TestCase {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Datasources test');

		$path = Plugin::path('Datasources') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}
}
