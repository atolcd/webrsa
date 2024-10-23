<?php
echo $this->Default3->titleForLayout();

$searchFormId = Inflector::camelize( Inflector::underscore( Inflector::classify( $this->request->params['controller'] ) )."_{$this->request->params['action']}_form" );
$actions['/'.Inflector::camelize( $this->request->params['controller'] ).'/'.$this->request->params['action'].'/#toggleform'] =  array(
    'title' => 'Visibilité formulaire',
    'text' => 'Formulaire',
    'class' => 'search',
    'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
);
echo $this->Default3->actions( $actions );
echo '<br/>';

//Formulaire
echo $this->Default3->DefaultForm->create( null, array( 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->params['action'] ), 'novalidate' => 'novalidate', 'id' => $searchFormId, 'class' => ( isset( $results ) ? 'folded' : 'unfolded' ) ) );


//Année
echo $this->Default3->subform(
    [
        'Search.annee' =>
        [
            'type' => 'select',
            'options' => $options['annee'],
            'required' => true
        ]
    ]
);
echo '<br/><br/>';

//Ville - facultatif multiple
echo $this->SearchForm->dependantCheckboxes(
    'Search.numcom',
    [
        'domain' => 'tableauxbords93',
        'type' => 'select',
        'multiple' => 'checkbox',
        'options' => $options['numcom'],
        'class' => 'divideInto3Columns',
        'buttons' => true,
        'autoCheck' => true,
        'hiddenField' => TRUE
    ]
);
echo '<br/><br/>';


//Structure referente
echo $this->SearchForm->multipleCheckboxes(
    'Search.structure',
    [
        'domain' => 'tableauxbords93',
        'type' => 'select',
        'multiple' => 'checkbox',
        'options' => $options['structure_referente'],
        'buttons' => true,
        'required' => true,
        'class' => 'a_observer'
    ]
);
echo '<br/><br/>';

//Référent -facultatif simple
echo $this->Default3->subform(
    [
        'Search.referent' =>
        [
            'type' => 'select',
            'options' => $options['referents'],
            'empty' => true,
            'required' => false
        ]
    ]
);

$referents = $options['referent'];

echo $this->Default3->DefaultForm->buttons(['Search' => ['id' => 'rechercher_button']] );

echo $this->Default3->DefaultForm->end();



//Affichage du tableau de résultats
if(isset($resultats)){
    // debug($resultats);
    ?>
    <h3> Périmètre : Nouveaux allocataires orientés </h3>
    <table id = 'TableauResultats1'>
        <thead>
            <tr>
                <th scope = 'col'></th>
                <th scope = 'col' style ='text-align:center'>Total <?= $annee-1 ?></th>
                <th scope = 'col' style ='text-align:center'>Janvier <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Février <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Mars <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Avril <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Mai <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Juin <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Juillet <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Août <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Septembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Octobre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Novembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Décembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Total <?= $annee?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.nveau_orient')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['nveau_orient'])) {echo $resultats[$annee-1]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['nveau_orient'])) {echo $resultats[1]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['nveau_orient'])) {echo $resultats[2]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['nveau_orient'])) {echo $resultats[3]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['nveau_orient'])) {echo $resultats[4]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['nveau_orient'])) {echo $resultats[5]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['nveau_orient'])) {echo $resultats[6]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['nveau_orient'])) {echo $resultats[7]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['nveau_orient'])) {echo $resultats[8]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['nveau_orient'])) {echo $resultats[9]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['nveau_orient'])) {echo $resultats[10]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['nveau_orient'])) {echo $resultats[11]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['nveau_orient'])) {echo $resultats[12]['nveau_orient'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['nveau_orient'])) {echo $resultats[$annee]['nveau_orient'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.nveau_orien_diag')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['nveau_orien_diag'])) {echo $resultats[$annee-1]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['nveau_orien_diag'])) {echo $resultats[1]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['nveau_orien_diag'])) {echo $resultats[2]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['nveau_orien_diag'])) {echo $resultats[3]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['nveau_orien_diag'])) {echo $resultats[4]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['nveau_orien_diag'])) {echo $resultats[5]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['nveau_orien_diag'])) {echo $resultats[6]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['nveau_orien_diag'])) {echo $resultats[7]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['nveau_orien_diag'])) {echo $resultats[8]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['nveau_orien_diag'])) {echo $resultats[9]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['nveau_orien_diag'])) {echo $resultats[10]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['nveau_orien_diag'])) {echo $resultats[11]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['nveau_orien_diag'])) {echo $resultats[12]['nveau_orien_diag'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['nveau_orien_diag'])) {echo $resultats[$annee]['nveau_orien_diag'];} ?></td>
            </tr>
        </tbody>
    </table>
    <p style="font-style: italic;">A noter : Le référent de parcours sélectionné dans le formulaire n'est pas pris en compte dans le calcul de ce tableau</p>
    <?php
    echo '<ul class="actionMenu">'
    .'<li>'
    . $this->Xhtml->exportLink(
        'Télécharger le tableau',
        array( 'controller' => 'tableauxbords93', 'action' => 'exportcsv_tableau1_donnees', '?' => ['annee' => $params['annee'], 'structures' => $params['structures'], 'referent' => $params['referent'], 'numcom' => $params['numcom'], 'tableau' => 'A'] )
    )
    .'</li>'
    .'<li>'
    . $this->Xhtml->exportdataLink(
        'Télécharger les données brutes',
        array( 'controller' => 'tableauxbords93', 'action' => 'exportcsv_tableau1_corpus', '?' => ['annee' => $params['annee'], 'structures' => $params['structures'], 'referent' => $params['referent'], 'numcom' => $params['numcom'], 'tableau' => 'A'] )
    )
    .'</li>'
