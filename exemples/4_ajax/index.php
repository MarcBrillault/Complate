<?php
	require('../../simple_html_dom.php');
	require('../../complate.php');
	
	include('fonctions.php');
	
	$complate	=	new Complate();
	$complate->setTemplate('template.html');
	
	//	On définit la liste des villes interrogeables
	$liste_villes		=	array(
		array('ville' => 'Paris'),
		array('ville' => 'Newcastle'),
		array('ville' => 'New York'),
		array('ville' => 'Madrid'),
		array('ville' => 'Berlin')
	);
	
	//	$zone est le paramètre qui va déterminer si on récupère tout le template ou seulement la partie nécessaire au rechargement AJAX
	if(isset($_GET['ville'])) {
		$ville	=	$_GET['ville'];
		$zone	=	true;
	}
	else {
		$ville	=	$liste_villes[0]['ville'];
		$zone	=	false;
	}
	
	$data	=	array(
		'liste_villes'	=>	$liste_villes,
		'ville'			=>	$ville,
		'previsions'	=>	previsions($ville)
	);
	
	$complate->setData($data);
	
	if($zone)
			$complate->useZone('previsions');
	
	echo $complate->getHtml();