<?php
	$this->pageTitle =  __d( 'decisionpersonnepcg66', "Decisionspersonnespcgs66::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Form->create( 'Decisionpersonnepcg66', array( 'type' => 'post', 'id' => 'decisionpersonnepcg66form' ) );

	$motif = Set::enum( Set::classicExtract( $decisionpersonnepcg66, 'Decisionpersonnepcg66.personnepcg66_situationpdo_id' ), $personnespcgs66Situationspdos );

	echo $this->Default2->view(
		$decisionpersonnepcg66,
		array(
			'Decisionpersonnepcg66.personnepcg66_situationpdo_id' => array( 'label' => 'Motif en question', 'value' => $motif ),
			'Decisionpersonnepcg66.datepropositions',
			'Decisionpdo.libelle',
			'Decisionpersonnepcg66.commentaire',
		),
		array(
			'class' => 'aere'
		)
	);

	echo '<div class="aere">';
	echo $this->Default->button(
		'backpdo',
		array(
			'controller' => 'decisionspersonnespcgs66',
			'action'     => 'index',
			$personnepcg66_id
		),
		array(
			'id' => 'Back',
			'label' => 'Retour au dossier'
		)
	);
	echo '</div>';

?>