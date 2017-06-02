<?php
echo '<table><thead>
<tr>
<th>Personne</th>
<th>Nom du demandeur</th>
<th>Adresse</th>
<th>Date de naissance</th>
<th>Création du dossier EP</th>
<th>Date de début du contrat</th>
<th>Date de fin du contrat</th>
<th>Date de signalement</th>
<th>Motif de passage en EP</th>
<th>Rang du passage en EP</th>
<th>Situation familiale</th>
<th>Nombre d\'enfants</th>
<th>Avis EP</th>
<th>Observations</th>
</tr>
</thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$lineOptions = array();
		foreach( $options['Decisionsignalementep93']['decision'] as $key => $label ) {
			if( !in_array( $key[0], array( 1, 2 ) ) || ( $key[0] == min( 2, $dossierep['Signalementep93']['rang'] ) ) ) {
				$lineOptions[$key] = $label;
			}
		}

		$hiddenFields = $this->Form->input( "Decisionsignalementep93.{$i}.id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionsignalementep93.{$i}.passagecommissionep_id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionsignalementep93.{$i}.etape", array( 'type' => 'hidden', 'value' => 'ep' ) ).
						$this->Form->input( "Decisionsignalementep93.{$i}.user_id", array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) );

		echo $this->Xhtml->tableCells(
			array(
				$dossierep['Personne']['id'],
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				$this->Locale->date( __( 'Locale->date' ), @$dossierep['Signalementep93']['Contratinsertion']['dd_ci'] ),
				$this->Locale->date( __( 'Locale->date' ), @$dossierep['Signalementep93']['Contratinsertion']['df_ci'] ),
				$this->Locale->date( __( 'Locale->date' ), @$dossierep['Signalementep93']['date'] ),
				@$dossierep['Signalementep93']['motif'],
				@$dossierep['Signalementep93']['rang'],
				Set::enum( @$dossierep['Personne']['Foyer']['sitfam'], $options['Foyer']['sitfam'] ),
				@$dossierep['Personne']['Foyer']['nbenfants'],
				array(
					$this->Form->input( "Decisionsignalementep93.{$i}.decision", array( 'type' => 'select', 'options' => $lineOptions, 'div' => false, 'label' => false, 'empty' => true ) ),
					array( 'id' => "Decisionsignalementep93{$i}ColumnDecision", 'class' => ( !empty( $this->validationErrors['Decisionsignalementep93'][$i]['decision'] ) ? 'error' : '' ) )
				),
				$this->Form->input( "Decisionsignalementep93.{$i}.commentaire", array( 'label' => false, 'type' => 'textarea' ) ).
				$hiddenFields
			),
			array( 'class' => "odd {$multiple}" ),
			array( 'class' => "even {$multiple}" )
		);
	}
	echo '</tbody></table>';
?>