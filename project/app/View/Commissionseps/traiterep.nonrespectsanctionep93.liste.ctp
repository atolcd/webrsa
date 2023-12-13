<?php
echo '<table><thead>
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
<th>Observations</th>
</tr>
</thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {

		$hiddenFields = $this->Form->input( "Decisionnonrespectsanctionep93.{$i}.id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionnonrespectsanctionep93.{$i}.passagecommissionep_id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionnonrespectsanctionep93.{$i}.etape", array( 'type' => 'hidden', 'value' => 'ep' ) ).
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
				array(
					$this->Form->input( "Decisionnonrespectsanctionep93.{$i}.decision", array( 'type' => 'select', 'options' => $options['Decisionnonrespectsanctionep93']['decision'], 'div' => false, 'label' => false, 'empty' => true, 'value' => $dossierep['Passagecommissionep'][0]['Decisionnonrespectsanctionep93'][0]['decision'] ) ),
					array( 'id' => "Decisionnonrespectsanctionep93{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionnonrespectsanctionep93'][$i]['decision'] ) ? 'error' : '' ) )
				),

				$this->Form->input( "Decisionnonrespectsanctionep93.{$i}.commentaire", array( 'label' =>false, 'type' => 'textarea', 'value' => $dossierep['Passagecommissionep'][0]['Decisionnonrespectsanctionep93'][0]['commentaire'] ) ).
				$hiddenFields
			),
			array( 'class' => 'odd' ),
			array( 'class' => 'even' )
		);
	}
	echo '</tbody></table>';
?>