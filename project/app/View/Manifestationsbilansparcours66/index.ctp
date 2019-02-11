<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'manifestationbilanparcours66', "Manifestationsbilansparcours66::{$this->action}" )
	);
?>
<ul class="actionMenu">
	<li><?php
			echo $this->Xhtml->addLink(
				'Ajouter',
				array(
					'action' => 'add',
					$bilanparcours66_id
				),
				$this->Permissions->checkDossier( $this->request->params['controller'], 'add', $dossierMenu )
			);
		?>
	</li>
</ul>
<?php
	echo $this->Default2->index(
		$manifestationsbilansparcours66,
		array(
			'Manifestationbilanparcours66.commentaire',
			'Manifestationbilanparcours66.datemanifestation',
			'Manifestationbilanparcours66.nb_fichiers_lies' => array( 'type' => 'integer' )
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Manifestationsbilansparcours66::edit' => array(
					'disabled' => !$this->Permissions->checkDossier( 'manifestationsbilansparcours66', 'edit', $dossierMenu )
				),
				'Manifestationsbilansparcours66::delete' => array(
					'disabled' => !$this->Permissions->checkDossier( 'manifestationsbilansparcours66', 'delete', $dossierMenu )
				),
				'Manifestationsbilansparcours66::filelink' => array(
					'disabled' => !$this->Permissions->checkDossier( 'manifestationsbilansparcours66', 'filelink', $dossierMenu )
				)
			)
		)
	);

	echo '<div class="aere">';
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'bilansparcours66',
			'action'     => 'index',
			$personne_id
		),
		array(
			'id' => 'Back',
			'class' => 'aere'
		)
	);
	echo '</div>';
?>