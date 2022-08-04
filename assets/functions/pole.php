<?php
abstract class PoleHandlingTools {

protected $output;

	abstract public function getRandomPoles ();

}
class PoleHandling extends PoleHandlingTools {

	public function getRandomPoles () {
		$output = range(1, 21);
		shuffle($output);
		return $output;
	}
}
?>

