<?php
	echo '<table><thead><tr>';

	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nir' ) );
	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nom' ) );
	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.adresse' ) );
	echo $this->Xhtml->tag( 'th', __d( Inflector::underscore(Inflector::classify($theme)), $theme.'.datedemande' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.proporeferent' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.decisioncov' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.choixcov' ), array( 'colspan' => 3 ) );
	echo $this->Xhtml->tag( 'th', __d( Inflector::underscore(Inflector::classify($theme)), $theme.'.commentaire' ) );
	echo $this->Xhtml->tag( 'th', 'Actions' );

echo '</tr></thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossiercov ) {
		echo $this->Form->input( "{$theme}.{$i}.id", array( 'type' => 'hidden', 'value' => $dossiercov[$theme]['id'] ) );
		echo $this->Xhtml->tableCells(
			array(
				$dossiercov['Personne']['nir'],
				implode( ' ', array( $dossiercov['Personne']['qual'], $dossiercov['Personne']['nom'], $dossiercov['Personne']['prenom'] ) ),
				implode( ' ', array( $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossiercov[$theme]['datedemande'] ),
				implode( ' - ', Hash::filter( (array)array( $dossiercov['Typeorient']['lib_type_orient'], $dossiercov['Structurereferente']['lib_struc'], implode( ' ', Hash::filter( (array)array( $dossiercov['Referent']['qual'], $dossiercov['Referent']['nom'], $dossiercov['Referent']['prenom'] ) ) ) ) ) ),
				$this->Form->input( "{$theme}.{$i}.decisioncov", array( 'type' => 'select', 'options' => $decisionscovs, 'label' => false, 'empty' => true ) ),
				$this->Form->input( "{$theme}.{$i}.typeorient_id", array( 'type' => 'select', 'options' => $typesorients, 'label' => false, 'empty' => true ) ),
				$this->Form->input( "{$theme}.{$i}.structurereferente_id", array( 'type' => 'select', 'options' => $structuresreferentes, 'label' => false, 'empty' => true ) ),
				$this->Form->input( "{$theme}.{$i}.referent_id", array( 'type' => 'select', 'options' => $referents, 'label' => false, 'empty' => true ) ),
				$this->Form->input( "{$theme}.{$i}.commentaire", array( 'type' => 'textarea', 'label' => false ) )
			)
		);
	}
	echo '</tbody></table>';
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php for( $i = 0 ; $i < count( $dossiers[$theme]['liste'] ) ; $i++ ):?>
			dependantSelect( 'Propononorientationprocov58<?php echo $i?>StructurereferenteId', 'Propononorientationprocov58<?php echo $i?>TypeorientId' );
			try { $( 'Propononorientationprocov58<?php echo $i?>StructurereferenteId' ).onchange(); } catch(id) { }

			dependantSelect( 'Propononorientationprocov58<?php echo $i?>ReferentId', 'Propononorientationprocov58<?php echo $i?>StructurereferenteId' );
			try { $( 'Propononorientationprocov58<?php echo $i?>ReferentId' ).onchange(); } catch(id) { }

			observeDisableFieldsOnValue(
				'Propononorientationprocov58<?php echo $i;?>Decisioncov',
				[ 'Propononorientationprocov58<?php echo $i;?>TypeorientId', 'Propononorientationprocov58<?php echo $i;?>StructurereferenteId', 'Propononorientationprocov58<?php echo $i;?>ReferentId' ],
				'refus',
				false
			);
		<?php endfor;?>
	});
</script>