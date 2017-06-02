<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->titleForLayout();

	echo $this->Default3->index(
		$results,
		array(
			'Cov58.datecommission',
			'Dossiercov58.themecov58',
			'Passagecov58.etatdossiercov',
			'Sitecov58.name',
			'Dossiercov58.created',
			'/Historiquescovs58/view/#Passagecov58.id#' => array(
				'disabled' => 'empty( "#Passagecov58.id#" )'
			),
			'/Covs58/view/#Cov58.id#' => array(
				'disabled' => 'empty( "#Cov58.id#" )'
			),
			'/Covs58/visualisationdecisions/#Cov58.id#' => array(
				'class' => 'view',
				'disabled' => 'empty( "#Cov58.id#" ) || "#Cov58.etatcov#" !== "finalise"'
			)

		),
		array(
			'options' => $options,
			'paginate' => false
		)
	);
?>