.'</ul>';

    ?>

    <br/><br/><br/>

    <h3> Périmètre : Tous les allocataires orientés </h3>
    <table id = 'TableauResultats2'>
        <thead>
            <tr>
                <th scope = 'col'></th>
                <th scope = 'col' style ='text-align:center'>Total <?= $annee-1 ?></th>
                <th scope = 'col' style ='text-align:center'>Janvier <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Février <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Mars <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Avril <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Mai <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Juin <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Juillet <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Août <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Septembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Octobre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Novembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Décembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Total <?= $annee?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1a3')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1a3'])) {echo $resultats[$annee-1]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1a3'])) {echo $resultats[1]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1a3'])) {echo $resultats[2]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1a3'])) {echo $resultats[3]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1a3'])) {echo $resultats[4]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1a3'])) {echo $resultats[5]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1a3'])) {echo $resultats[6]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1a3'])) {echo $resultats[7]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1a3'])) {echo $resultats[8]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1a3'])) {echo $resultats[9]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1a3'])) {echo $resultats[10]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1a3'])) {echo $resultats[11]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1a3'])) {echo $resultats[12]['tdb1a3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1a3'])) {echo $resultats[$annee]['tdb1a3'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1a4')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1a4'])) {echo $resultats[$annee-1]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1a4'])) {echo $resultats[1]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1a4'])) {echo $resultats[2]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1a4'])) {echo $resultats[3]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1a4'])) {echo $resultats[4]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1a4'])) {echo $resultats[5]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1a4'])) {echo $resultats[6]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1a4'])) {echo $resultats[7]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1a4'])) {echo $resultats[8]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1a4'])) {echo $resultats[9]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1a4'])) {echo $resultats[10]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1a4'])) {echo $resultats[11]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1a4'])) {echo $resultats[12]['tdb1a4'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1a4'])) {echo $resultats[$annee]['tdb1a4'];} ?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1a5')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1a5'])) {echo $resultats[$annee-1]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1a5'])) {echo $resultats[1]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1a5'])) {echo $resultats[2]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1a5'])) {echo $resultats[3]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1a5'])) {echo $resultats[4]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1a5'])) {echo $resultats[5]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1a5'])) {echo $resultats[6]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1a5'])) {echo $resultats[7]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1a5'])) {echo $resultats[8]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1a5'])) {echo $resultats[9]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1a5'])) {echo $resultats[10]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1a5'])) {echo $resultats[11]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1a5'])) {echo $resultats[12]['tdb1a5'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1a5'])) {echo $resultats[$annee]['tdb1a5'];} ?></td>
            </tr>
        </tbody>
    </table>

    <?php
    echo '<ul class="actionMenu">'
		.'<li>'
		. $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'controller' => 'tableauxbords93', 'action' => 'exportcsv_tableau1_donnees', '?' => ['annee' => $params['annee'], 'structures' => $params['structures'], 'referent' => $params['referent'], 'numcom' => $params['numcom'], 'tableau' => 'B'] )
		)
		.'</li>'
        .'<li>'
		. $this->Xhtml->exportdataLink(
			'Télécharger les données brutes',
			array( 'controller' => 'tableauxbords93', 'action' => 'exportcsv_tableau1_corpus', '?' => ['annee' => $params['annee'], 'structures' => $params['structures'], 'referent' => $params['referent'], 'numcom' => $params['numcom'], 'tableau' => 'B'] )
		)
		.'</li>'
	.'</ul>';

    ?>



    <br/><br/><br/>

    <h3> Périmètre : Rendez-vous de la structure sélectionnée </h3>
    <table id = 'TableauResultats3'>
        <thead>
            <tr>
                <th scope = 'col'></th>
                <th scope = 'col' style ='text-align:center'>Total <?= $annee-1 ?></th>
                <th scope = 'col' style ='text-align:center'>Janvier <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Février <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Mars <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Avril <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Mai <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Juin <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Juillet <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Août <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Septembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Octobre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Novembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Décembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Total <?= $annee?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b1')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b1'])) {echo $resultats[$annee-1]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b1'])) {echo $resultats[1]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b1'])) {echo $resultats[2]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b1'])) {echo $resultats[3]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b1'])) {echo $resultats[4]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b1'])) {echo $resultats[5]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b1'])) {echo $resultats[6]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b1'])) {echo $resultats[7]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b1'])) {echo $resultats[8]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b1'])) {echo $resultats[9]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b1'])) {echo $resultats[10]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b1'])) {echo $resultats[11]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b1'])) {echo $resultats[12]['tdb1b1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b1'])) {echo $resultats[$annee]['tdb1b1'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b1a')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b1a'])) {echo $resultats[$annee-1]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b1a'])) {echo $resultats[1]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b1a'])) {echo $resultats[2]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b1a'])) {echo $resultats[3]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b1a'])) {echo $resultats[4]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b1a'])) {echo $resultats[5]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b1a'])) {echo $resultats[6]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b1a'])) {echo $resultats[7]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b1a'])) {echo $resultats[8]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b1a'])) {echo $resultats[9]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b1a'])) {echo $resultats[10]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b1a'])) {echo $resultats[11]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b1a'])) {echo $resultats[12]['tdb1b1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b1a'])) {echo $resultats[$annee]['tdb1b1a'];} ?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b1b')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b1b'])) {echo $resultats[$annee-1]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b1b'])) {echo $resultats[1]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b1b'])) {echo $resultats[2]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b1b'])) {echo $resultats[3]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b1b'])) {echo $resultats[4]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b1b'])) {echo $resultats[5]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b1b'])) {echo $resultats[6]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b1b'])) {echo $resultats[7]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b1b'])) {echo $resultats[8]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b1b'])) {echo $resultats[9]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b1b'])) {echo $resultats[10]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b1b'])) {echo $resultats[11]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b1b'])) {echo $resultats[12]['tdb1b1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b1b'])) {echo $resultats[$annee]['tdb1b1b'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b1c')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b1c'])) {echo $resultats[$annee-1]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b1c'])) {echo $resultats[1]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b1c'])) {echo $resultats[2]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b1c'])) {echo $resultats[3]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b1c'])) {echo $resultats[4]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b1c'])) {echo $resultats[5]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b1c'])) {echo $resultats[6]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b1c'])) {echo $resultats[7]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b1c'])) {echo $resultats[8]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b1c'])) {echo $resultats[9]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b1c'])) {echo $resultats[10]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b1c'])) {echo $resultats[11]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b1c'])) {echo $resultats[12]['tdb1b1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b1c'])) {echo $resultats[$annee]['tdb1b1c'];} ?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b1d')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b1d'])) {echo $resultats[$annee-1]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b1d'])) {echo $resultats[1]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b1d'])) {echo $resultats[2]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b1d'])) {echo $resultats[3]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b1d'])) {echo $resultats[4]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b1d'])) {echo $resultats[5]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b1d'])) {echo $resultats[6]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b1d'])) {echo $resultats[7]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b1d'])) {echo $resultats[8]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b1d'])) {echo $resultats[9]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b1d'])) {echo $resultats[10]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b1d'])) {echo $resultats[11]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b1d'])) {echo $resultats[12]['tdb1b1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b1d'])) {echo $resultats[$annee]['tdb1b1d'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b2')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b2'])) {echo $resultats[$annee-1]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b2'])) {echo $resultats[1]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b2'])) {echo $resultats[2]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b2'])) {echo $resultats[3]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b2'])) {echo $resultats[4]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b2'])) {echo $resultats[5]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b2'])) {echo $resultats[6]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b2'])) {echo $resultats[7]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b2'])) {echo $resultats[8]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b2'])) {echo $resultats[9]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b2'])) {echo $resultats[10]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b2'])) {echo $resultats[11]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b2'])) {echo $resultats[12]['tdb1b2'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b2'])) {echo $resultats[$annee]['tdb1b2'];} ?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b2a')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b2a'])) {echo $resultats[$annee-1]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b2a'])) {echo $resultats[1]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b2a'])) {echo $resultats[2]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b2a'])) {echo $resultats[3]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b2a'])) {echo $resultats[4]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b2a'])) {echo $resultats[5]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b2a'])) {echo $resultats[6]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b2a'])) {echo $resultats[7]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b2a'])) {echo $resultats[8]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b2a'])) {echo $resultats[9]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b2a'])) {echo $resultats[10]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b2a'])) {echo $resultats[11]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b2a'])) {echo $resultats[12]['tdb1b2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b2a'])) {echo $resultats[$annee]['tdb1b2a'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b2b')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b2b'])) {echo $resultats[$annee-1]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b2b'])) {echo $resultats[1]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b2b'])) {echo $resultats[2]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b2b'])) {echo $resultats[3]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b2b'])) {echo $resultats[4]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b2b'])) {echo $resultats[5]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b2b'])) {echo $resultats[6]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b2b'])) {echo $resultats[7]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b2b'])) {echo $resultats[8]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b2b'])) {echo $resultats[9]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b2b'])) {echo $resultats[10]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b2b'])) {echo $resultats[11]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b2b'])) {echo $resultats[12]['tdb1b2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b2b'])) {echo $resultats[$annee]['tdb1b2b'];} ?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b2c')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b2c'])) {echo $resultats[$annee-1]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b2c'])) {echo $resultats[1]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b2c'])) {echo $resultats[2]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b2c'])) {echo $resultats[3]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b2c'])) {echo $resultats[4]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b2c'])) {echo $resultats[5]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b2c'])) {echo $resultats[6]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b2c'])) {echo $resultats[7]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b2c'])) {echo $resultats[8]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b2c'])) {echo $resultats[9]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b2c'])) {echo $resultats[10]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b2c'])) {echo $resultats[11]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b2c'])) {echo $resultats[12]['tdb1b2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b2c'])) {echo $resultats[$annee]['tdb1b2c'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b2d')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b2d'])) {echo $resultats[$annee-1]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b2d'])) {echo $resultats[1]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b2d'])) {echo $resultats[2]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b2d'])) {echo $resultats[3]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b2d'])) {echo $resultats[4]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b2d'])) {echo $resultats[5]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b2d'])) {echo $resultats[6]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b2d'])) {echo $resultats[7]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b2d'])) {echo $resultats[8]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b2d'])) {echo $resultats[9]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b2d'])) {echo $resultats[10]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b2d'])) {echo $resultats[11]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b2d'])) {echo $resultats[12]['tdb1b2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b2d'])) {echo $resultats[$annee]['tdb1b2d'];} ?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1b3')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1b3'])) {echo $resultats[$annee-1]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1b3'])) {echo $resultats[1]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1b3'])) {echo $resultats[2]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1b3'])) {echo $resultats[3]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1b3'])) {echo $resultats[4]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1b3'])) {echo $resultats[5]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1b3'])) {echo $resultats[6]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1b3'])) {echo $resultats[7]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1b3'])) {echo $resultats[8]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1b3'])) {echo $resultats[9]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1b3'])) {echo $resultats[10]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1b3'])) {echo $resultats[11]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1b3'])) {echo $resultats[12]['tdb1b3'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1b3'])) {echo $resultats[$annee]['tdb1b3'];} ?></td>
            </tr>
        </tbody>
    </table>

    <?php
    echo '<ul class="actionMenu">'
		.'<li>'
		. $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'controller' => 'tableauxbords93', 'action' => 'exportcsv_tableau1_donnees', '?' => ['annee' => $params['annee'], 'structures' => $params['structures'], 'referent' => $params['referent'], 'numcom' => $params['numcom'], 'tableau' => 'C'] )
		)
		.'</li>'
        .'<li>'
		. $this->Xhtml->exportdataLink(
			'Télécharger les données brutes',
			array( 'controller' => 'tableauxbords93', 'action' => 'exportcsv_tableau1_corpus', '?' => ['annee' => $params['annee'], 'structures' => $params['structures'], 'referent' => $params['referent'], 'numcom' => $params['numcom'], 'tableau' => 'C'] )
		)
		.'</li>'
	.'</ul>';

    ?>


