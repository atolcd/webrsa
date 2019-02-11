<?php
echo '<table id="Decisionnonrespectsanctionep93" class="tooltips"><thead>
<tr>
<th>Personne</th>
<th>Nom du demandeur</th>
<th>Adresse</th>
<th>Date de naissance</th>
<th>Création du dossier EP</th>
<th>Origine du dossier</th>
<th>Date d\'orientation</th>
<th>Rang du passage en EP</th>
<th>Situation familiale</th>
<th>Nombre d\'enfants</th>
<th>Dossier actif</th>
<th>Avis EP</th>
<th colspan="3">Décision CD</th>
<th>Observations</th>
</tr>
</thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$lineOptions = array();
		foreach( $options['Decisionnonrespectsanctionep93']['decision'] as $key => $label ) {
			if( !in_array( $key[0], array( 1, 2 ) ) || ( $key[0] == min( 2, $dossierep['Nonrespectsanctionep93']['rgpassage'] ) ) ) {
				$lineOptions[$key] = $label;
			}
		}

		$indexDecision = count( $dossierep['Passagecommissionep'][0]['Decisionnonrespectsanctionep93'] ) - 1;

		$innerTable = "<table id=\"innerTableDecisionnonrespectsanctionep93{$i}\" class=\"innerTable\">
			<tbody>
				<tr>
					<th>Observations de l'EP</th>
					<td>".Set::classicExtract( $dossierep, "Passagecommissionep.0.Decisionnonrespectsanctionep93.{$indexDecision}.commentaire" )."</td>
				</tr>
			</tbody>
		</table>";

		$idDecision = null;
		$idPassage = null;
		if (isset ($dossierep['Passagecommissionep'][0]['Decisionnonrespectsanctionep93'])) {
			$idDecision = Set::classicExtract( $dossierep, "Passagecommissionep.0.Decisionnonrespectsanctionep93.{$indexDecision}.id" );
			$idPassage = Set::classicExtract( $dossierep, "Passagecommissionep.0.Decisionnonrespectsanctionep93.{$indexDecision}.passagecommissionep_id" );
		}

		$hiddenFields = $this->Form->input( "Decisionnonrespectsanctionep93.{$i}.id", array( 'type' => 'hidden', 'value' => $idDecision ) ).
						$this->Form->input( "Decisionnonrespectsanctionep93.{$i}.passagecommissionep_id", array( 'type' => 'hidden', 'value' => $idPassage ) ).
						$this->Form->input( "Decisionnonrespectsanctionep93.{$i}.etape", array( 'type' => 'hidden', 'value' => 'cg' ) ).
						$this->Form->input( "Decisionnonrespectsanctionep93.{$i}.user_id", array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) );

		echo $this->Xhtml->tableCells(
			array(
				$dossierep['Personne']['id'],
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				Set::enum( @$dossierep['Nonrespectsanctionep93']['origine'], $options['Nonrespectsanctionep93']['origine'] ),
				$this->Locale->date( __( 'Locale->date' ), @$dossierep['Nonrespectsanctionep93']['Orientstruct']['date_valid'] ),
				@$dossierep['Nonrespectsanctionep93']['rgpassage'],
				Set::enum( @$dossierep['Personne']['Foyer']['sitfam'], $options['Foyer']['sitfam'] ),
				@$dossierep['Personne']['Foyer']['nbenfants'],
				Set::enum( @$dossierep['Dossierep']['actif'], $options['Dossierep']['actif'] ),
				Set::enum( @$dossierep['Passagecommissionep'][0]['Decisionnonrespectsanctionep93'][$indexDecision]['decision'], $options['Decisionnonrespectsanctionep93']['decision'] ),

				$this->Form->input( "Decisionnonrespectsanctionep93.{$i}.decisionpcg", array( 'legend' => false, 'options' => @$options['Decisionreorientationep93']['decisionpcg'], 'type' => 'radio' ) ),
				array(
					$this->Form->input( "Decisionnonrespectsanctionep93.{$i}.decision", array( 'type' => 'select', 'options' => $lineOptions, 'div' => false, 'label' => false, 'empty' => true ) ),
					array( 'id' => "Decisionnonrespectsanctionep93{$i}ColumnDecision", 'colspan' => 2, 'class' => ( !empty( $this->validationErrors['Decisionnonrespectsanctionep93'][$i]['decision'] ) ? 'error' : '' ) )
				),
				$this->Form->input( "Decisionnonrespectsanctionep93.{$i}.commentaire", array( 'label' =>false, 'type' => 'textarea' ) ).
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
			$( 'Decisionnonrespectsanctionep93<?php echo $i;?>DecisionpcgEnattente' ).observe( 'click', function() {
				$( 'Decisionnonrespectsanctionep93<?php echo $i;?>Decision' ).setValue( 'reporte' );
				fireEvent( $( 'Decisionnonrespectsanctionep93<?php echo $i;?>Decision' ),'change');
			} );
		<?php endfor;?>
	});
</script>
