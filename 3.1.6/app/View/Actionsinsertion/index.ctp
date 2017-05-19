<?php
	$this->pageTitle = 'Actions d\'insertion pour le contrat';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'une action d\'insertion';
	}
	else {
		$this->pageTitle = 'Actions d\'insertion ';
		$foyer_id = $this->request->data['Personne']['foyer_id'];
	}
?>

<h1><?php echo 'Actions d\'insertion pour le contrat ';?></h1>

<?php if( empty( $actionsinsertion ) ):?>
	<b>Aides</b>
		<?php
			echo $this->Xhtml->addLink(
				'Ajouter une aide',
				array( 'controller' => 'aidesdirectes', 'action' => 'add', $contratinsertion_id ),
				$this->Permissions->checkDossier( 'aidesdirectes', 'add', $dossierMenu )
			);
	?>
		<p class="notice">Ce contrat ne possède pas d'aides d'insertion.</p>

<b>Prestations</b>
		<?php       echo $this->Xhtml->addLink(
							'Ajouter une prestation',
							array( 'controller' => 'prestsform', 'action' => 'add', $contratinsertion_id ),
							$this->Permissions->checkDossier( 'prestsform', 'add', $dossierMenu )
						);
		?>
	<p class="notice">Ce contrat ne possède pas de prestations d'insertion.</p>

<?php else: ?>
<br />
	<h2>Aides</h2>
	<?php
		echo $this->Xhtml->addLink(
			'Ajouter une aide',
			array( 'controller' => 'aidesdirectes', 'action' => 'add', $contratinsertion_id ),
			$this->Permissions->checkDossier( 'aidesdirectes', 'add', $dossierMenu )
		);
	?>
	<table class="tooltips">
		<thead>
			<tr>
				<th >Type d'aide</th>
				<th >Libellé de l'aide</th>
				<th >Date de l'aide</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $actionsinsertion as $actioninsertion ):?>
				<?php foreach( $actioninsertion['Aidedirecte'] as $aidedirecte ):?>
				<?php

					echo $this->Xhtml->tableCells(
						array(
							h( $typo_aide[$aidedirecte['typo_aide']] ),
							h( $actions[$aidedirecte['lib_aide']] ),
							h( date_short( $aidedirecte['date_aide'] ) ),
							$this->Xhtml->editLink(
								'Éditer l\'aide ',
								array( 'controller' => 'aidesdirectes', 'action' => 'edit', $aidedirecte['id'] ),
								$this->Permissions->checkDossier( 'aidesdirectes', 'edit', $dossierMenu )
							)
						)
					);
				?>
				<?php endforeach;?>
			<?php endforeach;?>
		</tbody>
	</table>
<br />
	<h2>Prestations</h2>
	<?php
		echo $this->Xhtml->addLink(
			'Ajouter une prestation',
			array( 'controller' => 'prestsform', 'action' => 'add', $contratinsertion_id),
			$this->Permissions->checkDossier( 'prestsform', 'add', $dossierMenu )
		);
	?>
	<table class="tooltips" title="Prestations">
		<thead>
			<tr>
				<th>Libellé de la prestation</th>
				<th>Référent de la prestation</th>
				<th>Date de la prestation</th>
				<th colspan="2" class="action">Actions</th>

			</tr>
		</thead>
		<tbody>
			<?php foreach( $actionsinsertion as $actioninsertion ):?>
				<?php foreach( $actioninsertion['Prestform'] as $prestform ):?>
					<?php
						echo $this->Xhtml->tableCells(
							array(
								h( $actions[$prestform['lib_presta']] ),
								h( $prestform['Refpresta']['nomrefpresta'].' '.$prestform['Refpresta']['prenomrefpresta']),
								h( date_short( $prestform['date_presta'] ) ),
								$this->Xhtml->editLink(
									'Éditer la prestation ',
									array( 'controller' => 'prestsform', 'action' => 'edit', $prestform['id'] ),
									$this->Permissions->checkDossier( 'prestsform', 'edit', $dossierMenu )
								)
							)
						);
					?>
				<?php endforeach;?>
			<?php endforeach;?>
		</tbody>
	</table>

<?php endif;?>

<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'contratsinsertion',
			'action'     => 'index',
			$personne_id
		),
		array(
			'id' => 'Back'
		)
	);
?>