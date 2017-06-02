<?php
	echo $this->Default3->titleForLayout();

	echo $this->Default3->actions( array( '/Parametrages/index/#eps' => array( 'class' => 'back' ) ) );

	foreach( $erreurs as $erreur => $status ) {
		if( true === $status ) {
			echo $this->Html->tag( 'p', $erreur, array( 'class' => 'error' ) );
		}
	}

	if( false === array_search( true, $erreurs, true ) ) {
		echo $this->Default3->index(
			$results,
			$this->Translator->normalize(
				array(
					'Regroupementep.name',
					'/Compositionsregroupementseps/edit/#Regroupementep.id#' => array(
						'title' => true
					)
				)
			),
			array(
				'format' => $this->element( 'pagination_format', array( 'modelName' => 'Regroupementep' ) )
			)
		);
	}

	echo $this->Default3->actions( array( '/Parametrages/index/#eps' => array( 'class' => 'back' ) ) );
?>