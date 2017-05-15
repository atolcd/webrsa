<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	$domain = 'propopdo';
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'propopdo', "Criterespdos::{$this->action}" )
	)
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'DossierDtdemrsa', $( 'DossierDtdemrsaFromDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'DossierDatedecisionpdo', $( 'DossierDatedecisionpdoFromDay' ).up( 'fieldset' ), false );
	});
</script>
<?php
	if( is_array( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
		).'</li></ul>';
	}

	echo $this->Xform->create( 'Criterespdos', array( 'type' => 'post', 'action' => '/nouvelles/', 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );
?>
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

		<?php echo $this->Form->input( 'Dossier.dtdemrsa', array( 'name' => 'data[Dossier][dtdemrsa]', 'label' => 'Filtrer par date de demande RSA', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date de demande RSA</legend>
			<?php
				$dtdemrsa_from = Set::check( $this->request->data, 'Dossier.dtdemrsa_from' ) ? Set::extract( $this->request->data, 'Dossier.dtdemrsa_from' ) : strtotime( '-1 week' );
				$dtdemrsa_to = Set::check( $this->request->data, 'Dossier.dtdemrsa_to' ) ? Set::extract( $this->request->data, 'Dossier.dtdemrsa_to' ) : strtotime( 'now' );

				echo $this->Default2->subform(
					array(
						'Dossier.dtdemrsa_from' => array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $dtdemrsa_from ),
						'Dossier.dtdemrsa_to' => array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120,  'maxYear' => date( 'Y' ) + 5, 'selected' => $dtdemrsa_to ),
					),
					array(
						'options' => $options,
						'form' => false
					)
				);
			?>
		</fieldset>
</fieldset>
	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		echo $this->Search->paginationNombretotal();

		echo $this->Xform->submit( __( 'Search' ) );
		echo $this->Xform->end();
?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs ); ?>

	<?php if( isset( $criterespdos ) ):?>
	<br />
	<h2 class="noprint aere">Résultats de la recherche</h2>
	<?php
		$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';
	?>

	<?php if( is_array( $criterespdos ) && count( $criterespdos ) > 0  ):?>
		<?php echo $pagination;?>
		<table class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° dossier', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom du demandeur', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Etat du droit', 'Situationdossierrsa.etatdosrsa' );?></th>
					<th class="action">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach( $criterespdos as $index => $criterepdo ) {
						$innerTable = '<table id="innerTable'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>Commune de naissance</th>
									<td>'.h( $criterepdo['Personne']['nomcomnai'] ).'</td>
								</tr>
								<tr>
									<th>Date de naissance</th>
									<td>'.h( date_short( $criterepdo['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>Code INSEE</th>
									<td>'.h( $criterepdo['Adresse']['numcom'] ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $criterepdo['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>N° CAF</th>
									<td>'.h( $criterepdo['Dossier']['matricule'] ).'</td>
								</tr>
								<tr>
									<th>Rôle</th>
									<td>'.h( $rolepers[$criterepdo['Prestation']['rolepers']] ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $criterepdo, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $criterepdo, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';

						echo $this->Xhtml->tableCells(
							array(
								h( Set::classicExtract( $criterepdo, 'Dossier.numdemrsa' ) ),
								h( Set::enum( Set::classicExtract( $criterepdo, 'Personne.qual' ), $qual ).' '.Set::classicExtract( $criterepdo, 'Personne.nom' ).' '.Set::classicExtract( $criterepdo, 'Personne.prenom' ) ),
								h( value( $etatdosrsa, Set::classicExtract( $criterepdo, 'Situationdossierrsa.etatdosrsa' ) ) ),
								$this->Xhtml->viewLink(
									'Voir',
									array( 'controller' => 'propospdos', 'action' => 'index', Set::classicExtract( $criterepdo, 'Personne.id' ) ),
									$this->Permissions->check( 'propospdos', 'index' )
								),
								array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
							),
							array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
							array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
						);
					}
				?>
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
		</ul>
	<?php else:?>
		<p class="notice">Vos critères n'ont retourné aucune PDO.</p>
	<?php endif?>
<?php endif?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>