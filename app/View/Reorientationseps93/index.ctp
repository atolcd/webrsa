<?php
	if( empty( $this->pageTitle ) || $this->pageTitle == 'Reorientationseps93::index' ) {
		$this->pageTitle = 'Demandes de réorientation 93';
		echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	}

	echo $this->Default2->search(
		array(
			'Reorientationep93.mode' => array(
				'type' => 'select',
				'options' => array(
					'encours' => 'En cours de traitement',
					'traite' => 'Finalisés'
				)
			),
			'Dossierep.commissionep_id' => array( 'empty' => true, 'domain' => 'reorientationep93' )
		),
		array(
			'options' => $options
		)
	);
?>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsOnValue( 'SearchSaisineepreorientsr93Mode', [ 'SearchDossierepSeanceepId' ], 'traite', false );
	} );
</script>