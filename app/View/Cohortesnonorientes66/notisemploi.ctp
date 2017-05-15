<?php
	$this->pageTitle = 'Gestion des réponses';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
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
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'SearchDossierDtdemrsa', $( 'SearchDossierDtdemrsaFromDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'SearchNonoriente66Dateimpression', $( 'SearchNonoriente66DateimpressionFromDay' ).up( 'fieldset' ), false );
	});
</script>
<?php echo $this->Xform->create( 'Cohortenonoriente66', array( 'type' => 'post', 'action' => 'notisemploi', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>


        <fieldset>
			<?php echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );?>

            <legend>Filtrer par Dossier</legend>
				<?php echo $this->Xform->input( 'Search.Dossier.dtdemrsa', array( 'label' => 'Filtrer par date de demande', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Date de demande RSA</legend>
				<?php
					$dtdemrsaFromSelected = $dtdemrsaToSelected = array();
					if( !dateComplete( $this->request->data, 'Search.Dossier.dtdemrsa_from' ) ) {
						$dtdemrsaFromSelected = array( 'selected' => strtotime( '-1 week' ) );
					}
					if( !dateComplete( $this->request->data, 'Search.Dossier.dtdemrsa_to' ) ) {
						$dtdemrsaToSelected = array( 'selected' => strtotime( 'today' ) );
					}

					echo $this->Xform->input( 'Search.Dossier.dtdemrsa_from', Set::merge( array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 20 ), $dtdemrsaFromSelected ) );

					echo $this->Xform->input( 'Search.Dossier.dtdemrsa_to', Set::merge( array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 5, 'minYear' => date( 'Y' ) - 20), $dtdemrsaToSelected ) );
				?>
			</fieldset>
			<fieldset>
				<?php
					$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
					echo $this->Xform->input( 'Search.Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
				?>
			</fieldset>
			<?php
				if( !is_null($etatdosrsa)) {
					echo $this->Search->etatdosrsa( $etatdosrsa, 'Search.Situationdossierrsa.etatdosrsa' );
				}
			?>
            <?php

                echo $this->Default2->subform(
                    array(
                        'Search.Personne.nom' => array( 'label' => __d( 'personne', 'Personne.nom' ) ),
                        'Search.Personne.prenom' => array( 'label' => __d( 'personne', 'Personne.prenom' ) ),
                        'Search.Personne.nomnai' => array( 'label' => __d( 'personne', 'Personne.nomnai' ) ),
                        'Search.Personne.nir' => array( 'label' => __d( 'personne', 'Personne.nir' ) ),
                        'Search.Dossier.matricule' => array( 'label' => __d( 'dossier', 'Dossier.matricule' ) ),
                        'Search.Dossier.numdemrsa' => array( 'label' => __d( 'dossier', 'Dossier.numdemrsa' ) ),
						'Search.Adresse.nomcom' => array( 'label' => __d( 'adresse', 'Adresse.nomcom' ) ),
						'Search.Adresse.numcom' => array( 'label' => __d( 'adresse', 'Adresse.numcom' ), 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true ),
						'Search.Foyer.nbenfants' => array( 'label' => 'Foyer avec enfant', 'type' => 'select', 'options' => array( 'O' => 'Oui', 'N' => 'Non' ), 'empty' => true )
                    ),
                    array(
                        'options' => $options
                    )
                );

				if( Configure::read( 'CG.cantons' ) ) {
					echo $this->Xform->input( 'Search.Canton.canton', array( 'label' => 'Canton', 'type' => 'select', 'options' => $cantons, 'empty' => true ) );
				}
            ?>
        </fieldset>
		<?php echo $this->Xform->input( 'Search.Nonoriente66.dateimpression', array( 'label' => 'Filtrer par date d\'impression du courrier', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date d'impression du courrier</legend>
			<?php
				$dateimpressionFromSelected = $dateimpressionToSelected = array();
				if( !dateComplete( $this->request->data, 'Search.Nonoriente66.dateimpression_from' ) ) {
					$dateimpressionFromSelected = array( 'selected' => strtotime( '-1 week' ) );
				}
				if( !dateComplete( $this->request->data, 'Search.Nonoriente66.dateimpression_to' ) ) {
					$dateimpressionToSelected = array( 'selected' => strtotime( 'today' ) );
				}

				echo $this->Xform->input( 'Search.Nonoriente66.dateimpression_from', Set::merge( array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 20 ), $dateimpressionFromSelected ) );

				echo $this->Xform->input( 'Search.Nonoriente66.dateimpression_to', Set::merge( array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 5, 'minYear' => date( 'Y' ) - 20), $dateimpressionToSelected ) );
			?>
		</fieldset>

		<?php
			echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
			echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
			echo $this->Search->observeDisableFormOnSubmit( 'Search' );
		?>

    <div class="submit noprint">
        <?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
        <?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
    </div>

<?php echo $this->Xform->end();?>

<?php if( isset( $cohortesnonorientes66 ) ):?>
<h2 class="noprint">Résultats de la recherche</h2>
    <?php if( empty( $cohortesnonorientes66 ) ):?>
        <p class="notice"><?php echo 'Aucun allocataire non orienté.';?></p>
    <?php else:?>

	<?php echo $this->Xform->create( 'Nonoriente', array() );?>
	<?php
		foreach( Hash::flatten( $this->request->data['Search'] ) as $filtre => $value  ) {
			echo $this->Xform->input( "Search.{$filtre}", array( 'type' => 'hidden', 'value' => $value ) );
		}
	?>
	<?php $pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs ); ?>
	<?php echo $pagination;?>
    <table id="searchResults" class="tooltips">
        <thead>
            <tr>
                <th>N° Dossier</th>
                <th>N° CAF</th>
                <th>Date de demande</th>
                <th>Allocataire principal</th>
                <th>Etat du droit</th>
				<th>Commune - canton de l'allocataire</th>
				<th>Date impression courrier</th>
				<th>Nombre d'enfants</th>
				<th>Alerte composition du foyer ?</th>
				<th>Sélectionner</th>
				<th>Réponse de l'allocataire ?</th>
				<th class="action">Type d'orientation</th>
				<th class="action">Structure référente</th>
				<th class="action">Date d'orientation</th>
				<th class="action">Action</th>
				<th class="innerTableHeader noprint">Informations complémentaires</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach( $cohortesnonorientes66 as $index => $cohortenonoriente66 ):?>
            <?php
				$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>'.__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ).'</th>
							<td>'.Hash::get( $cohortenonoriente66, 'Structurereferenteparcours.lib_struc' ).'</td>
						</tr>
						<tr>
							<th>'.__d( 'search_plugin', 'Referentparcours.nom_complet' ).'</th>
							<td>'.Hash::get( $cohortenonoriente66, 'Referentparcours.nom_complet' ).'</td>
						</tr>
					</tbody>
				</table>';

				$tableCells = array(
						h( $cohortenonoriente66['Dossier']['numdemrsa'] ),
						h( $cohortenonoriente66['Dossier']['matricule'] ),
						h( date_short( $cohortenonoriente66['Dossier']['dtdemrsa'] ) ),
						h( $cohortenonoriente66['Personne']['nom'].' '.$cohortenonoriente66['Personne']['prenom'] ),
						h( $etatdosrsa[$cohortenonoriente66['Situationdossierrsa']['etatdosrsa']] ),
						h( $cohortenonoriente66['Adresse']['nomcom'].' - '.$cohortenonoriente66['Canton']['canton'] ),
						h( date_short( $cohortenonoriente66['Nonoriente66']['dateimpression'] ) ),
						h( $cohortenonoriente66['Foyer']['nbenfants'] ),
						$this->Gestionanomaliebdd->foyerErreursPrestationsAllocataires( $cohortenonoriente66, false ),
						$this->Xform->input( 'Orientstruct.'.$index.'.atraiter', array( 'label' => false, 'legend' => false, 'type' => 'checkbox', 'class' => 'atraiter' ) ),
						$this->Xform->input( 'Nonoriente66.'.$index.'.id', array( 'label' => false, 'legend' => false, 'type' => 'hidden', 'value' => $cohortenonoriente66['Nonoriente66']['id'] ) ).
						$this->Xform->input( 'Nonoriente66.'.$index.'.reponseallocataire', array( 'label' => false, 'legend' => false, 'type' => 'radio', 'options' => $options['reponseallocataire'] ) ),
						$this->Xform->input( 'Orientstruct.'.$index.'.typeorient_id', array( 'label' => false, 'type' => 'select', 'options' => $typesOrient, 'empty' => true, 'value' => ( !empty( $typeorient_id ) ? $typeorient_id : $cohortenonoriente66['Orientstruct']['typeorient_id'] ) ) ).
						$this->Xform->input( 'Orientstruct.'.$index.'.origine', array( 'label' => false, 'type' => 'hidden', 'value' => 'cohorte' ) ).
						$this->Xform->input( 'Orientstruct.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => $cohortenonoriente66['Orientstruct']['id'] ) ).
						$this->Xform->input( 'Orientstruct.'.$index.'.dossier_id', array( 'label' => false, 'type' => 'hidden', 'value' => $cohortenonoriente66['Foyer']['dossier_id'] ) ).
						$this->Xform->input( 'Orientstruct.'.$index.'.codeinsee', array( 'label' => false, 'type' => 'hidden', 'value' => $cohortenonoriente66['Adresse']['numcom'] ) ).
						$this->Xform->input( 'Orientstruct.'.$index.'.personne_id', array( 'label' => false, 'type' => 'hidden', 'value' => $cohortenonoriente66['Personne']['id'] ) ).
						$this->Xform->input( 'Orientstruct.'.$index.'.statut_orient', array( 'label' => false, 'type' => 'hidden', 'value' => 'Orienté' ) ),

						$this->Xform->input( 'Orientstruct.'.$index.'.structurereferente_id', array( 'label' => false, 'type' => 'select', 'options' => $structuresReferentes, 'empty' => true, 'value' => ( !empty( $structurereferente_id ) ? $structurereferente_id : $cohortenonoriente66['Orientstruct']['structurereferente_id'] ) ) ),

						$this->Xform->input( 'Orientstruct.'.$index.'.date_valid', array( 'label' => false, 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2  ) ),

						$this->Xhtml->viewLink(
							'Voir le dossier',
							array( 'controller' => 'dossiers', 'action' => 'view', $cohortenonoriente66['Dossier']['id'] ),
							$this->Permissions->check( 'dossiers', 'view' )
						),
						array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
					);


					echo $this->Xhtml->tableCells(
						$tableCells,
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);

                ?>
				<?php endforeach;?>
			</tbody>
		</table>
<?php
	echo $this->Xform->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocher( 'input.atraiter', true );" ) );
	echo $this->Xform->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocher( 'input.atraiter', true );" ) );

?>
		<?php echo $this->Xform->submit( 'Validation de la liste' );?>
		<?php echo $this->Xform->end();?>
	<?php endif;?>
<?php endif;?>
<script type="text/javascript">
	var structuresAutomatiques = <?php echo php_associative_array_to_js( $structuresAutomatiques );?>

	function changeStructuresAutomatiques( canton, typeorient_id, key ) {
// 		alert( structuresAutomatiques[canton][typeorient_id] );
		$( 'Orientstruct' + key + 'StructurereferenteId' ).value = structuresAutomatiques[canton][typeorient_id];
	}
</script>
<?php if( !empty( $cohortesnonorientes66 ) ):?>
	<?php foreach( $cohortesnonorientes66 as $key => $cohortenonoriente66 ):?>
		<script type="text/javascript">
			document.observe("dom:loaded", function() {
				<?php
					// Choix de la structure
					echo "$( 'Orientstruct{$key}TypeorientId' ).observe( 'change', function( event ) { changeStructuresAutomatiques( '{$cohortenonoriente66['Canton']['canton']}', $(this).value, {$key} ); } );\n";
				?>

				<?php
					// Choix du type d'orientation en fonction du nombre d'enfants
					if( $cohortenonoriente66['Foyer']['nbenfants'] == '0' ) {
						echo "$( 'Orientstruct{$key}TypeorientId' ).value = {$TypeorientIdPrepro};\n$( 'Orientstruct{$key}TypeorientId' ).simulate( 'change' );\n"; // FIXME
					}
					else {
						echo "$( 'Orientstruct{$key}TypeorientId' ).value = {$TypeorientIdSocial};\n$( 'Orientstruct{$key}TypeorientId' ).simulate( 'change' );\n"; // FIXME
					}
				?>

				dependantSelect( 'Orientstruct<?php echo $key;?>StructurereferenteId', 'Orientstruct<?php echo $key;?>TypeorientId' );
				try { $( 'Orientstruct<?php echo $key;?>StructurereferenteId' ).onchange(); } catch(id) { }

				<?php
					// Choix de la structure
					echo "$( 'Orientstruct{$key}TypeorientId' ).observe( 'change', function( event ) { changeStructuresAutomatiques( '{$cohortenonoriente66['Canton']['canton']}', $(this).value, {$key} ); } );\n";
				?>

				observeDisableFieldsOnCheckbox(
					'Orientstruct<?php echo $key;?>Atraiter',
					[
						'Nonoriente66<?php echo $key;?>Id',
						'Nonoriente66<?php echo $key;?>Reponseallocataire_',
						'Nonoriente66<?php echo $key;?>ReponseallocataireN',
						'Nonoriente66<?php echo $key;?>ReponseallocataireO',
						'Orientstruct<?php echo $key;?>Codeinsee',
						'Orientstruct<?php echo $key;?>Id',
						'Orientstruct<?php echo $key;?>DossierId',
						'Orientstruct<?php echo $key;?>TypeorientId',
						'Orientstruct<?php echo $key;?>Origine',
						'Orientstruct<?php echo $key;?>PersonneId',
						'Orientstruct<?php echo $key;?>StatutOrient',
						'Orientstruct<?php echo $key;?>StructurereferenteId',
						'Orientstruct<?php echo $key;?>DateValidYear',
						'Orientstruct<?php echo $key;?>DateValidMonth',
						'Orientstruct<?php echo $key;?>DateValidDay',
					],
					false
				);
			});
		</script>
	<?php endforeach;?>
<?php endif;?>