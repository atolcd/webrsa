<?php
	echo $this->Default3->titleForLayout();

	echo $this->Default3->actions( array( '/Courrierspdos/add' ) );

	echo $this->Default3->index(
		$results,
		$this->Translator->normalize(
			array(
				'Courrierpdo.name',
				'Courrierpdo.modeleodt',
				'/Courrierspdos/edit/#Courrierpdo.id#' => array(
					'title' => true
				),
				'/Courrierspdos/delete/#Courrierpdo.id#' => array(
					'title' => true,
					'confirm' => true,
					'disabled' => 'true == "#Courrierpdo.has_linkedrecords#"'
				)
			)
		),
		array(
			'format' => $this->element( 'pagination_format', array( 'modelName' => 'Courrierpdo' ) )
		)
	);

	echo $this->Default3->actions( array( '/Parametrages/index/#pdos' => array( 'class' => 'back' ) ) );
?>