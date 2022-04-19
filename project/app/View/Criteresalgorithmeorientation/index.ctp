<?php
	echo $this->Default3->titleForLayout();

	echo $this->Default3->actions(
		['/Parametrages/index' => ['class' => 'back']]);

	echo $this->Default3->index(
		$results,
		$this->Translator->normalize(
			[
				'Criterealgorithmeorientation.ordre',
				'Criterealgorithmeorientation.libelle',
				'Typeorientparent.lib_type_orient',
				'Typeorientenfant.lib_type_orient',
				'Valeurtag.name',
				'Criterealgorithmeorientation.actif' => ['id' => 'actif'],
				'/Criteresalgorithmeorientation/monter/#Criterealgorithmeorientation.id#' => array(
					'title' => false,
					'disabled' => "( '#Criterealgorithmeorientation.id#' == {$premier_id} ) || ( '#Criterealgorithmeorientation.code#' == 'FINAL' )"
				),
				'/Criteresalgorithmeorientation/descendre/#Criterealgorithmeorientation.id#' => array(
					'title' => false,
					'disabled' => "( '#Criterealgorithmeorientation.id#' == {$dernier_id} ) || ( '#Criterealgorithmeorientation.code#' == 'FINAL' )"
				),
				'/Criteresalgorithmeorientation/edit/#Criterealgorithmeorientation.id#' => array(
					'title' => true
				),
			]
		),
		array(
			'options' => null,
			'paginate' => false,
		)
	);
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		document.querySelectorAll("#actif").forEach( (el) => {
				if(el.classList.contains('false')) {
					console.log(el);
					el.parentNode.classList.add('disabled');
					el.parentNode.querySelector('.monter').classList.add('disabled');
					el.parentNode.querySelector('.descendre').classList.add('disabled');
				}
			});
	});
</script>
