<?php
	$this->pageTitle = 'Recherche par Orientation';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	$departement = Configure::read( 'Cg.departement' );
?>
<h1><?php echo $this->pageTitle; ?></h1>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'OrientstructDateValid', $( 'OrientstructDateValidFromDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'DossierDtdemrsa', $( 'DossierDtdemrsaFromDay' ).up( 'fieldset' ), false );
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
?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'Orientstruct', $this->passedArgs );?>
<?php echo $this->Form->create( 'Critere', array( 'type' => 'post', 'action' => '/index/', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
	<fieldset>
		<legend>Recherche par dossier</legend>
		<?php echo $this->Form->input( 'Critere.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
		<?php //echo $this->Form->input( 'Critere.etatdosrsa', array( 'label' => 'Situation dossier rsa', 'type' => 'select', 'options' => $etatdosrsa, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de dossier RSA', 'maxlength' => 15 ) );?>
		<?php echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule.large' ) ) );?>
		<?php
			echo $this->Search->natpf( $natpf );
// 			echo $this->Form->input( 'Detailcalculdroitrsa.natpf', array( 'label' => 'Nature de la prestation', 'type' => 'select', 'options' => $natpf, 'empty' => true ) );
		?>
		<?php echo $this->Form->input( 'Dossier.dtdemrsa', array( 'label' => 'Filtrer par date d\'ouverture de droit', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date de demande RSA</legend>
			<?php
				$dtdemrsa_from = Set::check( $this->request->data, 'Dossier.dtdemrsa_from' ) ? Set::extract( $this->request->data, 'Dossier.dtdemrsa_from' ) : strtotime( '-1 week' );
				$dtdemrsa_to = Set::check( $this->request->data, 'Dossier.dtdemrsa_to' ) ? Set::extract( $this->request->data, 'Dossier.dtdemrsa_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Form->input( 'Dossier.dtdemrsa_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $dtdemrsa_from ) );?>
			<?php echo $this->Form->input( 'Dossier.dtdemrsa_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120,  'maxYear' => date( 'Y' ) + 5, 'selected' => $dtdemrsa_to ) );?>
		</fieldset>
		<?php
			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
		?>
		<?php echo $this->Search->etatdosrsa($etatdosrsa); ?>
	</fieldset>
	<?php
		echo $this->Search->blocAllocataire(  );
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
	?>

	<fieldset>
		<legend>Recherche par parcours allocataire</legend>
		<?php
			echo $this->Form->input( 'Historiqueetatpe.identifiantpe', array( 'label' => 'Identifiant Pôle Emploi ', 'type' => 'text', 'maxlength' => 11 ) );
			echo $this->Form->input( 'Critere.hascontrat', array( 'label' => 'Possède un CER ? ', 'type' => 'select', 'options' => array( 'O' => 'Oui', 'N' => 'Non'), 'empty' => true ) );
			echo $this->Form->input( 'Critere.hasreferent', array( 'label' => 'Possède un référent ? ', 'type' => 'select', 'options' => array( 'O' => 'Oui', 'N' => 'Non'), 'empty' => true ) );
			echo $this->Form->input( 'Critere.isinscritpe', array( 'label' => 'Inscrit au Pôle Emploi ? ', 'type' => 'select', 'options' => array( 'O' => 'Oui', 'N' => 'Non'), 'empty' => true ) );
			if( $departement == 58 ) {
				echo $this->Xform->input( 'Activite.act', array( 'label' => 'Code activité', 'type' => 'select', 'empty' => true, 'options' => $act ) );
			}
		?>
	</fieldset>
	<fieldset>
		<legend>Recherche par orientation</legend>
		<?php
			$valueOrientstructDerniere = isset( $this->request->data['Orientstruct']['derniere'] ) ? $this->request->data['Orientstruct']['derniere'] : false;
			echo $this->Form->input( 'Orientstruct.derniere', array( 'label' => 'Uniquement la dernière orientation pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueOrientstructDerniere ) );
		?>
		<?php echo $this->Form->input( 'Orientstruct.date_valid', array( 'label' => 'Filtrer par date d\'orientation', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Date d'orientation</legend>
				<?php
					$date_valid_from = Set::check( $this->request->data, 'Orientstruct.date_valid_from' ) ? Set::extract( $this->request->data, 'Orientstruct.date_valid_from' ) : strtotime( '-1 week' );
					$date_valid_to = Set::check( $this->request->data, 'Orientstruct.date_valid_to' ) ? Set::extract( $this->request->data, 'Orientstruct.date_valid_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Form->input( 'Orientstruct.date_valid_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $date_valid_from ) );?>
				<?php echo $this->Form->input( 'Orientstruct.date_valid_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120,  'maxYear' => date( 'Y' ) + 5, 'selected' => $date_valid_to ) );?>
			</fieldset>

	<?php if( $departement == 66 ):?>
		<fieldset><legend>Orienté par</legend>
			<script type="text/javascript">
				document.observe("dom:loaded", function() {
					dependantSelect( 'OrientstructReferentorientantId', 'OrientstructStructureorientanteId' );
				});
			</script>

			<?php

				echo $this->Form->input( 'Orientstruct.structureorientante_id', array( 'label' => 'Structure', 'type' => 'select', 'options' => $structsorientantes, 'empty' => true ) );
				echo $this->Form->input( 'Orientstruct.referentorientant_id', array(  'label' => 'Nom du professionnel', 'type' => 'select', 'options' => $refsorientants,  'empty' => true ) );
			?>
		</fieldset>
	<?php endif;?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		dependantSelect( 'OrientstructStructurereferenteId', 'OrientstructTypeorientId' );
	});
</script>

		<?php
			if( $departement == 93 ) {
				echo $this->Form->input( 'Orientstruct.origine', array( 'label' => __d( 'orientstruct', 'Orientstruct.origine' ), 'type' => 'select', 'options' => $options['Orientstruct']['origine'], 'empty' => true ) );
			}
		?>

		<?php echo $this->Form->input( 'Orientstruct.typeorient_id', array( 'label' =>  __d( 'structurereferente', 'Structurereferente.lib_type_orient' ), 'type' => 'select' , 'options' => $typeorient, 'empty' => true ) );?>

		<?php echo $this->Form->input( 'Orientstruct.structurereferente_id', array( 'label' => ( $departement == 93 ? 'Structure référente' : 'Nom de la structure' ), 'type' => 'select' , 'options' => $sr, 'empty' => true  ) );?>

		<?php echo $this->Form->input( 'Orientstruct.statut_orient', array( 'label' => 'Statut de l\'orientation', 'type' => 'select', 'options' => $statuts, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Orientstruct.serviceinstructeur_id', array( 'label' => __( 'lib_service' ), 'type' => 'select' , 'options' => $typeservice, 'empty' => true ) );?>
	</fieldset>

	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		echo $this->Search->paginationNombretotal();
	?>

	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>

<!-- Résultats -->
<?php if( isset( $orients ) ):?>

	<h2 class="noprint">Résultats de la recherche</h2>
	<?php
		$domain_search_plugin = ( $departement == 93 ) ? 'search_plugin_93' : 'search_plugin';
	?>

	<?php if( is_array( $orients ) && count( $orients ) > 0  ):?>

		<?php echo $pagination;?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'Numéro dossier', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Allocataire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Commune', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date d\'ouverture droits', 'Dossier.dtdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date d\'orientation', 'Orientstruct.date_valid' );?></th>
					<?php if( $departement == 93 ):?>
						<th><?php echo $this->Xpaginator->sort( 'Préconisation d\'orientation', 'Orientstruct.propo_algo' );?></th>
						<th><?php echo $this->Xpaginator->sort( __d( 'orientstruct', 'Orientstruct.origine' ), 'Orientstruct.origine' );?></th>
					<?php endif;?>
					<th><?php echo $this->Xpaginator->sort( 'Type d\'orientation', 'Orientstruct.typeorient_id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Structure référente', 'Structurereferente.lib_struc' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Statut orientation', 'Orientstruct.statut_orient' );?></th>
					<?php if( $reorientationEp ):?>
						<th><?php echo $this->Xpaginator->sort( 'Date de passage en EP', 'Commissionep.dateseance' );?></th>
						<th>Décision EP</th>
					<?php endif;?>
					<th><?php echo $this->Xpaginator->sort( 'Soumis à droits et devoirs', 'Calculdroitrsa.toppersdrodevorsa' );?></th>
					<th class="action noprint">Actions</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $orients as $index => $orient ):?>
					<?php
						$activite = '';
						if( $departement == 58 ) {
							$activite = '<tr>
								<th>Code activité</th>
								<td>'.value( $act, Hash::get( $orient, 'Activite.act' ) ).'</td>
							</tr>';
						}

						$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>Etat du droit</th>
									<td>'.Set::classicExtract( $etatdosrsa, Set::classicExtract( $orient, 'Situationdossierrsa.etatdosrsa' ) ).'</td>
								</tr>
								<tr>
									<th>Commune de naissance</th>
									<td>'. $orient['Personne']['nomcomnai'].'</td>
								</tr>
								<tr>
									<th>Date de naissance</th>
									<td>'.date_short( $orient['Personne']['dtnai']).'</td>
								</tr>
								<tr>
									<th>Code INSEE</th>
									<td>'.$orient['Adresse']['numcom'].'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.$orient['Personne']['nir'].'</td>
								</tr>
								<tr>
									<th>Identifiant Pôle Emploi</th>
									<td>'.$orient['Historiqueetatpe']['identifiantpe'].'</td>
								</tr>
								<tr>
									<th>N° Téléphone</th>
									<td>'.h( $orient['Modecontact']['numtel'] ).'</td>
								</tr>
								<tr>
									<th>Rôle</th>
									<td>'.Set::enum( $orient['Prestation']['rolepers'], $rolepers ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $orient, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $orient, 'Referentparcours.nom_complet' ).'</td>
								</tr>
								'.$activite.'
							</tbody>
						</table>';

                        $adresseCanton = $orient['Adresse']['nomcom']."- \n".$orient['Canton']['canton'];


						$cells = array(
							h( $orient['Dossier']['numdemrsa'] ),
							h( $orient['Personne']['qual'].' '.$orient['Personne']['nom'].' '.$orient['Personne']['prenom'] ),
							nl2br( h( $adresseCanton ) ),
							h( date_short( $orient['Dossier']['dtdemrsa'] ) ),
							h( date_short( $orient['Orientstruct']['date_valid'] ) )
						);

						if( $departement == 93 ) {
							$cells[] = h( Set::enum( $orient['Orientstruct']['propo_algo'], $typeorient ) );
							$cells[] = h( Set::enum( $orient['Orientstruct']['origine'], $options['Orientstruct']['origine'] ) );
						}

						array_push(
							$cells,
							h( $orient['Typeorient']['lib_type_orient'] ),
							h( $orient['Structurereferente']['lib_struc'] ),
							h( $orient['Orientstruct']['statut_orient'] )
						);

						if( $reorientationEp ) {
							if( !empty( $orient['Dossierep']['themeep'] ) ) {
								$modeleDecision = 'Decision'.Inflector::underscore( Inflector::classify( $orient['Dossierep']['themeep'] ) );
								$decision = value( $enums[$modeleDecision]['decision'], Hash::get( $orient, "{$modeleDecision}.decision" ) );
							}
							else {
								$decision = null;
							}

							array_push(
								$cells,
								h( date_short( $orient['Commissionep']['dateseance'] ) ),
								h( $decision )
							);
						}

						array_push(
							$cells,
							( is_null( $orient['Calculdroitrsa']['toppersdrodevorsa'] ) ? $this->Xhtml->image( 'icons/help.png', array( 'alt' => '' ) ).' Non défini' : $this->Xhtml->boolean( $orient['Calculdroitrsa']['toppersdrodevorsa'] ) ),
							array(
								$this->Xhtml->viewLink(
									'Voir le dossier « '.$orient['Dossier']['numdemrsa'].' »',
									array( 'controller' => 'orientsstructs', 'action' => 'index', $orient['Personne']['id'] ),
									$this->Permissions->check( 'orientsstructs', 'index' ) && !Hash::get( $orient, 'Orientstruct.horszone' )
								),
								array( 'class' => 'noprint' )
							),
							array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
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

		<?php if( Set::extract( $this->request->params, 'paging.Orientstruct.count' ) > 65000 ):?>
			<p class="noprint" style="border: 1px solid #556; background: #ffe;padding: 0.5em;"><?php echo $this->Xhtml->image( 'icons/error.png' );?> <strong>Attention</strong>, il est possible que votre tableur ne puisse pas vous afficher les résultats au-delà de la 65&nbsp;000ème ligne.</p>
		<?php endif;?>
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
					array( 'controller' => 'criteres', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'criteres', 'exportcsv' )
				);
			?></li>
		</ul>
		<?php echo $pagination;?>
	<?php else:?>
		<p>Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>
<?php endif?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>