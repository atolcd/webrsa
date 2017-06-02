<?php
    $this->pageTitle = 'Recherche par Dossiers COVs';

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

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'Cov58Datecommission', $( 'Cov58DatecommissionFromDay' ).up( 'fieldset' ), false );
	});
</script>
<?php echo $this->Xform->create( 'Criteredossiercov58', array( 'type' => 'post', 'url' => array( 'action' => 'index' ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>

        <?php  echo $this->Xform->input( 'Criteredossiercov58.index', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>

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
            <legend>Filtrer par Dossier COV</legend>
            <?php
                $listThemes = array();
                foreach( $themes as $i => $theme ){
                    $listThemes[$i] = __d( 'dossiercov58',  'ENUM::THEMECOV::'.$themes[$i] );
                }


                echo $this->Default2->subform(
                    array(
                        'Passagecov58.etatdossiercov' => array( 'type' => 'select', 'options' => $options['etatdossiercov'] ),
                        'Dossiercov58.themecov58_id' => array( 'type' => 'select', 'options' => $listThemes )
                    ),
                    array(
                        'options' => $options
                    )
                );
            ?>

        </fieldset>
		<fieldset>
			<legend>Filtrer par Commission</legend>
			<?php echo $this->Default2->subform(
				array(
					'Cov58.sitecov58_id' => array( 'type' => 'select', 'option' => $sitescovs58, 'empty' => true )
				)
			); ?>
		</fieldset>
			<?php echo $this->Xform->input( 'Cov58.datecommission', array( 'label' => 'Filtrer par date de Commission', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Filtrer par période</legend>
				<?php
					$datecommission_from = Set::check( $this->request->data, 'Cov58.datecommission_from' ) ? Set::extract( $this->request->data, 'Cov58.datecommission_from' ) : strtotime( '-1 week' );
					$datecommission_to = Set::check( $this->request->data, 'Cov58.datecommission_to' ) ? Set::extract( $this->request->data, 'Cov58.datecommission_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Xform->input( 'Cov58.datecommission_from', array( 'label' => 'Du', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $datecommission_from ) );?>
				<?php echo $this->Xform->input( 'Cov58.datecommission_to', array( 'label' => 'Au', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $datecommission_to ) );?>
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
<?php $pagination = $this->Xpaginator->paginationBlock( 'Dossiercov58', $this->passedArgs ); ?>
<?php echo $pagination;?>
<?php if( isset( $dossierscovs58 ) ):?>
	<?php echo $this->Html->tag( 'h2', 'Résultats de la recherche' );?>
    <?php if( is_array( $dossierscovs58 ) && count( $dossierscovs58 ) > 0  ):?>
        <?php
            echo '<table><thead>';
                echo '<tr>
                    <th>'.$this->Xpaginator->sort( __d( 'dossier', 'Dossier.numdemrsa' ), 'Dossier.numdemrsa' ).'</th>
                    <th>'.$this->Xpaginator->sort( __d( 'personne', 'Personne.nom_complet' ), 'Personne.nom_complet' ).'</th>
                    <th>'.$this->Xpaginator->sort( __d( 'dossiercov58', 'Dossiercov58.themecov58_id' ), 'Dossiercov58.themecov58_id' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'cov58', 'Cov58.datecommission' ), 'Cov58.datecommission' ).'</th>
                    <th>'.$this->Xpaginator->sort( __d( 'passagecov58', 'Passagecov58.etatdossiercov' ), 'Passagecov58.etatdossiercov').'</th>
                    <th>'.$this->Xpaginator->sort( __d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ), 'Structurereferenteparcours.lib_struc').'</th>
                    <th>'.$this->Xpaginator->sort( __d( 'search_plugin', 'Referentparcours.nom_complet' ), 'Referentparcours.nom_complet').'</th>
                    <th>Action</th>
                </tr></thead><tbody>';

                foreach( $dossierscovs58 as $dossiercov58 ) {
                    echo '<tr>
                        <td>'.h( $dossiercov58['Dossier']['numdemrsa'] ).'</td>
                        <td>'.h( $dossiercov58['Personne']['nom_complet'] ).'</td>
                        <td>'.__d( 'dossiercov58',  'ENUM::THEMECOV::'.$themes[$dossiercov58['Dossiercov58']['themecov58_id']] ).'</td>
                        <td>'.h( date('d-m-Y à h:i', strtotime($dossiercov58['Cov58']['datecommission'])) ).'</td>
                        <td>'.h( Set::enum( Set::classicExtract( $dossiercov58, 'Passagecov58.etatdossiercov' ),  $options['etatdossiercov'] ) ).'</td>
						<td>'.h( Hash::get( $dossiercov58, 'Structurereferenteparcours.lib_struc' ) ).'</td>
						<td>'.h( Hash::get( $dossiercov58, 'Referentparcours.nom_complet' ) ).'</td>
                        <td>'.$this->Xhtml->link( 'Voir', array( 'controller' => 'personnes', 'action' => 'view', $dossiercov58['Personne']['id'] ) ).'</td>
                    </tr>';
                }
            echo '</tbody></table>';
    ?>
    <ul class="actionMenu">
        <li><?php
            echo $this->Xhtml->printLinkJs(
                'Imprimer le tableau',
                array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
            );
        ?></li>

    </ul>
<?php echo $pagination;?>

    <?php else:?>
        <?php echo $this->Xhtml->tag( 'p', 'Aucun résultat ne correspond aux critères choisis.', array( 'class' => 'notice' ) );?>
    <?php endif;?>
<?php endif;?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>