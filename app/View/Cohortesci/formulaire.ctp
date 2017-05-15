<h1><?php
	if( Configure::read( 'Cg.departement') == 66 ){
		$pageTitle = 'Contrats Particuliers à valider';
	}
	else{
		$pageTitle = 'Contrats à valider';
	}

	echo $this->pageTitle = $pageTitle;
	?>
</h1>
<?php require_once( 'filtre.ctp' );?>
<?php
	if( isset( $cohorteci ) ) {
		$pagination = $this->Xpaginator->paginationBlock( 'Contratinsertion', $this->passedArgs );
	}
	else {
		$pagination = '';
	}
?>
<!-- Résultats -->
<?php if( isset( $cohorteci ) ):?>

	<h2 class="noprint">Résultats de la recherche</h2>
	<?php
		$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';
	?>
<?php echo $pagination;?>
	<?php if( is_array( $cohorteci ) && count( $cohorteci ) > 0 ):?>
		<?php echo $this->Form->create( 'GestionContrat', array() );?>
		<?php
			echo '<div>';
			echo $this->Form->input( 'Filtre.date_saisi_ci', array( 'type' => 'hidden', 'id' => 'FiltreDateSaisiCi2' ) );
			echo $this->Form->input( 'Filtre.date_saisi_ci_from', array( 'type' => 'hidden', 'id' => 'FiltreDateSaisiCiFrom2' ) );
			echo $this->Form->input( 'Filtre.date_saisi_ci_to', array( 'type' => 'hidden', 'id' => 'FiltreDateSaisiCiTo2' ) );
			echo $this->Form->input( 'Filtre.nomcom', array( 'type' => 'hidden', 'id' => 'FiltreLocaadr2' ) );
			echo $this->Form->input( 'Filtre.numcom', array( 'type' => 'hidden', 'id' => 'FiltreNumcomptt2' ) );
			echo $this->Form->input( 'Filtre.pers_charg_suivi', array( 'type' => 'hidden', 'id' => 'FiltrePersChargSuivi2' ) );
			echo $this->Form->input( 'Filtre.decision_ci', array( 'type' => 'hidden', 'id' => 'FiltreDecisionCi2' ) );
			echo $this->Form->input( 'Filtre.datevalidation_ci', array( 'type' => 'hidden', 'id' => 'FiltreDatevalidationCi2' )  );
			echo $this->Form->input( 'Filtre.forme_ci', array( 'type' => 'hidden', 'id' => 'FiltreFormeCi2' ) );
			echo '</div>';
		?>

		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th>N° Dossier</th>
					<th>Nom de l'allocataire</th>
					<th>Commune de l'allocataire</th>
					<th>Date début contrat</th>
					<th>Date fin contrat</th>
					<?php if( $this->action != 'nouveaux' ):?>
						<th>Statut actuel</th>
					<?php endif;?>
					<th>Décision</th>
					<th>Date validation</th>
					<th>Observations</th>
					<th class="action">Action</th>
					<th class="innerTableHeader">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $cohorteci as $index => $contrat ):?>
					<?php
						$controller = 'contratsinsertion';
						if( Configure::read( 'Cg.departement' ) == 93 ) {
							$controller = 'cers93';
						}
					$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
						<tbody>
							<tr>
								<th>Date naissance</th>
								<td>'.h( date_short( $contrat['Personne']['dtnai'] ) ).'</td>
							</tr>
							<tr>
								<th>' . __d( 'dossier', 'Dossier.matricule.large' ) . '</th>
								<td>'.h( $contrat['Dossier']['matricule'] ).'</td>
							</tr>
							<tr>
								<th>NIR</th>
								<td>'.h( $contrat['Personne']['nir'] ).'</td>
							</tr>
							<tr>
								<th>Code postal</th>
								<td>'.h( $contrat['Adresse']['codepos'] ).'</td>
							</tr>
							<tr>
								<th>Code INSEE</th>
								<td>'.h( $contrat['Adresse']['numcom'] ).'</td>
							</tr>
							<tr>
								<th>Rôle</th>
								<td>'.h( $rolepers[$contrat['Prestation']['rolepers']] ).'</td>
							</tr>
							<tr>
								<th>État du dossier</th>
								<td>'.h( value( $etatdosrsa, $contrat['Situationdossierrsa']['etatdosrsa'] ) ).'</td>
							</tr>
							<tr>
								<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
								<td>'.Hash::get( $contrat, 'Structurereferenteparcours.lib_struc' ).'</td>
							</tr>
							<tr>
								<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
								<td>'.Hash::get( $contrat, 'Referentparcours.nom_complet' ).'</td>
							</tr>
						</tbody>
					</table>';
						$title = $contrat['Dossier']['numdemrsa'];
