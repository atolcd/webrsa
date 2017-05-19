<?php
// debug($this->request->data);
	echo '<table><thead><tr>';

	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nir' ) );
	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nom' ) );
	echo $this->Xhtml->tag( 'th', __d( 'adresse', 'Adresse.nomcom' ) );
	echo $this->Xhtml->tag( 'th', __d( Inflector::underscore(Inflector::classify($theme)), $theme.'.created' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.decisioncov' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.choixcov' ), array( 'colspan' => 3 ) );


	echo $this->Xhtml->tag( 'th', __d( Inflector::underscore(Inflector::classify($theme)), $theme.'.commentaire' ) );
	echo $this->Xhtml->tag( 'th', 'Actions' );




	echo '</tr></thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossiercov ) {

// debug($dossiers[$theme]['liste']);

	$hiddenFields = $this->Form->input( "Decisionpropoorientsocialecov58.{$i}.id", array( 'type' => 'hidden' ) ).
					$this->Form->input( "Decisionpropoorientsocialecov58.{$i}.etapecov", array( 'type' => 'hidden', 'value' => 'finalise' ) ).
					$this->Form->input( "Decisionpropoorientsocialecov58.{$i}.passagecov58_id", array( 'type' => 'hidden', 'value' => $dossiercov['Passagecov58'][0]['id'] ) );

		echo $this->Form->input( "{$theme}.{$i}.id", array( 'type' => 'hidden', 'value' => $dossiercov[$theme]['id'] ) );
		echo $this->Xhtml->tableCells(
			array(
				$dossiercov['Personne']['nir'],
				implode( ' ', array( $dossiercov['Personne']['qual'], $dossiercov['Personne']['nom'], $dossiercov['Personne']['prenom'] ) ),
				implode( ' ', array( $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossiercov[$theme]['created'] ),
				array(
					$this->Form->input( "Decisionpropoorientsocialecov58.{$i}.decisioncov", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => @$options['Decisionpropoorientsocialecov58']['decisioncov'] ) ),
					array( 'id' => "Decisionpropoorientsocialecov58{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionpropoorientsocialecov58'][$i]['decisioncov'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionpropoorientsocialecov58.{$i}.typeorient_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $typesorients ) ),
					( !empty( $this->validationErrors['Decisionpropoorientsocialecov58'][$i]['typeorient_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionpropoorientsocialecov58.{$i}.structurereferente_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $structuresreferentes ) ),
					( !empty( $this->validationErrors['Decisionpropoorientsocialecov58'][$i]['structurereferente_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionpropoorientsocialecov58.{$i}.referent_id", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $referents ) ),
					( !empty( $this->validationErrors['Decisionpropoorientsocialecov58'][$i]['referent_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisionpropoorientsocialecov58.{$i}.commentaire", array( 'label' => false, 'type' => 'textarea' ) ).
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
			dependantSelect( 'Decisionpropoorientsocialecov58<?php echo $i?>StructurereferenteId', 'Decisionpropoorientsocialecov58<?php echo $i?>TypeorientId' );
			try { $( 'Decisionpropoorientsocialecov58<?php echo $i?>StructurereferenteId' ).onchange(); } catch(id) { }

			dependantSelect( 'Decisionpropoorientsocialecov58<?php echo $i?>ReferentId', 'Decisionpropoorientsocialecov58<?php echo $i?>StructurereferenteId' );
			try { $( 'Decisionpropoorientsocialecov58<?php echo $i?>ReferentId' ).onchange(); } catch(id) { }

			observeDisableFieldsOnValue(
				'Decisionpropoorientsocialecov58<?php echo $i;?>Decisioncov',
				[ 'Decisionpropoorientsocialecov58<?php echo $i;?>TypeorientId', 'Decisionpropoorientsocialecov58<?php echo $i;?>StructurereferenteId', 'Decisionpropoorientsocialecov58<?php echo $i;?>ReferentId' ],
				'valide',
				false
			);
		<?php endfor;?>
	});
</script>