<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'contactpartenaire', "Contactspartenaires::{$this->action}" )
	);
        
    if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php if( $this->Permissions->check( 'contactspartenaires', 'add' ) ):?>
		<ul class="actionMenu">
			<?php
				echo '<li>'.$this->Xhtml->addLink(
					'Ajouter',
					array( 'controller' => 'contactspartenaires', 'action' => 'add' )
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
<?php echo $this->Xform->create( 'Contactpartenaire', array( 'type' => 'post', 'action' => 'index', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
		<fieldset>
			<?php echo $this->Xform->input( 'Contactpartenaire.index', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>

			<legend>Filtrer par Contact de partenaire</legend>
			<?php
				echo $this->Default2->subform(
					array(
						'Contactpartenaire.nom',
						'Contactpartenaire.prenom',
                        'Contactpartenaire.partenaire_id'
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
		if( !empty( $contactspartenaires ) ) {
            echo $this->Default2->index(
                $contactspartenaires,
                array(
                    'Contactpartenaire.qual',
                    'Contactpartenaire.nom',
                    'Contactpartenaire.prenom',
                    'Contactpartenaire.numtel',
                    'Contactpartenaire.numfax',
                    'Contactpartenaire.email',
                    'Partenaire.libstruc'
                ),
                array(
                    'cohorte' => false,
                    'actions' => array(
                        'Contactspartenaires::edit',
                        'Contactspartenaires::delete' => array( 'disabled' => '\'#Contactpartenaire.occurences#\' != "0"' )
                    ),
                    'options' => $options
                )
            );
        }
}

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'actionscandidats_personnes',
			'action'     => 'indexparams'
		),
		array(
			'id' => 'Back'
		)
	);
?>
