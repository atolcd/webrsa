<h1><?php echo $this->pageTitle = 'Liste des membres pour les équipes pluridisciplinaires';?></h1>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	if ( $compteurs['Fonctionmembreep'] == 0 ) {
		echo "<p class='error'>Merci d'ajouter au moins une fonction pour les membres avant d'ajouter un membre.</p>";
	}

	echo '<ul class="actionMenu"><li>'.$this->Xhtml->addLink(
		'Ajouter',
		array( 'controller' => 'membreseps', 'action' => 'add' ),
		$this->Permissions->check( 'membreseps', 'add' ) && ( $compteurs['Fonctionmembreep'] != 0 )
	).'</li></ul>';

	// Début du formulaire
	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
	).'</li></ul>';

	echo $this->Xform->create( 'Membreep', array( 'type' => 'post', 'action' => 'index', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );

	echo '<fieldset>';
	echo $this->Xform->input( 'Membreep.index', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );
		echo $this->Default2->subform(
			array(
				'Membreep.nom',
				'Membreep.prenom',
				'Membreep.ville',
				'Membreep.organisme',
				'Membreep.fonctionmembreep_id' => array( 'type' => 'select', 'options' => $options['Membreep']['fonctionmembreep_id']  )
			),
			array(
				'options' => $options
			)
		);
	echo '</fieldset>';

	echo '<div class="submit noprint">';
		echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );
		echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );
	echo '</div>';

	echo $this->Xform->end();
	// Fin du formulaire

	if( !empty( $this->request->data ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		echo $this->Default2->index(
			$membreseps,
			array(
				'Membreep.nomcomplet'=>array('type'=>'text'),
				'Fonctionmembreep.name',
				'Membreep.organisme',
				'Membreep.tel',
				'Membreep.adresse'=>array('type'=>'text'),
				'Membreep.mail'
			),
			array(
				'actions' => array(
					'Membreseps::edit',
					'Membreseps::delete'
				),
				'options' => $options
			)
		);
	}
?>
