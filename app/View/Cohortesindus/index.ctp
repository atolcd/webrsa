<?php
	$this->pageTitle = 'Gestion des indus';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1>Recherche par Indus</h1>
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
	$pagination = $this->Xpaginator->paginationBlock( 'Dossier', $this->passedArgs );
?>

<?php echo $this->Form->create( 'Cohorteindu', array( 'type' => 'post', 'action' => '/index/', 'id' => 'Search', 'class' => ( ( !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>

	<?php
		echo $this->Search->blocAllocataire();
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
	?>
	<fieldset>
		<legend>Recherche par dossier</legend>
		<?php
			echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de demande RSA' ) );
			echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15 ) );
			echo $this->Search->natpf( $natpf );

			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
			echo $this->Search->etatdosrsa($etatdosrsa);
		?>
	</fieldset>
	<fieldset>
		<legend>Recherche d'Indu</legend>
			<?php echo $this->Form->input( 'Cohorteindu.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
			<?php echo $this->Form->input( 'Cohorteindu.natpfcre', array( 'label' => 'Type d\'indu', 'type' => 'select', 'options' => $natpfcre, 'empty' => true ) );?>

			<?php echo $this->Form->input( 'Cohorteindu.typeparte', array( 'label' => 'Suivi', 'type' => 'select', 'options' => $typeparte, 'empty' => true ) ); ?>
			<?php /*echo $this->Form->input( 'Cohorteindu.structurereferente_id', array( 'label' => 'Structure référente', 'type' => 'select', 'options' => $sr , 'empty' => true )  ); */?>
			<?php
				echo $this->Form->input( 'Cohorteindu.compare', array( 'label' => 'Opérateurs', 'type' => 'select', 'options' => $comparators, 'empty' => true ) );
				echo $this->Form->input( 'Cohorteindu.mtmoucompta', array( 'label' => 'Montant de l\'indu', 'type' => 'text' ) );
			?>
	</fieldset>

	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		echo $this->Search->paginationNombretotal();
	?>

	<div class="submit noprint">
		<?php echo $this->Form->button( 'Filtrer', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>

<!-- Résultats -->
<?php if( isset( $cohorteindu ) ):?>

	<h2 class="noprint">Résultats de la recherche</h2>
	<?php
		$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';
	?>

	<?php if( is_array( $cohorteindu ) && count( $cohorteindu ) > 0 ):?>
		<?php echo $pagination;?>
			<table id="searchResults" class="tooltips">
				<thead>
					<tr>
						<th><?php echo $this->Xpaginator->sort( 'N° Dossier', 'Dossier.numdemrsa' );?></th>
						<th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
						<th><?php echo $this->Xpaginator->sort( 'Suivi', 'Dossier.typeparte' );?></th>
						<th><?php echo $this->Xpaginator->sort( 'Situation des droits', 'Situationdossierrsa.etatdosrsa' );?></th>

						<th>Date indus</th><!-- FIXME -->

						<th><?php echo $this->Xpaginator->sort( 'Montant initial de l\'indu', 'IndusConstates.mtmoucompta' );?></th>
						<th><?php echo $this->Xpaginator->sort( 'Montant transféré CG', 'IndusTransferesCG.mtmoucompta' );?></th>
						<th><?php echo $this->Xpaginator->sort( 'Remise CG', 'RemisesIndus.mtmoucompta' );?></th>

						<th class="action">Action</th>
						<th class="innerTableHeader">Informations complémentaires</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $cohorteindu as $index => $indu ):?>
						<?php
						$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>Date naissance</th>
									<td>'.h( date_short( $indu['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>' . __d( 'dossier', 'Dossier.matricule.large' ) . '</th>
									<td>'.h( $indu['Dossier']['matricule'] ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $indu['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>Code postal</th>
									<td>'.h( $indu['Adresse']['codepos'] ).'</td>
								</tr>
								<tr>
									<th>Code INSEE</th>
									<td>'.h( $indu['Adresse']['numcom'] ).'</td>
								</tr>
								<tr>
									<th>Rôle</th>
									<td>'.$rolepers[$indu['Prestation']['rolepers']].'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $indu, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $indu, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';
							$title = $indu['Dossier']['numdemrsa'];
							echo $this->Xhtml->tableCells(
								array(
									h( $indu['Dossier']['numdemrsa'] ),
									h( $indu['Personne']['nom'].' '.$indu['Personne']['prenom'] ),
									h( $indu['Dossier']['typeparte'] ),
									h( $etatdosrsa[$indu['Situationdossierrsa']['etatdosrsa']] ),
									$this->Locale->date( 'Date::miniLettre', $indu[0]['moismoucompta'] ),
									$this->Xhtml->tag( 'span', $this->Locale->money( $indu[0]['mt_indus_constate'] ), array( 'class' => 'number' ) ),
									$this->Xhtml->tag( 'span', $this->Locale->money( $indu[0]['mt_indus_transferes_c_g'] ), array( 'class' => 'number' ) ),
									$this->Xhtml->tag( 'span', $this->Locale->money( $indu[0]['mt_remises_indus'] ), array( 'class' => 'number' ) ),
									$this->Xhtml->viewLink(
										'Voir le contrat « '.$title.' »',
										array( 'controller' => 'indus', 'action' => 'view', $indu['Dossier']['id'] )
									),
									array( $innerTable, array( 'class' => 'innerTableCell' ) )
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
				echo $this->Xhtml->exportLink(
					'Télécharger le tableau',
					array( 'controller' => 'cohortesindus', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'cohortesindus', 'exportcsv' )
				);
			?></li>
		</ul>
	<?php else:?>
		<p>Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>
<?php endif?>

<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>