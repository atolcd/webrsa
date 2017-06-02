<?php
	echo $this->element(
		'WebrsaParametrages/index',
		array(
			'cells' => array(
				'Metierexerce.name',
				'/Metiersexerces/edit/#Metierexerce.id#' => array(
					'title' => true
				),
				'/Metiersexerces/delete/#Metierexerce.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Metierexerce.has_linkedrecords#"'
				)
			),
			'backUrl' => '/Parametrages/index/#contratsinsertion'
		)
	);
?>