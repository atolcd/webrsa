<?php
	echo '<table><thead><tr>';

	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nir' ) );
	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nom' ) );
	echo $this->Xhtml->tag( 'th', __d( 'adresse', 'Adresse.nomcom' ) );
	echo $this->Xhtml->tag( 'th', 'Date d\'orientation' );
	echo $this->Xhtml->tag( 'th', 'Orientation actuelle', array( 'colspan' => 2 ) );
	echo $this->Xhtml->tag( 'th', 'Proposition référent', array( 'colspan' => 2 ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.decisioncov' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.choixcov' ), array( 'colspan' => 3 ) );


	echo $this->Xhtml->tag( 'th', __d( 'decisionregressionorientationcov58', 'Decisionregressionorientationcov58.commentaire' ) );
	echo $this->Xhtml->tag( 'th', 'Actions' );




	echo '</tr></thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossiercov ) {
		$hiddenFields = $this->Form->input( "Decisionregressionorientationcov58.{$i}.id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionregressionorientationcov58.{$i}.etapecov", array( 'type' => 'hidden', 'value' => 'finalise' ) ).
						$this->Form->input( "Decisionregressionorientationcov58.{$i}.passagecov58_id", array( 'type' => 'hidden', 'value' => $dossiercov['Passagecov58'][0]['id'] ) );

		if( !empty( $dossiercov['Regressionorientationcov58']['Orientstruct']['Referent'] ) ){
			$referentactuel = implode( ' ', Hash::filter( (array)array( $dossiercov['Regressionorientationcov58']['Orientstruct']['Referent']['qual'], $dossiercov['Regressionorientationcov58']['Orientstruct']['Referent']['nom'], $dossiercov['Regressionorientationcov58']['Orientstruct']['Referent']['prenom'] ) ) );
		}
		else{
			$referentactuel = null;
		}

		if( !empty( $dossiercov['Regressionorientationcov58']['Referent'] ) ){
			$referentpropose = implode( ' ', Hash::filter( (array)array( $dossiercov['Regressionorientationcov58']['Referent']['qual'], $dossiercov['Regressionorientationcov58']['Referent']['nom'], $dossiercov['Regressionorientationcov58']['Referent']['prenom'] ) ) );
		}
		else{
			$referentpropose = null;
		}

		echo $this->Form->input( "{$theme}.{$i}.id", array( 'type' => 'hidden', 'value' => $dossiercov[$theme]['id'] ) );
		echo $this->Xhtml->tableCells(
			array(
				$dossiercov['Personne']['nir'],
				implode( ' ', array( $dossiercov['Personne']['qual'], $dossiercov['Personne']['nom'], $dossiercov['Personne']['prenom'] ) ),
				implode( ' ', array( $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossiercov['Regressionorientationcov58']['Orientstruct']['date_valid'] ),
				$dossiercov['Regressionorientationcov58']['Orientstruct']['Typeorient']['lib_type_orient'],
				implode( ' - ', Hash::filter( (array)array( $dossiercov['Regressionorientationcov58']['Orientstruct']['Structurereferente']['lib_struc'], $referentactuel ) ) ),
				$dossiercov['Regressionorientationcov58']['Typeorient']['lib_type_orient'],
				implode( ' - ', Hash::filter( (array)array( $dossiercov['Regressionorientationcov58']['Structurereferente']['lib_struc'], $referentpropose ) ) ),
				array(
					$this->Form->input( "Decisionregressionorientationcov58.{$i}.decisioncov", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => @$options['Decisionregressionorientationcov58']['decisioncov'] ) ),
					array( 'id' => "Decisionregressionorientationcov58{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionregressionorientationcov58'][$i]['decisioncov'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionregressionorientationcov58.{$i}.typeorient_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $typesorients ) ),
					( !empty( $this->validationErrors['Decisionregressionorientationcov58'][$i]['typeorient_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionregressionorientationcov58.{$i}.structurereferente_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $structuresreferentes ) ),
					( !empty( $this->validationErrors['Decisionregressionorientationcov58'][$i]['structurereferente_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionregressionorientationcov58.{$i}.referent_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $referents ) ),
					( !empty( $this->validationErrors['Decisionregressionorientationcov58'][$i]['referent_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisionregressionorientationcov58.{$i}.commentaire", array( 'label' => false, 'type' => 'textarea' ) ).
				$hiddenFields,
				$this->Xhtml->viewLink( 'Voir', array( 'controller' => 'orientsstructs', 'action' => 'index', $dossiercov['Personne']['id'] ), true, true ),
			)
		);
	}
	echo '</tbody></table>';
?>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		<?php for( $i = 0 ; $i < count( $dossiers[$theme]['liste'] ) ; $i++ ):?>
			dependantSelect( 'Decisionregressionorientationcov58<?php echo $i?>StructurereferenteId', 'Decisionregressionorientationcov58<?php echo $i?>TypeorientId' );
			try { $( 'Decisionregressionorientationcov58<?php echo $i?>StructurereferenteId' ).onchange(); } catch(id) { }

			dependantSelect( 'Decisionregressionorientationcov58<?php echo $i?>ReferentId', 'Decisionregressionorientationcov58<?php echo $i?>StructurereferenteId' );
			try { $( 'Decisionregressionorientationcov58<?php echo $i?>ReferentId' ).onchange(); } catch(id) { }

			observeDisableFieldsOnValue(
				'Decisionregressionorientationcov58<?php echo $i;?>Decisioncov',
				[ 'Decisionregressionorientationcov58<?php echo $i;?>TypeorientId', 'Decisionregressionorientationcov58<?php echo $i;?>StructurereferenteId', 'Decisionregressionorientationcov58<?php echo $i;?>ReferentId' ],
				[ null, '', 'annule', 'reporte' ],
				true
			);

			Commission.preremplissageDecisionOrientation(
				'Decisionregressionorientationcov58',
				'<?php echo $i;?>',
				[
					{
						value: 'accepte',
						typeorient_id: '<?php echo $dossiers[$theme]['liste'][$i]['Regressionorientationcov58']['typeorient_id'];?>',
						structurereferente_id: '<?php echo $dossiers[$theme]['liste'][$i]['Regressionorientationcov58']['structurereferente_id'];?>',
						referent_id: '<?php echo $dossiers[$theme]['liste'][$i]['Regressionorientationcov58']['referent_id'];?>'
					},
					{
						value: 'refuse',
						typeorient_id: '<?php echo $dossiers[$theme]['liste'][$i]['Regressionorientationcov58']['Orientstruct']['typeorient_id'];?>',
						structurereferente_id: '<?php echo $dossiers[$theme]['liste'][$i]['Regressionorientationcov58']['Orientstruct']['structurereferente_id'];?>',
						referent_id: '<?php echo $dossiers[$theme]['liste'][$i]['Regressionorientationcov58']['Orientstruct']['referent_id'];?>'
					},
				]
			);
		<?php endfor;?>
	} );
</script>