<?php
	function previsions($ville) {
		$previsions	=	array();
		$url		=	'http://api.openweathermap.org/data/2.5/forecast/daily?q='.urlencode($ville).'&mode=xml&units=metric&cnt=7';
		$xml		=	simplexml_load_file($url);
		foreach($xml->forecast->time as $time) {
			$previsions[]	=	array(
				'date'	=>	date('d/m/Y', strtotime((string)$time['day'])),
				'img'	=>	'img/'.(string)$time->symbol['var'].'.png',
				'min'	=>	round($time->temperature['min']),
				'max'	=>	round($time->temperature['max'])
			);
		}
		return $previsions;
	}