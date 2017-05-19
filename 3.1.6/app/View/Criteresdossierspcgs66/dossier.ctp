<?php
if (Configure::read('debug') > 0) {
    echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
    echo $this->Html->script(array('prototype.event.simulate.js', 'dependantselect.js'));
}

$domain = 'dossierpcg66';
echo $this->Xhtml->tag(
        'h1', $this->pageTitle = __d('dossierpcg66', "Criteresdossierspcgs66::{$this->action}")
)
?>
<script type="text/javascript">
    document.observe("dom:loaded", function() {
        observeDisableFieldsetOnCheckbox('Dossierpcg66Datereceptionpdo', $('Dossierpcg66DatereceptionpdoFromDay').up('fieldset'), false);
        observeDisableFieldsetOnCheckbox('Decisiondossierpcg66Datetransmissionop', $('Decisiondossierpcg66DatetransmissionopFromDay').up('fieldset'), false);
        observeDisableFieldsetOnCheckbox('Decisiondossierpcg66Datevalidation', $('Decisiondossierpcg66DatevalidationFromDay').up('fieldset'), false);
    });
</script>
<?php
if (is_array($this->request->data)) {
    echo '<ul class="actionMenu"><li>' . $this->Xhtml->link(
            $this->Xhtml->image(
                    'icons/application_form_magnify.png', array('alt' => '')
            ) . ' Formulaire', '#', array('escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;")
    ) . '</li></ul>';
}

echo $this->Xform->create('Criteredossierpcg66', array('type' => 'post', 'action' => 'dossier', 'id' => 'Search', 'class' => ( ( is_array($this->request->data) && !empty($this->request->data['Dossierpcg66']['recherche']) ) ? 'folded' : 'unfolded' )));

echo $this->Form->input('Dossierpcg66.recherche', array('label' => false, 'type' => 'hidden', 'value' => true));
?>
<?php
echo $this->Search->blocAllocataire();
echo $this->Search->blocAdresse($mesCodesInsee, $cantons);
?>
<fieldset>
    <legend>Recherche par dossier</legend>
    <?php
    echo $this->Form->input('Dossier.numdemrsa', array('label' => 'Numéro de demande RSA'));
    echo $this->Form->input('Dossier.matricule', array('label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15));

    $valueDossierDernier = isset($this->request->data['Dossier']['dernier']) ? $this->request->data['Dossier']['dernier'] : true;
    echo $this->Form->input('Dossier.dernier', array('label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier));
    echo $this->Search->etatdosrsa($etatdosrsa);
    echo $this->Search->natpf($natpf);
    ?>
</fieldset>
<fieldset>
    <legend>Recherche par Dossier PCG</legend>
    <?php echo $this->Xform->input('Dossierpcg66.datereceptionpdo', array('label' => 'Filtrer par date de réception de la PDO', 'type' => 'checkbox')); ?>
    <fieldset>
        <legend>Date de réception de la PDO</legend>
        <?php
        $datereceptionpdo_from = Set::check($this->request->data, 'Dossierpcg66.datereceptionpdo_from') ? Set::extract($this->request->data, 'Dossierpcg66.datereceptionpdo_from') : strtotime('-1 week');
        $datereceptionpdo_to = Set::check($this->request->data, 'Dossierpcg66.datereceptionpdo_to') ? Set::extract($this->request->data, 'Dossierpcg66.datereceptionpdo_to') : strtotime('now');
        ?>
        <?php echo $this->Form->input('Dossierpcg66.datereceptionpdo_from', array('label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date('Y'), 'minYear' => date('Y') - 120, 'selected' => $datereceptionpdo_from)); ?>
        <?php echo $this->Form->input('Dossierpcg66.datereceptionpdo_to', array('label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date('Y'), 'minYear' => date('Y') - 120, 'maxYear' => date('Y') + 5, 'selected' => $datereceptionpdo_to)); ?>
    </fieldset>
    <?php
    ///Formulaire de recherche pour les PDOs
    echo $this->Default2->subform(
            array(
        'Dossierpcg66.originepdo_id' => array('label' => __d('dossierpcg66', 'Dossierpcg66.originepdo_id'), 'type' => 'select', 'options' => $originepdo, 'empty' => true),
        'Dossierpcg66.typepdo_id' => array('label' => __d('dossierpcg66', 'Dossierpcg66.typepdo_id'), 'type' => 'select', 'options' => $typepdo, 'empty' => true),
        'Dossierpcg66.orgpayeur' => array('label' => __d('dossierpcg66', 'Dossierpcg66.orgpayeur'), 'type' => 'select', 'options' => $orgpayeur, 'empty' => true),
        'Dossierpcg66.poledossierpcg66_id' => array('label' => __d('dossierpcg66', 'Dossierpcg66.poledossierpcg66_id'), 'type' => 'select', 'multiple' => 'checkbox', 'options' => $polesdossierspcgs66, 'empty' => false)
            ), array(
        'options' => $options
            )
    );

    echo '<fieldset class="col2 noborder">';
    echo $this->Xform->input('Dossierpcg66.user_id', array('label' => __d('dossierpcg66', 'Dossierpcg66.user_id'), 'type' => 'select', 'multiple' => 'checkbox', 'options' => $gestionnaire, 'empty' => false));
    echo '</fieldset>';

    echo $this->Search->date('Dossierpcg66.dateaffectation');
    ?>
    <?php
    echo $this->Search->etatDossierPCG66($etatdossierpcg);


    echo $this->Xform->input('Decisiondossierpcg66.org_id', array('label' => 'Organismes auxquels sont transmis les dossiers', 'type' => 'select', 'multiple' => 'checkbox', 'options' => $listorganismes, 'empty' => false));

    echo '<fieldset class="col2 noborder">';
    echo $this->Xform->input('Traitementpcg66.situationpdo_id', array('label' => 'Motif concernant la personne', 'type' => 'select', 'multiple' => 'checkbox', 'options' => $motifpersonnepcg66, 'empty' => false));
    echo '</fieldset>';
    echo '<fieldset class="col2 noborder">';
    echo $this->Xform->input('Traitementpcg66.statutpdo_id', array('label' => 'Statut concernant la personne', 'type' => 'select', 'multiple' => 'checkbox', 'options' => $statutpersonnepcg66, 'empty' => false));
    echo '</fieldset>';

// 		echo $this->Form->input('Dossierpcg66.exists', array( 'label' => 'Corbeille pleine ?', 'type' => 'select', 'options' => $exists, 'empty' => true ) );
    echo $this->Form->input('Decisiondossierpcg66.useravistechnique_id', array('label' => 'Agent ayant émis l\'avis technique', 'type' => 'select', 'options' => $gestionnaire, 'empty' => true));
    echo $this->Form->input('Decisiondossierpcg66.userproposition_id', array('label' => 'Agent ayant émis la validation', 'type' => 'select', 'options' => $gestionnaire, 'empty' => true));
    echo '<fieldset class="col2 noborder">';
    echo $this->Xform->input('Decisiondossierpcg66.decisionpdo_id', array('label' => 'Décision', 'type' => 'select', 'multiple' => 'checkbox', 'options' => $decisionpdo, 'empty' => false));
    echo '</fieldset>';
    ?>
    <!-- Par date de transmission à l'OP-->
    <?php echo $this->Xform->input('Decisiondossierpcg66.datevalidation', array('label' => 'Filtrer par date de décision', 'type' => 'checkbox')); ?>
    <fieldset>
        <legend>Date de décision</legend>
        <?php
        $datevalidation_from = Set::check($this->request->data, 'Decisiondossierpcg66.datevalidation_from') ? Set::extract($this->request->data, 'Dossierpcg66.datevalidation_from') : strtotime('-1 week');
        $datevalidation_to = Set::check($this->request->data, 'Decisiondossierpcg66.datevalidation_to') ? Set::extract($this->request->data, 'Dossierpcg66.datevalidation_to') : strtotime('now');
        ?>
        <?php echo $this->Form->input('Decisiondossierpcg66.datevalidation_from', array('label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date('Y'), 'minYear' => date('Y') - 120, 'selected' => $datevalidation_from)); ?>
        <?php echo $this->Form->input('Decisiondossierpcg66.datevalidation_to', array('label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date('Y'), 'minYear' => date('Y') - 120, 'maxYear' => date('Y') + 5, 'selected' => $datevalidation_to)); ?>
    </fieldset>

    <!-- Par date de transmission à l'OP-->
    <?php echo $this->Xform->input('Decisiondossierpcg66.datetransmissionop', array('label' => 'Filtrer par date de transmission à l\'OP', 'type' => 'checkbox')); ?>
    <fieldset>
        <legend>Date de transmission à l'OP</legend>
        <?php
        $datetransmissionop_from = Set::check($this->request->data, 'Decisiondossierpcg66.datetransmissionop_from') ? Set::extract($this->request->data, 'Dossierpcg66.datetransmissionop_from') : strtotime('-1 week');
        $datetransmissionop_to = Set::check($this->request->data, 'Decisiondossierpcg66.datetransmissionop_to') ? Set::extract($this->request->data, 'Dossierpcg66.datetransmissionop_to') : strtotime('now');
        ?>
        <?php echo $this->Form->input('Decisiondossierpcg66.datetransmissionop_from', array('label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date('Y'), 'minYear' => date('Y') - 120, 'selected' => $datetransmissionop_from)); ?>
        <?php echo $this->Form->input('Decisiondossierpcg66.datetransmissionop_to', array('label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date('Y'), 'minYear' => date('Y') - 120, 'maxYear' => date('Y') + 5, 'selected' => $datetransmissionop_to)); ?>
    </fieldset>
    <?php echo $this->Xform->input('Decisiondossierpcg66.nbproposition', array('label' => 'Nombre de propositions de décision')); ?>
</fieldset>
<?php
echo $this->Romev3->fieldset( 'Categorieromev3', array( 'options' => $options ) );
echo $this->Search->referentParcours($structuresreferentesparcours, $referentsparcours);
echo $this->Search->paginationNombretotal('Dossierpcg66.paginationNombreTotal');
?>
<div class="submit noprint">
    <?php echo $this->Xform->button('Rechercher', array('type' => 'submit')); ?>
    <?php echo $this->Xform->button('Réinitialiser', array('type' => 'reset')); ?>
</div>
<?php echo $this->Xform->end(); ?>
<?php $pagination = $this->Xpaginator->paginationBlock('Dossierpcg66', $this->passedArgs); ?>

<?php if (isset($criteresdossierspcgs66)): ?>
    <br />
    <h2 class="noprint aere">Résultats de la recherche</h2>

    <?php if (is_array($criteresdossierspcgs66) && count($criteresdossierspcgs66) > 0): ?>
        <?php echo $pagination; ?>
        <table class="tooltips">
            <thead>
                <tr>
                    <th><?php echo $this->Xpaginator->sort('N° dossier', 'Dossier.numdemrsa'); ?></th>
                    <th><?php echo $this->Xpaginator->sort('Nom de la personne concernée', 'Personne.nom'); ?></th>
                    <th><?php echo $this->Xpaginator->sort('Origine de la PDO', 'Dossierpcg66.originepdo_id'); ?></th>
                    <th><?php echo $this->Xpaginator->sort('Type de dossier', 'Dossierpcg66.typepdo_id'); ?></th>
                    <th><?php echo $this->Xpaginator->sort('Date de création de la DO', 'Dossierpcg66.datereceptionpdo'); ?></th>
                    <th><?php echo $this->Xpaginator->sort('Gestionnaire', 'Dossierpcg66.user_id'); ?></th>
                    <th><?php echo $this->Xpaginator->sort('Nb de propositions de décisions', 'Dossierpcg66.nbpropositions'); ?></th>
                    <th><?php echo $this->Xpaginator->sort('Etat du dossier', 'Dossierpcg66.etatdossierpcg'); ?></th>
                    <th><?php echo $this->Xpaginator->sort('Date de réception du document', 'Traitementpcg66.datereception'); ?></th>
                    <th><?php echo $this->Xpaginator->sort('Décision prise', 'Decisionpdo.libelle'); ?></th>
                    <th>Motifs de(s) la(es) personne(s)</th>
                    <th>Statuts de(s) la(es) personne(s)</th>
                    <th>Nb de fichiers dans la corbeille</th>
                    <th class="action noprint">Verrouillé</th>
                    <th class="action" colspan="2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($criteresdossierspcgs66 as $index => $criteredossierpcg66) {
//debug($criteredossierpcg66);
                    // Liste des organismes auxquels on transmet le dossier
                    $orgs = vfListeToArray($criteredossierpcg66['Orgtransmisdossierpcg66']['listorgs']);
                    if (!empty($orgs)) {
                        $orgs = implode(',', $orgs);
                    } else {
                        $orgs = '';
                    }

                    $datetransmission = '';
                    if ($criteredossierpcg66['Dossierpcg66']['etatdossierpcg'] == 'transmisop') {
                        $datetransmission = ' à ' . $orgs . ' le ' . date_short(Set::classicExtract($criteredossierpcg66, 'Decisiondossierpcg66.datetransmissionop'));
                    } else if ($criteredossierpcg66['Dossierpcg66']['etatdossierpcg'] == 'atttransmisop') {
                        $datetransmission = ' à ' . $orgs; //FIXME variable mal nommée mais plus simple à mettre en place
                    }

                    $etatdosrsaValue = Set::classicExtract($criteredossierpcg66, 'Situationdossierrsa.etatdosrsa');
                    $etatDossierRSA = isset($etatdosrsa[$etatdosrsaValue]) ? $etatdosrsa[$etatdosrsaValue] : 'Non défini';

                    $differentsMotifs = vfListeToArray($criteredossierpcg66['Personnepcg66']['listemotifs']);
                    if (!empty($differentsMotifs)) {
                        $differentsMotifs = '<ul><li>' . implode('</li><li>', $differentsMotifs) . '</li></ul>';
                    } else {
                        $differentsMotifs = '';
                    }

                    $differentsStatuts = vfListeToArray($criteredossierpcg66['Personnepcg66']['listestatuts']);
                    if (!empty($differentsStatuts)) {
                        $differentsStatuts = '<ul><li>' . implode('</li><li>', $differentsStatuts) . '</li></ul>';
                    } else {
                        $differentsStatuts = '';
                    }

                    if (!empty($criteredossierpcg66['Dossierpcg66']['poledossierpcg66_id'])) {
                        $infoGestionnaire = Hash::get($criteredossierpcg66, 'Poledossierpcg66.name') . ' / ' . Hash::get($criteredossierpcg66, 'User.nom_complet');
                    } else {
                        $infoGestionnaire = Hash::get($criteredossierpcg66, 'User.nom_complet');
                    }

                    $personnesConcernees = Hash::extract($criteredossierpcg66, 'Personneconcernee.{n}.Personne.nom_complet');
                    if (!empty($personnesConcernees)) {
                        $personnesConcernees = '<ul><li>' . implode('</li><li>', $personnesConcernees) . '</li></ul>';
                    } else {
                        $personnesConcernees = '';
                    }

                    $innerTable = '<table id="innerTable' . $index . '" class="innerTable">
							<tbody>
								<tr>
									<th>Etat du droit</th>
									<td>' . h($etatDossierRSA) . '</td>
								</tr>
								<tr>
									<th>Commune de naissance</th>
									<td>' . h($criteredossierpcg66['Personne']['nomcomnai']) . '</td>
								</tr>
								<tr>
									<th>Date de naissance</th>
									<td>' . h(date_short($criteredossierpcg66['Personne']['dtnai'])) . '</td>
								</tr>
								<tr>
									<th>Code INSEE</th>
									<td>' . h($criteredossierpcg66['Adresse']['numcom']) . '</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>' . h($criteredossierpcg66['Personne']['nir']) . '</td>
								</tr>
								<tr>
									<th>N° CAF</th>
									<td>' . h($criteredossierpcg66['Dossier']['matricule']) . '</td>
								</tr>
								<tr>
									<th>' . __d('search_plugin', 'Structurereferenteparcours.lib_struc') . '</th>
									<td>' . Hash::get($criteredossierpcg66, 'Structurereferenteparcours.lib_struc') . '</td>
								</tr>
								<tr>
									<th>' . __d('search_plugin', 'Referentparcours.nom_complet') . '</th>
									<td>' . Hash::get($criteredossierpcg66, 'Referentparcours.nom_complet') . '</td>
								</tr>
							</tbody>
						</table>';

                    echo $this->Xhtml->tableCells(
                            array(
                        h(Set::classicExtract($criteredossierpcg66, 'Dossier.numdemrsa')),
                        $personnesConcernees,
                        h(Set::enum(Set::classicExtract($criteredossierpcg66, 'Dossierpcg66.originepdo_id'), $originepdo)),
                        h(Set::enum(Set::classicExtract($criteredossierpcg66, 'Dossierpcg66.typepdo_id'), $typepdo)),
                        h($this->Locale->date('Locale->date', Set::classicExtract($criteredossierpcg66, 'Dossierpcg66.datereceptionpdo'))),
                        h($infoGestionnaire),
                        h(@$criteredossierpcg66['Dossierpcg66']['nbpropositions']),
                        h(Set::enum(Set::classicExtract($criteredossierpcg66, 'Dossierpcg66.etatdossierpcg'), $options['Dossierpcg66']['etatdossierpcg']) . $datetransmission),
						h($this->Locale->date('Locale->date', Hash::get($criteredossierpcg66, 'Traitementpcg66.datereception'))),
                        h($criteredossierpcg66['Decisionpdo']['libelle']),
                        $differentsMotifs,
                        $differentsStatuts,
                        h($criteredossierpcg66['Fichiermodule']['nb_fichiers_lies']),
                        array(
                            ( $criteredossierpcg66['Dossier']['locked'] ?
                                    $this->Xhtml->image(
                                            'icons/lock.png', array('alt' => '', 'title' => 'Dossier verrouillé')
                                    ) : null
                            ),
                            array('class' => 'noprint')
                        ),
                        $this->Xhtml->viewLink(
                            'Voir',
                            array('controller' => 'dossierspcgs66', 'action' => 'index', Set::classicExtract($criteredossierpcg66, 'Dossierpcg66.foyer_id'))
                        ),
                        $this->Xhtml->editLink(
                            'Modifier',
                            array('controller' => 'dossierspcgs66', 'action' => 'edit', Set::classicExtract($criteredossierpcg66, 'Dossierpcg66.id')),
                            $this->Permissions->check( 'dossierspcgs66', 'edit' )
                        ),
                        array($innerTable, array('class' => 'innerTableCell noprint')),
                            ), array('class' => 'odd', 'id' => 'innerTableTrigger' . $index), array('class' => 'even', 'id' => 'innerTableTrigger' . $index)
                    );
                }
                ?>
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
                'Télécharger le tableau', array('controller' => 'criteresdossierspcgs66', 'action' => 'exportcsv', 'searchDossier') + Hash::flatten($this->request->data, '__'), $this->Permissions->check('criteresdossierspcgs66', 'exportcsv')
        );
        ?></li>
        </ul>
    <?php else: ?>
        <p class="notice">Vos critères n'ont retourné aucun dossier PCG.</p>
    <?php endif ?>
<?php endif ?>
<?php echo $this->Search->observeDisableFormOnSubmit('Search'); ?>