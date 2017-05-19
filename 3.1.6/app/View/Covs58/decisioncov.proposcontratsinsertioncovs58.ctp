<?php
	echo '<table><thead><tr>';
	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nir' ) );
	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nom' ) );
	echo $this->Xhtml->tag( 'th', __d( 'adresse', 'Adresse.nomcom' ) );
	echo $this->Xhtml->tag( 'th', __d( 'referent', 'Referent.nom_complet' ) );
	echo $this->Xhtml->tag( 'th', __d( 'propocontratinsertioncov58', 'Propocontratinsertioncov58.dd_ci' ) );
	echo $this->Xhtml->tag( 'th', __d( 'propocontratinsertioncov58', 'Propocontratinsertioncov58.duree_engag' ) );
	echo $this->Xhtml->tag( 'th', __d( 'propocontratinsertioncov58', 'Propocontratinsertioncov58.df_ci' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.decisioncov' ) );
	echo $this->Xhtml->tag( 'th', __d( Inflector::underscore(Inflector::classify($theme)), $theme.'.datevalidation' ) );
	echo $this->Xhtml->tag( 'th', __d( Inflector::underscore(Inflector::classify($theme)), $theme.'.commentaire' ) );
	echo $this->Xhtml->tag( 'th', 'Actions' );




	echo '</tr></thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossiercov ) {

	$hiddenFields = $this->Form->input( "Decisionpropocontratinsertioncov58.{$i}.id", array( 'type' => 'hidden' ) ).
					$this->Form->input( "Decisionpropocontratinsertioncov58.{$i}.etapecov", array( 'type' => 'hidden', 'value' => 'finalise' ) ).
					$this->Form->input( "Decisionpropocontratinsertioncov58.{$i}.passagecov58_id", array( 'type' => 'hidden', 'value' => $dossiercov['Passagecov58'][0]['id'] ) );

		echo $this->Form->input( "{$theme}.{$i}.id", array( 'type' => 'hidden', 'value' => $dossiercov[$theme]['id'] ) );
		echo $this->Xhtml->tableCells(
			array(
				$dossiercov['Personne']['nir'],
				implode( ' ', array( $dossiercov['Personne']['qual'], $dossiercov['Personne']['nom'], $dossiercov['Personne']['prenom'] ) ),
				implode( ' ', array( $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['codepos'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomcom'] ) ),
				implode( ' ', array( @$dossiercov['Propocontratinsertioncov58']['Referent']['qual'], @$dossiercov['Propocontratinsertioncov58']['Referent']['nom'], @$dossiercov['Propocontratinsertioncov58']['Referent']['prenom'] ) ),


				array(
					$this->Form->input( "Decisionpropocontratinsertioncov58.{$i}.dd_ci", array( 'type' => 'date', 'default' => $dossiercov['Propocontratinsertioncov58']['dd_ci'], 'dateFormat' => 'DMY', 'label' => false, 'maxYear' => date('Y') + 3, 'minYear' => 2009 ) ),
					array( 'class' => ( !empty( $this->validationErrors['Decisionpropocontratinsertioncov58'][$i]['dd_ci'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionpropocontratinsertioncov58.{$i}.duree_engag", array( 'type' => 'text', 'default' => $dossiercov['Propocontratinsertioncov58']['duree_engag'], 'label' => false ) ),
					array( 'class' => ( !empty( $this->validationErrors['Decisionpropocontratinsertioncov58'][$i]['duree_engag'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionpropocontratinsertioncov58.{$i}.df_ci", array( 'type' => 'date', 'default' => $dossiercov['Propocontratinsertioncov58']['df_ci'], 'dateFormat' => 'DMY', 'label' => false, 'maxYear' => date('Y') + 3, 'minYear' => 2009 ) ),
					array( 'class' => ( !empty( $this->validationErrors['Decisionpropocontratinsertioncov58'][$i]['df_ci'] ) ? 'error' : '' ) )
				),

				array(
					$this->Form->input( "Decisionpropocontratinsertioncov58.{$i}.decisioncov", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => @$options['Decisionpropocontratinsertioncov58']['decisioncov'] ) ),
					array( 'id' => "Decisionpropocontratinsertioncov58{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionpropocontratinsertioncov58'][$i]['decisioncov'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionpropocontratinsertioncov58.{$i}.datevalidation", array( 'type' => 'date', 'default' => $cov58['Cov58']['datecommission'], 'dateFormat' => 'DMY', 'label' => false, 'maxYear' => date('Y') + 2, 'minYear' => 2009 ) ),
					( !empty( $this->validationErrors['Decisionpropocontratinsertioncov58'][$i]['datevalidation'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisionpropocontratinsertioncov58.{$i}.commentaire", array( 'label' => false, 'type' => 'textarea' ) ).
				$hiddenFields,
				$this->Xhtml->viewLink( 'Voir', array( 'controller' => 'contratsinsertion', 'action' => 'index', $dossiercov['Personne']['id'] ), true, true ),
			)
		);
	}
	echo '</tbody></table>';
// debug($dossiercov);
?>


<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php for( $i = 0 ; $i < count( $dossiers[$theme]['liste'] ) ; $i++ ):?>
			observeDisableFieldsOnValue(
				'Decisionpropocontratinsertioncov58<?php echo $i;?>Decisioncov',
				[ 'Decisionpropocontratinsertioncov58<?php echo $i;?>DatevalidationDay', 'Decisionpropocontratinsertioncov58<?php echo $i;?>DatevalidationMonth', 'Decisionpropocontratinsertioncov58<?php echo $i;?>DatevalidationYear' ],
				'valide',
				false
			);
		<?php endfor;?>
	});
</script>


<script type="text/javascript">
	function checkDatesToRefresh( i ) {
		if( ( $F( 'Decisionpropocontratinsertioncov58' + i + 'DdCiMonth' ) ) && ( $F( 'Decisionpropocontratinsertioncov58' + i + 'DdCiYear' ) ) && ( $F( 'Decisionpropocontratinsertioncov58' + i + 'DureeEngag' ) ) ) {
			setDateIntervalCer( 'Decisionpropocontratinsertioncov58' + i + 'DdCi', 'Decisionpropocontratinsertioncov58' + i + 'DfCi', $F( 'Decisionpropocontratinsertioncov58' + i + 'DureeEngag' ), false );
		}
	}

	document.observe( "dom:loaded", function() {
		<?php for( $i = 0 ; $i < count( $dossiers[$theme]['liste'] ) ; $i++ ):?>
			new MaskedInput( '#Decisionpropocontratinsertioncov58<?php echo $i;?>DureeEngag', '9?9' );

			Event.observe( $( 'Decisionpropocontratinsertioncov58<?php echo $i;?>DdCiDay' ), 'change', function() {
				checkDatesToRefresh( <?php echo $i;?> );
			} );
			Event.observe( $( 'Decisionpropocontratinsertioncov58<?php echo $i;?>DdCiMonth' ), 'change', function() {
				checkDatesToRefresh( <?php echo $i;?> );
			} );
			Event.observe( $( 'Decisionpropocontratinsertioncov58<?php echo $i;?>DdCiYear' ), 'change', function() {
				checkDatesToRefresh( <?php echo $i;?> );
			} );

			Event.observe( $( 'Decisionpropocontratinsertioncov58<?php echo $i;?>DureeEngag' ), 'change', function() {
				checkDatesToRefresh( <?php echo $i;?> );
			} );
		<?php endfor;?>
	} );
</script>