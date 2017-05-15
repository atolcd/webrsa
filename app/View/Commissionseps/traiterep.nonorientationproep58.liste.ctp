<?php
echo '<table><thead>
<tr>
<th>Nom du demandeur</th>
<th>Adresse</th>
<th>Date de naissance</th>
<th>Création du dossier EP</th>
<th>Date d\'orientation</th>
<th>Proposition validée en COV le</th>
<th>Orientation actuelle</th>
<th colspan="4">Avis EPL</th>
<th>Observations</th>
</tr>
</thead><tbody>';
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$hiddenFields = $this->Form->input( "Decisionnonorientationproep58.{$i}.id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionnonorientationproep58.{$i}.passagecommissionep_id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionnonorientationproep58.{$i}.etape", array( 'type' => 'hidden', 'value' => 'ep' ) ).
						$this->Form->input( "Decisionnonorientationproep58.{$i}.user_id", array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) );

		echo $this->Xhtml->tableCells(
			array(
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Nonorientationproep58']['Orientstruct']['date_valid'] ),

				$this->Locale->date( __( 'Locale->datetime' ), Set::classicExtract( $dossierep, 'Nonorientationproep58.Decisionpropononorientationprocov58.Passagecov58.Cov58.datecommission' ) ),
				implode(
					' - ',
					Hash::filter( (array)array(
						$dossierep['Nonorientationproep58']['Orientstruct']['Typeorient']['lib_type_orient'],
						$dossierep['Nonorientationproep58']['Orientstruct']['Structurereferente']['lib_struc'],
						implode(
							' ',
							Hash::filter( (array)array(
								@$dossierep['Nonorientationproep58']['Orientstruct']['Referent']['qual'],
								@$dossierep['Nonorientationproep58']['Orientstruct']['Referent']['nom'],
								@$dossierep['Nonorientationproep58']['Orientstruct']['Referent']['prenom']
							) )
						)
					) )
				),

				array(
					$this->Form->input( "Decisionnonorientationproep58.{$i}.decision", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $options['Decisionnonorientationproep58']['decision'] ) ),
					array( 'id' => "Decisionnonorientationproep58{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionnonorientationproep58'][$i]['decision'] ) ? 'error' : '' ) )
				),
				array(
					$this->Form->input( "Decisionnonorientationproep58.{$i}.typeorient_id", array( 'type' => 'select', 'label' => false, 'options' => $typesorients, 'empty' => true ) ),
					( !empty( $this->validationErrors['Decisionnonorientationproep58'][$i]['typeorient_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionnonorientationproep58.{$i}.structurereferente_id", array( 'label' => false, 'options' => $structuresreferentes, 'empty' => true, 'type' => 'select' ) ),
					( !empty( $this->validationErrors['Decisionnonorientationproep58'][$i]['structurereferente_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionnonorientationproep58.{$i}.referent_id", array( 'label' => false, 'options' => $referents, 'empty' => true, 'type' => 'select' ) ),
					( !empty( $this->validationErrors['Decisionnonorientationproep58'][$i]['referent_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisionnonorientationproep58.{$i}.commentaire", array( 'label' =>false, 'type' => 'textarea' ) ).
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
			dependantSelect( 'Decisionnonorientationproep58<?php echo $i?>StructurereferenteId', 'Decisionnonorientationproep58<?php echo $i?>TypeorientId' );
			try { $( 'Decisionnonorientationproep58<?php echo $i?>StructurereferenteId' ).onchange(); } catch(id) { }

			dependantSelect( 'Decisionnonorientationproep58<?php echo $i?>ReferentId', 'Decisionnonorientationproep58<?php echo $i?>StructurereferenteId' );
			try { $( 'Decisionnonorientationproep58<?php echo $i?>ReferentId' ).onchange(); } catch(id) { }

			$( 'Decisionnonorientationproep58<?php echo $i;?>Decision' ).observe( 'change', function() {
				changeColspanFormAnnuleReporteEps( 'Decisionnonorientationproep58<?php echo $i;?>DecisionColumn', 4, 'Decisionnonorientationproep58<?php echo $i;?>Decision', [ 'Decisionnonorientationproep58<?php echo $i;?>TypeorientId', 'Decisionnonorientationproep58<?php echo $i;?>StructurereferenteId', 'Decisionnonorientationproep58<?php echo $i;?>ReferentId' ] );
			});
			changeColspanFormAnnuleReporteEps( 'Decisionnonorientationproep58<?php echo $i;?>DecisionColumn', 4, 'Decisionnonorientationproep58<?php echo $i;?>Decision', [ 'Decisionnonorientationproep58<?php echo $i;?>TypeorientId', 'Decisionnonorientationproep58<?php echo $i;?>StructurereferenteId', 'Decisionnonorientationproep58<?php echo $i;?>ReferentId' ] );


			observeDisableFieldsOnValue(
				'Decisionnonorientationproep58<?php echo $i;?>Decision',
				[
					'Decisionnonorientationproep58<?php echo $i;?>TypeorientId',
					'Decisionnonorientationproep58<?php echo $i;?>StructurereferenteId',
					'Decisionnonorientationproep58<?php echo $i;?>ReferentId'
				],
				'',
				true
			);

		<?php endfor;?>
	});
</script>