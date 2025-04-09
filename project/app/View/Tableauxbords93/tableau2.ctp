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


//Année et trimestre
echo $this->Default3->subform(
    [
        'Search.annee_trimestre' =>
        [
            'type' => 'select',
            'options' => $options['annee_trimestre'],
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

//Structure
echo $this->Default3->subform(
    [
        'Search.structure' =>
        [
            'type' => 'select',
            'options' => $options['structure_referente'],
            'empty' => true,
            'required' => true
        ]
    ]
);

//Référent -facultatif simple
echo $this->Default3->subform(
    [
        'Search.referent' =>
        [
            'type' => 'select',
            'options' => $options['referent'],
            'empty' => true,
            'required' => false
        ]
    ]
);

echo $this->Default3->DefaultForm->buttons(['Search' => ['id' => 'rechercher_button']] );

echo $this->Default3->DefaultForm->end();



//Affichage du tableau de résultats
if(isset($resultats)){
?>
    <p><?= __d('tableauxbords93', 'Search.annee_trimestre')?> : <?=$params_affichage['date']?></p>
    <p><?= __d('tableauxbords93', 'Search.structure')?> : <?=$params_affichage['structure']?></p>
    <p><?= __d('tableauxbords93', 'Search.referent')?> : <?=$params_affichage['referent'] != null ? $params_affichage['referent'] : 'Non sélectionné'?></p>
    <p><?= __d('tableauxbords93', 'Search.numcom')?> : <?=$params_affichage['numcom'] != '' ? $params_affichage['numcom'] : 'Non sélectionné'?></p>

    <br/><br/>
    <table id = 'TableauResultats'>
        <thead>
            <tr>
                <th scope = 'col'></th>
                <th scope = 'col' style ='text-align:center'><?= __d('tableauxbords93', 'Tableau2.titre.colonneA')?></th>
                <th scope = 'col' style ='text-align:center'><?= __d('tableauxbords93', 'Tableau2.titre.colonneB')?></th>
                <th scope = 'col' style ='text-align:center'><?= __d('tableauxbords93', 'Tableau2.titre.colonneC')?></th>
                <th scope = 'col' style ='text-align:center'><?= __d('tableauxbords93', 'Tableau2.titre.colonneD')?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.t')?></th>
                <td style ='text-align:center'><?=$resultats['t_a']?></td>
                <td style ='text-align:center'><?=$resultats['t_b']?></td>
                <td style ='text-align:center'><?=$resultats['t_c']?></td>
                <td style ='text-align:center'><?=$resultats['t_d']?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row' colspan = '5' style = 'font-weight:bold'><?= __d('tableauxbords93', 'Tableau2.titre.c')?></th>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.c1bis')?></th>
                    <td style ='text-align:center'><?=$resultats['c1bis_a']?></td>
                    <td style ='text-align:center'><?=$resultats['c1bis_b']?></td>
                    <td style ='text-align:center'><?=$resultats['c1bis_c']?></td>
                    <td style ='text-align:center'><?=$resultats['c1bis_d']?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.c3')?></th>
                    <td style ='text-align:center'><?=$resultats['c3_a']?></td>
                    <td style ='text-align:center'><?=$resultats['c3_b']?></td>
                    <td style ='text-align:center'><?=$resultats['c3_c']?></td>
                    <td style ='text-align:center'><?=$resultats['c3_d']?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.c1')?></th>
                    <td style ='text-align:center'><?=$resultats['c1_a']?></td>
                    <td style ='text-align:center'><?=$resultats['c1_b']?></td>
                    <td style ='text-align:center'><?=$resultats['c1_c']?></td>
                    <td style ='text-align:center'><?=$resultats['c1_d']?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.c2')?></th>
                    <td style ='text-align:center'><?=$resultats['c2_a']?></td>
                    <td style ='text-align:center'><?=$resultats['c2_b']?></td>
                    <td style ='text-align:center'><?=$resultats['c2_c']?></td>
                    <td style ='text-align:center'><?=$resultats['c2_d']?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.c4')?></th>
                    <td style ='text-align:center'><?=$resultats['c4_a']?></td>
                    <td style ='text-align:center'><?=$resultats['c4_b']?></td>
                    <td style ='text-align:center'><?=$resultats['c4_c']?></td>
                    <td style ='text-align:center'><?=$resultats['c4_d']?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.c6') . $params_affichage['annee']?></th>
                    <td style ='text-align:center'><?=$resultats['c6_a']?></td>
                    <td style ='text-align:center'><?=$resultats['c6_b']?></td>
                    <td style ='text-align:center'><?=$resultats['c6_c']?></td>
                    <td style ='text-align:center'><?=$resultats['c6_d']?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.c7')  . $params_affichage['annee']?></th>
                    <td style ='text-align:center'><?=$resultats['c7_a']?></td>
                    <td style ='text-align:center'><?=$resultats['c7_b']?></td>
                    <td style ='text-align:center'><?=$resultats['c7_c']?></td>
                    <td style ='text-align:center'><?=$resultats['c7_d']?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row' colspan = '5' style = 'font-weight:bold'><?= __d('tableauxbords93', 'Tableau2.titre.p')?></th>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.p1')?></th>
                    <td style ='text-align:center'><?=$resultats['p1_a']?></td>
                    <td style ='text-align:center'><?=$resultats['p1_b']?></td>
                    <td style ='text-align:center'><?=$resultats['p1_c']?></td>
                    <td style ='text-align:center'><?=$resultats['p1_d']?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.p2')?></th>
                    <td style ='text-align:center'><?=$resultats['p2_a']?></td>
                    <td style ='text-align:center'><?=$resultats['p2_b']?></td>
                    <td style ='text-align:center'><?=$resultats['p2_c']?></td>
                    <td style ='text-align:center'><?=$resultats['p2_d']?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.p3')?></th>
                    <td style ='text-align:center'><?=$resultats['p3_a']?></td>
                    <td style ='text-align:center'><?=$resultats['p3_b']?></td>
                    <td style ='text-align:center'><?=$resultats['p3_c']?></td>
                    <td style ='text-align:center'><?=$resultats['p3_d']?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.p4')?></th>
                    <td style ='text-align:center'><?=$resultats['p4_a']?></td>
                    <td style ='text-align:center'><?=$resultats['p4_b']?></td>
                    <td style ='text-align:center'><?=$resultats['p4_c']?></td>
                    <td style ='text-align:center'><?=$resultats['p4_d']?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.p5')?></th>
                    <td style ='text-align:center'><?=$resultats['p5_a']?></td>
                    <td style ='text-align:center'><?=$resultats['p5_b']?></td>
                    <td style ='text-align:center'><?=$resultats['p5_c']?></td>
                    <td style ='text-align:center'><?=$resultats['p5_d']?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.p6')?></th>
                    <td style ='text-align:center'><?=$resultats['p6_a']?></td>
                    <td style ='text-align:center'><?=$resultats['p6_b']?></td>
                    <td style ='text-align:center'><?=$resultats['p6_c']?></td>
                    <td style ='text-align:center'><?=$resultats['p6_d']?></td>
            </tr>
            <tr class="even">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.p7')?></th>
                    <td style ='text-align:center'><?=$resultats['p7_a']?></td>
                    <td style ='text-align:center'><?=$resultats['p7_b']?></td>
                    <td style ='text-align:center'><?=$resultats['p7_c']?></td>
                    <td style ='text-align:center'><?=$resultats['p7_d']?></td>
            </tr>
            <tr class="odd">
                <th scope = 'row'><?= __d('tableauxbords93', 'Tableau2.titre.p8')?></th>
                    <td style ='text-align:center'><?=$resultats['p8_a']?></td>
                    <td style ='text-align:center'><?=$resultats['p8_b']?></td>
                    <td style ='text-align:center'><?=$resultats['p8_c']?></td>
                    <td style ='text-align:center'><?=$resultats['p8_d']?></td>
            </tr>
        </tbody>
    </table>

<?php
echo '<ul class="actionMenu">'
		.'<li>'
		. $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'controller' => 'tableauxbords93', 'action' => 'exportcsv_tableau2_donnees', '?' => ['date' => $params['date'], 'structure' => $params['structure'], 'referent' => $params['referent'], 'numcom' => $params['numcom']] )
		)
		.'</li>'
        .'<li>'
		. $this->Xhtml->exportdataLink(
			'Télécharger les données brutes',
			array( 'controller' => 'tableauxbords93', 'action' => 'exportcsv_tableau2_corpus', '?' => ['date' => $params['date'], 'structure' => $params['structure'], 'referent' => $params['referent'], 'numcom' => $params['numcom']] )
		)
		.'</li>'
	.'</ul>';

}



?>

<script type="text/javascript">

document.observe("dom:loaded", function() {

    //On cache le formulaire si on est sur une page de résultats
    if($('TableauResultats') != null){
        $('Tableauxbord93Tableau2Form').toggle();
    }

    dependantSelect( 'SearchReferent', 'SearchStructure' );

    //Le bouton enregistrer ne s'active que si tous les champs obligatoires sont remplis
    if($('SearchStructure').value == ''){
        $('rechercher_button').disable();
    }
    $('SearchStructure').observe( 'change', function( event ) {
        disableFieldsOnValue(
            'SearchStructure',
            [
                'rechercher_button',
            ],
            '',
            true
        );
    });
});


</script>