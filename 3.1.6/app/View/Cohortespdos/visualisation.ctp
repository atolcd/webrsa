<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php $this->pageTitle = 'Gestion des PDOs';?>

<h1>Gestion des PDOs</h1>

<?php
	if( isset( $cohortepdo ) ) {
		$pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs );
	}
	else {
		$pagination = '';
	}
?>

<?php require_once( 'filtre.ctp' );?>

<!-- Résultats -->

<?php if( isset( $cohortepdo ) ):?>

	<h2 class="noprint">Résultats de la recherche</h2>

	<?php if( is_array( $cohortepdo ) && count( $cohortepdo ) > 0 ):?>
		<?php echo $this->Form->create( 'GestionPDO', array() );?>
	<?php echo $pagination;?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'N° CAF/MSA', 'Dossier.matricule' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Ville', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de la demande RSA', 'Dossier.dtdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Gestionnaire', 'Propopdo.user_id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Commentaire', 'Propopdo.commentairepdo' );?></th>

					<th class="action">Action</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $cohortepdo as $index => $pdo ):?>
				<?php
					$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>Date naissance</th>
									<td>'.h( date_short( $pdo['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $pdo['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>Code postal</th>
									<td>'.h( $pdo['Adresse']['codepos'] ).'</td>
								</tr>
							</tbody>
						</table>';
						$title = $pdo['Dossier']['numdemrsa'];

					echo $this->Xhtml->tableCells(
						array(
							h( $pdo['Personne']['nom'].' '.$pdo['Personne']['prenom'] ),
							h( Set::extract( $pdo, 'Dossier.matricule' ) ),
							h( Set::extract( $pdo, 'Adresse.nomcom' ) ),
							h( date_short( Set::extract( $pdo, 'Dossier.dtdemrsa' ) ) ),
							h( Set::enum( Set::classicExtract( $pdo, 'Propopdo.user_id' ), $gestionnaire ) ),
							h( Set::classicExtract( $pdo, 'Propopdo.commentairepdo' ) ),
							$this->Xhtml->viewLink(
								'Voir la PDO « '.$title.' »',
								array( 'controller' => 'propospdos', 'action' => 'index', $pdo['Personne']['id'] )
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
					array( 'controller' => 'cohortespdos', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'cohortespdos', 'exportcsv' )
				);
			?></li>
		</ul>
		<?php echo $this->Form->end();?>

	<?php else:?>
		<p class="notice">Aucune PDO dans la cohorte.</p>
	<?php endif?>
<?php endif?>