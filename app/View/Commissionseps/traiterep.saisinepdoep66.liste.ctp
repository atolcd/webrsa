<?php
echo '<table><thead>
<tr>
<th>Nom du demandeur</th>
<th>Adresse</th>
<th>Date de naissance</th>
<th>Création du dossier EP</th>
<th>Motif(s) de la PDO</th>
<th>Description du traitement</th>
<th colspan=\'2\'>Avis de l\'EP</th>
<th>Observations</th>
</tr>
</thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$listeSituationPdo = array();
		foreach($dossierep['Saisinepdoep66']['Traitementpdo']['Propopdo']['Situationpdo'] as $situationpdo) {
			$listeSituationPdo[] = $situationpdo['libelle'];
		}

		$hiddenFields = $this->Form->input( "Decisionsaisinepdoep66.{$i}.id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionsaisinepdoep66.{$i}.passagecommissionep_id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionsaisinepdoep66.{$i}.datedecisionpdo", array( 'type' => 'hidden', 'value' => date( 'Y-m-d', strtotime( $commissionep['Commissionep']['dateseance'] ) ) ) ).
						$this->Form->input( "Decisionsaisinepdoep66.{$i}.etape", array( 'type' => 'hidden', 'value' => 'ep' ) ).
						$this->Form->input( "Decisionsaisinepdoep66.{$i}.user_id", array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) );

		echo $this->Xhtml->tableCells(
			array(
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				implode(' / ', $listeSituationPdo),
				$dossierep['Saisinepdoep66']['Traitementpdo']['Descriptionpdo']['name'],

				array(
					$this->Form->input( "Decisionsaisinepdoep66.{$i}.decision", array( 'label' => false, 'options' => $options['Decisionsaisinepdoep66']['decision'], 'empty' => true ) ),
					array( 'id' => "Decisionsaisinepdoep66{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionsaisinepdoep66'][$i]['decision'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionsaisinepdoep66.{$i}.decisionpdo_id", array( 'label' => false, 'options' => @$options['Decisionsaisinepdoep66']['decisionpdo_id'], 'empty' => true ) ),
					( !empty( $this->validationErrors['Decisionsaisinepdoep66'][$i]['decisionpdo_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisionsaisinepdoep66.{$i}.commentaire", array( 'label' => false, 'type' => 'textarea' ) ).
				$hiddenFields
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
				[
					'Decisionsaisinepdoep66<?php echo $i;?>DecisionpdoId'
				],
				'avis',
				false
			);

			$( 'Decisionsaisinepdoep66<?php echo $i;?>Decision' ).observe( 'change', function() {
				changeColspanFormAnnuleReporteEps( 'Decisionsaisinepdoep66<?php echo $i;?>DecisionColumn', 2, 'Decisionsaisinepdoep66<?php echo $i;?>Decision', [ 'Decisionsaisinepdoep66<?php echo $i;?>DecisionpdoId' ] );
			});
			changeColspanFormAnnuleReporteEps( 'Decisionsaisinepdoep66<?php echo $i;?>DecisionColumn', 2, 'Decisionsaisinepdoep66<?php echo $i;?>Decision', [ 'Decisionsaisinepdoep66<?php echo $i;?>DecisionpdoId' ] );
		<?php endfor;?>
	});
</script>