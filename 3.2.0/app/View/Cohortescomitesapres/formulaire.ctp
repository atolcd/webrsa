<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php if( isset( $comitesapres ) && is_array( $comitesapres ) && count( $comitesapres ) > 0 ):?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php for( $i = 0 ; $i < count( $comitesapres ) ; $i++ ):?>
		observeDisableFieldsOnValue( 'ApreComiteapre<?php echo $i;?>Decisioncomite', [ 'ApreComiteapre<?php echo $i;?>Montantattribue' ], 'ACC', false );
		<?php endfor;?>
	});
</script>
<?php endif;?>

<h1><?php echo $this->pageTitle = 'Décisions des comités';?></h1>

<?php
	if( isset( $comitesapres ) ) {
		$pagination = $this->Xpaginator->paginationBlock( 'Comiteapre', $this->passedArgs );
	}
	else {
		$pagination = '';
	}
?>

<?php  require_once  'filtre.ctp' ;?>
<!-- Résultats -->

<?php if( isset( $comitesapres ) ):?>
	<?php echo $this->Html->tag( 'h2', 'Résultats de la recherche' );?>
	<?php if( is_array( $comitesapres ) && count( $comitesapres ) > 0 ):?>
		<?php echo $pagination;?>
		<?php echo $this->Xform->create( 'Cohortecomiteapre', array() );?>

		<?php
			$filtre = Set::extract( $this->request->data, 'Cohortecomiteapre' );
			if( !empty( $filtre ) ) {
				foreach( $filtre as $key => $value ) {
					echo $this->Xform->input( "Cohortecomiteapre.{$key}", array( 'type' => 'hidden', 'value' => $value ) );
				}
			}
		?>

		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° demande RSA', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom de l\'allocataire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Commune de l\'allocataire', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de demande APRE', 'Apre.datedemandeapre' );?></th>
					<th>Décision comité examen</th>
					<th><?php echo $this->Xpaginator->sort( 'Date de décision comité', 'Comiteapre.datecomite' );?></th>
					<th>Montant demandé</th>
					<th>Montant attribué</th>
					<th>Observations</th>
					<th class="action noprint">Actions</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $comitesapres as $index => $comite ):?>
				<?php
					$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>N° CAF</th>
									<td>'.h( $comite['Dossier']['matricule'] ).'</td>
								</tr>
								<tr>
									<th>Date naissance</th>
									<td>'.h( date_short( $comite['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $comite['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>Code postal</th>
									<td>'.h( $comite['Adresse']['codepos'] ).'</td>
								</tr>
							</tbody>
						</table>';
						$title = $comite['Dossier']['numdemrsa'];


					$apre_id = Set::extract( $comite, 'ApreComiteapre.apre_id');
					$comiteapre_id = Set::extract( $comite, 'ApreComiteapre.comiteapre_id');
					$aprecomiteapre_id = Set::extract( $comite, 'ApreComiteapre.id');

					echo $this->Xhtml->tableCells(
						array(
							h( Set::classicExtract( $comite, 'Dossier.numdemrsa') ),
							h( Set::classicExtract( $comite, 'Personne.qual').' '.Set::classicExtract( $comite, 'Personne.nom').' '.Set::classicExtract( $comite, 'Personne.prenom') ),
							h( Set::classicExtract( $comite, 'Adresse.nomcom') ),
							h( $this->Locale->date( 'Date::short', Set::extract( $comite, 'Apre.datedemandeapre' ) ) ),

							$this->Xform->enum( 'ApreComiteapre.'.$index.'.decisioncomite', array( 'label' => false, 'type' => 'select', 'options' => $options['decisioncomite'], 'empty' => true ) ).
							$this->Xform->input( 'ApreComiteapre.'.$index.'.apre_id', array( 'label' => false, 'div' => false, 'value' => $apre_id, 'type' => 'hidden' ) ).
							$this->Xform->input( 'ApreComiteapre.'.$index.'.id', array( 'label' => false, 'div' => false, 'value' => $aprecomiteapre_id, 'type' => 'hidden' ) ).
							$this->Xform->input( 'ApreComiteapre.'.$index.'.comiteapre_id', array( 'label' => false, 'type' => 'hidden', 'value' => $comiteapre_id ) ).
							$this->Xform->input( 'Comiteapre.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => Set::extract( $comite, 'Comiteapre.id' ) ) ).
							$this->Xform->input( 'Apre.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => Set::extract( $comite, 'Apre.id' ) ) ),


							h( $this->Locale->date( 'Date::short', Set::extract( $comite, 'Comiteapre.datecomite' ) ) ),
							h( Set::classicExtract( $comite, 'Apre.montanttotal') ),
							$this->Xform->input( 'ApreComiteapre.'.$index.'.montantattribue', array( 'label' => false, 'type' => 'text', 'value' => Set::classicExtract( $comite, 'Apre.montanttotal' ) ) ),
							$this->Xform->input( 'ApreComiteapre.'.$index.'.observationcomite', array( 'label' => false, 'type' => 'text', 'rows' => 3 ) ),
							$this->Xhtml->viewLink(
								'Voir l\'APRE',
								array( 'controller' => 'apres', 'action' => 'index', Set::classicExtract( $comite, 'Personne.id' ) ),
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
		<?php echo $this->Xform->submit( 'Validation de la liste', array( 'onclick' => 'return confirm( "Êtes-vous sûr de vouloir valider ?" )' ) );?>
		<?php echo $this->Xform->end();?>


	<?php else:?>
		<p>Aucune APRE présente pour ce Comité d'examen.</p>
	<?php endif?>
<?php endif?>