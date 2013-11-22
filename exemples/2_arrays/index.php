<?php
	require('../../simple_html_dom.php');
	require('../../complate.php');
	
	$complate	=	new Complate();
	$complate->setTemplate('template.html');
	
	//	Gestion de l'url
	$page	=	(!isset($_GET['page'])) ? 1 : $_GET['page'];
	$complate->setUrl($page);
	
	$data	=	array(
		'liste1'	=>	array(
			array('texte'	=>	'Premier élément'),
			array('texte'	=>	'Second élément'),
			array('texte'	=>	'Troisième élément')
		),
		'liste2'	=>	array(
			array('titre' => 'Premier élément', 'texte' => 'Premier texte', 'image' => 'http://lorempixel.com/400/150/nature/1'),
			array('titre' => 'Second élément', 'texte' => 'Second texte', 'image' => 'http://lorempixel.com/400/150/nature/2'),
			array('titre' => 'Troisième élément', 'texte' => 'Troisième texte', 'image' => 'http://lorempixel.com/400/150/nature/3'),
		),
		'menu'	=>	array(
			array('url' => 1, 'texte' => 'Premier élément'),
			array('url' => 2, 'texte' => 'Second élément'),
			array('url' => 3, 'texte' => 'Troisième élément'),
		)
	);
	
	$complate->setData($data);
	
	echo $complate->getHtml();