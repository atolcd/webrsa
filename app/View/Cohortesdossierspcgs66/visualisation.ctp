<?php
$this->pageTitle = 'Dossiers PCGs affectés';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<h1><?php echo $this->pageTitle; ?></h1>

<?php
echo '<ul class="actionMenu"><li>' . $this->Xhtml->link(
        $this->Xhtml->image(
                'icons/application_form_magnify.png', array('alt' => '')
        ) . ' Formulaire', '#', array('escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;")
) . '</li></ul>';
?>
<script type="text/javascript">
    document.observe("dom:loaded", function() {
        observeDisableFieldsetOnCheckbox('SearchDossierpcg66Datereceptionpdo', $('SearchDossierpcg66DatereceptionpdoFromDay').up('fieldset'), false);
    });
</script>

<?php echo $this->Xform->create('Cohortedossierpcg66', array('type' => 'post', 'action' => 'affectes', 'id' => 'Search', 'class' => ( ( is_array($this->request->data) && !empty($this->request->data) ) ? 'folded' : 'unfolded' ))); ?>


<fieldset>
<?php echo $this->Xform->input('Search.active', array('type' => 'hidden', 'value' => true)); ?>

    <legend>Filtrer par Dossier PCG</legend>
    <?php echo $this->Xform->input('Search.Dossierpcg66.datereceptionpdo', array('label' => 'Filtrer par date de réception du dossier', 'type' => 'checkbox')); ?>
    <fieldset>
        <legend>Filtrer par période</legend>
    <?php
    $datereceptionpdo_from = Set::check($this->request->data, 'Search.Dossierpcg66.datereceptionpdo_from') ? Set::extract($this->request->data, 'Search.Dossierpcg66.datereceptionpdo_from') : strtotime('-1 week');
    $datereceptionpdo_to = Set::check($this->request->data, 'Search.Dossierpcg66.datereceptionpdo_to') ? Set::extract($this->request->data, 'Search.Dossierpcg66.datereceptionpdo_to') : strtotime('now');
    ?>
        <?php echo $this->Xform->input('Search.Dossierpcg66.datereceptionpdo_from', array('label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date('Y') + 1, 'minYear' => date('Y') - 10, 'selected' => $datereceptionpdo_from)); ?>
        <?php echo $this->Xform->input('Search.Dossierpcg66.datereceptionpdo_to', array('label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date('Y') + 1, 'minYear' => date('Y') - 10, 'selected' => $datereceptionpdo_to)); ?>
    </fieldset>
        <?php
        echo $this->Default2->subform(
            array(
                'Search.Originepdo.libelle' => array('label' => __d('dossierpcg66', 'Dossierpcg66.originepdo_id'), 'type' => 'select', 'options' => $originepdo, 'empty' => true),
                'Search.Dossierpcg66.serviceinstructeur_id' => array('label' => 'Service instructeur', 'options' => $serviceinstructeur, 'empty' => true),
                'Search.Typepdo.libelle' => array('label' => __d('dossierpcg66', 'Dossierpcg66.typepdo_id'), 'type' => 'select', 'options' => $typepdo, 'empty' => true),
                'Search.Dossierpcg66.orgpayeur' => array('label' => __d('dossierpcg66', 'Dossierpcg66.orgpayeur'), 'type' => 'select', 'options' => $orgpayeur, 'empty' => true),
                'Search.Personne.nom' => array('label' => __d('personne', 'Personne.nom')),
                'Search.Personne.prenom' => array('label' => __d('personne', 'Personne.prenom')),
                'Search.Personne.nomnai' => array('label' => __d('personne', 'Personne.nomnai')),
                'Search.Personne.nir' => array('label' => __d('personne', 'Personne.nir')),
                'Search.Dossier.matricule' => array('label' => __d('dossier', 'Dossier.matricule')),
                'Search.Dossier.numdemrsa' => array('label' => __d('dossier', 'Dossier.numdemrsa')),
                'Search.Adresse.nomcom' => array('label' => __d('adresse', 'Adresse.nomcom')),
                'Search.Adresse.numcom' => array('label' => __d('adresse', 'Adresse.numcom'), 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true)
            ),
            array(
                'options' => $options
            )
        );

        if (Configure::read('CG.cantons')) {
            echo $this->Xform->input('Search.Canton.canton', array('label' => 'Canton', 'type' => 'select', 'options' => $cantons, 'empty' => true));
        }

//                 echo $this->Search->etatDossierPCG66( $etatdossierpcg, 'Search' );
// debug($options);
        echo $this->Search->multipleCheckboxChoice($options['Dossierpcg66']['etatdossierpcg'], 'Search.Dossierpcg66.etatdossierpcg');

        echo $this->Xform->input( 'Search.Dossierpcg66.poledossierpcg66_id', array('label' => __d('dossierpcg66', 'Dossierpcg66.poledossierpcg66_id'), 'type' => 'select', 'multiple' => 'checkbox', 'options' => $polesdossierspcgs66, 'empty' => false) );
        echo '<fieldset class="col2 noborder">';
        echo $this->Xform->input('Search.Dossierpcg66.user_id', array('label' => __d('dossierpcg66', 'Dossierpcg66.user_id'), 'type' => 'select', 'multiple' => 'checkbox', 'options' => $gestionnaire, 'empty' => false));
        echo '</fieldset>';

        echo $this->Search->date('Search.Dossierpcg66.dateaffectation');
        ?>
</fieldset>

    <?php
    echo $this->Search->referentParcours($structuresreferentesparcours, $referentsparcours, 'Search');
    echo $this->Search->paginationNombretotal('Search.Pagination.nombre_total');
    echo $this->Search->observeDisableFormOnSubmit('Search');
    ?>

<div class="submit noprint">
<?php echo $this->Xform->button('Rechercher', array('type' => 'submit')); ?>
<?php echo $this->Xform->button('Réinitialiser', array('type' => 'reset')); ?>
</div>

    <?php echo $this->Xform->end(); ?>

    <?php if (isset($cohortedossierpcg66)): ?>
    <?php if (empty($cohortedossierpcg66)): ?>
        <?php
        switch ($this->action) {
            case 'affectes':
                $message = 'Aucun Dossier PCG ne correspond à vos critères.';
                break;
            default:
                $message = 'Aucun Dossier PCG affecté n\'a été trouvé.';
        }
        ?>
        <p class="notice"><?php echo $message; ?></p>
    <?php else: ?>
<h2 class="noprint">Résultats de la recherche</h2>
        <?php $pagination = $this->Xpaginator->paginationBlock('Dossierpcg66', $this->passedArgs); ?>
        <?php echo $pagination; ?>
        <?php
        foreach (Hash::flatten($this->request->data['Search']) as $filtre => $value) {
            echo $this->Form->input("Search.{$filtre}", array('type' => 'hidden', 'value' => $value));
        }
        ?>
        <table id="searchResults" class="tooltips">
            <thead>
                <tr>
                    <th>N° Dossier</th>
                    <th>Alloctaire principal</th>
                    <th>Commune de l'allocataire</th>
                    <th><?php echo $this->Xpaginator->sort('Date de réception DO', 'Dossierpcg66.datereceptionpdo'); ?></th>
                    <th>Type de dossier</th>
                    <th><?php echo $this->Xpaginator->sort('Origine du dossier', 'Originepdo.libelle'); ?></th>
                    <th>Organisme payeur</th>
                    <th>Service instructeur</th>
                    <th>Pôle du gestionnaire</th>
                    <th>Gestionnaire</th>
                    <th>Date d'affectation</th>
                    <th class="action">Action</th>
                    <th class="innerTableHeader noprint">Informations complémentaires</th>
                </tr>
            </thead>
            <tbody>
        <?php foreach ($cohortedossierpcg66 as $index => $dossierpcg66affecte): ?>
            <?php
            $innerTable = '<table id="innerTablesearchResults' . $index . '" class="innerTable">
					<tbody>
						<tr>
							<th>' . __d('search_plugin', 'Structurereferenteparcours.lib_struc') . '</th>
							<td>' . Hash::get($dossierpcg66affecte, 'Structurereferenteparcours.lib_struc') . '</td>
						</tr>
						<tr>
							<th>' . __d('search_plugin', 'Referentparcours.nom_complet') . '</th>
							<td>' . Hash::get($dossierpcg66affecte, 'Referentparcours.nom_complet') . '</td>
						</tr>
					</tbody>
				</table>';

            $gestionnaires = Set::enum(Hash::get($dossierpcg66affecte, 'Dossierpcg66.user_id'), $gestionnaire);

            echo $this->Xhtml->tableCells(
                    array(
                h($dossierpcg66affecte['Dossier']['numdemrsa']),
                h($dossierpcg66affecte['Personne']['nom'] . ' ' . $dossierpcg66affecte['Personne']['prenom']),
                h($dossierpcg66affecte['Adresse']['nomcom']),
                h(date_short($dossierpcg66affecte['Dossierpcg66']['datereceptionpdo'])),
                h($dossierpcg66affecte['Typepdo']['libelle']),
                h($dossierpcg66affecte['Originepdo']['libelle']),
                h($dossierpcg66affecte['Dossierpcg66']['orgpayeur']),
                h($dossierpcg66affecte['Serviceinstructeur']['lib_service']),
                h(Set::enum(Set::classicExtract($dossierpcg66affecte, 'Dossierpcg66.poledossierpcg66_id'), $polesdossierspcgs66)),
                h($gestionnaires),
                h(date_short($dossierpcg66affecte['Dossierpcg66']['dateaffectation'])),
                $this->Xhtml->viewLink(
                        'Voir le dossier', array('controller' => 'dossierspcgs66', 'action' => 'index', $dossierpcg66affecte['Dossierpcg66']['foyer_id']), $this->Permissions->check('dossierspcgs66', 'index')
                ),
                array($innerTable, array('class' => 'innerTableCell noprint')),
                    ), array('class' => 'odd', 'id' => 'innerTableTrigger' . $index), array('class' => 'even', 'id' => 'innerTableTrigger' . $index)
            );
            ?>
                <?php endforeach; ?>
            </tbody>
        </table>

                <?php echo $pagination; ?>
        <ul class="actionMenu">
            <li><?php
                echo $this->Xhtml->printLinkJs(
                        'Imprimer le tableau', array('onclick' => 'printit(); return false;', 'class' => 'noprint')
                );
                ?></li>
            <li><?php
                echo $this->Xhtml->exportLink(
                        'Télécharger le tableau', array('controller' => 'cohortesdossierspcgs66', 'action' => 'exportcsv', $this->action) + Hash::flatten($this->request->data, '__'), $this->Permissions->check('cohortesdossierspcgs66', 'exportcsv')
                );
                ?></li>
        </ul>
            <?php endif ?>

            <?php
         endif?>