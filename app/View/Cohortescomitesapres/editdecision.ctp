<?php
	$this->pageTitle = 'Modification Décisions du comité';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<?php if( isset( $apre['Comiteapre'] ) && is_array( $apre['Comiteapre'] ) && count( $apre['Comiteapre'] ) > 0 ):?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsOnValue( 'ApreComiteapreDecisioncomite', [ 'ApreComiteapreMontantattribue' ], 'ACC', false );
	});
</script>
<?php endif;?>

<h1>Modification de la Décision du Comité</h1>

<?php if( isset( $apre['Comiteapre'] ) ):?>
		<?php echo $this->Xform->create( 'Cohortecomiteapre', array() );?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th>N° demande RSA</th>
					<th>Nom de l'allocataire</th>
					<th>Commune de l'allocataire</th>
					<th>Date de demande APRE</th>
					<th>Décision comité examen</th>
					<th>Date de décision comité</th>
					<th>Montant attribué</th>
					<th>Observations</th>
					<th class="action noprint">Actions</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$innerTable = '<table id="innerTablesearchResults0" class="innerTable">
							<tbody>
								<tr>
									<th>Date naissance</th>
									<td>'.h( date_short( $apre['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $apre['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>Code postal</th>
									<td>'.h( $apre['Adresse']['codepos'] ).'</td>
								</tr>
							</tbody>
						</table>';
					$title = $apre['Dossier']['numdemrsa'];

					$apre_id = Set::classicExtract( $aprecomiteapre, 'ApreComiteapre.apre_id');
					$comiteapre_id = Set::classicExtract( $aprecomiteapre, 'ApreComiteapre.comiteapre_id');
					$aprecomiteapre_id = Set::classicExtract( $aprecomiteapre, 'ApreComiteapre.id');

					echo $this->Xhtml->tableCells(
						array(
							h( Set::classicExtract( $apre, 'Dossier.numdemrsa') ),
							h( Set::classicExtract( $apre, 'Personne.qual').' '.Set::classicExtract( $apre, 'Personne.nom').' '.Set::classicExtract( $apre, 'Personne.prenom') ),
							h( Set::classicExtract( $apre, 'Adresse.nomcom') ),
							h( $this->Locale->date( 'Date::short', Set::extract( $apre, 'Apre.datedemandeapre' ) ) ),

							$this->Xform->enum( 'ApreComiteapre.decisioncomite', array( 'label' => false, 'type' => 'select', 'options' => $options['decisioncomite'], 'selected' => Set::classicExtract( $aprecomiteapre, 'ApreComiteapre.decisioncomite' ) ) ).
							$this->Xform->input( 'ApreComiteapre.apre_id', array( 'label' => false, 'div' => false, 'value' => $apre_id, 'type' => 'hidden' ) ).
							$this->Xform->input( 'ApreComiteapre.id', array( 'label' => false, 'div' => false, 'value' => $aprecomiteapre_id, 'type' => 'hidden' ) ).
							$this->Xform->input( 'ApreComiteapre.comiteapre_id', array( 'label' => false, 'type' => 'hidden', 'value' => $comiteapre_id ) ).
							$this->Xform->input( 'Comiteapre.id', array( 'label' => false, 'type' => 'hidden', 'value' => $comiteapre_id ) ).
							$this->Xform->input( 'Apre.id', array( 'label' => false, 'type' => 'hidden', 'value' => $apre_id ) ),

							h( $this->Locale->date( 'Date::short', Set::extract( $comiteapre, 'Comiteapre.datecomite' ) ) ),
							$this->Xform->input( 'ApreComiteapre.montantattribue', array( 'label' => false, 'type' => 'text', 'maxlength' => 7, 'value' => Set::classicExtract( $aprecomiteapre, 'ApreComiteapre.montantattribue') ) ),

							$this->Xform->input( 'ApreComiteapre.observationcomite', array( 'label' => false, 'type' => 'text', 'rows' => 3, 'value' => Set::classicExtract( $aprecomiteapre, 'ApreComiteapre.observationcomite') ) ),
							$this->Xhtml->viewLink(
								'Voir le comite « '.Set::extract( $aprecomiteapre, 'Comiteapre.id' ).' »',
								array( 'controller' => 'comitesapres', 'action' => 'view', Set::extract( $aprecomiteapre, 'Comiteapre.id' ) ),
								true,
								true
							),
							array( $innerTable, array( 'class' => 'innerTableCell' ) )
						),
						array( 'class' => 'odd', 'id' => 'innerTableTrigger0' ),
						array( 'class' => 'even', 'id' => 'innerTableTrigger0' )
					);
				?>
			</tbody>
		</table>

		<div class="submit">
			<?php
				echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
				echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
			?>
		</div>
		<?php echo $this->Xform->end();?>
<?php endif;?>