<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle = $pageTitle;?></h1>

<?php require_once( dirname( __FILE__ ).DS.'filtre.ctp' ); ?>

<?php $pagination = $this->Xpaginator->paginationBlock( 'ActioncandidatPersonne', $this->passedArgs ); ?>
<?php if ($pagination):?>
<h2 class="noprint">Résultats de la recherche</h2>
<?php echo $pagination;?>
<?php endif;?>
<?php if( isset( $cohortefichecandidature66 ) ):?>
	<?php if( is_array( $cohortefichecandidature66 ) && count( $cohortefichecandidature66 ) > 0  ):?>
		<?php echo $this->Form->create( 'SuiviActioncandidatPersonne', array() );?>
		<?php
			foreach( Hash::flatten( $this->request->data['Search'] ) as $filtre => $value  ) {
				echo $this->Form->input( "Search.{$filtre}", array( 'id' => null, 'type' => 'hidden', 'value' => $value ) );
			}
		?>
	<table id="searchResults" class="tooltips">
		<thead>
			<tr>
				<th>N° Dossier</th>
				<th>Nom de l'allocataire</th>
				<th>Commune de l'allocataire</th>
				<th>Action engagée</th>
				<th>Partenaire lié</th>
				<th>Nom du prescripteur</th>
				<th>Date de signature</th>
				<th>Venu(e) ?</th>
				<th>Retenu(e) ?</th>
				<th>Informations complémentaires</th>
				<th>Sortie</th>
				<th>Motif de sortie</th>
				<th>Date de sortie</th>
				<th class="action">Action</th>
				<th class="innerTableHeader noprint">Informations complémentaires</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach( $cohortefichecandidature66 as $index => $ficheenattente ):?>
			<?php
				$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>'.__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ).'</th>
							<td>'.Hash::get( $ficheenattente, 'Structurereferenteparcours.lib_struc' ).'</td>
						</tr>
						<tr>
							<th>'.__d( 'search_plugin', 'Referentparcours.nom_complet' ).'</th>
							<td>'.Hash::get( $ficheenattente, 'Referentparcours.nom_complet' ).'</td>
						</tr>
					</tbody>
				</table>';

					$title = $ficheenattente['Dossier']['numdemrsa'];

					$array1 = array(
						h( $ficheenattente['Dossier']['numdemrsa'] ),
						h( $ficheenattente['Personne']['qual'].' '.$ficheenattente['Personne']['nom'].' '.$ficheenattente['Personne']['prenom'] ),
						h( $ficheenattente['Adresse']['nomcom'] ),
						h( $ficheenattente['Actioncandidat']['name'] ),
						h( $ficheenattente['Partenaire']['libstruc'] ),
						h( $ficheenattente['Referent']['qual'].' '.$ficheenattente['Referent']['nom'].' '.$ficheenattente['Referent']['prenom'] ),
						h( date_short( $ficheenattente['ActioncandidatPersonne']['datesignature'] ) ),
						h( Set::enum( $ficheenattente['ActioncandidatPersonne']['bilanvenu'], $options['bilanvenu'] ) ),
						h( Set::enum( $ficheenattente['ActioncandidatPersonne']['bilanretenu'], $options['bilanretenu'] ) ),
						h( $ficheenattente['ActioncandidatPersonne']['infocomplementaire'] ),
					);

					$array2 = array(
// 						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.atraiter', array( 'label' => false, 'legend' => false, 'type' => 'checkbox', 'checked' => 'checked' ) ),

						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => $ficheenattente['ActioncandidatPersonne']['id'] ) ).
						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.personne_id', array( 'label' => false, 'type' => 'hidden', 'value' => $ficheenattente['ActioncandidatPersonne']['personne_id'] ) ).
                        $this->Form->input( 'ActioncandidatPersonne.'.$index.'.dossier_id', array( 'label' => false, 'type' => 'hidden', 'value' => $ficheenattente['Dossier']['id'] ) ).
						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.actioncandidat_id', array( 'label' => false, 'type' => 'hidden', 'value' => $ficheenattente['ActioncandidatPersonne']['actioncandidat_id'] ) ).
						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.referent_id', array( 'label' => false, 'type' => 'hidden', 'value' => $ficheenattente['ActioncandidatPersonne']['referent_id'] ) ).
						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.bilanvenu', array( 'label' => false, 'type' => 'hidden', 'value' => $ficheenattente['ActioncandidatPersonne']['bilanvenu'] ) ).
						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.bilanretenu', array( 'label' => false, 'type' => 'hidden', 'value' => $ficheenattente['ActioncandidatPersonne']['bilanretenu'] ) ).
						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.infocomplementaire', array( 'label' => false, 'type' => 'hidden', 'value' => $ficheenattente['ActioncandidatPersonne']['infocomplementaire'] ) ).
						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.issortie', array( 'label' => false, 'type' => 'checkbox'/*, 'value' => 1*/ ) ),

						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.motifsortie_id', array( 'label' => false, 'empty' => true,  'type' => 'select', 'options' => $motifssortie[$index], 'selected' => $ficheenattente['ActioncandidatPersonne']['motifsortie_id'] ) ),

						$this->Form->input( 'ActioncandidatPersonne.'.$index.'.sortiele', array( 'label' => false, /*'empty' => true,*/  'type' => 'date', 'dateFormat' => 'DMY', 'selected' => $ficheenattente['ActioncandidatPersonne']['proposition_sortiele'] ) ),
						$this->Xhtml->viewLink(
							'Voir le contrat « '.$title.' »',
							array( 'controller' => 'actionscandidats_personnes', 'action' => 'index', $ficheenattente['ActioncandidatPersonne']['personne_id'] )
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
	<?php echo $pagination;?>
	<?php echo $this->Form->submit( 'Validation de la liste' );?>
<?php echo $this->Form->end();?>


	<?php else:?>
		<p class="notice">Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>
<?php endif?>

<script type="text/javascript">
    <?php foreach( $cohortefichecandidature66 as $key => $fichecandidature66 ):?>
	    observeDisableFieldsOnCheckbox(
		'ActioncandidatPersonne<?php echo $key;?>Issortie',
		[
		    'ActioncandidatPersonne<?php echo $key;?>MotifsortieId',
		    'ActioncandidatPersonne<?php echo $key;?>SortieleDay',
		    'ActioncandidatPersonne<?php echo $key;?>SortieleMonth',
		    'ActioncandidatPersonne<?php echo $key;?>SortieleYear',
		],
		false
	    );
    <?php endforeach;?>
</script>