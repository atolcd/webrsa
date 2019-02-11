<?php
// debug($this->request->data);
	echo '<table><thead><tr>';

	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nir' ) );
	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nom' ) );
	echo $this->Xhtml->tag( 'th', __d( 'adresse', 'Adresse.nomcom' ) );
	echo $this->Xhtml->tag( 'th', __d( Inflector::underscore(Inflector::classify($theme)), $theme.'.datedemande' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Propoorientationcov58.referentorientant_id' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.proporeferent' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.decisioncov' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.choixcov' ), array( 'colspan' => 3 ) );


	echo $this->Xhtml->tag( 'th', __d( Inflector::underscore(Inflector::classify($theme)), $theme.'.commentaire' ) );
	echo $this->Xhtml->tag( 'th', 'Actions' );




	echo '</tr></thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossiercov ) {

// debug($dossiers[$theme]['liste']);

	$hiddenFields = $this->Form->input( "Decisionpropoorientationcov58.{$i}.id", array( 'type' => 'hidden' ) ).
					$this->Form->input( "Decisionpropoorientationcov58.{$i}.etapecov", array( 'type' => 'hidden', 'value' => 'finalise' ) ).
					$this->Form->input( "Decisionpropoorientationcov58.{$i}.passagecov58_id", array( 'type' => 'hidden', 'value' => $dossiercov['Passagecov58'][0]['id'] ) );

		echo $this->Form->input( "{$theme}.{$i}.id", array( 'type' => 'hidden', 'value' => $dossiercov[$theme]['id'] ) );
		echo $this->Xhtml->tableCells(
			array(
				$dossiercov['Personne']['nir'],
				implode( ' ', array( $dossiercov['Personne']['qual'], $dossiercov['Personne']['nom'], $dossiercov['Personne']['prenom'] ) ),
				implode( ' ', array( $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossiercov[$theme]['datedemande'] ),
				Set::enum( Set::classicExtract( $dossiercov, 'Propoorientationcov58.referentorientant_id' ), $referentsorientants ),
				implode( ' - ', Hash::filter( (array)array( $dossiercov['Propoorientationcov58']['Typeorient']['lib_type_orient'], $dossiercov['Propoorientationcov58']['Structurereferente']['lib_struc'], implode( ' ', Hash::filter( (array)array( @$dossiercov['Propoorientationcov58']['Referent']['qual'], @$dossiercov['Propoorientationcov58']['Referent']['nom'], @$dossiercov['Propoorientationcov58']['Referent']['prenom'] ) ) ) ) ) ),

				array(
					$this->Form->input( "Decisionpropoorientationcov58.{$i}.decisioncov", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => @$options['Decisionpropoorientationcov58']['decisioncov'] ) ),
					array( 'id' => "Decisionpropoorientationcov58{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionpropoorientationcov58'][$i]['decisioncov'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionpropoorientationcov58.{$i}.typeorient_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $typesorients ) ),
					( !empty( $this->validationErrors['Decisionpropoorientationcov58'][$i]['typeorient_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionpropoorientationcov58.{$i}.structurereferente_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $structuresreferentes ) ),
					( !empty( $this->validationErrors['Decisionpropoorientationcov58'][$i]['structurereferente_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionpropoorientationcov58.{$i}.referent_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $referents ) ),
					( !empty( $this->validationErrors['Decisionpropoorientationcov58'][$i]['referent_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisionpropoorientationcov58.{$i}.commentaire", array( 'label' => false, 'type' => 'textarea' ) ).
				$hiddenFields,
				$this->Xhtml->viewLink( 'Voir', array( 'controller' => 'orientsstructs', 'action' => 'index', $dossiercov['Personne']['id'] ), true, true ),
			)
		);
	}
	echo '</tbody></table>';
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php for( $i = 0 ; $i < count( $dossiers[$theme]['liste'] ) ; $i++ ):?>
			dependantSelect( 'Decisionpropoorientationcov58<?php echo $i?>StructurereferenteId', 'Decisionpropoorientationcov58<?php echo $i?>TypeorientId' );
			try { $( 'Decisionpropoorientationcov58<?php echo $i?>StructurereferenteId' ).onchange(); } catch(id) { }

			dependantSelect( 'Decisionpropoorientationcov58<?php echo $i?>ReferentId', 'Decisionpropoorientationcov58<?php echo $i?>StructurereferenteId' );
			try { $( 'Decisionpropoorientationcov58<?php echo $i?>ReferentId' ).onchange(); } catch(id) { }

			observeDisableFieldsOnValue(
				'Decisionpropoorientationcov58<?php echo $i;?>Decisioncov',
				[ 'Decisionpropoorientationcov58<?php echo $i;?>TypeorientId', 'Decisionpropoorientationcov58<?php echo $i;?>StructurereferenteId', 'Decisionpropoorientationcov58<?php echo $i;?>ReferentId' ],
				'refuse',
				false
			);
		<?php endfor;?>
	});
</script>