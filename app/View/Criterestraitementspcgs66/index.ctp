<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	$domain = 'traitementpcg66';
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'traitementpcg66', "Criterestraitementspcgs66::{$this->action}" )
	)
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'Traitementpcg66Dateecheance', $( 'Traitementpcg66DateecheanceFromDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'Traitementpcg66Daterevision', $( 'Traitementpcg66DaterevisionFromDay' ).up( 'fieldset' ), false );
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

	echo $this->Xform->create( 'Criteretraitementpcg66', array( 'type' => 'post', 'action' => 'index', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data['Traitementpcg66']['recherche'] ) ) ? 'folded' : 'unfolded' ) ) );
    echo $this->Form->input( 'Traitementpcg66.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );
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
			echo $this->Search->natpf( $natpf );
		?>
	</fieldset>
<fieldset>
	<legend>Recherche par traitement</legend>
		<?php
//        echo $this->Xform->input( 'Dossierpcg66.user_id', array( 'label' => __d( 'traitementpcg66', 'Dossierpcg66.user_id' ), 'type' => 'select', 'options' => $gestionnaire, 'empty' => true ) );

            echo $this->Default2->subform(
                array(
                    'Dossierpcg66.poledossierpcg66_id' => array( 'label' => __d( 'dossierpcg66', 'Dossierpcg66.poledossierpcg66_id' ), 'type' => 'select', 'multiple' => 'checkbox',  'options' => $polesdossierspcgs66, 'empty' => false )
                ),
                array(
                    'options' => $options
                )
            );
            echo '<fieldset class="col2 noborder">';
            echo $this->Xform->input( 'Dossierpcg66.user_id', array( 'label' => __d( 'dossierpcg66', 'Dossierpcg66.user_id' ), 'type' => 'select', 'multiple' => 'checkbox',  'options' => $gestionnaire, 'empty' => false ) );
            echo '</fieldset>';
            echo $this->Search->date( 'Dossierpcg66.dateaffectation' );
        ?>
		<?php echo $this->Xform->input( 'Traitementpcg66.dateecheance', array( 'label' => 'Filtrer par date d\'échéance du traitement', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date d'échéance du traitement</legend>
			<?php
				$dateecheance_from = Set::check( $this->request->data, 'Traitementpcg66.dateecheance_from' ) ? Set::extract( $this->request->data, 'Traitementpcg66.dateecheance_from' ) : strtotime( '-1 week' );
				$dateecheance_to = Set::check( $this->request->data, 'Traitementpcg66.dateecheance_to' ) ? Set::extract( $this->request->data, 'Traitementpcg66.dateecheance_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Form->input( 'Traitementpcg66.dateecheance_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 10, 'selected' => $dateecheance_from ) );?>
			<?php echo $this->Form->input( 'Traitementpcg66.dateecheance_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 10, 'maxYear' => date( 'Y' ) + 5,  'selected' => $dateecheance_to ) );?>
		</fieldset>

		<?php echo $this->Xform->input( 'Traitementpcg66.daterevision', array( 'label' => 'Filtrer par date de révision du traitement', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date de révision du traitement</legend>
			<?php
				$daterevision_from = Set::check( $this->request->data, 'Traitementpcg66.daterevision_from' ) ? Set::extract( $this->request->data, 'Traitementpcg66.daterevision_from' ) : strtotime( '-1 week' );
				$daterevision_to = Set::check( $this->request->data, 'Traitementpcg66.daterevision_to' ) ? Set::extract( $this->request->data, 'Traitementpcg66.daterevision_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Form->input( 'Traitementpcg66.daterevision_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 10, 'selected' => $daterevision_from ) );?>
			<?php echo $this->Form->input( 'Traitementpcg66.daterevision_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 10, 'maxYear' => date( 'Y' ) + 5,  'selected' => $daterevision_to ) );?>
		</fieldset>

        <?php
            echo $this->Search->date( 'Traitementpcg66.created', 'Date de création du traitement' );

        ?>
	<?php
		///Formulaire de recherche pour les PDOs
        echo '<fieldset class="col2 noborder">';
        echo $this->Xform->input( 'Traitementpcg66.situationpdo_id', array( 'label' => 'Motif concernant la personne', 'type' => 'select', 'multiple' => 'checkbox', 'options' => $motifpersonnepcg66, 'empty' => false ) );
        echo '</fieldset>';

        echo '<fieldset class="col2 noborder">';
        echo $this->Xform->input( 'Traitementpcg66.statutpdo_id', array( 'label' => 'Statut de la personne', 'type' => 'select', 'multiple' => 'checkbox', 'options' => $statutpersonnepcg66, 'empty' => false ) );
        echo '</fieldset>';

        echo $this->Default2->subform(
            array(
                'Traitementpcg66.descriptionpdo_id' => array( 'label' => __d( 'traitementpcg66', 'Traitementpcg66.descriptionpdo_id' ), 'type' => 'select', 'options' => $descriptionpdo, 'empty' => true ),
                'Traitementpcg66.typetraitement' => array( 'label' => __d( 'traitementpcg66', 'Traitementpcg66.typetraitement' ), 'type' => 'select', 'options' => $options['Traitementpcg66']['typetraitement'], 'empty' => true ),
                'Traitementpcg66.clos' => array( 'label' => __d( 'traitementpcg66', 'Traitementpcg66.clos' ), 'type' => 'select', 'options' => $options['Traitementpcg66']['clos'], 'empty' => true ),
                'Traitementpcg66.annule' => array( 'label' => __d( 'traitementpcg66', 'Traitementpcg66.annule' ), 'type' => 'select', 'options' => $options['Traitementpcg66']['annule'], 'empty' => true ),
                'Traitementpcg66.regime' => array( 'label' => __d( 'traitementpcg66', 'Traitementpcg66.regime' ) ),
                'Traitementpcg66.saisonnier' => array( 'label' => __d( 'traitementpcg66', 'Traitementpcg66.saisonnier' ) ),
                'Traitementpcg66.nrmrcs' => array( 'label' => __d( 'traitementpcg66', 'Traitementpcg66.nrmrcs' ) )
            ),
            array(
                    'options' => $options
            )
        );
        echo $this->Xform->input('Dossierpcg66.exists', array( 'label' => 'Corbeille pleine ?', 'type' => 'select', 'options' => $exists, 'empty' => true ) );
	?>
</fieldset>
<?php
	echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
	echo $this->Search->paginationNombretotal( 'Traitementpcg66.paginationNombreTotal' );
?>
	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Xform->end();?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'Traitementpcg66', $this->passedArgs ); ?>

	<?php if( isset( $criterestraitementspcgs66 ) ):?>
	<br />
	<h2 class="noprint aere">Résultats de la recherche</h2>

	<?php if( is_array( $criterestraitementspcgs66 ) && count( $criterestraitementspcgs66 ) > 0  ):?>
		<?php echo $pagination;?>
		<table class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° dossier', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom de la personne concernée', 'Personne.nom' );?></th>
                                        <th><?php echo $this->Xpaginator->sort( 'Date de création de la DO', 'Dossierpcg66.datereceptionpdo' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Gestionnaire', 'Dossierpcg66.user_id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Type de traitement', 'Traitementpcg66.typetraitement' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de création du traitement', 'Traitementpcg66.created' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Motif de la situation', 'Situationpdo.libelle' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Description du traitement', 'Traitementpcg66.descriptionpdo_id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de réception du traitement', 'Traitementpcg66.datereception' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date d\'échéance du traitement', 'Traitementpcg66.dateecheance' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Clos ?', 'Traitementpcg66.clos' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Annulé ?', 'Traitementpcg66.annule' );?></th>
					<th>Nb de fichiers dans la corbeille</th>
                    <th class="action noprint">Verrouillé</th>
					<th class="action">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $criterestraitementspcgs66 as $index => $criteretraitementpcg66 ) {

						$etatdosrsaValue = Set::classicExtract( $criteretraitementpcg66, 'Situationdossierrsa.etatdosrsa' );
						$etatDossierRSA = isset( $etatdosrsa[$etatdosrsaValue] ) ? $etatdosrsa[$etatdosrsaValue] : 'Non défini';

						$innerTable = '<table id="innerTable'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>Etat du droit</th>
									<td>'.h( $etatDossierRSA ).'</td>
								</tr>
								<tr>
									<th>Commune de naissance</th>
									<td>'.h( $criteretraitementpcg66['Personne']['nomcomnai'] ).'</td>
								</tr>
								<tr>
									<th>Date de naissance</th>
									<td>'.h( date_short( $criteretraitementpcg66['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>Code INSEE</th>
									<td>'.h( $criteretraitementpcg66['Adresse']['numcom'] ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $criteretraitementpcg66['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>N° CAF</th>
									<td>'.h( $criteretraitementpcg66['Dossier']['matricule'] ).'</td>
								</tr>
								<tr>
									<th>'.__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $criteretraitementpcg66, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( 'search_plugin', 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $criteretraitementpcg66, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';

						echo $this->Xhtml->tableCells(
							array(
								h( Set::classicExtract( $criteretraitementpcg66, 'Dossier.numdemrsa' ) ),
								h( Set::enum( Set::classicExtract( $criteretraitementpcg66, 'Personne.qual' ), $qual ).' '.Set::classicExtract( $criteretraitementpcg66, 'Personne.nom' ).' '.Set::classicExtract( $criteretraitementpcg66, 'Personne.prenom' ) ),
								h( $this->Locale->date( 'Locale->date',  Set::classicExtract( $criteretraitementpcg66, 'Dossierpcg66.datereceptionpdo' ) ) ),
                                h( Hash::get( $criteretraitementpcg66, 'User.nom_complet' ) ),
								h( Set::enum( Set::classicExtract( $criteretraitementpcg66, 'Traitementpcg66.typetraitement' ), $options['Traitementpcg66']['typetraitement'] ) ),
                                h( date_short( Set::classicExtract( $criteretraitementpcg66, 'Traitementpcg66.created' ) ) ),
								h( Set::classicExtract( $criteretraitementpcg66, 'Situationpdo.libelle' ) ),
								h( Set::enum( Set::classicExtract( $criteretraitementpcg66, 'Traitementpcg66.descriptionpdo_id' ), $descriptionpdo ) ),
								h( date_short( Set::classicExtract( $criteretraitementpcg66, 'Traitementpcg66.datereception' ) ) ),
								h( date_short( Set::classicExtract( $criteretraitementpcg66, 'Traitementpcg66.dateecheance' ) ) ),
								h( Set::enum( Set::classicExtract( $criteretraitementpcg66, 'Traitementpcg66.clos' ), $options['Traitementpcg66']['clos'] ) ),
								h( Set::enum( Set::classicExtract( $criteretraitementpcg66, 'Traitementpcg66.annule' ), $options['Traitementpcg66']['annule'] ) ),
								h( $criteretraitementpcg66['Fichiermodule']['nb_fichiers_lies'] ),
                                array(
                                    ( $criteretraitementpcg66['Dossier']['locked'] ?
                                        $this->Xhtml->image(
                                            'icons/lock.png',
                                            array( 'alt' => '', 'title' => 'Dossier verrouillé' )
                                        ) : null
                                    ),
                                    array( 'class' => 'noprint' )
                                ),
								$this->Xhtml->viewLink(
									'Voir',
									array( 'controller' => 'traitementspcgs66', 'action' => 'index', Set::classicExtract( $criteretraitementpcg66, 'Personnepcg66.personne_id' ), Set::classicExtract( $criteretraitementpcg66, 'Dossierpcg66.id' ) )
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
					array( 'controller' => 'criterestraitementspcgs66', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' )
//					$this->Permissions->check( 'criterestraitementspcgs66', 'exportcsv' )
				);
			?></li>
		</ul>
	<?php else:?>
		<p class="notice">Vos critères n'ont retourné aucun traitement.</p>
	<?php endif?>
<?php endif?>
<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>