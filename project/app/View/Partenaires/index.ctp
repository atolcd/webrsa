<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'partenaire', "Partenaires::{$this->action}" )
	);

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php if( $this->Permissions->check( 'partenaires', 'add' ) ):?>
		<ul class="actionMenu">
			<?php
				echo '<li>'.$this->Xhtml->addLink(
					'Ajouter',
					array( 'controller' => 'partenaires', 'action' => 'add' )
				).' </li>';
			?>
		</ul>
	<?php endif;?>
<?php

	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
	).'</li></ul>';
?>
<?php echo $this->Xform->create( 'Partenaire', array( 'type' => 'post', 'url' => array( 'action' => 'index' ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
		<fieldset>
			<?php echo $this->Xform->input( 'Partenaire.index', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>

			<legend>Filtrer par Partenaire</legend>
			<?php
				echo $this->Default2->subform(
					array(
						'Partenaire.libstruc',
						'Partenaire.ville',
						'Partenaire.codepartenaire'
					),
					array(
						'options' => $options
					)
				);
			?>
		</fieldset>

		<div class="submit noprint">
			<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
			<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
		</div>

<?php echo $this->Xform->end();?>

<?php
	if( !empty( $this->request->data ) ) {
		if( !empty( $partenaires ) ) {
			echo $this->Default2->index(
				$partenaires,
				array(
					'Partenaire.libstruc',
					'Partenaire.numtel',
					'Partenaire.email',
					'Partenaire.ville'
				),
				array(
					'cohorte' => false,
					'actions' => array(
						'Partenaires::edit',
						'Partenaires::delete' => array( 'disabled' => '\'#Partenaire.occurences#\' != "0"' )
					)
				)
			);
		}
		else {
			echo '<p class="notice">Aucun partenaire présent</p>';
		}
	}
?>
