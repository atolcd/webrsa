<?php
	$personne = Hash::extract( $dossierMenu, "Foyer.Personne.{n}[id={$dossierMenu['personne_id']}]" );
	$personne = array( 'Personne' => $personne[0] );

	echo $this->Default3->titleForLayout( $personne );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	$structurereferente_id = suffix( Hash::get( $this->request->data, 'Contratinsertion.structurereferente_id' ) );
	$referent_id = suffix( Hash::get( $this->request->data, 'Contratinsertion.referent_id' ) );
	if( !empty( $structurereferente_id ) && !empty( $referent_id ) ) {
		$this->request->data['Contratinsertion']['referent_id'] = "{$structurereferente_id}_{$referent_id}";
	}

	echo $this->Default3->form(
		array(
			'Contratinsertion.id',
			'Contratinsertion.personne_id' => array( 'type' => 'hidden', 'value' => $dossierMenu['personne_id'] ),
			'Contratinsertion.structurereferente_id' => array( 'empty' => true ),
			'Contratinsertion.referent_id' => array( 'empty' => true ),
			'Contratinsertion.nature_projet',
			'Contratinsertion.observ_ci',
			'Contratinsertion.observ_benef',
			'Contratinsertion.duree_engag' => array( 'empty' => true ),
			'Contratinsertion.dd_ci' => array( 'empty' => true, 'dateFormat' => 'DMY' ),
			'Contratinsertion.df_ci' => array( 'empty' => true, 'dateFormat' => 'DMY' ),
			'Contratinsertion.lieu_saisi_ci',
			'Contratinsertion.date_saisi_ci' => array( 'empty' => true, 'dateFormat' => 'DMY' ),
		),
		array(
			'options' => array(
				'Contratinsertion' => array(
					'structurereferente_id' => $structures,
					'referent_id' => $referents,
					'duree_engag' => $duree_engag,
					'decision_ci' => $decision_ci
				)
			)
		)
	);

	echo $this->Observer->dependantSelect(
		array(
			'Contratinsertion.structurereferente_id' => 'Contratinsertion.referent_id'
		)
	);

	echo $this->Observer->disableFormOnSubmit( $this->Html->domId( "Contratinsertion.{$this->action}.form" ) );
?>
<script type="text/javascript">
	function checkDatesToRefresh() {
		if( ( $F( 'ContratinsertionDdCiMonth' ) ) && ( $F( 'ContratinsertionDdCiYear' ) ) && ( $F( 'ContratinsertionDureeEngag' ) ) ) {
			setDateIntervalCer( 'ContratinsertionDdCi', 'ContratinsertionDfCi', $F( 'ContratinsertionDureeEngag' ), false );
		}
	}

	document.observe( "dom:loaded", function() {
		Event.observe( $( 'ContratinsertionDdCiDay' ), 'change', function() {
			checkDatesToRefresh();
		} );
		Event.observe( $( 'ContratinsertionDdCiMonth' ), 'change', function() {
			checkDatesToRefresh();
		} );
		Event.observe( $( 'ContratinsertionDdCiYear' ), 'change', function() {
			checkDatesToRefresh();
		} );

		Event.observe( $( 'ContratinsertionDureeEngag' ), 'change', function() {
			checkDatesToRefresh();
		} );
	});
</script>