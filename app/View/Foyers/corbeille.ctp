<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php foreach( $listeCourriers as $index => $listeCourrier ):?>
			dependantSelect( 'Foyer<?php echo $index;?>Traitementpcg66Id', 'Foyer<?php echo $index;?>Dossierpcg66Id' );
			
			observeDisableFieldsOnRadioValue(
				'Corbeille',
				'data[Foyer][<?php echo $index;?>][action]',
				[
					'Foyer<?php echo $index;?>Dossierpcg66Id',
					'Foyer<?php echo $index;?>Traitementpcg66Id'
				],
				[ 'Valider' ],
				true
			);
		<?php endforeach;?>
	});
</script>

	<?php
		echo $this->Xhtml->tag(
			'h1',
			$this->pageTitle = __d( 'foyer', "Foyer::{$this->action}", true )
		);
		
		// Bouton action Ajouter qui permet de lier des fichiers
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->addLink(
			__d( 'foyer','Foyer.filelink', true ),
			array( 'controller' => 'foyers', 'action' => 'filelink', $foyer_id )
		).' </li></ul>';
		
		if( $hasFichierLie['Fichiermodule']['nb_fichiers_lies'] == 0 ) {
			echo '<p class="notice"> Aucun courrier présent dans la corbeille</p>';
		}
		else{
			echo $this->Xform->create( null, array( 'id' => 'Corbeille' ) );
			echo '<table>';
			echo '<thead>
					<tr>
						<th>Nom de la pièce jointe</th>
						<th>Date d\'ajout</th>
						<th>Dossier concerné</th>
						<th>Traitement concerné</th>
						<th class="action">Actions</th>
					</tr>
				</thead>';
			echo '<tbody>';
			foreach( $listeCourriers as $index => $courrier ) {
				echo $this->Xhtml->tableCells(
					array(
						$courrier['Fichiermodule']['name'],
						date_short( $courrier['Fichiermodule']['created'] ),
						// Choix du dispatch
						$this->Xform->input( "Foyer.{$index}.dossierpcg66_id", array( 'label' => false, 'type' => 'select', 'options' => $options['Dossierpcg66']['id'], 'empty' => true ) ),
						$this->Xform->input( "Foyer.{$index}.traitementpcg66_id", array( 'label' => false, 'type' => 'select', 'options' => $options['Traitementpcg66']['id'], 'empty' => true ) ),
						// Actions
						$this->Xform->input( "Foyer.{$index}.fichiermodule_id", array( 'type' => 'hidden', 'value' => $courrier['Fichiermodule']['id'] ) ).
						$this->Xform->input( "Foyer.{$index}.action", array( 'separator' => '<br />','div' => false, 'legend' => false, 'type' => 'radio', 'empty' => false, 'default' => 'En attente', 'options' => $options['actions'] ) )
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			}
			echo '</tbody>';
			echo '</table>';
			echo $this->Xform->submit( 'Validation de la liste' );
			
			echo $this->Xform->end();

		}
	?>