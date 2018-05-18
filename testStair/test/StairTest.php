<?php 

use PHPUnit\Framework\TestCase;

//use app\validate_stairs;
require_once ("app/validate_stairs.php");

class StairTest extends TestCase {

	public function testStair() {

		$validate_stairs = new Cards();

		$results1 = $validate_stairs->validateIsStair([9,10,11,12,13]);
		$this->assertEquals($results1, true, "Is Stair: 9, 10, 11, 12, 13");
		$results2 = $validate_stairs->validateIsStair([14,2,3,4,5]);
		$this->assertEquals($results2, true, "Is Stair: 14, 2, 3, 4, 5");
		$results3 = $validate_stairs->validateIsStair([7,7,12,11,3,4,14]);
		$this->assertEquals($results3, false, "Is invalid Hand: 7, 7, 12, 11, 3, 4, 14");
		$results4 = $validate_stairs->validateIsStair([7,8,12,13,14]);
		$this->assertEquals($results4, false, "Is invalid Hand: 7, 8, 12, 13, 14");

	}
}
