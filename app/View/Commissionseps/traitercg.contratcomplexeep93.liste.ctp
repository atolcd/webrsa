<?php
echo '<table id="Decisioncontratcomplexeep93" class="tooltips"><thead>
<tr>
<th rowspan="2">Personne</th>
<th rowspan="2">Nom du demandeur</th>
<th rowspan="2">Adresse</th>
<th rowspan="2">Date de naissance</th>
<th rowspan="2">Création du dossier EP</th>
<th rowspan="2">Date de début du contrat</th>
<th rowspan="2">Date de fin du contrat</th>
<th rowspan="2">Avis EP</th>
<th colspan="5">Décision CD</th>
<th rowspan="2">Observations</th>
<th rowspan="2" class="innerTableHeader noprint">Avis EP</th>
</tr>
<tr>
	<th>Décision PCG</th>
	<th>Décision</th>
	<th>Date de validation</th>
	<th>Observations du contrat</th>
	<th>Observations décision</th>
</tr>
</thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$indexDecision = count( $dossierep['Passagecommissionep'][0]['Decisioncontratcomplexeep93'] ) - 1;

		$innerTable = "<table id=\"innerTableDecisioncontratcomplexeep93{$i}\" class=\"innerTable\">
			<tbody>
				<tr>
					<th>Observations de l'EP</th>
					<td>".Set::classicExtract( $dossierep, "Passagecommissionep.0.Decisioncontratcomplexeep93.{$indexDecision}.commentaire" )."</td>
				</tr>
			</tbody>
		</table>";

		$hiddenFields = $this->Form->input( "Decisioncontratcomplexeep93.{$i}.id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisioncontratcomplexeep93.{$i}.passagecommissionep_id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisioncontratcomplexeep93.{$i}.etape", array( 'type' => 'hidden', 'value' => 'cg' ) ).
						$this->Form->input( "Decisioncontratcomplexeep93.{$i}.user_id", array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) );

		echo $this->Xhtml->tableCells(
			array(
				$dossierep['Personne']['id'],
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				$this->Locale->date( __( 'Locale->date' ), @$dossierep['Contratcomplexeep93']['Contratinsertion']['dd_ci'] ),
				$this->Locale->date( __( 'Locale->date' ), @$dossierep['Contratcomplexeep93']['Contratinsertion']['df_ci'] ),
				implode(
					' - ',
					Hash::filter( (array)
						array(
							Set::enum( @$dossierep['Passagecommissionep'][0]['Decisioncontratcomplexeep93'][$indexDecision]['decision'], $options['Decisioncontratcomplexeep93']['decision'] ),
							$this->Locale->date( 'Locale->date', @$dossierep['Passagecommissionep'][0]['Decisioncontratcomplexeep93'][$indexDecision]['datevalidation_ci'] ),
							@$dossierep['Passagecommissionep'][0]['Decisioncontratcomplexeep93'][$indexDecision]['observ_ci'],
							@$dossierep['Passagecommissionep'][0]['Decisioncontratcomplexeep93'][$indexDecision]['raisonnonpassage']
						)
					)
				),

				$this->Form->input( "Decisioncontratcomplexeep93.{$i}.decisionpcg", array( 'legend' => false, 'options' => @$options['Decisionreorientationep93']['decisionpcg'], 'type' => 'radio' ) ),
				array(
					$this->Form->input( "Decisioncontratcomplexeep93.{$i}.decision", array( 'type' => 'select', 'options' => $options['Decisioncontratcomplexeep93']['decision'], 'label' => false, 'empty' => true ) ),
					array( 'id' => "Decisioncontratcomplexeep93{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisioncontratcomplexeep93'][$i]['decision'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisioncontratcomplexeep93.{$i}.datevalidation_ci", array( 'type' => 'date', 'label' => false, 'dateFormat' => __( 'Locale->dateFormat' ), 'empty' => true ) ),
					( !empty( $this->validationErrors['Decisioncontratcomplexeep93'][$i]['datevalidation_ci'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisioncontratcomplexeep93.{$i}.observ_ci", array( 'type' => 'textarea', 'label' => false ) ),
					( !empty( $this->validationErrors['Decisioncontratcomplexeep93'][$i]['observ_ci'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisioncontratcomplexeep93.{$i}.observationdecision", array( 'type' => 'textarea', /*'div' => false, */'label' => false ) ),
					( !empty( $this->validationErrors['Decisioncontratcomplexeep93'][$i]['observationdecision'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisioncontratcomplexeep93.{$i}.commentaire", array( 'label' => false, 'type' => 'textarea' ) ).
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
			

			$( 'Decisioncontratcomplexeep93<?php echo $i;?>DecisionpcgEnattente' ).observe( 'click', function() {
				$( 'Decisioncontratcomplexeep93<?php echo $i;?>Decision' ).setValue( 'reporte' );
				fireEvent( $( 'Decisioncontratcomplexeep93<?php echo $i;?>Decision' ), 'change' );
			} );

			$( 'Decisioncontratcomplexeep93<?php echo $i;?>Decision' ).observe( 'change', function() {
				changeColspanFormAnnuleReporteEps( 'Decisioncontratcomplexeep93<?php echo $i;?>DecisionColumn', 4, 'Decisioncontratcomplexeep93<?php echo $i;?>Decision', [ 'Decisioncontratcomplexeep93<?php echo $i;?>ObservCi', 'Decisioncontratcomplexeep93<?php echo $i;?>DatevalidationCiDay', 'Decisioncontratcomplexeep93<?php echo $i;?>DatevalidationCiMonth', 'Decisioncontratcomplexeep93<?php echo $i;?>DatevalidationCiYear', 'Decisioncontratcomplexeep93<?php echo $i;?>Observationdecision' ] );
			});
			changeColspanFormAnnuleReporteEps( 'Decisioncontratcomplexeep93<?php echo $i;?>DecisionColumn', 4, 'Decisioncontratcomplexeep93<?php echo $i;?>Decision', [ 'Decisioncontratcomplexeep93<?php echo $i;?>ObservCi', 'Decisioncontratcomplexeep93<?php echo $i;?>DatevalidationCiDay', 'Decisioncontratcomplexeep93<?php echo $i;?>DatevalidationCiMonth', 'Decisioncontratcomplexeep93<?php echo $i;?>DatevalidationCiYear', 'Decisioncontratcomplexeep93<?php echo $i;?>Observationdecision' ] );
			
			observeDisableFieldsOnValue(
				'Decisioncontratcomplexeep93<?php echo $i;?>Decision',
				[
					'Decisioncontratcomplexeep93<?php echo $i;?>DatevalidationCiDay',
					'Decisioncontratcomplexeep93<?php echo $i;?>DatevalidationCiMonth',
					'Decisioncontratcomplexeep93<?php echo $i;?>DatevalidationCiYear'
				],
				'valide',
				false
			);
		<?php endfor;?>
	});
</script>