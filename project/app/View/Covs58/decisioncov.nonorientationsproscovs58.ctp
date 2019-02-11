<?php
	echo '<table><thead><tr>';

	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nir' ) );
	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nom' ) );
	echo $this->Xhtml->tag( 'th', __d( 'adresse', 'Adresse.nomcom' ) );
	echo $this->Xhtml->tag( 'th', 'Date d\'orientation' );
	echo $this->Xhtml->tag( 'th', 'Orientation actuelle' );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.decisioncov' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.choixcov' ), array( 'colspan' => 3 ) );


	echo $this->Xhtml->tag( 'th', __d( 'decisionnonorientationprocov58', 'Decisionnonorientationprocov58.commentaire' ) );
	echo $this->Xhtml->tag( 'th', 'Actions' );




	echo '</tr></thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossiercov ) {
// debug($dossiercov);

	$hiddenFields = $this->Form->input( "Decisionnonorientationprocov58.{$i}.id", array( 'type' => 'hidden' ) ).
					$this->Form->input( "Decisionnonorientationprocov58.{$i}.etapecov", array( 'type' => 'hidden', 'value' => 'finalise' ) ).
					$this->Form->input( "Decisionnonorientationprocov58.{$i}.passagecov58_id", array( 'type' => 'hidden', 'value' => $dossiercov['Passagecov58'][0]['id'] ) );

		if( !empty( $dossiercov['Nonorientationprocov58']['Orientstruct']['Referent'] ) ){
			$referent = implode( ' ', Hash::filter( (array)array( $dossiercov['Nonorientationprocov58']['Orientstruct']['Referent']['qual'], $dossiercov['Nonorientationprocov58']['Orientstruct']['Referent']['nom'], $dossiercov['Nonorientationprocov58']['Orientstruct']['Referent']['prenom'] ) ) );
		}
		else{
			$referent = null;
		}


		echo $this->Form->input( "{$theme}.{$i}.id", array( 'type' => 'hidden', 'value' => $dossiercov[$theme]['id'] ) );
		echo $this->Xhtml->tableCells(
			array(
				$dossiercov['Personne']['nir'],
				implode( ' ', array( $dossiercov['Personne']['qual'], $dossiercov['Personne']['nom'], $dossiercov['Personne']['prenom'] ) ),
				implode( ' ', array( $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossiercov['Nonorientationprocov58']['Orientstruct']['date_valid'] ),
				implode( ' - ', Hash::filter( (array)array( $dossiercov['Nonorientationprocov58']['Orientstruct']['Typeorient']['lib_type_orient'], $dossiercov['Nonorientationprocov58']['Orientstruct']['Structurereferente']['lib_struc'], $referent ) ) ),
				array(
					$this->Form->input( "Decisionnonorientationprocov58.{$i}.decisioncov", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => @$options['Decisionnonorientationprocov58']['decisioncov'] ) ),
					array( 'id' => "Decisionnonorientationprocov58{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionnonorientationprocov58'][$i]['decisioncov'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionnonorientationprocov58.{$i}.typeorient_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $typesorients ) ),
					( !empty( $this->validationErrors['Decisionnonorientationprocov58'][$i]['typeorient_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionnonorientationprocov58.{$i}.structurereferente_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $structuresreferentes ) ),
					( !empty( $this->validationErrors['Decisionnonorientationprocov58'][$i]['structurereferente_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionnonorientationprocov58.{$i}.referent_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $referents ) ),
					( !empty( $this->validationErrors['Decisionnonorientationprocov58'][$i]['referent_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisionnonorientationprocov58.{$i}.commentaire", array( 'label' => false, 'type' => 'textarea' ) ).
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
			dependantSelect( 'Decisionnonorientationprocov58<?php echo $i?>StructurereferenteId', 'Decisionnonorientationprocov58<?php echo $i?>TypeorientId' );
			try { $( 'Decisionnonorientationprocov58<?php echo $i?>StructurereferenteId' ).onchange(); } catch(id) { }

			dependantSelect( 'Decisionnonorientationprocov58<?php echo $i?>ReferentId', 'Decisionnonorientationprocov58<?php echo $i?>StructurereferenteId' );
			try { $( 'Decisionnonorientationprocov58<?php echo $i?>ReferentId' ).onchange(); } catch(id) { }

			observeDisableFieldsOnValue(
				'Decisionnonorientationprocov58<?php echo $i;?>Decisioncov',
				[ 'Decisionnonorientationprocov58<?php echo $i;?>TypeorientId', 'Decisionnonorientationprocov58<?php echo $i;?>StructurereferenteId', 'Decisionnonorientationprocov58<?php echo $i;?>ReferentId' ],
				[ null, '', 'annule', 'reporte' ],
				true
			);

			Commission.preremplissageDecisionOrientation(
				'Decisionnonorientationprocov58',
				'<?php echo $i;?>',
				[
					{
					value: 'maintienref',
					typeorient_id: '<?php echo $dossiers[$theme]['liste'][$i]['Nonorientationprocov58']['Orientstruct']['typeorient_id'];?>',
					structurereferente_id: '<?php echo $dossiers[$theme]['liste'][$i]['Nonorientationprocov58']['Orientstruct']['structurereferente_id'];?>',
					referent_id: '<?php echo $dossiers[$theme]['liste'][$i]['Nonorientationprocov58']['Orientstruct']['referent_id'];?>'
					}
				]
			);
		<?php endfor;?>
	} );
</script>