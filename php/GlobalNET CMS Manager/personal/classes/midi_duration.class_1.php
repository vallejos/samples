<?php
//require('midi.class.php');

class MidiDuration extends Midi{
	
	//---------------------------------------------------------------
	// returns duration in format mm:ss
	//---------------------------------------------------------------
	function getDuration(){
		$maxTime=0;
		foreach ($this->tracks as $track){
			$msgStr = $track[count($track)-1];
			list($time) = explode(" ", $msgStr);
			$maxTime = max($maxTime, $time);
		}		
		$seconds = $maxTime * $this->getTempo() / $this->getTimebase() / 1000000;
                $mins = floor ($seconds / 60);
                $secs = $seconds % 60;
                return $mins . ":" . $secs;
	}
}


// TEST:
// $midi = new MidiDuration();
// $midi->importMid($file);
// echo 'Duration [sec]: '.$midi->getDuration(); // 69.14 sec for bossa.mid
	
?>