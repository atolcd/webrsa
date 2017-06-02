<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	$domain = 'propopdo';
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'propopdo', "Criterespdos::{$this->action}" )
	)
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'PropopdoDatereceptionpdo', $( 'PropopdoDatereceptionpdoFromDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'DecisionpropopdoDatedecisionpdo', $( 'DecisionpropopdoDatedecisionpdoFromDay' ).up( 'fieldset' ), false );
	});
</script>
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

	echo $this->Xform->create( 'Criterespdos', array( 'type' => 'post', 'action' => '/index/', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );
?>
<?php
	echo $this->Search->blocAllocataire();
	echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
?>
<fieldset>
	<legend>Recherche par dossier</legend>
	<?php
		echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de demande RSA' ) );
		echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15 ) );

		$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
		echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
		echo $this->Search->etatdosrsa($etatdosrsa);
	?>
</fieldset>
<fieldset>
	<legend>Recherche par date de décision</legend>
		<?php echo $this->Form->input( 'Propopdo.datereceptionpdo', array( 'label' => 'Filtrer par date de réception de la PDO', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date de proposition de la décision PDO</legend>
			<?php
				$datereceptionpdo_from = Set::check( $this->request->data, 'Propopdo.datereceptionpdo_from' ) ? Set::extract( $this->request->data, 'Propopdo.datereceptionpdo_from' ) : strtotime( '-1 week' );
				$datereceptionpdo_to = Set::check( $this->request->data, 'Propopdo.datereceptionpdo_to' ) ? Set::extract( $this->request->data, 'Propopdo.datereceptionpdo_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Form->input( 'Propopdo.datereceptionpdo_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datereceptionpdo_from ) );?>
			<?php echo $this->Form->input( 'Propopdo.datereceptionpdo_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'maxYear' => date( 'Y' ) + 5,  'selected' => $datereceptionpdo_to ) );?>
		</fieldset>
</fieldset>
<fieldset>
	<legend>Recherche par date de réception</legend>
		<?php echo $this->Form->input( 'Decisionpropopdo.datedecisionpdo', array( 'label' => 'Filtrer par date de décision de la PDO', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date de proposition de la décision PDO</legend>
			<?php
				$datedecisionpdo_from = Set::check( $this->request->data, 'Decisionpropopdo.datedecisionpdo_from' ) ? Set::extract( $this->request->data, 'Decisionpropopdo.datedecisionpdo_from' ) : strtotime( '-1 week' );
				$datedecisionpdo_to = Set::check( $this->request->data, 'Decisionpropopdo.datedecisionpdo_to' ) ? Set::extract( $this->request->data, 'Decisionpropopdo.datedecisionpdo_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Form->input( 'Decisionpropopdo.datedecisionpdo_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datedecisionpdo_from ) );?>
			<?php echo $this->Form->input( 'Decisionpropopdo.datedecisionpdo_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datereceptionpdo_to ) );?>
		</fieldset>
</fieldset>
	<?php
		$valueTraitementEncours = isset( $this->request->data['Propopdo']['traitementencours'] ) ? $this->request->data['Propopdo']['traitementencours'] : true;
		echo $this->Form->input( 'Propopdo.traitementencours', array( 'label' => 'Uniquement les PDOs possédant un traitement avec une date d\'échéance', 'type' => 'checkbox' ) );

		///Formulaire de recherche pour les PDOs
		echo $this->Default2->subform(
			array(
				'Propopdo.originepdo_id' => array( 'label' => __d( 'propopdo', 'Propopdo.originepdo_id' ), 'type' => 'select', 'options' => $originepdo, 'empty' => true ),
				'Propopdo.etatdossierpdo' => array( 'label' => __d( 'propopdo', 'Propopdo.etatdossierpdo' ), 'type' => 'select', 'options' => $options['etatdossierpdo'], 'empty' => true ),
				'Decisionpropopdo.decisionpdo_id' => array( 'label' => __d( 'decisionpropopdo', 'Decisionpropopdo.decisionpdo_id' ), 'type' => 'select', 'options' => $decisionpdo, 'empty' => true ),
				'Propopdo.user_id' => array( 'label' => __d( 'propopdo', 'Propopdo.user_id' ), 'type' => 'select', 'options' => $gestionnaire, 'empty' => true ),
				'Propopdo.motifpdo' => array( 'label' => __d( 'propopdo', 'Propopdo.motifpdo' ), 'type' => 'select', 'options' => $motifpdo, 'empty' => true  )
			),
			array(
				'form' => false,
				'options' => $options
			)
		);

	?>
	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		echo $this->Search->paginationNombretotal();
	?>
<?php echo $this->Xform->end( 'Rechercher' ); ?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'Propopdo', $this->passedArgs ); ?>

	<?php if( isset( $criterespdos ) ):?>
	<br />
	<h2 class="noprint aere">Résultats de la recherche</h2>
	<?php
		$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';
	?>

	<?php if( is_array( $criterespdos ) && count( $criterespdos ) > 0  ):?>
		<?php echo $pagination;?>
		<table class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° dossier', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom du demandeur', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Proposition de décision', 'Decisionpropopdo.decisionpdo_id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Origine de la PDO', 'Propopdo.originepdo_id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Motif de la PDO', 'Propopdo.motifpdo' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date du contrat', 'Propopdo.datereceptionpdo' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Gestionnaire', 'Propopdo.user_id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Etat du dossier', 'Propopdo.etatdossierpdo' );?></th>
					<th class="action">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $criterespdos as $index => $criterepdo ) {
						$innerTable = '<table id="innerTable'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>Etat du droit</th>
									<td>'.h( Set::enum( Set::classicExtract( $criterepdo, 'Situationdossierrsa.etatdosrsa' ),$criterepdo ) ).'</td>
								</tr>
								<tr>
									<th>Commune de naissance</th>
									<td>'.h( $criterepdo['Personne']['nomcomnai'] ).'</td>
								</tr>
								<tr>
									<th>Date de naissance</th>
									<td>'.h( date_short( $criterepdo['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>Code INSEE</th>
									<td>'.h( $criterepdo['Adresse']['numcom'] ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $criterepdo['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>N° CAF</th>
									<td>'.h( $criterepdo['Dossier']['matricule'] ).'</td>
								</tr>
								<tr>
									<th>Rôle</th>
									<td>'.h( $rolepers[$criterepdo['Prestation']['rolepers']] ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $criterepdo, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $criterepdo, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';

						echo $this->Xhtml->tableCells(
							array(
								h( Set::classicExtract( $criterepdo, 'Dossier.numdemrsa' ) ),
								h( Set::enum( Set::classicExtract( $criterepdo, 'Personne.qual' ), $qual ).' '.Set::classicExtract( $criterepdo, 'Personne.nom' ).' '.Set::classicExtract( $criterepdo, 'Personne.prenom' ) ),
								h( Set::enum( Set::classicExtract( $criterepdo, 'Decisionpropopdo.decisionpdo_id' ), $decisionpdo ) ),
								h( Set::enum( Set::classicExtract( $criterepdo, 'Propopdo.originepdo_id' ), $originepdo ) ),
								h( Set::enum( Set::classicExtract( $criterepdo, 'Propopdo.motifpdo' ), $motifpdo ) ),
								h( $this->Locale->date( 'Locale->date',  Set::classicExtract( $criterepdo, 'Propopdo.datereceptionpdo' ) ) ),
								h( Set::enum( Set::classicExtract( $criterepdo, 'Propopdo.user_id' ), $gestionnaire ) ),
								h( Set::enum( Set::classicExtract( $criterepdo, 'Propopdo.etatdossierpdo' ), $options['etatdossierpdo'] ) ),
								$this->Xhtml->viewLink(
									'Voir',
									array( 'controller' => 'propospdos', 'action' => 'index', Set::classicExtract( $criterepdo, 'Propopdo.personne_id' ) )
								),
								array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
							),
							array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
							array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
						);
					}
				?>
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
					array( 'controller' => 'criterespdos', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'criterespdos', 'exportcsv' )
				);
			?></li>
		</ul>
	<?php else:?>
		<p class="notice">Vos critères n'ont retourné aucune PDO.</p>
	<?php endif?>
<?php endif?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>