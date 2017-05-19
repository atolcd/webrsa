<?php
	$this->pageTitle = 'Recherche de demande APRE/ADRE';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle; ?></h1>


<?php $pagination = $this->Xpaginator->paginationBlock( 'Apre', $this->passedArgs );?>
<?php
    if( is_array( $this->request->data ) ) {
        echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
            $this->Xhtml->image(
                'icons/application_form_magnify.png',
                array( 'alt' => '' )
            ).' Formulaire',
            '#',
            array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'critereapreform' ).toggle(); return false;" )
        ).'</li></ul>';
    }

?>


<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<script type="text/javascript">
        document.observe("dom:loaded", function() {
            dependantSelect(
                'FiltreTypeaideapre66Id',
                'FiltreThemeapre66Id'
            );
	});
</script>



<?php /*echo $this->Xform->create( 'Critereapre', array( 'type' => 'post', 'action' => '/formulaire/', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );*/
    echo $this->Xform->create( 'Critereapre', array( 'id' => 'critereapreform', 'class' => ( !empty( $this->request->data ) ? 'folded' : 'unfolded' ) ) );?>

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
		<?php
			if( Configure::read( 'debug' ) > 0 ) {
				echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
			}
		?>
		<script type="text/javascript">
			document.observe("dom:loaded", function() {
				dependantSelect( 'FiltreReferentId', 'FiltreStructurereferenteId' );
			});
		</script>
    <fieldset>
        <legend>Recherche par demande APRE/ADRE</legend>
            <?php echo $this->Xform->input( 'Filtre.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>

            <?php echo $this->Xform->input( 'Filtre.datedemandeapre', array( 'label' => 'Filtrer par date de demande APRE/ADRE', 'type' => 'checkbox' ) );?>
            <fieldset>
                <legend>Date de la saisie de la demande</legend>
                <?php
                    $datedemandeapre_from = Set::check( $this->request->data, 'Filtre.datedemandeapre_from' ) ? Set::extract( $this->request->data, 'Filtre.datedemandeapre_from' ) : strtotime( '-1 week' );
                    $datedemandeapre_to = Set::check( $this->request->data, 'Filtre.datedemandeapre_to' ) ? Set::extract( $this->request->data, 'Filtre.datedemandeapre_to' ) : strtotime( 'now' );
                ?>
                <?php echo $this->Xform->input( 'Filtre.datedemandeapre_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datedemandeapre_from ) );?>
                <?php echo $this->Xform->input( 'Filtre.datedemandeapre_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 5, 'minYear' => date( 'Y' ) - 120, 'selected' => $datedemandeapre_to ) );?>
            </fieldset>

            <?php ?>
			<?php
				echo $this->Form->input( 'Filtre.structurereferente_id', array( 'label' => 'Structure référente', 'type' => 'select' , 'options' => $structures, 'empty' => true  ) );
				echo $this->Form->input( 'Filtre.referent_id', array( 'label' => 'Référent/prescripteur', 'type' => 'select' , 'options' => $referents, 'empty' => true  ) );
			?>
            <?php
				echo $this->Xform->enum( 'Filtre.activitebeneficiaire', array(  'label' => 'Activité du bénéficiaire', 'options' => array( 'P' => 'Recherche d\'Emploi', 'E' => 'Emploi' , 'F' => 'Formation', 'C' => 'Création d\'Entreprise' ) ) );

				echo $this->Xform->enum( 'Filtre.themeapre66_id', array(  'label' => 'Thème de l\'aide', 'options' => $themes, 'empty' => true ) );

				echo $this->Xform->enum( 'Filtre.typeaideapre66_id', array(  'label' => 'Type d\'aide', 'options' => $typesaides, 'empty' => true ) );


            ?>

            <?php
				echo $this->Xform->enum( 'Filtre.etatdossierapre', array(  'label' => 'Etat du dossier APRE/ADRE', 'options' => 	$options['etatdossierapre'] ) );
				echo $this->Xform->enum( 'Filtre.isdecision', array(  'label' => 'Décision émise concernant le dossier APRE/ADRE', 'type' => 'radio', 'options' => $options['isdecision'] ) );

            ?>
            <fieldset class="noborder" id="avisdecision">
				<?php echo $this->Xform->input( 'Filtre.decisionapre', array( 'label' => 'Accord/Rejet', 'type' => 'radio', 'options' => $options['decisionapre'] ) ); ?>
            </fieldset>

    </fieldset>

	<?php echo $this->Search->paginationNombretotal(); ?>

    <div class="submit noprint">
        <?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
        <?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
    </div>

<?php echo $this->Xform->end();?>

<!-- Résultats -->
<?php if( isset( $apres ) ):?>

    <?php
        $totalCount = Set::classicExtract( $this->request->params, 'paging.Apre.count' );
    ?>


    <h2 class="noprint">Résultats de la recherche</h2>

    <?php echo $pagination;?>
    <?php if( is_array( $apres ) && count( $apres ) > 0  ):?>

        <table id="searchResults" class="tooltips">
            <thead>
                <tr>
                    <th><?php echo $this->Xpaginator->sort( 'N° Dossier RSA', 'Dossier.numdemrsa' );?></th>
                    <th><?php echo $this->Xpaginator->sort( 'N° demande APRE/ADRE', 'Apre.numeroapre' );?></th>
                    <th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
                    <th><?php echo $this->Xpaginator->sort( 'Commune de l\'allocataire', 'Adresse.nomcom' );?></th>
                    <th><?php echo $this->Xpaginator->sort( 'Date de demande APRE/ADRE', 'Aideapre66.datedemande' );?></th>
                    <th><?php echo $this->Xpaginator->sort( 'Structure référente', 'Structurereferente.lib_struc' );?></th>
                    <th><?php echo $this->Xpaginator->sort( 'Référent/Prescripteur', 'Referent.nom' );?></th>
                    <th><?php echo $this->Xpaginator->sort( 'Activité du bénéficiaire', 'Apre.activitebeneficiaire' );?></th>
                    <th><?php echo $this->Xpaginator->sort( 'Etat du dossier APRE/ADRE', 'Apre.etatdossierapre' );?></th>
                    <th><?php echo $this->Xpaginator->sort( 'Décision émise ?', 'Apre.isdecision' );?></th>
                    <th><?php echo $this->Xpaginator->sort( 'Accord ou rejet', 'Aideapre66.decisionapre' );?></th>
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
									<th>'.__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $apre, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( 'search_plugin', 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $apre, 'Referentparcours.nom_complet' ).'</td>
								</tr>
                            </tbody>
                        </table>';

						$activites = array( 'P' => 'Recherche d\'Emploi', 'E' => 'Emploi' , 'F' => 'Formation', 'C' => 'Création d\'Entreprise' );
// debug( $apre );
                        echo $this->Xhtml->tableCells(
                            array(
                                h( Set::classicExtract( $apre, 'Dossier.numdemrsa' ) ),
                                h( Set::classicExtract( $apre, 'Apre.numeroapre' ) ),
                                h( $apre['Personne']['nom'].' '.$apre['Personne']['prenom'] ),
                                h( $apre['Adresse']['nomcom'] ),
                                h( $this->Locale->date( 'Date::short', Set::extract( $apre, 'Aideapre66.datedemande' ) ) ),
                                h( $apre['Structurereferente']['lib_struc']),
                                h( $apre['Referent']['nom'].' '.$apre['Referent']['prenom'] ),
                                h( Set::enum( Set::classicExtract( $apre, 'Apre.activitebeneficiaire' ), $activites ) ),
                                h( Set::enum( Set::classicExtract( $apre, 'Apre.etatdossierapre' ), $options['etatdossierapre'] ) ),
                                h( Set::enum( Set::classicExtract( $apre, 'Apre.isdecision' ), $options['isdecision'] ) ),
                                h( Set::enum( Set::classicExtract( $apre, 'Aideapre66.decisionapre' ), $options['decisionapre'] ) ),
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
        <p>Vos critères n'ont retourné aucune demande d'APRE/ADRE.</p>
    <?php endif?>

<?php endif?>

<script type="text/javascript">
    document.observe("dom:loaded", function() {
        observeDisableFieldsetOnCheckbox( 'FiltreDatedemandeapre', $( 'FiltreDatedemandeapreFromDay' ).up( 'fieldset' ), false );
//         observeDisableFieldsetOnCheckbox( 'FiltreDateprint', $( 'FiltreDateimpressionapreFromDay' ).up( 'fieldset' ), false );


		observeDisableFieldsetOnRadioValue(
			'critereapreform',
			'data[Filtre][isdecision]',
			$( 'avisdecision' ),
			'O',
			false,
			true
		);
    });
</script>

<?php echo $this->Search->observeDisableFormOnSubmit( 'critereapreform' ); ?>