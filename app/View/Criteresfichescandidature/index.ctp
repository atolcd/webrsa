<?php
	$this->pageTitle = 'Recherche par Fiches de candidature';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
	).'</li></ul>';
?>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'ActioncandidatPersonneDatesignature', $( 'ActioncandidatPersonneDatesignatureFromDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'DossierDtdemrsa', $( 'DossierDtdemrsaFromDay' ).up( 'fieldset' ), false );
	});
</script>


<script type="text/javascript">
	document.observe("dom:loaded", function() {
    // On affiche le fieldset avec les programmes type région
    // uniquement si l'ID est celui paramétré dans le webrsa.inc
    observeDisableFieldsetOnValue(
        'PartenaireLibstruc',
        $( 'blocregion' ),
        ['<?php echo implode( "', '", Configure::read( "ActioncandidatPersonne.Partenaire.id" ) );?>'],
        false,
        true
    );


		dependantSelect( 'ActioncandidatPersonneActioncandidatId', 'PartenaireLibstruc' );
	});
</script>

<?php echo $this->Xform->create( 'Criterefichecandidature', array( 'type' => 'post', 'action' => $this->action, 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>

	<?php echo $this->Xform->input( 'ActioncandidatPersonne.indexparams', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
	<?php
		echo $this->Search->blocAllocataire( );
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
		<?php echo $this->Xform->input( 'Dossier.dtdemrsa', array( 'label' => 'Filtrer par date de demande RSA', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Filtrer par période</legend>
			<?php
				$dtdemrsa_from = Set::check( $this->request->data, 'Dossier.dtdemrsa_from' ) ? Set::extract( $this->request->data, 'Dossier.dtdemrsa_from' ) : strtotime( '-1 week' );
				$dtdemrsa_to = Set::check( $this->request->data, 'Dossier.dtdemrsa_to' ) ? Set::extract( $this->request->data, 'Dossier.dtdemrsa_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Xform->input( 'Dossier.dtdemrsa_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $dtdemrsa_from ) );?>
			<?php echo $this->Xform->input( 'Dossier.dtdemrsa_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $dtdemrsa_to ) );?>
		</fieldset>

	</fieldset>

	<fieldset>
		<legend>Filtrer par Fiche de candidature</legend>
		<?php

			echo $this->Default2->subform(
				array(
					'Partenaire.libstruc' => array( 'type' => 'select', 'options' => $partenaires ),
					'ActioncandidatPersonne.actioncandidat_id' => array( 'type' => 'select', 'options' => $listeactions ),
					'ActioncandidatPersonne.referent_id' => array( 'type' => 'select', 'options' => $referents ),
					'ActioncandidatPersonne.positionfiche' => array( 'type' => 'select', 'options' => $options['positionfiche'] ),
				),
				array(
					'options' => $options
				)
			);

            echo '<fieldset id="blocregion" class="noborder">';
            echo $this->Default2->subform(
				array(
                    'ActioncandidatPersonne.formationregion' => array( 'label' => 'Nom de la formation', 'type' => 'text' ),
					'Progfichecandidature66.id' => array( 'label' => 'Nom du(des) programme(s)', 'type' => 'select', 'multiple' => 'checkbox', 'empty' => false, 'options' => $progsfichescandidatures66 )
				),
				array(
					'options' => $options
				)
			);
            echo '</fieldset>';

		?>

		<?php echo $this->Xform->input( 'ActioncandidatPersonne.datesignature', array( 'label' => 'Filtrer par date de Fiche de candidature', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Filtrer par période</legend>
			<?php
				$datesignature_from = Set::check( $this->request->data, 'ActioncandidatPersonne.datesignature_from' ) ? Set::extract( $this->request->data, 'ActioncandidatPersonne.datesignature_from' ) : strtotime( '-1 week' );
				$datesignature_to = Set::check( $this->request->data, 'ActioncandidatPersonne.datesignature_to' ) ? Set::extract( $this->request->data, 'ActioncandidatPersonne.datesignature_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Xform->input( 'ActioncandidatPersonne.datesignature_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $datesignature_from ) );?>
			<?php echo $this->Xform->input( 'ActioncandidatPersonne.datesignature_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $datesignature_to ) );?>
		</fieldset>
	</fieldset>

	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		echo $this->Search->paginationNombretotal();
	?>

	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>

<?php echo $this->Xform->end();?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'ActioncandidatPersonne', $this->passedArgs ); ?>
<?php if ($pagination):?>
<h2 class="noprint">Résultats de la recherche</h2>
<?php echo $pagination;?>
<?php endif;?>
<?php if( isset( $actionscandidats_personnes ) ):?>
	<?php if( is_array( $actionscandidats_personnes ) && count( $actionscandidats_personnes ) > 0  ):?>
		<?php
			echo '<table id="searchResults" class="tooltips"><thead>';
				echo '<tr>
					<th>'.$this->Xpaginator->sort( __d( 'actioncandidat_personne', 'ActioncandidatPersonne.actioncandidat_id' ), 'ActioncandidatPersonne.actioncandidat_id' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'partenaire', 'Partenaire.libstruc' ), 'Partenaire.libstruc' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'personne', 'Personne.nom_complet' ), 'Personne.nom_complet' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'referent', 'Referent.nom_complet' ), 'Referent.nom_complet' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'actioncandidat_personne', 'ActioncandidatPersonne.positionfiche' ), 'ActioncandidatPersonne.positionfiche' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'actioncandidat_personne', 'ActioncandidatPersonne.datesignature' ), 'ActioncandidatPersonne.datesignature' ).'</th>
					<th>Actions</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr></thead><tbody>';
			foreach( $actionscandidats_personnes as $index => $actioncandidat_personne ) {
				$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>Code INSEE</th>
							<td>'.$actioncandidat_personne['Adresse']['numcom'].'</td>
						</tr>
						<tr>
							<th>Localité</th>
							<td>'.$actioncandidat_personne['Adresse']['nomcom'].'</td>
						</tr>
						<tr>
							<th>'.__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ).'</th>
							<td>'.Hash::get( $actioncandidat_personne, 'Structurereferenteparcours.lib_struc' ).'</td>
						</tr>
						<tr>
							<th>'.__d( 'search_plugin', 'Referentparcours.nom_complet' ).'</th>
							<td>'.Hash::get( $actioncandidat_personne, 'Referentparcours.nom_complet' ).'</td>
						</tr>
					</tbody>
				</table>';

				echo '<tr>
					<td>'.h( $actioncandidat_personne['Actioncandidat']['name'] ).'</td>
					<td>'.h( $actioncandidat_personne['Partenaire']['libstruc'] ).'</td>
					<td>'.h( $actioncandidat_personne['Personne']['nom_complet'] ).'</td>
					<td>'.h( $actioncandidat_personne['Referent']['nom_complet'] ).'</td>
					<td>'.h( Set::enum( Set::classicExtract( $actioncandidat_personne, 'ActioncandidatPersonne.positionfiche' ),  $options['positionfiche'] ) ).'</td>',
					'<td>'.h( date_short( $actioncandidat_personne['ActioncandidatPersonne']['datesignature'] ) ).'</td>',
					'<td>'.$this->Xhtml->link( 'Voir', array( 'controller' => 'actionscandidats_personnes', 'action' => 'index', $actioncandidat_personne['ActioncandidatPersonne']['personne_id'] ) ).'</td>
					<td class="innerTableCell noprint">'.$innerTable.'</td>
				</tr>';
			}
			echo '</tbody></table>';
	?>
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
				array( 'controller' => 'criteresfichescandidature', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
				$this->Permissions->check( 'criteresfichescandidature', 'exportcsv' )
			);
		?></li>
	</ul>
<?php echo $pagination;?>

	<?php else:?>
		<?php echo $this->Xhtml->tag( 'p', 'Aucun résultat ne correspond aux critères choisis.', array( 'class' => 'notice' ) );?>
	<?php endif;?>
<?php endif;?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>