<?php
	$this->pageTitle = 'Propositions de décision';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'decisiontraitementpcg66', "Decisionstraitementspcgs66::{$this->action}" ).' '.$traitementpcg66['Descriptionpdo']['name']
	);
?>
<ul class="actionMenu">
	<?php
		echo '<li>'.$this->Xhtml->addLink(
			'Ajout d\'une décision',
			 array( 'controller' => 'decisionstraitementspcgs66', 'action' => 'add', $traitementpcg66['Traitementpcg66']['id'] ),
			$this->Permissions->checkDossier( 'decisionstraitementspcgs66', 'add', $dossierMenu )
		).' </li>';
	?>
</ul>
<?php
	echo $this->Default2->index(
		$listeDecisions,
		array(
			'Decisiontraitementpcg66.valide',
			'Decisiontraitementpcg66.commentaire',
			'Decisiontraitementpcg66.created'
		),
		array(
			'options' => $options
		)
	);

	echo '<div class="aere">';
	echo $this->Default->button(
		'backpdo',
		array(
			'controller' => 'traitementspcgs66',
			'action'     => 'index',
			$traitementpcg66['Personnepcg66']['personne_id'],
			$traitementpcg66['Personnepcg66']['dossierpcg66_id']
		),
		array(
			'id' => 'Back',
			'label' => 'Retour au dossier'
		)
	);
	echo '</div>';
