<?php
	echo '<table><thead><tr>';

	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nir' ) );
	echo $this->Xhtml->tag( 'th', __d( 'personne', 'Personne.nom' ) );
	echo $this->Xhtml->tag( 'th', __d( 'adresse', 'Adresse.nomcom' ) );
	echo $this->Xhtml->tag( 'th', __d( 'referent', 'Referent.nom_complet' ) );
	echo $this->Xhtml->tag( 'th', __d( 'dossiercov58', 'Dossiercov58.decisioncov' ) );
	echo $this->Xhtml->tag( 'th', __d( Inflector::underscore(Inflector::classify($theme)), $theme.'.datevalidation' ) );
	echo $this->Xhtml->tag( 'th', __d( Inflector::underscore(Inflector::classify($theme)), $theme.'.commentaire' ) );
	echo $this->Xhtml->tag( 'th', 'Actions' );

	echo '</tr></thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossiercov ) {
// debug($dossiercov);
		echo $this->Form->input( "{$theme}.{$i}.id", array( 'type' => 'hidden', 'value' => $dossiercov[$theme]['id'] ) );
		echo $this->Xhtml->tableCells(
			array(
				$dossiercov['Personne']['nir'],
				implode( ' ', array( $dossiercov['Personne']['qual'], $dossiercov['Personne']['nom'], $dossiercov['Personne']['prenom'] ) ),
				implode( ' ', array( $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['codepos'], $dossiercov['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomcom'] ) ),
				implode( ' ', array( $dossiercov['Propocontratinsertioncov58']['Referent']['qual'], $dossiercov['Propocontratinsertioncov58']['Referent']['nom'], $dossiercov['Propocontratinsertioncov58']['Referent']['prenom'] ) ),
				$this->Form->input( "{$theme}.{$i}.decisioncov", array( 'type' => 'select', 'options' => $decisionscovs, 'label' => false, 'empty' => true ) ),
				$this->Form->input( "{$theme}.{$i}.datevalidation", array( 'type' => 'date', 'selected' => $cov58['Cov58']['datecommission'], 'dateFormat' => 'DMY', 'label' => false, 'maxYear'=>date('Y')+2, 'minYear'=>date('Y')-2 ) ),
				$this->Form->input( "{$theme}.{$i}.commentaire", array( 'type' => 'textarea', 'label' => false ) ),
				$this->Xhtml->viewLink( 'Voir', array( 'controller' => 'contratsinsertion', 'action' => 'index', $dossiercov['Personne']['id'] ), true, true )
			)
		);
	}
	echo '</tbody></table>';
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php for( $i = 0 ; $i < count( $dossiers[$theme]['liste'] ) ; $i++ ):?>
			observeDisableFieldsOnValue(
				'Propocontratinsertioncov58<?php echo $i;?>Decisioncov',
				[ 'Propocontratinsertioncov58<?php echo $i;?>DatevalidationDay', 'Propocontratinsertioncov58<?php echo $i;?>DatevalidationMonth', 'Propocontratinsertioncov58<?php echo $i;?>DatevalidationYear' ],
				'accepte',
				false
			);
		<?php endfor;?>
	});
</script>