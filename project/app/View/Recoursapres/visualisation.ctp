<?php
	$this->pageTitle = 'Visualisation des décisions des recours';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1>Décisions des recours</h1>

<?php
	if( isset( $recoursapres ) ) {
		$pagination = $this->Xpaginator->paginationBlock( 'ApreComiteapre', $this->passedArgs );
	}
	else {
		$pagination = '';
	}
?>

<?php require_once  'filtre.ctp' ;?>

<!-- Résultats -->

<?php if( isset( $recoursapres ) ):?>
	<h2 class="noprint">Résultats de la recherche</h2>

	<?php if( is_array( $recoursapres ) && count( $recoursapres ) > 0 ):?>
		<?php echo $this->Form->create( 'RecoursApre', array('novalidate' => true) );?>
	<?php echo $pagination;?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° demande APRE', 'Apre.numeroapre' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Commune de l\'allocataire', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date demande APRE', 'Apre.datedemandeapre' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Décision comité examen', 'ApreComiteapre.decisioncomite' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date décision comité', 'Comiteapre.datecomite' );?></th>
					<th>Demande de recours</th>
					<th>Date recours</th>
					<th>Observations</th>

					<th class="action">Notification Bénéficiaire</th>
					<th class="action">Notification Référent</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $recoursapres as $index => $recours ):?>
				<?php
					$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>Date naissance</th>
									<td>'.h( date_short( $recours['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $recours['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>Code postal</th>
									<td>'.h( $recours['Adresse']['codepos'] ).'</td>
								</tr>
							</tbody>
						</table>';
						$title = $recours['Dossier']['numdemrsa'];

					echo $this->Xhtml->tableCells(
						array(
							h( Set::classicExtract( $recours, 'Apre.numeroapre' ) ),
							h( Set::classicExtract( $recours, 'Personne.qual' ).' '.Set::classicExtract( $recours, 'Personne.nom' ).' '.Set::classicExtract( $recours, 'Personne.prenom' ) ),
							h( Set::classicExtract( $recours, 'Adresse.nomcom' ) ),
							h( $this->Locale->date( 'Date::short', Set::classicExtract( $recours, 'Apre.datedemandeapre' ) ) ),
							h( Set::enum( Set::classicExtract( $recours, 'ApreComiteapre.decisioncomite' ), $options['decisioncomite'] ) ),
							h( $this->Locale->date( 'Date::short', Set::classicExtract( $recours, 'Comiteapre.datecomite' ) ) ),
							h( Set::enum( Set::classicExtract( $recours, 'ApreComiteapre.recoursapre' ), $options['recoursapre'] ) ),
							h( $this->Locale->date( 'Date::short', Set::classicExtract( $recours, 'ApreComiteapre.daterecours' ) ) ),
							h( Set::classicExtract( $recours, 'ApreComiteapre.observationrecours' ) ),
							$this->Xhtml->printLink(
								'Imprimer pour le bénéficiaire',
								array( 'controller' => 'recoursapres', 'action' => 'impression', Set::classicExtract( $recours, 'ApreComiteapre.apre_id' ), 'dest' => 'beneficiaire' ),
								$this->Permissions->check( 'recoursapres', 'impression' )
							),
							$this->Xhtml->printLink(
								'Imprimer pour le référent',
								array( 'controller' => 'recoursapres', 'action' => 'impression', Set::classicExtract( $recours, 'ApreComiteapre.apre_id' ), 'dest' => 'referent' ),
								$this->Permissions->check( 'recoursapres', 'impression' )
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
					array( 'controller' => 'recoursapres', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' )
				);
			?></li>
		</ul>
		<?php echo $this->Form->end();?>

	<?php else:?>
		<p class="notice">Aucune demande de recours présente.</p>
	<?php endif?>
<?php endif?>