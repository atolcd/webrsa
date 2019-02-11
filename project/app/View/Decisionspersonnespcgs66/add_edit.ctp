<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$personneComplet = Set::classicExtract( $personnepcg66, 'Personne.qual' ).' '.Set::classicExtract( $personnepcg66, 'Personne.nom' ).' '.Set::classicExtract( $personnepcg66, 'Personne.prenom' );
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'decisionpersonnepcg66', "Decisionspersonnespcgs66::{$this->action}" ).' pour '.$personneComplet
	);

	echo $this->Xform->create( 'Decisionpersonnepcg66', array( 'id' => 'decisionpersonnepcg66form' ) );
	if( Set::check( $this->request->data, 'Decisionpersonnepcg66.id' ) ){
		echo $this->Xform->input( 'Decisionpersonnepcg66.id', array( 'type' => 'hidden' ) );
	}
?>

<fieldset><legend>Proposition de décision</legend>
	<fieldset id="Decision" class="invisible">
		<?php
			echo $this->Default2->subform(
				array(
					'Decisionpersonnepcg66.personnepcg66_situationpdo_id' => array( 'label' =>  ( __( 'Motifs' ) ), 'type' => 'select', 'empty' => true, 'required' => true, 'options' => $personnespcgs66Situationspdos ),
					'Decisionpersonnepcg66.decisionpdo_id' => array( 'label' =>  ( __( 'Décision du Conseil Départemental' ) ), 'type' => 'select', 'options' => $options, 'required' => true, 'empty' => true ),
					'Decisionpersonnepcg66.datepropositions' => array( 'label' =>  ( __( 'Date de propositon' ) ), 'type' => 'date', 'dateFormat' => 'DMY', 'required' => true, 'empty' => true, 'minYear' => date('Y') - 5, 'maxYear' => date('Y') + 1 )
				),
				array(
					'options' => $options
				)
			);

			echo $this->Default2->subform(
				array(
					'Decisionpersonnepcg66.commentaire' => array( 'label' =>  'Proposition : ', 'type' => 'textarea', 'rows' => 3 ),
				),
				array(
					'options' => $options
				)
			);
		?>
	</fieldset>

</fieldset>

<?php
	echo "<div class='submit'>";
		echo $this->Form->submit('Enregistrer', array('div'=>false));
		echo $this->Form->button( 'Retour', array( 'type' => 'button', 'onclick'=>"location.replace('".Router::url( array( 'controller' => 'decisionspersonnespcgs66', 'action' => 'index', $personnepcg66_id ) )."')" ) );
	echo "</div>";

	echo $this->Form->end();
?>

<?php echo $this->Xform->end();?>