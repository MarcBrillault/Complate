<?php
	require('../../simple_html_dom.php');
	require('../../complate.php');
	
	$complate	=	new Complate();
	$complate->setTemplate('template.html');
	
	$data	=	array(
			'commentaires'	=>	array(
				array('image' => 'https://s3.amazonaws.com/uifaces/faces/twitter/chadengle/73.jpg', 'nom' => 'Chadengle', 'texte' => 'Nulla eu risus tortor. Nullam vel pellentesque lacus. Ut eu lobortis lacus, nec gravida lacus. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vestibulum ac orci cursus, laoreet.'),
				array('image' => 'https://s3.amazonaws.com/uifaces/faces/twitter/divya/73.jpg', 'nom' => 'Divya', 'texte' => 'Nunc blandit volutpat pulvinar. Etiam id risus congue, condimentum ipsum at, placerat elit. Ut aliquet orci ligula. Nulla eget tincidunt turpis. Aliquam erat volutpat. Pellentesque vestibulum turpis in commodo auctor.'),
				array('image' => 'https://s3.amazonaws.com/uifaces/faces/twitter/joelhelin/73.jpg', 'nom' => 'Joël Helin', 'texte' => 'Suspendisse potenti. Nunc imperdiet non dui eu sodales. Nulla posuere velit at ornare scelerisque. Cras vulputate erat eget interdum scelerisque. In molestie, leo sed porta suscipit, massa orci interdum quam.'),
				array('image' => 'https://s3.amazonaws.com/uifaces/faces/twitter/raquelwilson/73.jpg', 'nom' => 'Raquel Wilson', 'texte' => 'Donec eleifend leo sit amet blandit facilisis. Integer sapien nibh, tempus at nisl vel, eleifend mattis purus. Fusce et fringilla neque. Vivamus bibendum rutrum gravida. Phasellus sit amet dapibus orci.'),
				array('image' => 'https://s3.amazonaws.com/uifaces/faces/twitter/kurafire/73.jpg', 'nom' => 'Kurafire', 'texte' => 'Maecenas blandit lacus at suscipit ornare. In vitae metus vestibulum, vehicula risus vel, venenatis turpis. Pellentesque blandit, justo ac gravida malesuada, libero erat convallis nulla, in viverra lacus felis et.'),
				array('image' => 'https://s3.amazonaws.com/uifaces/faces/twitter/findingjenny/73.jpg', 'nom' => 'Finding Jenny', 'texte' => 'Nullam urna ligula, hendrerit a tincidunt eu, suscipit at mi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi facilisis purus eu eleifend sollicitudin. Ut rutrum tempor nisi at aliquet.')
			)
	);
	
	$complate->setData($data);
	
	if(isset($_GET['nocomment']))
			$complate->setData('commentaires', false);
	
	echo $complate->getHtml();