// debug( $contrat );
						$array1 = array(
							h( $contrat['Dossier']['numdemrsa'] ),
							h( $contrat['Personne']['nom'].' '.$contrat['Personne']['prenom'] ),
							h( $contrat['Adresse']['nomcom'] ),
							h( date_short( $contrat['Contratinsertion']['dd_ci'] ) ),
							h( date_short( $contrat['Contratinsertion']['df_ci'] ) )
						);

						if( $this->action != 'nouveaux' ){
							$array1[] = h( Set::extract( $decision_ci, Set::extract( $contrat, 'Contratinsertion.decision_ci' ) ).' '.date_short( $contrat['Contratinsertion']['datevalidation_ci'] ) );// statut BD
						}

						$array2 = array(
							$this->Form->input( 'Contratinsertion.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => $contrat['Contratinsertion']['id'] ) ).

							$this->Form->input( 'Contratinsertion.'.$index.'.personne_id', array( 'label' => false, 'type' => 'hidden', 'value' => $contrat['Contratinsertion']['personne_id'] ) ).

							$this->Form->input( 'Contratinsertion.'.$index.'.dossier_id', array( 'label' => false, 'type' => 'hidden', 'value' => $contrat['Dossier']['id'] ) ).
							$this->Form->input( 'Contratinsertion.'.$index.'.decision_ci', array( 'label' => false, 'type' => 'select', 'options' => $decision_ci, 'value' => $contrat['Contratinsertion']['proposition_decision_ci'] ) ),

							$this->Form->input( 'Contratinsertion.'.$index.'.datevalidation_ci', array( 'label' => false, 'type' => 'date', 'selected' => $contrat['Contratinsertion']['proposition_datevalidation_ci'], 'dateFormat' => 'DMY' ) ),

							$this->Form->input( 'Contratinsertion.'.$index.'.observ_ci', array( 'label' => false, 'type' => 'text', 'rows' => 2, 'value' => $contrat['Contratinsertion']['observ_ci'] ) ),

							$this->Xhtml->viewLink(
								'Voir le contrat « '.$title.' »',
								array( 'controller' => $controller, 'action' => 'view', $contrat['Contratinsertion']['id'] )
							),
							array( $innerTable, array( 'class' => 'innerTableCell' ) )
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
	<?php echo $pagination;?>
	<?php echo $this->Form->submit( 'Validation de la liste' );?>
	<?php echo $this->Form->end();?>

	<?php else:?>
		<p>Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>
<?php endif?>

<?php if( isset( $cohorteci ) ):?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		<?php foreach( array_keys( $cohorteci ) as $index ):?>
		observeDisableFieldsOnValue(
			'Contratinsertion<?php echo $index;?>DecisionCi',
			[ 'Contratinsertion<?php echo $index;?>DatevalidationCiDay', 'Contratinsertion<?php echo $index;?>DatevalidationCiMonth', 'Contratinsertion<?php echo $index;?>DatevalidationCiYear' ],
			'V',
			false
		);
		<?php endforeach;?>
	} );
</script>
<?php endif;?>