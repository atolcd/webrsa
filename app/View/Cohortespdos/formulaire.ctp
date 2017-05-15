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

<?php  require_once( 'filtre.ctp' );?>
<!-- Résultats -->

<?php if( isset( $cohortepdo ) ):?>
	<?php echo $this->Html->tag( 'h2', 'Résultats de la recherche' );?>
	<?php if( is_array( $cohortepdo ) && count( $cohortepdo ) > 0 ):?>
		<?php echo $pagination;?>
		<?php echo $this->Form->create( 'Cohortepdo', array() );?>
		<?php
			$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

			echo '<div>';
			echo $this->Form->input( 'Cohortepdo.numcom', array( 'type' => 'hidden', 'id' => 'CohortepdoNumcomptt2' ) );
			echo $this->Form->input( 'Cohortepdo.matricule', array( 'type' => 'hidden', 'id' => 'CohortepdoMatricule2' ) );
			echo $this->Form->input( 'Cohortepdo.nom', array( 'type' => 'hidden', 'id' => 'CohortepdoNom2' ) );
			echo $this->Form->input( 'Cohortepdo.prenom', array( 'type' => 'hidden', 'id' => 'CohortepdoPrenom2' ) );
			echo $this->Form->input( 'Cohortepdo.nomcom', array( 'type' => 'hidden', 'id' => 'CohortepdoLocaadr2' ) );
			echo $this->Form->input( 'Cohortepdo.user_id', array( 'type' => 'hidden', 'id' => 'CohortepdoUserId2' ) );
			echo '</div>';
		?>

		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th>Nom de l'allocataire</th>
					<th>Date de demande RSA</th>
					<th>Adresse</th>
					<th>Gestionnaire</th>
					<th>Commentaires</th>
					<th class="action noprint">Action</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $cohortepdo as $index => $pdo ):?>
				<?php
					$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>N° Dossier</th>
									<td>'.h( $pdo['Dossier']['numdemrsa'] ).'</td>
								</tr>
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
									<th>État du dossier</th>
									<td>'.h( value( $etatdosrsa, Set::classicExtract( $pdo, 'Situationdossierrsa.etatdosrsa' ) ) ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $pdo, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $pdo, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';
					$title = $pdo['Dossier']['numdemrsa'];

					$personne_id = $pdo['Personne']['id'];

					echo $this->Xhtml->tableCells(
						array(
							h( $pdo['Personne']['nom'].' '.$pdo['Personne']['prenom'] ),
							h( date_short( $pdo['Dossier']['dtdemrsa'] ) ),
							h( Set::classicExtract( $pdo, 'Adresse.nomcom' ) ),

							$this->Form->input( 'Propopdo.'.$index.'.dossier_id', array( 'label' => false, 'div' => false, 'value' => $pdo['Dossier']['id'], 'type' => 'hidden' ) ).
							$this->Form->input( 'Propopdo.'.$index.'.personne_id', array( 'label' => false, 'div' => false, 'value' => $personne_id, 'type' => 'hidden' ) ).
							$this->Form->input( 'Propopdo.'.$index.'.id', array( 'label' => false, 'div' => false, 'type' => 'hidden', 'value' => $pdo['Propopdo']['id'] ) ).

							$this->Form->input( 'Propopdo.'.$index.'.user_id', array('label' => false, 'type' => 'select', 'options' => $gestionnaire, 'empty' => true ) ),

							$this->Form->input( 'Propopdo.'.$index.'.commentairepdo', array( 'label' => false, 'type' => 'text', 'rows' => 3 ) ),
							$this->Xhtml->viewLink(
								'Voir le dossier « '.$pdo['Dossier']['numdemrsa'].' »',
								array( 'controller' => 'propospdos', 'action' => 'index', $pdo['Personne']['id'] ),
								true,
								true
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
		<?php echo $this->Form->submit( 'Validation de la liste' );?>
		<?php echo $this->Form->end();?>

	<?php else:?>
		<p class="notice">Aucune PDO dans la cohorte.</p>
	<?php endif?>
<?php endif?>