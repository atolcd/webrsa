<?php
echo '<table id="Decisionsaisinepdoep66" class="tooltips"><thead>
<tr>
<th>Nom du demandeur</th>
<th>Adresse</th>
<th>Date de naissance</th>
<th>Création du dossier EP</th>
<th>Motif(s) de la PDO</th>
<th>Description du traitement</th>
<th colspan=\'2\'>Avis de l\'EP</th>
<th colspan=\'3\'>Décision CG</th>
<th>Observations</th>
<th class="innerTableHeader noprint">Avis EP</th>
</tr>
</thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$listeSituationPdo = array();
		foreach($dossierep['Saisinepdoep66']['Traitementpdo']['Propopdo']['Situationpdo'] as $situationpdo) {
			$listeSituationPdo[] = $situationpdo['libelle'];
		}

		$decisionep = $dossierep['Passagecommissionep'][0]['Decisionsaisinepdoep66'][count($dossierep['Passagecommissionep'][0]['Decisionsaisinepdoep66'])-1];

		$innerTable = "<table id=\"innerTableDecisionsaisinepdoep66{$i}\" class=\"innerTable\">
			<tbody>
				<tr>
					<th>Observations de l'EP</th>
					<td>".Set::classicExtract( $decisionep, "commentaire" )."</td>
				</tr>
			</tbody>
		</table>";

		$hiddenFields = $this->Form->input( "Decisionsaisinepdoep66.{$i}.id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionsaisinepdoep66.{$i}.etape", array( 'type' => 'hidden', 'value' => 'cg' ) ).
						$this->Form->input( "Decisionsaisinepdoep66.{$i}.passagecommissionep_id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionsaisinepdoep66.{$i}.user_id", array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) );

		echo $this->Xhtml->tableCells(
			array(
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				implode(' / ', $listeSituationPdo),
				$dossierep['Saisinepdoep66']['Traitementpdo']['Descriptionpdo']['name'],

				$options['Decisionsaisinepdoep66']['decision'][$decisionep['decision']],
				@$decisionep['Decisionpdo']['libelle'],
				
				array(
					$this->Form->input( "Decisionsaisinepdoep66.{$i}.decision", array( 'label' => false, 'options' => @$options['Decisionsaisinepdoep66']['decision'], 'empty' => true ) ),
					array( 'id' => "Decisionsaisinepdoep66{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionsaisinepdoep66'][$i]['decision'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionsaisinepdoep66.{$i}.datedecisionpdo", array( 'label' => false, 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>date('Y')-1, 'empty' => false ) ),
					( !empty( $this->validationErrors['Decisionsaisinepdoep66'][$i]['datedecisionpdo'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionsaisinepdoep66.{$i}.decisionpdo_id", array( 'label' => false, 'type' => 'select', 'options' => $options['Decisionsaisinepdoep66']['decisionpdo_id'], 'empty' => true ) ),
					( !empty( $this->validationErrors['Decisionsaisinepdoep66'][$i]['decisionpdo_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisionsaisinepdoep66.{$i}.commentaire", array( 'label' =>false, 'type' => 'textarea' ) ).
				$hiddenFields,
				array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
			),
			array( 'class' => "odd {$multiple}" ),
			array( 'class' => "even {$multiple}" )
		);
	}
	echo '</tbody></table>';

?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php for( $i = 0 ; $i < count( $dossiers[$theme]['liste'] ) ; $i++ ):?>
			observeDisableFieldsOnValue(
				'Decisionsaisinepdoep66<?php echo $i;?>Decision',
				[ 'Decisionsaisinepdoep66<?php echo $i;?>Datedecisionpdo', 'Decisionsaisinepdoep66<?php echo $i;?>DecisionpdoId', 'Decisionsaisinepdoep66<?php echo $i;?>DatedecisionpdoDay', 'Decisionsaisinepdoep66<?php echo $i;?>DatedecisionpdoMonth', 'Decisionsaisinepdoep66<?php echo $i;?>DatedecisionpdoYear' ],
				'avis',
				false
			);

			$( 'Decisionsaisinepdoep66<?php echo $i;?>Decision' ).observe( 'change', function() {
				changeColspanFormAnnuleReporteEps( 'Decisionsaisinepdoep66<?php echo $i;?>DecisionColumn', 3, 'Decisionsaisinepdoep66<?php echo $i;?>Decision', [ 'Decisionsaisinepdoep66<?php echo $i;?>DatedecisionpdoDay', 'Decisionsaisinepdoep66<?php echo $i;?>DecisionpdoId' ] );
			});
			changeColspanFormAnnuleReporteEps( 'Decisionsaisinepdoep66<?php echo $i;?>DecisionColumn', 3, 'Decisionsaisinepdoep66<?php echo $i;?>Decision', [ 'Decisionsaisinepdoep66<?php echo $i;?>DatedecisionpdoDay', 'Decisionsaisinepdoep66<?php echo $i;?>DecisionpdoId' ] );
		<?php endfor;?>
	});
</script>