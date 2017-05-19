<?php
	$this->pageTitle = 'Recherche par dossier / allocataire';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<ul class="actionMenu">
	<?php
		if( Configure::read( 'Cg.departement' ) == 66 ) {
			if( $this->Permissions->check( 'ajoutdossierscomplets', 'add' ) ) {
				echo '<li>'.$this->Xhtml->addLink(
					'Ajouter un dossier',
					array( 'controller' => 'ajoutdossierscomplets', 'action' => 'add' )
				).' </li>';
			}
		}
		else {
			if( $this->Permissions->check( 'ajoutdossiers', 'wizard' ) ) {
				echo '<li>'.$this->Xhtml->addLink(
					'Ajouter un dossier',
					array( 'controller' => 'ajoutdossiers', 'action' => 'wizard' )
				).' </li>';
			}
		}

		if( $this->Permissions->check( 'dossierssimplifies', 'add' ) ) {
			if( Configure::read( 'Cg.departement' ) != 58 ) {
				echo '<li>'.$this->Xhtml->addSimpleLink(
					'Ajouter une préconisation d\'orientation',
					array( 'controller' => 'dossierssimplifies', 'action' => 'add' )
				).' </li>';
			}
		}

		if( is_array( $this->request->data ) ) {
			echo '<li>'.$this->Xhtml->link(
				$this->Xhtml->image(
					'icons/application_form_magnify.png',
					array( 'alt' => '' )
				).' Formulaire',
				'#',
				array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
			).'</li>';
		}

		$formSent = ( isset( $this->request->data['Dossier']['recherche'] ) && $this->request->data['Dossier']['recherche'] );
	?>
</ul>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'DossierDtdemrsa', $( 'DossierDtdemrsaFromDay' ).up( 'fieldset' ), false );
	});
</script>