<br/><br/><br/>

    <h3> Périmètre : CER signés par l'allocataire dans la structure sélectionnée </h3>
    <table id = 'TableauResultats4'>
        <thead>
            <tr>
                <th scope = 'col'></th>
                <th scope = 'col' style ='text-align:center'>Total <?= $annee-1 ?></th>
                <th scope = 'col' style ='text-align:center'>Janvier <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Février <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Mars <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Avril <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Mai <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Juin <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Juillet <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Août <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Septembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Octobre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Novembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Décembre <?= $annee?></th>
                <th scope = 'col' style ='text-align:center'>Total <?= $annee?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c1')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c1'])) {echo $resultats[$annee-1]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c1'])) {echo $resultats[1]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c1'])) {echo $resultats[2]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c1'])) {echo $resultats[3]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c1'])) {echo $resultats[4]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c1'])) {echo $resultats[5]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c1'])) {echo $resultats[6]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c1'])) {echo $resultats[7]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c1'])) {echo $resultats[8]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c1'])) {echo $resultats[9]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c1'])) {echo $resultats[10]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c1'])) {echo $resultats[11]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c1'])) {echo $resultats[12]['tdb1c1'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c1'])) {echo $resultats[$annee]['tdb1c1'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c1a')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c1a'])) {echo $resultats[$annee-1]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c1a'])) {echo $resultats[1]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c1a'])) {echo $resultats[2]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c1a'])) {echo $resultats[3]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c1a'])) {echo $resultats[4]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c1a'])) {echo $resultats[5]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c1a'])) {echo $resultats[6]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c1a'])) {echo $resultats[7]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c1a'])) {echo $resultats[8]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c1a'])) {echo $resultats[9]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c1a'])) {echo $resultats[10]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c1a'])) {echo $resultats[11]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c1a'])) {echo $resultats[12]['tdb1c1a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c1a'])) {echo $resultats[$annee]['tdb1c1a'];} ?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c1b')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c1b'])) {echo $resultats[$annee-1]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c1b'])) {echo $resultats[1]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c1b'])) {echo $resultats[2]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c1b'])) {echo $resultats[3]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c1b'])) {echo $resultats[4]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c1b'])) {echo $resultats[5]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c1b'])) {echo $resultats[6]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c1b'])) {echo $resultats[7]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c1b'])) {echo $resultats[8]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c1b'])) {echo $resultats[9]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c1b'])) {echo $resultats[10]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c1b'])) {echo $resultats[11]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c1b'])) {echo $resultats[12]['tdb1c1b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c1b'])) {echo $resultats[$annee]['tdb1c1b'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c1c')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c1c'])) {echo $resultats[$annee-1]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c1c'])) {echo $resultats[1]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c1c'])) {echo $resultats[2]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c1c'])) {echo $resultats[3]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c1c'])) {echo $resultats[4]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c1c'])) {echo $resultats[5]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c1c'])) {echo $resultats[6]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c1c'])) {echo $resultats[7]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c1c'])) {echo $resultats[8]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c1c'])) {echo $resultats[9]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c1c'])) {echo $resultats[10]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c1c'])) {echo $resultats[11]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c1c'])) {echo $resultats[12]['tdb1c1c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c1c'])) {echo $resultats[$annee]['tdb1c1c'];} ?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c1d')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c1d'])) {echo $resultats[$annee-1]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c1d'])) {echo $resultats[1]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c1d'])) {echo $resultats[2]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c1d'])) {echo $resultats[3]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c1d'])) {echo $resultats[4]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c1d'])) {echo $resultats[5]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c1d'])) {echo $resultats[6]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c1d'])) {echo $resultats[7]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c1d'])) {echo $resultats[8]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c1d'])) {echo $resultats[9]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c1d'])) {echo $resultats[10]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c1d'])) {echo $resultats[11]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c1d'])) {echo $resultats[12]['tdb1c1d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c1d'])) {echo $resultats[$annee]['tdb1c1d'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row' colspan = '15'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c2')?></th>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c2a')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c2a'])) {echo $resultats[$annee-1]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c2a'])) {echo $resultats[1]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c2a'])) {echo $resultats[2]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c2a'])) {echo $resultats[3]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c2a'])) {echo $resultats[4]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c2a'])) {echo $resultats[5]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c2a'])) {echo $resultats[6]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c2a'])) {echo $resultats[7]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c2a'])) {echo $resultats[8]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c2a'])) {echo $resultats[9]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c2a'])) {echo $resultats[10]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c2a'])) {echo $resultats[11]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c2a'])) {echo $resultats[12]['tdb1c2a'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c2a'])) {echo $resultats[$annee]['tdb1c2a'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c2b')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c2b'])) {echo $resultats[$annee-1]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c2b'])) {echo $resultats[1]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c2b'])) {echo $resultats[2]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c2b'])) {echo $resultats[3]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c2b'])) {echo $resultats[4]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c2b'])) {echo $resultats[5]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c2b'])) {echo $resultats[6]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c2b'])) {echo $resultats[7]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c2b'])) {echo $resultats[8]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c2b'])) {echo $resultats[9]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c2b'])) {echo $resultats[10]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c2b'])) {echo $resultats[11]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c2b'])) {echo $resultats[12]['tdb1c2b'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c2b'])) {echo $resultats[$annee]['tdb1c2b'];} ?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c2c')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c2c'])) {echo $resultats[$annee-1]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c2c'])) {echo $resultats[1]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c2c'])) {echo $resultats[2]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c2c'])) {echo $resultats[3]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c2c'])) {echo $resultats[4]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c2c'])) {echo $resultats[5]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c2c'])) {echo $resultats[6]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c2c'])) {echo $resultats[7]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c2c'])) {echo $resultats[8]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c2c'])) {echo $resultats[9]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c2c'])) {echo $resultats[10]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c2c'])) {echo $resultats[11]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c2c'])) {echo $resultats[12]['tdb1c2c'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c2c'])) {echo $resultats[$annee]['tdb1c2c'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c2d')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c2d'])) {echo $resultats[$annee-1]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c2d'])) {echo $resultats[1]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c2d'])) {echo $resultats[2]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c2d'])) {echo $resultats[3]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c2d'])) {echo $resultats[4]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c2d'])) {echo $resultats[5]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c2d'])) {echo $resultats[6]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c2d'])) {echo $resultats[7]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c2d'])) {echo $resultats[8]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c2d'])) {echo $resultats[9]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c2d'])) {echo $resultats[10]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c2d'])) {echo $resultats[11]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c2d'])) {echo $resultats[12]['tdb1c2d'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c2d'])) {echo $resultats[$annee]['tdb1c2d'];} ?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c2e')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c2e'])) {echo $resultats[$annee-1]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c2e'])) {echo $resultats[1]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c2e'])) {echo $resultats[2]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c2e'])) {echo $resultats[3]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c2e'])) {echo $resultats[4]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c2e'])) {echo $resultats[5]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c2e'])) {echo $resultats[6]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c2e'])) {echo $resultats[7]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c2e'])) {echo $resultats[8]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c2e'])) {echo $resultats[9]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c2e'])) {echo $resultats[10]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c2e'])) {echo $resultats[11]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c2e'])) {echo $resultats[12]['tdb1c2e'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c2e'])) {echo $resultats[$annee]['tdb1c2e'];} ?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau1a.titre.tdb1c2f')?></th>
                <td style ='text-align:center'><?php if(isset($resultats[$annee-1]['tdb1c2f'])) {echo $resultats[$annee-1]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[1]['tdb1c2f'])) {echo $resultats[1]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[2]['tdb1c2f'])) {echo $resultats[2]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[3]['tdb1c2f'])) {echo $resultats[3]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[4]['tdb1c2f'])) {echo $resultats[4]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[5]['tdb1c2f'])) {echo $resultats[5]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[6]['tdb1c2f'])) {echo $resultats[6]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[7]['tdb1c2f'])) {echo $resultats[7]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[8]['tdb1c2f'])) {echo $resultats[8]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[9]['tdb1c2f'])) {echo $resultats[9]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[10]['tdb1c2f'])) {echo $resultats[10]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[11]['tdb1c2f'])) {echo $resultats[11]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[12]['tdb1c2f'])) {echo $resultats[12]['tdb1c2f'];} ?></td>
                <td style ='text-align:center'><?php if(isset($resultats[$annee]['tdb1c2f'])) {echo $resultats[$annee]['tdb1c2f'];} ?></td>
            </tr>
        </tbody>
    </table>



    <?php
    echo '<ul class="actionMenu">'
		.'<li>'
		. $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'controller' => 'tableauxbords93', 'action' => 'exportcsv_tableau1_donnees', '?' => ['annee' => $params['annee'], 'structures' => $params['structures'], 'referent' => $params['referent'], 'numcom' => $params['numcom'], 'tableau' => 'D'] )
		)
		.'</li>'
        .'<li>'
		. $this->Xhtml->exportdataLink(
			'Télécharger les données brutes',
			array( 'controller' => 'tableauxbords93', 'action' => 'exportcsv_tableau1_corpus', '?' => ['annee' => $params['annee'], 'structures' => $params['structures'], 'referent' => $params['referent'], 'numcom' => $params['numcom'], 'tableau' => 'D'] )
		)
		.'</li>'
	.'</ul>';

    ?>


    <?php
}

