<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<?php $this->pageTitle = 'Gestion des PDOs';?>

<h1>Gestion des PDOs</h1>

<?php
	if( is_array( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
		).'</li></ul>';
	}

	if( isset( $cohortepdo ) ) {
		$pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs );
	}
	else {
		$pagination = '';
	}
?>

<?php echo $this->Form->create( 'Cohortepdo', array( 'type' => 'post', 'action' => '/index/', 'id' => 'Search', 'class' => ( ( !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
	<fieldset>
		<legend>Recherche PDO</legend>
		<?php echo $this->Form->input( 'Cohortepdo.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
		<?php echo $this->Form->input( 'Cohortepdo.numcom', array( 'label' => 'Numéro de commune au sens INSEE', 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Cohortepdo.typepdo_id', array( 'label' => __d( 'propopdo', 'Propopdo.typepdo' ), 'type' => 'select', 'options' => $typepdo, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Cohortepdo.decisionpdo_id', array( 'label' => __d( 'propopdo', 'Propopdo.decisionpdo' ), 'type' => 'select', 'options' => $decisionpdo, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Cohortepdo.datedecisionpdo', array( 'label' => __d( 'propopdo', 'Propopdo.datedecisionpdo' ), 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear'=>date('Y'), 'minYear'=>date('Y')-80, 'empty' => true ) );?>
	</fieldset>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Filtrer', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>

<!-- Résultats -->

<?php if( isset( $cohortepdo ) ):?>

	<h2 class="noprint">Résultats de la recherche</h2>

	<?php if( is_array( $cohortepdo ) && count( $cohortepdo ) > 0 ):?>
		<?php echo $this->Form->create( 'GestionPDO', array() );?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° PDO', 'Pdo.id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom'.' '.'Personne.prenom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Suivi', 'Dossier.typeparte' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Situation des droits', 'Situationdossierrsa.etatdosrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Type de PDO', 'Pdo.typepdo_id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de décision PDO', 'Pdo.decisionpdo_id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Décision PDO', 'Pdo.datedecisionpdo' );?></th>
					<th class="action">Action</th>
					<th class="innerTableHeader">Informations complémentaires</th>
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
									<th>' . __d( 'dossier', 'Dossier.matricule.large' ) . '</th>
									<td>'.h( $pdo['Dossier']['matricule'] ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $pdo['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>Code postal</th>
									<td>'.h( $pdo['Adresse']['codepos'] ).'</td>
								</tr>
								<tr>
									<th>'.__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $pdo, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( 'search_plugin', 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $pdo, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';
						$title = $pdo['Dossier']['numdemrsa'];

					$statut_avis = Set::extract( $pdo, 'Pdo.'.$index.'.avisdero' );
					echo $this->Xhtml->tableCells(
						array(
							h( $pdo['Pdo']['id'] ),
							h( $pdo['Personne']['nom'].' '.$pdo['Personne']['prenom'] ),
							h( $pdo['Dossier']['typeparte'] ),
							h( value( $etatdosrsa, Set::extract( $pdo, 'Situationdossierrsa.etatdosrsa' ) ) ),
							h( value( $typepdo, Set::extract( 'Pdo.typepdo_id', $pdo ) ) ),
							h( date_short( Set::extract( 'Pdo.datedecisionpdo', $pdo ) ) ),
							h( value( $decisionpdo, Set::extract( 'Pdo.decisionpdo_id', $pdo ) ) ),

							$this->Xhtml->viewLink(
								'Voir le contrat « '.$title.' »',
								array( 'controller' => 'propospdos', 'action' => 'index', $pdo['Propopdo']['personne_id'] ),
								$this->Permissions->check( 'propospdos', 'index' )
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
		<?php echo $this->Form->submit( 'Validation de la liste' );?>
		<?php echo $this->Form->end();?>

	<?php else:?>
		<p>Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>
<?php endif?>