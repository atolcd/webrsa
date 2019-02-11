<?php
	if( empty( $this->request->data ) ) {
		echo $this->Xhtml->tag( 'h1', $this->pageTitle = __d( 'nonrespectsanctionep93', 'Nonrespectssanctionseps93::index' ) );
	}

	echo $this->Default2->search(
		array(
			'Nonrespectsanctionep93.mode' => array(
				'type' => 'select',
				'options' => array(
					'encours' => 'En cours de traitement',
					'traite' => 'Finalisés'
				)
			),
			'Dossierep.commissionep_id' => array( 'empty' => true, 'domain' => 'nonrespectsanctionep93' )
		),
		array(
			'options' => $options
		)
	);
?>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsOnValue( 'SearchNonrespectsanctionep93Mode', [ 'SearchDossierepSeanceepId' ], 'traite', false );
	} );
</script>