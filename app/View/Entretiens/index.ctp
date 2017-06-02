<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'entretien', "Entretiens::{$this->action}" )
	);
	echo $this->element( 'ancien_dossier' );
	$departement = Configure::read( 'Cg.departement' );
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
?>
	<?php if( $this->Permissions->checkDossier( 'entretiens', 'add', $dossierMenu ) ):?>
		<ul class="actionMenu">
			<?php
				echo '<li>'.
					$this->Xhtml->addLink(
						'Ajouter un entretien',
						array( 'controller' => 'entretiens', 'action' => 'add', $personne_id ),
						WebrsaAccess::addIsEnabled('/entretiens/add/'.$personne_id, $ajoutPossible)
					).
				' </li>';
			?>
		</ul>
	<?php endif;?>
	<?php if( isset( $entretiens ) ):?>
		<?php if( empty( $entretiens ) ):?>
			<?php $message = 'Aucun entretien n\'a été trouvé.';?>
			<p class="notice"><?php echo $message;?></p>
		<?php else:?>

		<?php $pagination = $this->Xpaginator->paginationBlock( 'Entretien', $this->passedArgs ); ?>
		<?php echo $pagination;?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th>Date de l'entretien</th>
					<th>Structure référente</th>
					<th>Nom du prescripteur</th>
					<th>Type d'entretien</th>
					<th>Objet de l'entretien</th>
					<th>A revoir le</th>
					<th class="action" colspan="<?php echo ( $departement == 66 ? 6 : 5 );?>">Action</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach( $entretiens as $index => $entretien ):?>
				<?php
					$nbFichiersLies = 0;
					$nbFichiersLies = ( isset( $entretien['Fichiermodule'] ) ? count( $entretien['Fichiermodule'] ) : 0 );

					$cells = array(
						h( date_short(  $entretien['Entretien']['dateentretien'] ) ),
						h( $entretien['Structurereferente']['lib_struc'] ),
						h( $entretien['Referent']['nom_complet'] ),
						h( Set::enum( $entretien['Entretien']['typeentretien'], $options['Entretien']['typeentretien'] ) ),
						h( $entretien['Objetentretien']['name'] ),
						h( $this->Locale->date( 'Date::miniLettre', $entretien['Entretien']['arevoirle'] ) ),
						$this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => 'entretiens', 'action' => 'view', $entretien['Entretien']['id'] ),
							WebrsaAccess::isEnabled($entretien, '/entretiens/view/'.$entretien['Entretien']['id'])
						),
						$this->Xhtml->editLink(
							'Editer l\'orientation',
							array( 'controller' => 'entretiens', 'action' => 'edit', $entretien['Entretien']['id'] ),
							WebrsaAccess::isEnabled($entretien, '/entretiens/edit/'.$entretien['Entretien']['id'])
						),
					);

					$cells = $departement == 66 ?
						array_merge(
							$cells,
							array(
								$this->Default2->button(
									'print',
									array( 'controller' => 'entretiens', 'action' => 'impression',
									$entretien['Entretien']['id'] ),
									array(
										'class' => 'action_impression',
										'enabled' => WebrsaAccess::isEnabled(
											$entretien, '/entretiens/impression/'.$entretien['Entretien']['id']
										)
									)
								),
							)	
						) : $cells
					;

					$cells = array_merge(
						$cells,
						array(
							$this->Xhtml->deleteLink(
								'Supprimer l\'entretien',
								array( 'controller' => 'entretiens', 'action' => 'delete', $entretien['Entretien']['id'] ),
								WebrsaAccess::isEnabled($entretien, '/entretiens/delete/'.$entretien['Entretien']['id'])
							),
							$this->Xhtml->fileLink(
								'Fichiers liés',
								array( 'controller' => 'entretiens', 'action' => 'filelink', $entretien['Entretien']['id'] ),
								WebrsaAccess::isEnabled($entretien, '/entretiens/filelink/'.$entretien['Entretien']['id'])
							),
							h( '('.$nbFichiersLies.')' )
						)
					);

					echo $this->Xhtml->tableCells(
						$cells,
						array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
						array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
					);
					?>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php endif?>
	<?php endif?>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>

<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>