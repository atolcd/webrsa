<?php
	$this->pageTitle = 'APRE/ADREs notifiées';

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
<?php echo $this->Xform->create( 'Cohortevalidationapre66', array( 'type' => 'post', 'action' => $this->action,  'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>

        <fieldset>
            <?php /*echo $this->Xform->input( 'Cohortevalidationapre66.validees', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );*/?>
			<?php echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );?>

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


<?php if( isset( $cohortevalidationapre66 ) ):?>
<h2 class="noprint">Résultats de la recherche</h2>
    <?php if( empty( $cohortevalidationapre66 ) ):?>
        <?php
            switch( $this->action ) {
                case 'validees':
                    $message = 'Aucune APRE/ADRE ne correspond à vos critères.';
                    break;
                default:
                    $message = 'Aucune APRE/ADRE de validée n\'a été trouvée.';
            }
        ?>
        <p class="notice"><?php echo $message;?></p>
    <?php else:?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'Apre66', $this->passedArgs ); ?>
<?php echo $pagination;?>
	<?php
		foreach( Hash::flatten( $this->request->data['Search'] ) as $filtre => $value  ) {
			echo $this->Form->input( "Search.{$filtre}", array( 'type' => 'hidden', 'value' => $value ) );
		}
	?>
    <table id="searchResults" class="tooltips">
        <thead>
            <tr>
                <th>N° Demande APRE/ADRE</th>
                <th>Nom de l'allocataire</th>
                <th>Référent APRE/ADRE</th>
                <th>Date demande APRE/ADRE</th>
                <th>Etat du dossier</th>
                <th>Décision</th>
                <th>Montant accordé</th>
                <th>Motif du rejet</th>
                <th>Date de la décision</th>
                <th colspan="2" class="action">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach( $cohortevalidationapre66 as $index => $validationapre ):?>
            <?php

                    $innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
                        <tbody>
							<tr>
                                <th>Thème de l\'aide</th>
                                <td>'.h( $validationapre['Themeapre66']['name'] ).'</td>
                            </tr>
                             <tr>
                                <th>Type d\'aide</th>
                                <td>'.h( $validationapre['Typeaideapre66']['name'] ).'</td>
                            </tr>
                            <tr>
                                <th>Date naissance</th>
                                <td>'.h( date_short( $validationapre['Personne']['dtnai'] ) ).'</td>
                            </tr>
                            <tr>
                                <th>' . __d( 'dossier', 'Dossier.matricule.large' ) . '</th>
                                <td>'.h( $validationapre['Dossier']['matricule'] ).'</td>
                            </tr>
                            <tr>
                                <th>NIR</th>
                                <td>'.h( $validationapre['Personne']['nir'] ).'</td>
                            </tr>
                            <tr>
                                <th>Code postal</th>
                                <td>'.h( $validationapre['Adresse']['codepos'] ).'</td>
                            </tr>
                            <tr>
                                <th>Commune</th>
                                <td>'.h( $validationapre['Adresse']['nomcom'] ).'</td>
                            </tr>
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

                    echo $this->Xhtml->tableCells(
						array(
							h( $validationapre['Apre66']['numeroapre'] ),
							h( $validationapre['Personne']['nom_complet'] ),
							h( $validationapre['Referent']['nom_complet'] ),
							h( date_short(  $validationapre['Aideapre66']['datedemande'] ) ),
							h( Set::enum( Set::classicExtract( $validationapre, 'Apre66.etatdossierapre' ), $options['etatdossierapre'] ) ),
							h( Set::enum( Set::classicExtract( $validationapre, 'Aideapre66.decisionapre' ), $optionsaideapre66['decisionapre'] ) ),
							h( (  $validationapre['Aideapre66']['montantaccorde'] ) ),
							h( $validationapre['Aideapre66']['motifrejetequipe'] ),
							h( date_short(  $validationapre['Aideapre66']['datemontantaccorde'] ) ),
							$this->Xhtml->viewLink(
								'Voir le contrat',
								array( 'controller' => 'apres66', 'action' => 'index', $validationapre['Personne']['id'] ),
								$this->Permissions->check( 'apres66', 'index' )
							),
							$this->Xhtml->notificationsApreLink(
								'Notifier la décision',
								array( 'controller' => 'apres66', 'action' => 'notifications', $validationapre['Apre66']['id'] ),
								$this->Permissions->check( 'apres66', 'notifications' )
							),
							array( $innerTable, array( 'class' => 'innerTableCell' ) ),
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
                array( 'controller' => 'cohortesvalidationapres66', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
				$this->Permissions->check( 'cohortesvalidationapres66', 'exportcsv' )
            );
        ?></li>
		<li><?php
            echo $this->Xhtml->printCohorteLink(
				'Imprimer la cohorte',
				array( 'controller' => 'cohortesvalidationapres66', 'action' => 'notificationsCohorte', $this->action ) + Hash::flatten( $this->request->data, '__' ),
				$this->Permissions->check( 'cohortesvalidationapres66', 'notificationsCohorte' )
			);
        ?></li>
    </ul>
<?php endif?>
<?php endif?>