<?php echo $this->Form->create( 'Dossier', array( 'type' => 'post', 'action' => '/index/', 'id' => 'Search', 'class' => ( $formSent ? 'folded' : 'unfolded' ) ) );?>

	<fieldset>
		<legend>Recherche par dossier</legend>
		<?php echo $this->Form->input( 'Dossier.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
		<?php echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de dossier RSA' ) );?>
		<?php echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule.large' ), 'maxlength' => 15 ) );?>
		<?php echo $this->Form->input( 'Detailcalculdroitrsa.natpf', array( 'label' => 'Nature de la prestation', 'type' => 'select', 'options' => $natpf, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Calculdroitrsa.toppersdrodevorsa', array( 'label' => 'Soumis à Droit et Devoir', 'type' => 'select', 'options' => $toppersdrodevorsa, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Dossier.anciennete_dispositif', array( 'label' => 'Ancienneté dans le dispositif', 'type' => 'select', 'options' => $anciennete_dispositif, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Serviceinstructeur.id', array( 'label' => __( 'lib_service' ), 'type' => 'select' , 'options' => $typeservice, 'empty' => true ) );?>

		<?php echo $this->Form->input( 'Dossier.fonorg', array( 'label' => 'Organisme émetteur du dossier', 'type' => 'select' , 'options' => $fonorg, 'empty' => true ) );?>

		<?php echo $this->Form->input( 'Dossier.dtdemrsa', array( 'label' => 'Filtrer par date de demande', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date de demande RSA</legend>
			<?php
				$dtdemrsa_from = Set::check( $this->request->data, 'Dossier.dtdemrsa_from' ) ? Set::extract( $this->request->data, 'Dossier.dtdemrsa_from' ) : strtotime( '-1 week' );
				$dtdemrsa_to = Set::check( $this->request->data, 'Dossier.dtdemrsa_to' ) ? Set::extract( $this->request->data, 'Dossier.dtdemrsa_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Form->input( 'Dossier.dtdemrsa_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $dtdemrsa_from ) );?>
			<?php echo $this->Form->input( 'Dossier.dtdemrsa_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'maxYear' => date( 'Y' ) + 5, 'selected' => $dtdemrsa_to ) );?>
		</fieldset>
		<?php
			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );

		?>
		<?php echo $this->Search->etatdosrsa($etatdosrsa); ?>

		<?php echo $this->Form->input( 'Foyer.sitfam', array( 'label' => 'Filtrer par situation familiale', 'type' => 'select', 'empty' => true, 'options' => $sitfam ) );?>
	</fieldset>

	<?php
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
		echo $this->Search->blocAllocataire( $trancheage, $sexe );
// 		if( Configure::read( 'Cg.departement' ) == 66 ) {
			echo '<fieldset>';
			echo $this->Xform->input( 'Dsp.natlog', array( 'label' => 'Conditions de logement', 'type' => 'select', 'empty' => true, 'options' => $natlog ) );
			echo $this->Xform->input( 'Prestation.rolepers', array( 'label' => 'Rôle de la personne ?', 'type' => 'select', 'options' => $chooserolepers, 'empty' => true ) );
			if( Configure::read( 'Cg.departement' ) == 58 ) {
				echo $this->Xform->input( 'Activite.act', array( 'label' => 'Code activité', 'type' => 'select', 'empty' => true, 'options' => $act ) );
			}
			echo '</fieldset>';
// 		}
	?>

	<fieldset>
		<legend>Recherche par parcours de l'allocataire</legend>
		<?php
			if( Configure::read( 'Cg.departement' ) == 58 ){
				echo $this->Form->input( 'Dsp.exists', array( 'label' => 'Possède une DSP ?', 'type' => 'select', 'options' => $exists, 'empty' => true ) );
				echo $this->Form->input( 'PersonneReferent.referent_id', array( 'label' => 'Travailleur social chargé de l\'évaluation', 'type' => 'select', 'options' => $referents, 'empty' => true ) );
				echo $this->Form->input( 'Personne.etat_dossier_orientation', array( 'label' => __d( 'personne', 'Personne.etat_dossier_orientation' ), 'type' => 'select', 'options' => $etat_dossier_orientation, 'empty' => true ) );
			}
			else if( Configure::read( 'Cg.departement' ) != 93 ){
				echo $this->Form->input( 'Orientstruct.exists', array( 'label' => 'Possède une orientation ? ', 'type' => 'select', 'options' => $exists, 'empty' => true ) );
			}
			if( Configure::read( 'Cg.departement' ) == 66 ){
				echo $this->Form->input( 'Cui.exists', array( 'label' => 'Possède un CUI ? ', 'type' => 'select', 'options' => $exists, 'empty' => true ) );
			}

			echo $this->Form->input( 'Personne.hascontrat', array( 'label' => 'Possède un CER ? ', 'type' => 'select', 'options' => array( 'O' => 'Oui', 'N' => 'Non'), 'empty' => true ) );
		?>
	</fieldset>

	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		echo $this->Search->paginationNombretotal();
	?>

	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type'=>'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>

<!-- Résultats -->
<?php if( isset( $dossiers ) ):?>
	<h2 class="noprint">Résultats de la recherche</h2>
	<?php
		$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';
	?>

	<?php if( is_array( $dossiers ) && count( $dossiers ) > 0 ):?>
		<?php
			$pagination = $this->Xpaginator->paginationBlock( 'Dossier', $this->passedArgs );
			echo $pagination;
		?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'Numéro de dossier', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de demande', 'Dossier.dtdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'NIR', 'Personne.nir' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Etat du droit', 'Situationdossierrsa.etatdosrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Allocataire', 'Personne.nom' );?></th><!-- FIXME: qual/nom/prénom -->
					<?php if( Configure::read( 'Cg.departement' ) == 66 ):?>
						<th>Adresse de l'allocataire</th>
					<?php else:?>
						<th><?php echo $this->Xpaginator->sort( 'Commune de l\'allocataire', 'Adresse.nomcom' );?></th>
					<?php endif;?>

					<th class="action noprint">Actions</th>
					<th class="action noprint">Verrouillé</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $dossiers as $index => $dossier ):?>
					<?php
						$title = $dossier['Dossier']['numdemrsa'];

						$activite = '';
						if( Configure::read( 'Cg.departement' ) == 58 ) {
							$activite = '<tr>
								<th>Code activité</th>
								<td>'.value( $act, Hash::get( $dossier, 'Activite.act' ) ).'</td>
							</tr>
							<tr>
								<th>'.__d( 'personne', 'Personne.etat_dossier_orientation' ).'</th>
								<td>'.h( value( $etat_dossier_orientation, Hash::get( $dossier, 'Personne.etat_dossier_orientation' ) ) ).'</td>
							</tr>';
						}

						$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>' . __d( 'dossier', 'Dossier.matricule.large' ) . '</th>
									<td>'.$dossier['Dossier']['matricule'].'</td>
								</tr>
								<tr>
									<th>Date de naissance</th>
									<td>'.date_short( $dossier['Personne']['dtnai'] ).'</td>
								</tr>
								<tr>
									<th>Code INSEE</th>
									<td>'.$dossier['Adresse']['numcom'].'</td>
								</tr>
								<tr>
									<th>Rôle</th>
									<td>'.Set::enum( Set::classicExtract( $dossier, 'Prestation.rolepers' ), $rolepers ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $dossier, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $dossier, 'Referentparcours.nom_complet' ).'</td>
								</tr>
								'.$activite.'
							</tbody>
						</table>';

						if( Configure::read( 'Cg.departement' ) != 66 ) {
							$array1 = array(
								h( $dossier['Dossier']['numdemrsa'] ),
								h( date_short( $dossier['Dossier']['dtdemrsa'] ) ),
								h( $dossier['Personne']['nir'] ),
								h( isset( $etatdosrsa[Set::classicExtract( $dossier, 'Situationdossierrsa.etatdosrsa' )] ) ? $etatdosrsa[Set::classicExtract( $dossier, 'Situationdossierrsa.etatdosrsa' )] : '' ),
								implode(
									' ',
									array(
										$dossier['Personne']['qual'],
										$dossier['Personne']['nom'],
										implode( ' ', array( $dossier['Personne']['prenom'], $dossier['Personne']['prenom2'], $dossier['Personne']['prenom3'] ) )
									)
								),
								h( Set::extract(  $dossier, 'Adresse.nomcom' ) ),
							);
						}
						else {
							$array1 = array(
								h( $dossier['Dossier']['numdemrsa'] ),
								h( date_short( $dossier['Dossier']['dtdemrsa'] ) ),
								h( $dossier['Personne']['nir'] ),
								h( isset( $etatdosrsa[Set::classicExtract( $dossier, 'Situationdossierrsa.etatdosrsa' )] ) ? $etatdosrsa[Set::classicExtract( $dossier, 'Situationdossierrsa.etatdosrsa' )] : '' ),
								implode(
									' ',
									array(
										$dossier['Personne']['qual'],
										$dossier['Personne']['nom'],
										implode( ' ', array( $dossier['Personne']['prenom'], $dossier['Personne']['prenom2'], $dossier['Personne']['prenom3'] ) )
									)
								),
								nl2br( h( Set::classicExtract(  $dossier, 'Adresse.numvoie' ).' '.Set::classicExtract( $dossier, 'Adresse.libtypevoie' ).' '.Set::classicExtract(  $dossier, 'Adresse.nomvoie' )."\n".Set::classicExtract(  $dossier, 'Adresse.codepos' ).' '.Set::classicExtract(  $dossier, 'Adresse.nomcom' ) ) )
							);
						}

						$array2 = array(
							array(
								$this->Xhtml->viewLink(
									'Voir le dossier « '.$title.' »',
									array( 'controller' => 'dossiers', 'action' => 'view', $dossier['Dossier']['id'] )
								),
								array( 'class' => 'noprint' )
							),
							array(
								( $dossier['Dossier']['locked'] ?
									$this->Xhtml->image(
										'icons/lock.png',
										array( 'alt' => '', 'title' => 'Dossier verrouillé' )
									) : null
								),
								array( 'class' => 'noprint' )
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
		<?php if( Set::extract( $this->request->params, 'paging.Dossier.count' ) > 65000 ):?>
			<p class="noprint" style="border: 1px solid #556; background: #ffe;padding: 0.5em;"><?php echo $this->Xhtml->image( 'icons/error.png' );?> <strong>Attention</strong>, il est possible que votre tableur ne puisse pas vous afficher les résultats au-delà de la 65&nbsp;000ème ligne.</p>
		<?php endif;?>
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
					array( 'controller' => 'dossiers', 'action' => 'exportcsv1' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'dossiers', 'exportcsv1' )
				);
			?></li>
		</ul>
		<?php echo $pagination;?>
	<?php else:?>
		<p>Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>
<?php endif?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>