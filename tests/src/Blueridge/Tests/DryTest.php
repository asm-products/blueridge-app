<?php
/**
 * Blueridge
 *
 * @copyright Ninelabs 2013
 * @author Moses Ngone <moses@ninelabs.com>
 * @since v1.1.0
 */

namespace Blueridge\Tests;

use PHPUnit_Framework_TestCase;

/**
 * Dry run test
 */
class DryTest extends PHPUnit_Framework_TestCase
{
	public function testTrueIsTrue()
	{
		$foo = true;
		$this->assertTrue($foo);
	}
}