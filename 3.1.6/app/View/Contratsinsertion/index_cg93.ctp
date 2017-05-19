<?php  $this->pageTitle = 'Dossier de la personne';?>

<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'un CER';
	}
	else {
		$this->pageTitle = 'CER ';
//		$foyer_id = $this->request->data['Personne']['foyer_id'];
	}
?>
<h1><?php  echo 'CER  ';?></h1>
<?php echo $this->element( 'ancien_dossier' );?>
<?php if( empty( $contratsinsertion ) ):?>
	<p class="notice">Cette personne ne possède pas encore de CER.</p>
<?php endif;?>

<?php if( isset( $signalementseps93 ) && !empty( $signalementseps93 ) ):?>
	<h2>Signalements pour non respect du contrat</h2>
	<table class="tooltips">
		<thead>
			<tr>
				<th>Date début contrat</th>
				<th>Date fin contrat</th>
				<th>Date signalement</th>
				<th>Rang signalement</th>
				<th>État dossier EP</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach( $signalementseps93 as $signalementep93 ):?>
			<?php
				$etatdossierep = Set::enum( $signalementep93['Passagecommissionep']['etatdossierep'], $optionsdossierseps['Passagecommissionep']['etatdossierep'] );
				if( empty( $etatdossierep ) ) {
					$etatdossierep = 'En attente';
				}
			?>
			<tr>
				<td><?php echo $this->Locale->date( 'Locale->date', $signalementep93['Contratinsertion']['dd_ci'] );?></td>
				<td><?php echo $this->Locale->date( 'Locale->date', $signalementep93['Contratinsertion']['df_ci'] );?></td>
				<td><?php echo $this->Locale->date( 'Locale->date', $signalementep93['Signalementep93']['date'] );?></td>
				<td><?php echo h( $signalementep93['Signalementep93']['rang'] );?></td>
				<td><?php echo h( $etatdossierep );?></td>
				<td class="action"><?php echo $this->Default->button( 'edit', array( 'controller' => 'signalementseps', 'action' => 'edit', $signalementep93['Signalementep93']['id'] ), array( 'enabled' => ( empty( $signalementep93['Passagecommissionep']['etatdossierep'] ) ) ) );?></td>
				<td class="action"><?php echo $this->Default->button( 'delete', array( 'controller' => 'signalementseps', 'action' => 'delete', $signalementep93['Signalementep93']['id'] ), array( 'enabled' => ( empty( $signalementep93['Passagecommissionep']['etatdossierep'] ) ), 'confirm' => 'Confirmer la suppression du signalement ?' ) );?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
<?php endif;?>

<?php if( isset( $contratscomplexeseps93 ) && !empty( $contratscomplexeseps93 ) ):?>
	<h2>Passages en EP pour contrats complexes</h2>
	<table class="tooltips">
		<thead>
			<tr>
				<th>Date début contrat</th>
				<th>Date fin contrat</th>
				<th>Date de création du dossier d'EP</th>
				<th>État dossier EP</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach( $contratscomplexeseps93 as $signalementep93 ):?>
			<?php
				$etatdossierep = Set::enum( $signalementep93['Passagecommissionep']['etatdossierep'], $optionsdossierseps['Passagecommissionep']['etatdossierep'] );
				if( empty( $etatdossierep ) ) {
					$etatdossierep = 'En attente';
				}
			?>
			<tr>
				<td><?php echo $this->Locale->date( 'Locale->date', $signalementep93['Contratinsertion']['dd_ci'] );?></td>
				<td><?php echo $this->Locale->date( 'Locale->date', $signalementep93['Contratinsertion']['df_ci'] );?></td>
				<td><?php echo $this->Locale->date( 'Locale->date', $signalementep93['Contratcomplexeep93']['created'] );?></td>
				<td><?php echo h( $etatdossierep );?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
<?php endif;?>

<?php if( $this->Permissions->checkDossier( 'contratsinsertion', 'add', $dossierMenu ) ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter un CER',
				array( 'controller' => 'contratsinsertion', 'action' => 'add', $personne_id )
			).' </li>';
		?>
	</ul>
<?php endif;?>

