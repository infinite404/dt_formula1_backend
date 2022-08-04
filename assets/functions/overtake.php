<?php
abstract class OvertakeTools {

protected $output;

	abstract public function overtake ($array, $heroId);

}
class Overtake extends OvertakeTools {

	public function overtake ($array, $heroId) {
		//reordering array if hero is not first or bigger number given than the number of drivers
		//find position of hero
		$heroPosition = $array[$heroId];

		//overtake 1 position
		if ($heroPosition != "1") {
			$enemyPosition = $heroPosition - 1;
		} else {
			$enemyPosition = $heroPosition;
		}

		//search who has this position
		$enemyKey = array_search ($enemyPosition, $array);
		$array[$enemyKey] = $heroPosition;
		$array[$heroId] = $enemyPosition;
		$output = $array;

		return $output;
	}
}
?>
