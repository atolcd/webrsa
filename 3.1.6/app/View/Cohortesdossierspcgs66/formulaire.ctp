<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<?php
    $this->pageTitle = 'Dossiers PCGs en attente d\'affectation';
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
		observeDisableFieldsetOnCheckbox( 'SearchDossierpcg66Datereceptionpdo', $( 'SearchDossierpcg66DatereceptionpdoFromDay' ).up( 'fieldset' ), false );
	});
</script>

<?php echo $this->Xform->create( 'Cohortedossierpcg66', array( 'type' => 'post', 'action' => 'enattenteaffectation', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>


        <fieldset>
			<?php echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );?>

            <legend>Filtrer par Dossier PCG</legend>
			<?php echo $this->Xform->input( 'Search.Dossierpcg66.datereceptionpdo', array( 'label' => 'Filtrer par date de réception du dossier', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Filtrer par période</legend>
				<?php
					$datereceptionpdo_from = Set::check( $this->request->data, 'Search.Dossierpcg66.datereceptionpdo_from' ) ? Set::extract( $this->request->data, 'Search.Dossierpcg66.datereceptionpdo_from' ) : strtotime( '-1 week' );
					$datereceptionpdo_to = Set::check( $this->request->data, 'Search.Dossierpcg66.datereceptionpdo_to' ) ? Set::extract( $this->request->data, 'Search.Dossierpcg66.datereceptionpdo_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Xform->input( 'Search.Dossierpcg66.datereceptionpdo_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $datereceptionpdo_from ) );?>
				<?php echo $this->Xform->input( 'Search.Dossierpcg66.datereceptionpdo_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $datereceptionpdo_to ) );?>
			</fieldset>
            <?php

                echo $this->Default2->subform(
                    array(
                        'Search.Originepdo.libelle' => array( 'label' => __d( 'dossierpcg66', 'Dossierpcg66.originepdo_id' ), 'type' => 'select', 'options' => $originepdo, 'empty' => true ),
                        'Search.Dossierpcg66.serviceinstructeur_id' => array(  'label' => 'Service instructeur', 'options' => $serviceinstructeur, 'empty' => true ),
                        'Search.Typepdo.libelle' => array( 'label' => __d( 'dossierpcg66', 'Dossierpcg66.typepdo_id' ), 'type' => 'select', 'options' => $typepdo, 'empty' => true ),
                        'Search.Dossierpcg66.orgpayeur' => array( 'label' =>  __d( 'dossierpcg66', 'Dossierpcg66.orgpayeur' ), 'type' => 'select', 'options' => $orgpayeur, 'empty' => true ),
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
<?php $pagination = $this->Xpaginator->paginationBlock( 'Dossierpcg66', $this->passedArgs ); ?>
<?php if ($pagination):?>
<h2 class="noprint">Résultats de la recherche</h2>
<?php echo $pagination;?>
<?php endif;?>
<?php if( isset( $cohortedossierpcg66 ) ):?>
    <?php if( is_array( $cohortedossierpcg66 ) && count( $cohortedossierpcg66 ) > 0  ):?>
        <?php echo $this->Form->create( 'Affectationdossierpcg66', array() );?>
		<?php
			foreach( Hash::flatten( $this->request->data['Search'] ) as $filtre => $value  ) {
				echo $this->Form->input( "Search.{$filtre}", array( 'type' => 'hidden', 'value' => $value ) );
			}
		?>
    <table id="searchResults" class="tooltips">
        <thead>
            <tr>
                <th>N° Dossier</th>
                <th>Alloctaire principal</th>
                <th>Commune de l'allocataire</th>
                <th><?php echo $this->Xpaginator->sort( 'Date de réception DO', 'Dossierpcg66.datereceptionpdo' );?></th>
                <th>Type de dossier</th>
                <th><?php echo $this->Xpaginator->sort( 'Origine du dossier', 'Originepdo.libelle' );?></th>
                <th>Organisme payeur</th>
                <th>Service instructeur</th>
                <th class="action">Sélection</th>
                <th class="action">Pôle du gestionnaire</th>
                <th class="action">Gestionnaire</th>
                <th class="action">Date d'affectation</th>
                <th class="action">Action</th>
				<th class="innerTableHeader noprint">Informations complémentaires</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach( $cohortedossierpcg66 as $index => $affectationdossierpcg66 ):?>
            <?php
                $innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
                    <tbody>
                            <tr>
                                <th>'.__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ).'</th>
                                <td>'.Hash::get( $affectationdossierpcg66, 'Structurereferenteparcours.lib_struc' ).'</td>
                            </tr>
                            <tr>
                                <th>'.__d( 'search_plugin', 'Referentparcours.nom_complet' ).'</th>
                                <td>'.Hash::get( $affectationdossierpcg66, 'Referentparcours.nom_complet' ).'</td>
                            </tr>
                    </tbody>
                </table>';

                    $title = $affectationdossierpcg66['Dossier']['numdemrsa'];

                    $array1 = array(
                        h( $affectationdossierpcg66['Dossier']['numdemrsa'] ),
                        h( $affectationdossierpcg66['Personne']['nom'].' '.$affectationdossierpcg66['Personne']['prenom'] ),
                        h( $affectationdossierpcg66['Adresse']['nomcom'] ),
                        h( date_short( $affectationdossierpcg66['Dossierpcg66']['datereceptionpdo'] ) ),
                        h( $affectationdossierpcg66['Typepdo']['libelle'] ),
                        h( $affectationdossierpcg66['Originepdo']['libelle'] ),
                        h( $affectationdossierpcg66['Dossierpcg66']['orgpayeur'] ),
                        h( $affectationdossierpcg66['Serviceinstructeur']['lib_service'] ),
                    );

                    $checked = @$this->request->data['Dossierpcg66'][$index]['atraiter'];

                    $array2 = array(
                        $this->Form->input( 'Dossierpcg66.'.$index.'.atraiter', array( 'label' => false, 'legend' => false, 'type' => 'checkbox', 'checked' => !empty( $checked ) ) ),

                        $this->Form->input( 'Dossierpcg66.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => $affectationdossierpcg66['Dossierpcg66']['id'] ) ).
                        $this->Form->input( 'Dossierpcg66.'.$index.'.foyer_id', array( 'label' => false, 'type' => 'hidden', 'value' => $affectationdossierpcg66['Dossierpcg66']['foyer_id'] ) ).
                        $this->Form->input( 'Dossierpcg66.'.$index.'.originepdo_id', array( 'label' => false, 'type' => 'hidden', 'value' => $affectationdossierpcg66['Dossierpcg66']['originepdo_id'] ) ).
                        $this->Form->input( 'Dossierpcg66.'.$index.'.typepdo_id', array( 'label' => false, 'type' => 'hidden', 'value' => $affectationdossierpcg66['Dossierpcg66']['typepdo_id'] ) ).
                        $this->Form->input( 'Dossierpcg66.'.$index.'.dossier_id', array( 'label' => false, 'type' => 'hidden', 'value' => $affectationdossierpcg66['Dossier']['id'] ) ).
                        $this->Form->input( 'Dossierpcg66.'.$index.'.etatdossierpcg', array( 'label' => false, 'type' => 'hidden', 'value' => $affectationdossierpcg66['Dossierpcg66']['etatdossierpcg'] ) ).
                        $this->Form->input( 'Dossierpcg66.'.$index.'.poledossierpcg66_id', array( 'label' => false, 'type' => 'select', 'options' => $polesdossierspcgs66, 'empty' => true ) ),

                        $this->Form->input( 'Dossierpcg66.'.$index.'.user_id', array( 'label' => false, 'type' => 'select', 'options' => $gestionnaires, 'empty' => true ) ),
                        
                        $this->Form->input( 'Dossierpcg66.'.$index.'.dateaffectation', array( 'label' => false, 'type' => 'date', 'dateFormat' => 'DMY', 'empty' => false, 'maxYear' => date('Y') + 2 ) ),

                        $this->Xhtml->viewLink(
                            'Voir le dossier « '.$title.' »',
                            array( 'controller' => 'dossierspcgs66', 'action' => 'index', $affectationdossierpcg66['Dossierpcg66']['foyer_id'] ),
                            $this->Permissions->check( 'dossierspcgs66', 'index' )
                        ),
                        array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
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

<script type="text/javascript">
document.observe( "dom:loaded", function() {

} );
</script>

<script type="text/javascript">
    <?php foreach( $cohortedossierpcg66 as $key => $affectationdossierpcg66 ):?>
        observeDisableFieldsOnCheckbox(
            'Dossierpcg66<?php echo $key;?>Atraiter',
            [
                'Dossierpcg66<?php echo $key;?>Poledossierpcg66Id',
                'Dossierpcg66<?php echo $key;?>UserId',
                'Dossierpcg66<?php echo $key;?>DateaffectationYear',
                'Dossierpcg66<?php echo $key;?>DateaffectationMonth',
                'Dossierpcg66<?php echo $key;?>DateaffectationDay'
            ],
            false
        );

        dependantSelect( 'Dossierpcg66<?php echo $key;?>UserId', 'Dossierpcg66<?php echo $key;?>Poledossierpcg66Id' );
    <?php endforeach;?>
</script>
    <?php else:?>
        <p class="notice">Vos critères n'ont retourné aucun dossier.</p>
    <?php endif?>
<?php endif?>