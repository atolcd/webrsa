<?php
	$this->pageTitle = 'Etat des demandes d\'APRE';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle; ?></h1>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'FiltreDatedemandeapre', $( 'FiltreDatedemandeapreFromDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'FiltreDaterelance', $( 'FiltreDaterelanceFromDay' ).up( 'fieldset' ), false );

	});
</script>

<?php
	$pagination = $this->Xpaginator->paginationBlock( 'Apre', $this->passedArgs );

	if( is_array( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Critereapre' ).toggle(); return false;" )
		).'</li></ul>';
	}

	echo $this->Xform->create( 'Critereapre', array( 'id' => 'Critereapre', 'class' => ( !empty( $this->request->data ) ? 'folded' : 'unfolded' ) ) );?>
	<?php
		echo $this->Search->blocAllocataire(  );
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
	?>

	<fieldset>
		<legend>Recherche par dossier</legend>
        <?php
			echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de dossier RSA' ) );
			echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule.large' ), 'maxlength' => 15 ) );

			echo $this->Search->etatdosrsa($etatdosrsa);

            $valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
            echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
        ?>
    </fieldset>
	<fieldset>
		<legend>Recherche par demande APRE</legend>
			<?php echo $this->Xform->input( 'Filtre.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
			<?php echo $this->Xform->input( 'Filtre.datedemandeapre', array( 'label' => 'Filtrer par date de demande APRE', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Date de la saisie de la demande</legend>
				<?php
					$datedemandeapre_from = Set::check( $this->request->data, 'Filtre.datedemandeapre_from' ) ? Set::extract( $this->request->data, 'Filtre.datedemandeapre_from' ) : strtotime( '-1 week' );
					$datedemandeapre_to = Set::check( $this->request->data, 'Filtre.datedemandeapre_to' ) ? Set::extract( $this->request->data, 'Filtre.datedemandeapre_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Xform->input( 'Filtre.datedemandeapre_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datedemandeapre_from ) );?>
				<?php echo $this->Xform->input( 'Filtre.datedemandeapre_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datedemandeapre_to ) );?>
			</fieldset>
				<?php echo $this->Xform->enum( 'Filtre.eligibiliteapre', array(  'label' => 'Eligibilité de l\'APRE', 'options' => $options['eligibiliteapre'] ) );?>

			<?php echo $this->Xform->enum( 'Filtre.typedemandeapre', array(  'label' => 'Type de demande', 'options' => $options['typedemandeapre'] ) );?>
			<?php echo $this->Xform->enum( 'Filtre.activitebeneficiaire', array(  'label' => 'Activité du bénéficiaire', 'options' => $options['activitebeneficiaire'] ) );?>
			<?php echo $this->Xform->enum( 'Filtre.natureaidesapres', array(  'label' => 'Nature de l\'aide', 'options' => $natureAidesApres, 'empty' => true ) );?>

			<?php echo $this->Xform->input( 'Filtre.numcom', array( 'label' => 'Numéro de commune au sens INSEE', 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true ) );?>
			<?php
				if( Configure::read( 'CG.cantons' ) ) {
					echo $this->Xform->input( 'Canton.canton', array( 'label' => 'Canton', 'type' => 'select', 'options' => $cantons, 'empty' => true ) );
				}
			?>
	</fieldset>
	<fieldset>
		<legend>Recherche par Relance</legend>
		<?php echo $this->Xform->input( 'Filtre.daterelance', array( 'label' => 'Filtrer par date de relance', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Date de la saisie de la relance</legend>
				<?php
					$daterelance_from = Set::check( $this->request->data, 'Filtre.daterelance_from' ) ? Set::extract( $this->request->data, 'Filtre.daterelance_from' ) : strtotime( '-1 week' );
					$daterelance_to = Set::check( $this->request->data, 'Filtre.daterelance_to' ) ? Set::extract( $this->request->data, 'Filtre.daterelance_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Xform->input( 'Filtre.daterelance_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $daterelance_from ) );?>
				<?php echo $this->Xform->input( 'Filtre.daterelance_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $daterelance_to ) );?>
			</fieldset>

			<?php echo $this->Xform->enum( 'Filtre.etatdossierapre', array(  'label' => 'Etat du dossier APRE', 'options' => $options['etatdossierapre'] ) );?>
			<?php echo $this->Xform->input( 'Filtre.nomcom', array( 'label' => 'Commune de l\'allocataire ', 'type' => 'text' ) );?>
	</fieldset>

	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>

<?php echo $this->Xform->end();?>

<!-- Résultats -->
<?php if( isset( $apres ) ):?>

	<h2 class="noprint">Résultats de la recherche</h2>
	<?php
		$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';
		echo $pagination;
	?>
	<?php if( is_array( $apres ) && count( $apres ) > 0  ):?>

		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° Dossier RSA', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'N° demande APRE', 'Apre.numeroapre' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Commune de l\'allocataire', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de demande APRE', 'Apre.datedemandeapre' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Eligibilité', 'Apre.etatdossierapre' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Etat du dossier APRE', 'Apre.etatdossierapre' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de relance', 'Relanceapre.daterelance' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date comité examen', 'Comiteapre.datecomite' );?></th>

					<th class="action noprint">Actions</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $apres as $index => $apre ):?>
					<?php
						$title = $apre['Dossier']['numdemrsa'];

						$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>N° CAF</th>
									<td>'.$apre['Dossier']['matricule'].'</td>
								</tr>
								<tr>
									<th>Date de naissance</th>
									<td>'.date_short( $apre['Personne']['dtnai'] ).'</td>
								</tr>
								<tr>
									<th>Code INSEE</th>
									<td>'.$apre['Adresse']['numcom'].'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.$apre['Personne']['nir'].'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $apre, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $apre, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';

						$aidesApre = array();
						$naturesaide = Set::classicExtract( $apre, 'Apre.Natureaide' );
						foreach( $naturesaide as $natureaide => $nombre ) {
							if( $nombre > 0 ) {
								$aidesApre[] = h( Set::classicExtract( $natureAidesApres, $natureaide ) );
							}
						}

						echo $this->Xhtml->tableCells(
							array(
								h( Set::classicExtract( $apre, 'Dossier.numdemrsa' ) ),
								h( Set::classicExtract( $apre, 'Apre.numeroapre' ) ),
								h( $apre['Personne']['nom'].' '.$apre['Personne']['prenom'] ),
								h( $apre['Adresse']['nomcom'] ),
								h( $this->Locale->date( 'Date::short', Set::extract( $apre, 'Apre.datedemandeapre' ) ) ),
								h( Set::enum( Set::classicExtract( $apre, 'Apre.eligibiliteapre' ), $options['eligibiliteapre'] ) ),
								h( Set::enum( Set::classicExtract( $apre, 'Apre.etatdossierapre' ), $options['etatdossierapre'] ) ),
								h( $this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Relanceapre.daterelance' ) ) ),
								h( $this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Comiteapre.datecomite' ) ) ),
								array(
									$this->Xhtml->viewLink(
										'Voir le dossier « '.$title.' »',
										array( 'controller' => 'apres'.Configure::read( 'Apre.suffixe' ), 'action' => 'index', $apre['Apre']['personne_id'] ),
										$this->Permissions->check( 'apres'.Configure::read( 'Apre.suffixe' ), 'index' )
									),
									array( 'class' => 'noprint' )
								),
								array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
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
					array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
				);
			?></li>
			<li><?php
				echo $this->Xhtml->exportLink(
					'Télécharger le tableau',
					array( 'controller' => 'criteresapres', 'action' => 'exportcsv', $this->action ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'criteresapres', 'exportcsv' )
				);
			?></li>
		</ul>

	<?php else:?>
		<p>Vos critères n'ont retourné aucune demande d'APRE.</p>
	<?php endif?>

<?php endif?>