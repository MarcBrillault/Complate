<?php
	require('../../simple_html_dom.php');
	require('../../complate.php');
	
	$complate	=	new Complate();
	$complate->setTemplate('template.html');
	
	$data	=	array(
		'titre'		=>	'Remplacements de chaînes',
		'exemple1'	=>	'Le contenu affiché ici remplace un contenu temporaire placé entre des balises de commentaires HTML.',
		'src1'		=>	'http://lorempixel.com/650/200/nature/1/',
		'src2'		=>	'http://lorempixel.com/650/200/nature/2/',
		'src3'		=>	'http://lorempixel.com/650/200/nature/3/',
		'titre3'	=>	'Titre de la troisième image'
	);
	
	$complate->setData($data);
	
	echo $complate->getHtml();