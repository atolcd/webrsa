<script type="text/javascript">
	function toutAccepter() {
		regex = new RegExp('(data\\[Decisionreorientationep93\\]\\[){1}(\\d+)(\\]\\[decision\\]){1}');

		$('Reorientationep93').select('select').each(function(editable){
			if(regex.test(editable.name)){
				editable.setValue("accepte");
				id = (editable.name).match(regex)[2];
				//On récupère l'id pour able les champs struc / referent associés
				$('Decisionreorientationep93'+id+'TypeorientId').disabled = false;
				input = $('Decisionreorientationep93'+id+'TypeorientId').up( 'div.input' );
				input.removeClassName( 'disabled' );
				$('Decisionreorientationep93'+id+'StructurereferenteId').disabled = false;
				input2 = $('Decisionreorientationep93'+id+'StructurereferenteId').up( 'div.input' );
				input2.removeClassName( 'disabled' );
			}

		});
	}
</script>
<?php

echo $this->Form->button( 'Tout accepter', array( 'type' => 'button', 'onclick' => "return toutAccepter();" ) );


echo '<table><thead>
<tr>
<th>Personne</th>
<th>Nom du demandeur</th>
<th>Adresse</th>
<th>Date de naissance</th>
<th>Création du dossier EP</th>
<th>Motif de la demande</th>
<th>Orientation actuelle</th>
<th>Structure référente actuelle</th>
<th>Orientation préconisée</th>
<th>Structure référente préconisée</th>
<th colspan=\'3\'>Avis Réorientation</th>
<th>Observations</th>
</tr>
</thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$hiddenFields = $this->Form->input( "Decisionreorientationep93.{$i}.id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionreorientationep93.{$i}.passagecommissionep_id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionreorientationep93.{$i}.etape", array( 'type' => 'hidden', 'value' => 'ep' ) ).
						$this->Form->input( "Decisionreorientationep93.{$i}.user_id", array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) );

		echo $this->Xhtml->tableCells(
			array(
				$dossierep['Personne']['id'],
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				$dossierep['Reorientationep93']['Motifreorientep93']['name'],
				isset($dossierep['Reorientationep93']['Orientstruct']['Typeorient']['lib_type_orient']) ? $dossierep['Reorientationep93']['Orientstruct']['Typeorient']['lib_type_orient'] : null,
				isset($dossierep['Reorientationep93']['Orientstruct']['Structurereferente']['lib_struc']) ? $dossierep['Reorientationep93']['Orientstruct']['Structurereferente']['lib_struc'] : null,
				@$dossierep['Reorientationep93']['Typeorient']['lib_type_orient'],
				@$dossierep['Reorientationep93']['Structurereferente']['lib_struc'],

				array(
					$this->Form->input( "Decisionreorientationep93.{$i}.decision", array( 'label' => false, 'type' => 'select', 'options' => @$options['Decisionreorientationep93']['decision'], 'empty' => true, 'value' => isset($dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['decision']) ? $dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['decision'] : null ) ),
					array( 'id' => "Decisionreorientationep93{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionreorientationep93'][$i]['decision'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionreorientationep93.{$i}.typeorient_id", array( 'label' => false, 'options' => $typesorients, 'empty' => true, 'value' => isset($dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['Structurereferente']['typeorient_id']) ? $dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['Structurereferente']['typeorient_id'] : $dossierep['Reorientationep93']['typeorient_id'] )),
					( !empty( $this->validationErrors['Decisionreorientationep93'][$i]['typeorient_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input(
						"Decisionreorientationep93.{$i}.structurereferente_id",
						array(
							'label' => false,
							'options' => $structuresreferentes,
							'empty' => true,
							'value' => (isset($dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['Structurereferente']['typeorient_id'])) ? ($dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['Structurereferente']['typeorient_id']."_".$dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['Structurereferente']['id']) : ($dossierep['Reorientationep93']['typeorient_id']."_".$dossierep['Reorientationep93']['structurereferente_id'])
						)
					),
					( !empty( $this->validationErrors['Decisionreorientationep93'][$i]['structurereferente_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisionreorientationep93.{$i}.commentaire", array( 'label' =>false, 'type' => 'textarea', 'value' => isset($dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['commentaire']) ? $dossierep['Passagecommissionep'][0]['Decisionreorientationep93'][0]['commentaire'] : null ) ).
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
			dependantSelect( 'Decisionreorientationep93<?php echo $i?>StructurereferenteId', 'Decisionreorientationep93<?php echo $i?>TypeorientId' );
			try { $( 'Decisionreorientationep93<?php echo $i?>StructurereferenteId' ).onchange(); } catch(id) { }


			$( 'Decisionreorientationep93<?php echo $i;?>Decision' ).observe( 'change', function() {
				changeColspanFormAnnuleReporteEps( 'Decisionreorientationep93<?php echo $i;?>DecisionColumn', 3, 'Decisionreorientationep93<?php echo $i;?>Decision', [ 'Decisionreorientationep93<?php echo $i;?>TypeorientId', 'Decisionreorientationep93<?php echo $i;?>StructurereferenteId' ] );
			});
			changeColspanFormAnnuleReporteEps( 'Decisionreorientationep93<?php echo $i;?>DecisionColumn', 3, 'Decisionreorientationep93<?php echo $i;?>Decision', [ 'Decisionreorientationep93<?php echo $i;?>TypeorientId', 'Decisionreorientationep93<?php echo $i;?>StructurereferenteId' ] );


			observeDisableFieldsOnValue(
				'Decisionreorientationep93<?php echo $i;?>Decision',
				[ 'Decisionreorientationep93<?php echo $i;?>TypeorientId', 'Decisionreorientationep93<?php echo $i;?>StructurereferenteId' ],
				'accepte',
				false
			);
		<?php endfor;?>
	});
</script>