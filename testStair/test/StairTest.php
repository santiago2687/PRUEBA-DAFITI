<?php 

use PHPUnit\Framework\TestCase;

require_once ("app/validate_stairs.php");

class StairTest extends TestCase {

	public function testStair() {

		$validate_stairs = new Cards();

		$results1 = $validate_stairs->validateIsStair([9,10,11,12]);
		$this->assertEquals($results1, false, "you can not enter less than 7 cards");

		$results1 = $validate_stairs->validateIsStair([2,9,10,11,12,11,3,6]);
		$this->assertEquals($results1, false, "you can not enter more than 7 cards ");

		$results1 = $validate_stairs->validateIsStair([1,9,10,11,12]);
		$this->assertEquals($results1, false, "can not enter cards under than 2");

		$results1 = $validate_stairs->validateIsStair([9,10,11,12,13,15]);
		$this->assertEquals($results1, false, "can not enter cards greater than 14 ");

		$results2 = $validate_stairs->validateIsStair([14,2,3,4,5]);
		$this->assertEquals($results2, true, "Is Stair: 14, 2, 3, 4, 5");

		$results3 = $validate_stairs->validateIsStair([7,7,12,11,3,4,14]);
		$this->assertEquals($results3, false, "Is invalid Hand: 7, 7, 12, 11, 3, 4, 14");

		$results4 = $validate_stairs->validateIsStair([7,8,12,13,14]);
		$this->assertEquals($results4, false, "Is invalid Hand: 7, 8, 12, 13, 14");

	}
}
