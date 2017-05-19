<?php
    $this->pageTitle = 'Recherche par Entretiens';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
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
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<script type="text/javascript">
    document.observe("dom:loaded", function() {
        dependantSelect( 'EntretienReferentId', 'EntretienStructurereferenteId' );
    });
</script>

<?php echo $this->Xform->create( 'Critereentretien', array( 'type' => 'post', 'action' => $this->action,  'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
	<?php echo $this->Xform->input( 'Critereentretien.index', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
	<?php
		echo $this->Search->blocAllocataire();
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
	?>
	<fieldset>
		<legend>Recherche par dossier</legend>
		<?php
			echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de demande RSA' ) );
			echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15 ) );

			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
			echo $this->Search->etatdosrsa($etatdosrsa);
		?>
	</fieldset>
	<fieldset>
		<legend>Filtrer par Entretiens</legend>
		<?php
			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Default2->subform(
				array(
					'Entretien.arevoirle' => array( 'label' => __d( 'entretien', 'Entretien.arevoirle' ), 'type' => 'date', 'dateFormat' => 'MY', 'empty' => true, 'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2 ),
					'Entretien.structurereferente_id' => array( 'label' => __d( 'entretien', 'Entretien.structurereferente_id' ), 'empty' => true, 'options' => $structs ),
					'Entretien.referent_id' => array( 'label' => __d( 'entretien', 'Entretien.referent_id' ), 'empty' => true, 'options' => $referents  ),
					'Entretien.dateentretien' => array( 'type' => 'checkbox' )
				),
				array(
					'options' => $options
				)
			);

			echo $this->Html->tag(
					'fieldset',
					$this->Html->tag( 'legend', __d( 'entretien', 'Entretien.dateentretien_checkbox' ) )
					.$this->Default2->subform(
					array(
						'Entretien.dateentretien_from' => array( 'label' => __d( 'entretien', 'Entretien.dateentretien_from' ), 'empty' => true, 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => 2009, 'maxYear' => date('Y')+1 ),
						'Entretien.dateentretien_to' => array( 'label' => __d( 'entretien', 'Entretien.dateentretien_to' ), 'empty' => true, 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => 2009, 'maxYear' => date('Y')+1 ),
					),
					array(
						'options' => $options
					)
				)
			);
		?>
	</fieldset>

	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		echo $this->Search->paginationNombretotal();
	?>

    <div class="submit noprint">
        <?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
        <?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
    </div>

<?php echo $this->Xform->end();?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'EntretienDateentretien', $( 'EntretienDateentretienFromDay' ).up( 'fieldset' ), false );
	});
</script>

<?php if( isset( $entretiens ) ):?>
<h2 class="noprint">Résultats de la recherche</h2>
    <?php if( empty( $entretiens ) ):?>
        <?php $message = 'Aucun entretien n\'a été trouvé.';?>
        <p class="notice"><?php echo $message;?></p>
    <?php else:?>
	<?php
		$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';
	?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'Entretien', $this->passedArgs ); ?>
<?php echo $pagination;?>
    <table id="searchResults" class="tooltips">
        <thead>
            <tr>
                <th><?php echo $this->Xpaginator->sort( 'Date de l\'entretien', 'Entretien.dateentretien' );?></th>
                <th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
                <th><?php echo $this->Xpaginator->sort( 'Commune de l\'allocataire', 'Adresse.nomcom' );?></th>
                <th><?php echo $this->Xpaginator->sort( 'Structure référente', 'Structurereferente.lib_struc' );?></th>
                <th><?php echo $this->Xpaginator->sort( 'Référent', 'Referent.nom' );?></th>
                <th><?php echo $this->Xpaginator->sort( 'Type d\'entretien', 'Entretien.typeentretien' );?></th>
                <th><?php echo $this->Xpaginator->sort( 'Objet de l\'entretien', 'Objetentretien.name' );?></th>
                <th><?php echo $this->Xpaginator->sort( 'A revoir le', 'Entretien.arevoirle' );?></th>
                <th class="action">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach( $entretiens as $index => $entretien ):?>
            <?php
                    $innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
                        <tbody>
                            <tr>
                                <th>Date naissance</th>
                                <td>'.h( date_short( $entretien['Personne']['dtnai'] ) ).'</td>
                            </tr>
                            <tr>
                                <th>' . __d( 'dossier', 'Dossier.matricule.large' ) . '</th>
                                <td>'.h( $entretien['Dossier']['matricule'] ).'</td>
                            </tr>
                            <tr>
                                <th>NIR</th>
                                <td>'.h( $entretien['Personne']['nir'] ).'</td>
                            </tr>
                            <tr>
                                <th>Code postal</th>
                                <td>'.h( $entretien['Adresse']['codepos'] ).'</td>
                            </tr>
                            <tr>
                                <th>Code INSEE</th>
                                <td>'.h( $entretien['Adresse']['numcom'] ).'</td>
                            </tr>
							<tr>
								<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
								<td>'.Hash::get( $entretien, 'Structurereferenteparcours.lib_struc' ).'</td>
							</tr>
							<tr>
								<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
								<td>'.Hash::get( $entretien, 'Referentparcours.nom_complet' ).'</td>
							</tr>
                        </tbody>
                    </table>';
                    $title = $entretien['Dossier']['numdemrsa'];

                    echo $this->Xhtml->tableCells(
                            array(
                                h( date_short(  $entretien['Entretien']['dateentretien'] ) ),
                                h( $entretien['Personne']['qual'].' '.$entretien['Personne']['nom'].' '.$entretien['Personne']['prenom'] ),
                                h( $entretien['Adresse']['nomcom'] ),
                                h( $entretien['Structurereferente']['lib_struc'] ),
                                h( $entretien['Referent']['qual'].' '.$entretien['Referent']['nom'].' '.$entretien['Referent']['prenom'] ),
                                h( Set::enum( $entretien['Entretien']['typeentretien'], $options['typeentretien'] ) ),
                                h( $entretien['Objetentretien']['name'] ),
                                h( $this->Locale->date( 'Date::miniLettre', $entretien['Entretien']['arevoirle'] ) ),
                                $this->Xhtml->viewLink(
                                    'Voir le contrat',
                                    array( 'controller' => 'entretiens', 'action' => 'index', $entretien['Personne']['id'] ),
                                    $this->Permissions->check( 'entretiens', 'index' ) && !Hash::get( $entretien, 'Entretien.horszone' )
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
                    array( 'controller' => 'criteresentretiens', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'criteresentretiens', 'exportcsv' )
                );
            ?></li>
        </ul>
    <?php echo $pagination;?>

<?php endif?>
<?php endif?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>