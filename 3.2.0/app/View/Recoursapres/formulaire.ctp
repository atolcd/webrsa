<?php
	$this->pageTitle = 'Demandes de recours';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( isset( $recoursapres ) ) {
		$pagination = $this->Xpaginator->paginationBlock( 'ApreComiteapre', $this->passedArgs );
	}
	else {
		$pagination = '';
	}
?>

<?php  require_once  'filtre.ctp' ;?>

<!-- Résultats -->

<?php if( isset( $recoursapres ) ):?>
	<?php echo $this->Html->tag( 'h2', 'Résultats de la recherche' );?>
	<?php if( is_array( $recoursapres ) && count( $recoursapres ) > 0 ):?>
		<?php echo $pagination;?>
		<?php echo $this->Xform->create( 'Recoursapre', array() );?>

		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° demande APRE', 'Apre.numeroapre' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Commune de l\'allocataire', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date demande APRE', 'Apre.datedemandeapre' );?></th>
					<th>Décision comité examen</th>
					<th><?php echo $this->Xpaginator->sort( 'Date décision comité', 'Comiteapre.datecomite' );?></th>
					<th>Demande de recours</th>
					<th>Date recours</th>
					<th>Observations</th>
					<th class="action noprint">Actions</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $recoursapres as $index => $recours ):?>
				<?php
					$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>N° CAF</th>
									<td>'.h( $recours['Dossier']['matricule'] ).'</td>
								</tr>
								<tr>
									<th>Date naissance</th>
									<td>'.h( date_short( $recours['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $recours['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>Code postal</th>
									<td>'.h( $recours['Adresse']['codepos'] ).'</td>
								</tr>
							</tbody>
						</table>';
						$title = $recours['Dossier']['numdemrsa'];


					$apre_id = Set::extract( $recours, 'ApreComiteapre.apre_id');
					$recoursapre_id = Set::extract( $recours, 'ApreComiteapre.comiteapre_id');
					$aprecomiteapre_id = Set::extract( $recours, 'ApreComiteapre.id');

					$valueRecourapre = Set::classicExtract( $this->request->data, 'ApreComiteapre.'.$index.'.recoursapre' );
					echo $this->Xhtml->tableCells(
						array(
							h( Set::classicExtract( $recours, 'Apre.numeroapre') ),
							h( Set::classicExtract( $recours, 'Personne.qual').' '.Set::classicExtract( $recours, 'Personne.nom').' '.Set::classicExtract( $recours, 'Personne.prenom') ),
							h( Set::classicExtract( $recours, 'Adresse.nomcom') ),
							h( $this->Locale->date( 'Date::short', Set::extract( $recours, 'Apre.datedemandeapre' ) ) ),
							h( Set::enum( Set::classicExtract( $recours, 'ApreComiteapre.decisioncomite'), $options['decisioncomite'] ) ),
							h( $this->Locale->date( 'Date::short', Set::extract( $recours, 'Comiteapre.datecomite' ) ) ),
							$this->Xform->enum( 'ApreComiteapre.'.$index.'.recoursapre', array( 'legend' => false, 'type' => 'radio', 'separator' => '<br />', 'options' => $options['recoursapre'], 'value' => ( !empty( $valueRecourapre ) ? $valueRecourapre : 'N' ) ) ).
							$this->Xform->input( 'ApreComiteapre.'.$index.'.apre_id', array( 'label' => false, 'div' => false, 'value' => $apre_id, 'type' => 'hidden' ) ).
							$this->Xform->input( 'ApreComiteapre.'.$index.'.id', array( 'label' => false, 'div' => false, 'value' => $aprecomiteapre_id, 'type' => 'hidden' ) ).
							$this->Xform->input( 'ApreComiteapre.'.$index.'.comiteapre_id', array( 'label' => false, 'type' => 'hidden', 'value' => $recoursapre_id ) ).
							$this->Xform->input( 'Comiteapre.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => Set::extract( $recours, 'Comiteapre.id' ) ) ).
							$this->Xform->input( 'Apre.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => Set::extract( $recours, 'Apre.id' ) ) ),

							$this->Xform->input( 'ApreComiteapre.'.$index.'.daterecours', array( 'label' => false, 'type' => 'date', 'dateFormat' => 'DMY' ) ),
							$this->Xform->input( 'ApreComiteapre.'.$index.'.observationrecours', array( 'label' => false, 'type' => 'text', 'rows' => 3 ) ),
							$this->Xhtml->viewLink(
								'Voir le comite « '.Set::extract( $recours, 'Comiteapre.id' ).' »',
								array( 'controller' => 'comitesapres', 'action' => 'view', Set::extract( $recours, 'Comiteapre.id' ) ),
								true,
								true
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
		<?php echo $this->Xform->submit( 'Validation de la liste' );?>
		<?php echo $this->Xform->end();?>

	<?php else:?>
		<p class="notice">Aucune demande de recours présente dans la cohorte.</p>
	<?php endif?>
<?php endif?>