$(document).ready(function() {
	$('#previsions_villes').change(function() {
		var ville	=	$(this).val();
		$.get(
			'index.php',
			{ville:	ville},
			function(data) {
				$('#previsions').replaceWith(data);
			}
		);
	});
});