?>


<script type="text/javascript">

document.observe("dom:loaded", function() {


    //On cache le formulaire si on est sur une page de résultats
    if($('TableauResultats1') != null){
        $('Tableauxbord93Tableau1Form').toggle();
    }

    //On ajoute la classe manquante sur le fieldset des structures
    $('SearchStructure').parentElement.classList.add("divideInto2Columns");

    //On met à jour le référent
    selector = "input[name='data[Search][structure][]']";
    toutCocher(selector);
    updateReferents();
    ref = <?php echo json_encode($params['referent']) ?>;
    if(ref !== "non"){
        select_ref = document.getElementById("SearchReferent");
        select_ref.value = <?php echo $params['referent'] ?>;
    }


    //a chaque changement de structure, on remet à jour la liste des référents
    list = document.getElementsByClassName("a_observer");
    for(let item of list){
        item.children[0].observe( 'change', function( event ) {
            updateReferents();
        });
    }

    document.getElementById('toutcocher').onclick = function( event ) {
            toutCocher(selector);
            updateReferents();
    };

    document.getElementById('toutdecocher').onclick = function( event ) {
            toutDecocher(selector);
            updateReferents();
    };
});

function getCheckedStructures(){

    structures = [];
    list = document.getElementsByClassName("a_observer");
    for(let item of list){
        if(item.children[0].checked) {
            structures.push([(item.children[0].value),(item.children[0].labels[0].innerText)]);
        }
    }

    return structures;
}

function getReferents(structures){

    referentsbase = <?php echo json_encode($referents) ?>;
    referents = '<option value=""></option>';


    for (struct of structures) {
        if(typeof referentsbase[struct[0]] !== 'undefined'){
            referents += '<optgroup label="'+struct[1]+'">';

            console.log(struct[0]);
            console.log(referentsbase[struct[0]]);
            for(const [key, value] of Object.entries(referentsbase[struct[0]])){
                referents += '<option value="'+key+'">'+value+'</option>';
            }
            referents += '</optgroup>';
        }
    };

    return referents;
}

function updateReferents(){
    //on récupère toutes les structures cochées
    structures = getCheckedStructures();

    //on récupère les options des référents en fonction des structures actuellement cochées
    referents = getReferents(structures);

    //on update la liste des référents
    select_ref = document.getElementById("SearchReferent");
    select_ref.innerHTML = referents;
}


</script>