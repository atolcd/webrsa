<h1><?php

	if( Configure::read( 'Cg.departement' ) != 66 ) {
		$pageTitle = 'Contrats validés';
	}
	else{
		$pageTitle = 'Décisions prises';
	}

	echo $this->pageTitle = $pageTitle;
	?>
</h1><?php require_once( 'filtre.ctp' );?>

<?php
	if( isset( $cohorteci ) ) {
		$pagination = $this->Xpaginator->paginationBlock( 'Contratinsertion', $this->passedArgs );
	}
	else {
		$pagination = '';
	}
?>
<?php if( !empty( $this->request->data ) ):?>
	<?php echo $this->Html->tag( 'h2', 'Résultats de la recherche' );?>
	<?php if( empty( $cohorteci ) ):?>
		<?php
			switch( $this->action ) {
				case 'valides':
					$message = 'Aucun contrat ne correspond à vos critères.';
					break;
				default:
					$message = 'Aucun contrat de validé n\'a été trouvé.';
			}
		?>
		<p class="notice"><?php echo $message;?></p>
	<?php else:?>
		<?php
			$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';
		?>
		<h2 class="noprint">Résultats de la recherche</h2>
		<?php echo $pagination;?>
		<table class="tooltips default2">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° Dossier', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Commune', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de début contrat', 'Contratinsertion.dd_ci' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de fin contrat', 'Contratinsertion.df_ci' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Décision', 'Contratinsertion.decision_ci' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Observations', 'Contratinsertion.observ_ci' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Forme du contrat', 'Contratinsertion.forme_ci' );?></th>

					<?php if( Configure::read( 'Cg.departement' ) == 66 ):?>
						<th><?php echo $this->Xpaginator->sort( 'Position du contrat', 'Contratinsertion.positioncer' );?></th>
						<th colspan="5" class="action">Action</th>
					<?php else:?>
						<th class="action">Action</th>
					<?php endif;?>
					<th class="innerTableHeader">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$controller = 'contratsinsertion';
					if( Configure::read( 'Cg.departement' ) == 93 ) {
						$controller = 'cers93';
					}
					foreach( $cohorteci as $index => $contrat ):?>
						<?php
						$innerTable = '<table id="innerTable'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>Date naissance</th>
									<td>'.h( date_short( $contrat['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>' . __d( 'dossier', 'Dossier.matricule.large' ) . '</th>
									<td>'.h( $contrat['Dossier']['matricule'] ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $contrat['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>Code postal</th>
									<td>'.h( $contrat['Adresse']['codepos'] ).'</td>
								</tr>
								<tr>
									<th>Code INSEE</th>
									<td>'.h( $contrat['Adresse']['numcom'] ).'</td>
								</tr>
								<tr>
									<th>Rôle</th>
									<td>'.h( $rolepers[$contrat['Prestation']['rolepers']] ).'</td>
								</tr>
								<tr>
									<th>État du dossier</th>
									<td>'.h( $etatdosrsa[$contrat['Situationdossierrsa']['etatdosrsa']] ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $contrat, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $contrat, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';
						$title = $contrat['Dossier']['numdemrsa'];

						$array1 = array(
							h( $contrat['Dossier']['numdemrsa'] ),
							h( $contrat['Personne']['nom'].' '.$contrat['Personne']['prenom'] ),
							h( $contrat['Adresse']['nomcom'] ),
							h( date_short( $contrat['Contratinsertion']['dd_ci'] ) ),
							h( date_short( $contrat['Contratinsertion']['df_ci'] ) ),
							h( $decision_ci[$contrat['Contratinsertion']['decision_ci']].' '.date_short( $contrat['Contratinsertion']['datedecision'] ) ),
							h( $contrat['Contratinsertion']['observ_ci'] ),
							h( Set::classicExtract( $forme_ci, $contrat['Contratinsertion']['forme_ci'] ) ),
						);

						$array2 = array();
						if( Configure::read( 'Cg.departement' ) == 66 ){
							$array2 = array(
								h( Set::enum( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ), $numcontrat['positioncer'] ) ),
								$this->Default2->button(
									'view',
									array( 'controller' => 'contratsinsertion', 'action' => 'view',
									$contrat['Contratinsertion']['id'] ),
									array(
										'enabled' => (
											$this->Permissions->check( 'contratsinsertion', 'view' ) == 1
										)
									)
								),
								$this->Default2->button(
									'ficheliaisoncer',
									array( 'controller' => 'contratsinsertion', 'action' => 'ficheliaisoncer',
									$contrat['Contratinsertion']['id'] ),
									array(
										'enabled' => (
											$this->Permissions->check( 'contratsinsertion', 'ficheliaisoncer' )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ) != 'fincontrat' )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ) != 'annule' )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.decision_ci' ) == 'N' )
										)
									)
								),
								$this->Default2->button(
									'notifbenef',
									array( 'controller' => 'contratsinsertion', 'action' => 'notifbenef',
									$contrat['Contratinsertion']['id'] ),
									array(
										'enabled' => (
											$this->Permissions->check( 'contratsinsertion', 'notifbenef' )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ) != 'fincontrat' )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ) != 'annule' )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.decision_ci' ) != 'E' )
										)
									)
								),
								$this->Default2->button(
									'notifop',
									array( 'controller' => 'contratsinsertion', 'action' => 'notificationsop',
									$contrat['Contratinsertion']['id'] ),
									array(
										'enabled' => (
											( $this->Permissions->check( 'contratsinsertion', 'notificationsop' ) == 1 )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ) != 'fincontrat' )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ) != 'annule' )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.decision_ci' ) == 'V' )
										)
									)
								),
								$this->Default2->button(
									'print',
									array( 'controller' => 'contratsinsertion', 'action' => 'impression',
									$contrat['Contratinsertion']['id'] ),
									array(
										'enabled' => (
											( $this->Permissions->check( 'contratsinsertion', 'impression' ) == 1 )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ) != 'annule' )
											&& ( Set::classicExtract( $contrat, 'Contratinsertion.positioncer' ) != 'fincontrat' )
										)
									)
								),
								array( $innerTable, array( 'class' => 'innerTableCell' ) ),
							);
						}
						else{
							$array2 = array(
								$this->Xhtml->viewLink(
									'Voir le contrat',
									array( 'controller' => $controller, 'action' => 'view', $contrat['Contratinsertion']['id'] ),
									$this->Permissions->check( $controller, 'view' )
								),
								array( $innerTable, array( 'class' => 'innerTableCell' ) ),
							);
						}


						echo $this->Xhtml->tableCells(
							Set::merge( $array1, $array2 ),
							array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
							array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
						);
					?>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php echo $pagination;?>
		<ul class="actionMenu">
			<li><?php
				echo $this->Xhtml->printLinkJs(
					'Imprimer le tableau',
					array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
				);
			?></li>
			<li><?php
				echo $this->Xhtml->exportLink(
					'Télécharger le tableau',
					array( 'controller' => 'cohortesci', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'cohortesci', 'exportcsv' )
				);
			?></li>
		</ul>
	<?php endif;?>
<?php endif;?>