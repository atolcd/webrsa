<?php
	echo $this->Default3->titleForLayout();

	echo $this->Default3->actions( array( '/Motifsreorientseps93/add' ) );

	echo $this->Default3->index(
		$results,
		$this->Translator->normalize(
			array(
				'Motifreorientep93.name',
				'/Motifsreorientseps93/edit/#Motifreorientep93.id#' => array(
					'title' => true
				),
				'/Motifsreorientseps93/delete/#Motifreorientep93.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Motifreorientep93.has_linkedrecords#"'
				)
			)
		),
		array(
			'format' => $this->element( 'pagination_format', array( 'modelName' => 'Motifreorientep93' ) )
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'parametrages',
			'action'     => 'index',
			'#'     => 'eps'
		)
	);
?>