<?php if( !empty( $contratsinsertion ) ):?>
	<?php if( Configure::read( 'Cg.departement' ) == 93 && !empty( $erreursCandidatePassage ) ):?>
		<h2>Raisons pour lesquelles le contrat ne peut pas être signalé</h2>
		<div class="error_message">
			<?php if( count( $erreursCandidatePassage ) > 1 ):?>
			<ul>
				<?php foreach( $erreursCandidatePassage as $erreur ):?>
					<li><?php echo __d( 'relancenonrespectsanctionep93', "Erreur.{$erreur}" );?></li>
				<?php endforeach;?>
			</ul>
			<?php else:?>
				<p><?php echo __d( 'relancenonrespectsanctionep93', "Erreur.{$erreursCandidatePassage[0]}" );?></p>
			<?php endif;?>
		</div>
	<?php endif;?>
	<table class="tooltips">
		<thead>
			<tr>
				<th>Type contrat</th>
				<th>Rang contrat</th>
				<th>Date début</th>
				<th>Date fin</th>
				<th>Décision</th>
				<th colspan="9" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $contratsinsertion as $contratinsertion ):?>
				<?php
					$dureeTolerance = Configure::read( 'Signalementep93.dureeTolerance' );

					$enCours = (
						( strtotime( $contratinsertion['Contratinsertion']['dd_ci'] ) <= time() )
						&& ( strtotime( $contratinsertion['Contratinsertion']['df_ci'] ) + ( $dureeTolerance * 24 * 60 * 60 ) >= time() )
					);

					$isValid = Set::extract( $contratinsertion, 'Contratinsertion.decision_ci' );
					$block = true;
					if( $isValid == 'V'  ){
						$block = false;
					}

					$contratenep = in_array( $contratinsertion['Contratinsertion']['id'], $contratsenep );

					echo $this->Xhtml->tableCells(
						array(
							h( Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.forme_ci' ), $forme_ci ) ),
							h( Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.num_contrat' ),  $options['num_contrat'] ) ),
							h( date_short( isset( $contratinsertion['Contratinsertion']['dd_ci'] ) ) ? date_short( $contratinsertion['Contratinsertion']['dd_ci']  ) : null ),
							h( date_short( isset( $contratinsertion['Contratinsertion']['df_ci'] ) ) ? date_short( $contratinsertion['Contratinsertion']['df_ci'] ) : null ),
							h( Set::enum( Set::extract( $contratinsertion, 'Contratinsertion.decision_ci' ), $decision_ci ).' '.$this->Locale->date( 'Date::short', Set::extract( $contratinsertion, 'Contratinsertion.datevalidation_ci' ) ) ),
							$this->Xhtml->validateLink(
								'Valider le CER ',
								array( 'controller' => 'contratsinsertion', 'action' => 'valider', $contratinsertion['Contratinsertion']['id'] ),
								$this->Permissions->checkDossier( 'contratsinsertion', 'valider', $dossierMenu ) && !$contratenep

							),
							$this->Xhtml->actionsLink(
								'Actions pour le CER',
								array( 'controller' => 'actionsinsertion', 'action' => 'index', $contratinsertion['Contratinsertion']['id'] ),
								$this->Permissions->checkDossier( 'actionsinsertion', 'index', $dossierMenu ) && !$contratenep
							),
							$this->Xhtml->viewLink(
								'Voir le CER',
								array( 'controller' => 'contratsinsertion', 'action' => 'view', $contratinsertion['Contratinsertion']['id']),
								$this->Permissions->checkDossier( 'contratsinsertion', 'view', $dossierMenu )
							),
							$this->Xhtml->editLink(
								'Éditer le CER ',
								array( 'controller' => 'contratsinsertion', 'action' => 'edit', $contratinsertion['Contratinsertion']['id'] ),
									$this->Permissions->checkDossier( 'contratsinsertion', 'edit', $dossierMenu ) && $block && !$contratenep
							),
							$this->Xhtml->printLink(
								'Imprimer le CER',
								array( 'controller' => 'contratsinsertion', 'action' => 'impression', $contratinsertion['Contratinsertion']['id'] ),
								$this->Permissions->checkDossier( 'contratsinsertion', 'impression', $dossierMenu )
							),
							$this->Xhtml->deleteLink(
								'Supprimer le CER ',
								array( 'controller' => 'contratsinsertion', 'action' => 'delete', $contratinsertion['Contratinsertion']['id'] ),
								$this->Permissions->checkDossier( 'contratsinsertion', 'delete', $dossierMenu ) && !$contratenep
							),
							$this->Xhtml->saisineEpLink(
								'Signalement',
								array( 'controller' => 'signalementseps', 'action' => 'add', $contratinsertion['Contratinsertion']['id'] ),
								$this->Permissions->checkDossier( 'signalementseps', 'add', $dossierMenu )
								&& $enCours
								&& !$block
								&& ( $contratinsertion['Contratinsertion']['forme_ci'] == 'S' )
								&& ( !isset( $signalementseps93 ) || empty( $signalementseps93 ) )
								&& empty( $erreursCandidatePassage )
								&& !$contratenep
							),
							$this->Xhtml->fileLink(
								'Fichiers liés',
								array( 'controller' => 'contratsinsertion', 'action' => 'filelink', $contratinsertion['Contratinsertion']['id'] ),
								$this->Permissions->checkDossier( 'contratsinsertion', 'filelink', $dossierMenu )
							),
							h( '('.Set::classicExtract( $contratinsertion, 'Fichiermodule.nb_fichiers_lies' ).')' )
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			<?php endforeach;?>
		</tbody>
	</table>
<?php  endif;?>