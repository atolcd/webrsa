<?php
	$this->pageTitle = 'Propositions de décision';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'decisionpersonnepcg66', "Decisionspersonnespcgs66::{$this->action}" ).' '.$nompersonne
	);
?>
<?php if( $this->Permissions->checkDossier( 'decisionspersonnespcgs66', 'add', $dossierMenu ) ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajout d\'une proposition de décision',
				 array( 'controller' => 'decisionspersonnespcgs66', 'action' => 'add', $personnepcg66_id ),
				$this->Permissions->checkDossier( 'decisionspersonnespcgs66', 'add', $dossierMenu )
			).' </li>';
		?>
	</ul>
<?php endif;?>
<?php
	echo $this->Default2->index(
		$listeDecisions,
		array(
			'Personnepcg66Situationpdo.Situationpdo.libelle',
			'Decisionpdo.libelle',
			'Decisionpersonnepcg66.datepropositions',
			'Decisionpersonnepcg66.commentaire'
		),
		array(
			'actions' => array(
				'Decisionspersonnespcgs66::view' => array(
					'disabled' => !$this->Permissions->checkDossier( 'decisionspersonnespcgs66', 'view', $dossierMenu )
				),
				'Decisionspersonnespcgs66::edit' => array(
					'disabled' => !$this->Permissions->checkDossier( 'decisionspersonnespcgs66', 'edit', $dossierMenu )
				),
				'Decisionspersonnespcgs66::print' => array(
					'url' => array( 'controller' => 'decisionspersonnespcgs66', 'action' => 'gedooo' ),
					'disabled' => !$this->Permissions->checkDossier( 'decisionspersonnespcgs66', 'gedooo', $dossierMenu )
				),
				'Decisionspersonnespcgs66::delete' => array(
					'disabled' => !$this->Permissions->checkDossier( 'decisionspersonnespcgs66', 'delete', $dossierMenu )
				)
			),
			'options' => $options
		)
	);

	echo '<div class="aere">';
	echo $this->Default->button(
		'backpdo',
		array(
			'controller' => 'dossierspcgs66',
			'action'     => 'edit',
			$dossierpcg66_id
		),
		array(
			'id' => 'Back',
			'label' => 'Retour au dossier'
		)
	);
	echo '</div>';
?>