<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php $this->pageTitle = 'Notifications des décisions des comités';?>

<h1>Notification des Comités</h1>

<?php
	if( isset( $comitesapres ) ) {
		$pagination = $this->Xpaginator->paginationBlock( 'Comiteapre', $this->passedArgs );
	}
	else {
		$pagination = '';
	}
?>

<?php require_once  'filtre.ctp' ;?>

<!-- Résultats -->

<?php if( isset( $comitesapres ) ):?>

	<h2 class="noprint">Résultats de la recherche</h2>

	<?php if( is_array( $comitesapres ) && count( $comitesapres ) > 0 ):?>
		<?php echo $this->Form->create( 'NotifComite', array( 'novalidate' => true ) );?>
	<?php echo $pagination;?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° demande RSA', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Commune de l\'allocataire', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de demande APRE', 'Apre.datedemandeapre' );?></th>
					<th>Décision comité examen</th>
					<th><?php echo $this->Xpaginator->sort( 'Date de décision comité', 'Comiteapre.datecomite' );?></th>
					<th>Montant attribué</th>
					<th>Observations</th>

					<th class="action">Notification Bénéficiaire</th>
					<th class="action">Notification Référent</th>
					<th class="action">Notification Tiers prestataire</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $comitesapres as $index => $comite ):?>
				<?php
					$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>Date naissance</th>
									<td>'.h( date_short( $comite['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $comite['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>Code postal</th>
									<td>'.h( $comite['Adresse']['codepos'] ).'</td>
								</tr>
							</tbody>
						</table>';
					$title = $comite['Dossier']['numdemrsa'];

				///FIXME:::::Doublon avec le contrôleur cohortecomiteapre
					//Pour masquer les champs imprimer en cas de Non accord
					$typedecision = ( Set::enum( Set::classicExtract( $comite, 'ApreComiteapre.decisioncomite' ), $options['decisioncomite'] ) );

					//Pour masquer les champs imprimer en cas de HorsFormation
					$modelFormation = array( 'Formqualif', 'Formpermfimo', 'Permisb', 'Actprof' );
					foreach( $comite['Apre']['Natureaide'] as $model => $value ) {
						if( $value == 0 ){
							unset( $comite['Apre']['Natureaide'][$model] );
						}
					}

					if( array_any_key_exists( $modelFormation, $comite['Apre']['Natureaide'] ) ) {
						$typeformation = 'Formation';
					}
					else {
						$typeformation = 'HorsFormation';
					}

					$isTiers = false;
					if( $typedecision == 'Accord' && $typeformation == 'Formation' ) {
						$isTiers = true;
					}

					$apreComiteapreId = Set::classicExtract( $comite, 'ApreComiteapre.id' );

					echo $this->Xhtml->tableCells(
						array(
							h( Set::classicExtract( $comite, 'Dossier.numdemrsa' ) ),
							h( Set::classicExtract( $comite, 'Personne.qual' ).' '.Set::classicExtract( $comite, 'Personne.nom' ).' '.Set::classicExtract( $comite, 'Personne.prenom' ) ),
							h( Set::classicExtract( $comite, 'Adresse.nomcom' ) ),
							h( $this->Locale->date( 'Date::short', Set::classicExtract( $comite, 'Apre.datedemandeapre' ) ) ),
							h( Set::enum( Set::classicExtract( $comite, 'ApreComiteapre.decisioncomite' ), $options['decisioncomite'] ) ),
							h( $this->Locale->date( 'Date::short', Set::classicExtract( $comite, 'Comiteapre.datecomite' ) ) ),
							h( Set::classicExtract( $comite, 'ApreComiteapre.montantattribue' ) ),
							h( Set::classicExtract( $comite, 'ApreComiteapre.observationcomite' ) ),
							$this->Xhtml->printLink(
								'Imprimer pour le bénéficiaire',
								array( 'controller' => 'cohortescomitesapres', 'action' => 'impression', $apreComiteapreId, 'dest' => 'beneficiaire' ),
								$this->Permissions->check( 'cohortescomitesapres', 'impression' )
							),
							$this->Xhtml->printLink(
								'Imprimer pour le référent',
								array( 'controller' => 'cohortescomitesapres', 'action' => 'impression', $apreComiteapreId, 'dest' => 'referent' ),
								$this->Permissions->check( 'cohortescomitesapres', 'impression' )
							),
							$this->Xhtml->printLink(
								'Imprimer pour le tiers prestataire',
								array( 'controller' => 'cohortescomitesapres', 'action' => 'impression', $apreComiteapreId, 'dest' => 'tiers' ),
								$isTiers,
								$this->Permissions->check( 'cohortescomitesapres', 'impression' )
							),
							array( $innerTable, array( 'class' => 'innerTableCell' ) )
						),
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
					array( 'onclick' => 'printit(); return false;' )
				);
			?></li>

			<li><?php
				echo $this->Xhtml->exportLink(
					'Télécharger le tableau',
					array( 'controller' => 'cohortescomitesapres', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'cohortescomitesapres', 'exportcsv' )
				);
			?></li>
		</ul>
		<?php echo $this->Form->end();?>

	<?php else:?>
		<p>Aucune notification présente dans la cohorte.</p>
	<?php endif?>
<?php endif?>