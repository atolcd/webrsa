<?php
	$this->pageTitle = 'APRE/ADREs à valider';

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
            dependantSelect(
                'SearchAideapre66Typeaideapre66Id',
                'SearchAideapre66Themeapre66Id'
            );
	});
</script>

<?php echo $this->Xform->create( 'Cohortevalidationapre66', array( 'type' => 'post', 'action' => 'apresavalider', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>


        <fieldset>
			<?php echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );?>

            <?php

/* echo $this->Xform->input( 'Cohortevalidationapre66.apresavalider', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );*/?>

            <legend>Filtrer par APRE/ADRE</legend>
            <?php

                echo $this->Default2->subform(
                    array(
						'Search.Aideapre66.themeapre66_id' => array(  'label' => 'Thème de l\'aide', 'options' => $themes, 'empty' => true ),
						'Search.Aideapre66.typeaideapre66_id' => array(  'label' => 'Type d\'aide', 'options' => $typesaides, 'empty' => true ),
                        'Search.Apre66.numeroapre' => array( 'label' => __d( 'apre', 'Apre.numeroapre' ) ),
                        'Search.Apre66.referent_id' => array( 'label' => __d( 'apre', 'Apre.referent_id' ), 'options' => $referents ),
                        'Search.Personne.nom' => array( 'label' => __d( 'personne', 'Personne.nom' ) ),
                        'Search.Personne.prenom' => array( 'label' => __d( 'personne', 'Personne.prenom' ) ),
                        'Search.Personne.nomnai' => array( 'label' => __d( 'personne', 'Personne.nomnai' ) ),
                        'Search.Personne.nir' => array( 'label' => __d( 'personne', 'Personne.nir' ) ),
                        'Search.Dossier.matricule' => array( 'label' => __d( 'dossier', 'Dossier.matricule' ) ),
                        'Search.Dossier.numdemrsa' => array( 'label' => __d( 'dossier', 'Dossier.numdemrsa' ) ),
						'Search.Adresse.nomcom' => array( 'label' => __d( 'adresse', 'Adresse.nomcom' ) ),
						'Search.Adresse.numcom' => array( 'label' => __d( 'adresse', 'Adresse.numcom' ), 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true )
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
<?php $pagination = $this->Xpaginator->paginationBlock( 'Apre66', $this->passedArgs ); ?>
<?php if ($pagination):?>
<h2 class="noprint">Résultats de la recherche</h2>
<?php echo $pagination;?>
<?php endif;?>

<?php if( isset( $cohortevalidationapre66 ) ):?>
    <?php if( is_array( $cohortevalidationapre66 ) && count( $cohortevalidationapre66 ) > 0  ):?>
        <?php echo $this->Form->create( 'ValidationApre', array() );?>
		<?php
			foreach( Hash::flatten( $this->request->data['Search'] ) as $filtre => $value  ) {
				echo $this->Form->input( "Search.{$filtre}", array( 'type' => 'hidden', 'value' => $value ) );
			}
		?>
    <table id="searchResults" class="tooltips">
        <thead>
            <tr>
				<th>N° demande APRE/ADRE</th>
                <th>N° Dossier</th>
                <th>Nom de l'allocataire</th>
                <th>Référent APRE/ADRE</th>
                <th>Date demande APRE/ADRE</th>
                <th>Montant proposé</th>
                <th>Sélectionner</th>
                <th>Décision APRE/ADRE</th>
                <th>Montant accordé</th>
                <th>Motif du rejet</th>
                <th>Date de la décision</th>
                <th class="action">Action</th>
				<th class="innerTableHeader noprint">Informations complémentaires</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach( $cohortevalidationapre66 as $index => $validationapre ):?>
            <?php
				$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>'.__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ).'</th>
							<td>'.Hash::get( $validationapre, 'Structurereferenteparcours.lib_struc' ).'</td>
						</tr>
						<tr>
							<th>'.__d( 'search_plugin', 'Referentparcours.nom_complet' ).'</th>
							<td>'.Hash::get( $validationapre, 'Referentparcours.nom_complet' ).'</td>
						</tr>
					</tbody>
				</table>';

				$title = $validationapre['Dossier']['numdemrsa'];

                    $array1 = array(
						h( $validationapre['Apre66']['numeroapre'] ),
                        h( $validationapre['Dossier']['numdemrsa'] ),
                        h( $validationapre['Personne']['nom_complet'] ),
                        h( $validationapre['Referent']['nom_complet'] ),
                        h( date_short( $validationapre['Aideapre66']['datedemande'] ) ),
                        h( $validationapre['Aideapre66']['montantpropose'] ),
                    );

                    $array2 = array(
						$this->Form->input( 'Apre66.'.$index.'.atraiter', array( 'label' => false, 'legend' => false, 'type' => 'checkbox' ) ),
                        $this->Form->input( 'Apre66.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Apre66']['id'] ) ).
                        $this->Form->input( 'Apre66.'.$index.'.personne_id', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Apre66']['personne_id'] ) ).
                        $this->Form->input( 'Apre66.'.$index.'.dossier_id', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Dossier']['id'] ) ).
                        $this->Form->input( 'Apre66.'.$index.'.isdecision', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Apre66']['isdecision'] ) ).
                        $this->Form->input( 'Apre66.'.$index.'.etatdossierapre', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Apre66']['etatdossierapre'] ) ).
                        $this->Form->input( 'Aideapre66.'.$index.'.datemontantpropose', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Aideapre66']['datemontantpropose'] ) ).
                        $this->Form->input( 'Aideapre66.'.$index.'.montantpropose', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Aideapre66']['montantpropose'] ) ).
                        $this->Form->input( 'Aideapre66.'.$index.'.typeaideapre66_id', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Aideapre66']['typeaideapre66_id'] ) ).
                        $this->Form->input( 'Aideapre66.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Aideapre66']['id'] ) ).
                        $this->Form->input( 'Aideapre66.'.$index.'.apre_id', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Aideapre66']['apre_id'] ) ).
                        $this->Form->input( 'Aideapre66.'.$index.'.dossier_id', array( 'label' => false, 'type' => 'hidden', 'value' => $validationapre['Dossier']['id'] ) ).
                        $this->Form->input( 'Aideapre66.'.$index.'.decisionapre', array( 'label' => false, 'empty' => true, 'type' => 'select', 'options' => $optionsaideapre66['decisionapre'], 'value' => $validationapre['Aideapre66']['decisionapre'] ) ),

                        $this->Form->input( 'Aideapre66.'.$index.'.montantaccorde', array( 'label' => false, 'value' => $validationapre['Aideapre66']['montantpropose'] ) ), //FIXME

                        $this->Form->input( 'Aideapre66.'.$index.'.motifrejetequipe', array( 'label' => false, 'rows' => 2, 'value' => $validationapre['Aideapre66']['motifrejetequipe'] ) ),

                        $this->Form->input( 'Aideapre66.'.$index.'.datemontantaccorde', array( 'label' => false, /*'empty' => true,*/  'type' => 'date', 'dateFormat' => 'DMY', 'selected' => $validationapre['Aideapre66']['proposition_datemontantaccorde'] ) ),


                        $this->Xhtml->viewLink(
                            'Voir le contrat « '.$title.' »',
                            array( 'controller' => 'apres66', 'action' => 'index', $validationapre['Apre66']['personne_id'] ),
                            $this->Permissions->check( 'apres66', 'index' )
                        ),
                        array( $innerTable, array( 'class' => 'innerTableCell' ) )
                    );

                    echo $this->Xhtml->tableCells(
                        Set::merge( $array1, $array2 ),
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
        <p class="notice">Vos critères n'ont retourné aucun dossier.</p>
    <?php endif?>
<?php endif?>

<?php if( isset( $cohortevalidationapre66 ) ):?>
    <script type="text/javascript">
        <?php foreach( $cohortevalidationapre66 as $index => $validationapre ):?>

	    observeDisableFieldsOnCheckbox(
			'Apre66<?php echo $index;?>Atraiter',
			[
				'Aideapre66<?php echo $index;?>Decisionapre',
				'Aideapre66<?php echo $index;?>Montantaccorde',
				'Aideapre66<?php echo $index;?>Motifrejetequipe',
				'Aideapre66<?php echo $index;?>DatemontantaccordeDay',
				'Aideapre66<?php echo $index;?>DatemontantaccordeMonth',
				'Aideapre66<?php echo $index;?>DatemontantaccordeYear'
			],
			false
	    );


            observeDisableFieldsOnValue(
                'Aideapre66<?php echo $index;?>Decisionapre',
                [
                    'Aideapre66<?php echo $index;?>Montantaccorde'
                ],
                'REF',
                true
            );
            //Données pour le type d'activité du bénéficiare
            observeDisableFieldsOnValue(
                'Aideapre66<?php echo $index;?>Decisionapre',
                [
                    'Aideapre66<?php echo $index;?>Montantaccorde'
                ],
                'ACC',
                false
            );
            //Données pour le type d'activité du bénéficiare
            observeDisableFieldsOnValue(
                'Aideapre66<?php echo $index;?>Decisionapre',
                [
                    'Aideapre66<?php echo $index;?>Motifrejetequipe'
                ],
                'ACC',
                true
            );
            observeDisableFieldsOnValue(
                'Aideapre66<?php echo $index;?>Decisionapre',
                [
                    'Aideapre66<?php echo $index;?>Motifrejetequipe'
                ],
                'REF',
                false
            );

        <?php endforeach;?>
    </script>
<?php endif;?>