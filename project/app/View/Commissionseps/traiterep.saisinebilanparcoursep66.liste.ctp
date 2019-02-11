<?php
echo '<table>
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup span="4" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
		<colgroup span="5" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
		<colgroup />
		<thead>
			<tr>
				<th rowspan="2">Nom du demandeur</th>
				<th rowspan="2">Adresse</th>
				<th rowspan="2">Date de naissance</th>
				<th rowspan="2">Création du dossier EP</th>
				<th rowspan="2">Orientation actuelle</th>
				<th colspan="4" rowspan="2">Proposition référent</th>
				<th colspan="6">Avis EPL</th>
				<th rowspan="2">Observations</th>
				<th rowspan="2">Action</th>
			</tr>
			<tr>
				<th>Commentaire<br />Bénéficiaire</th>
				<th>Avis</th>
				<th>SOCIAL/Emploi</th>
				<th>Type d\'orientation</th>
				<th>Structure référente</th>
				<th>Référent</th>
			</tr>
		</thead>
	<tbody>';
$typeorientprincipale = Configure::read( 'Orientstruct.typeorientprincipale' );
$typeorientemploiId = $typeorientprincipale['Emploi'][0];
	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

		$hiddenFields =
						$this->Form->input( "Bilanparcours66.{$i}.parenttypeorient_id", array( 'type' => 'hidden', 'value' => $dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Typeorient']['parentid'] ) ).
						$this->Form->input( "Bilanparcours66.{$i}.oldstructurereferente_id", array( 'type' => 'hidden', 'value' => $dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['structurereferente_id'] ) ).
						$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.changementrefparcours", array( 'type' => 'hidden', 'value' => $dossierep['Saisinebilanparcoursep66']['changementrefparcours'] ) ).
						$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.passagecommissionep_id", array( 'type' => 'hidden' ) ).
						$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.etape", array( 'type' => 'hidden', 'value' => 'ep' ) ).
						$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.user_id", array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) );

		$typesorientsprincipales =
						$this->Xhtml->tag(
							'div',
							$this->Xform->input( "Decisionsaisinebilanparcoursep66.{$i}.typeorientprincipale_id", array( 'div' => false, 'label' => false, 'options' => $options['Saisinebilanparcoursep66']['typeorientprincipale_id'], 'empty' => true ) )
						);

		echo $this->Xhtml->tableCells(
			array(
				implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
				implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
				$this->Locale->date( __( 'Locale->date' ), $dossierep['Dossierep']['created'] ),
				implode( ' - ', Hash::filter( (array)array(
					@$dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Typeorient']['lib_type_orient'],
					@$dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Structurereferente']['lib_struc'],
					Hash::filter( (array)array(
						@$dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Referent']['qual'],
						@$dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Referent']['nom'],
						@$dossierep['Saisinebilanparcoursep66']['Bilanparcours66']['Orientstruct']['Referent']['prenom']
					) )
				) ) ),
				$options['Saisinebilanparcoursep66']['choixparcours'][Set::classicExtract( $dossierep, "Saisinebilanparcoursep66.choixparcours" )],
				@$options['Saisinebilanparcoursep66']['changementrefparcours'][Set::classicExtract( $dossierep, "Saisinebilanparcoursep66.changementrefparcours" )],
				@$liste_typesorients[Set::classicExtract( $dossierep, "Saisinebilanparcoursep66.typeorient_id" )],
				@$liste_structuresreferentes[Set::classicExtract( $dossierep, "Saisinebilanparcoursep66.structurereferente_id" )],
				//commentaire bénéficiaire
				$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.commentairebeneficiaire", array( 'label' =>false, 'type' => 'textarea' ) ),

				array(
					$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.decision", array( 'label' => false, 'options' => @$options['Decisionsaisinebilanparcoursep66']['decision'] ) ),
					array( 'id' => "Decisionsaisinebilanparcoursep66{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisionsaisinebilanparcoursep66'][$i]['decision'] ) ? 'error' : '' ) )
				),
				array(
					$typesorientsprincipales,
					( !empty( $this->validationErrors['Decisionsaisinebilanparcoursep66'][$i]['typeorientprincipale_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.typeorient_id", array( 'label' => false, 'options' => $options['Saisinebilanparcoursep66']['typeorient_id'], 'empty' => true ) ),
					( !empty( $this->validationErrors['Decisionsaisinebilanparcoursep66'][$i]['typeorient_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.structurereferente_id", array( 'label' => false, 'options' => $structuresreferentes, 'empty' => true, 'type' => 'select' ) ),
					( !empty( $this->validationErrors['Decisionsaisinebilanparcoursep66'][$i]['structurereferente_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				array(
					$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.referent_id", array( 'label' => false, 'options' => $referents, 'empty' => true, 'type' => 'select' ) ),
					( !empty( $this->validationErrors['Decisionsaisinebilanparcoursep66'][$i]['referent_id'] ) ? array( 'class' => 'error' ) : array() )
				),
				$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.checkcomm", array( 'label' =>false, 'type' => 'checkbox' ) ).
				$this->Form->input( "Decisionsaisinebilanparcoursep66.{$i}.commentaire", array( 'label' =>false, 'type' => 'textarea' ) ).
				$hiddenFields,
				array( $this->Xhtml->link( 'Voir', array( 'controller' => 'dossiers', 'action' => 'view', $dossierep['Personne']['Foyer']['dossier_id'] ), array( 'class' => 'external' ) ), array( 'class' => 'button view' ) )
			),
			array( 'class' => "odd {$multiple}" ),
			array( 'class' => "even {$multiple}" )
		);
	}
	echo '</tbody></table>';
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		var selects = new Array();

		<?php for( $i = 0 ; $i < count( $dossiers[$theme]['liste'] ) ; $i++ ):?>
			dependantSelect( 'Decisionsaisinebilanparcoursep66<?php echo $i?>TypeorientId', 'Decisionsaisinebilanparcoursep66<?php echo $i?>TypeorientprincipaleId' );
			try { $( 'Decisionsaisinebilanparcoursep66<?php echo $i?>TypeorientId' ).onchange(); } catch(id) { }

			dependantSelect( 'Decisionsaisinebilanparcoursep66<?php echo $i?>StructurereferenteId', 'Decisionsaisinebilanparcoursep66<?php echo $i?>TypeorientId' );
			try { $( 'Decisionsaisinebilanparcoursep66<?php echo $i?>StructurereferenteId' ).onchange(); } catch(id) { }

			dependantSelect( 'Decisionsaisinebilanparcoursep66<?php echo $i?>ReferentId', 'Decisionsaisinebilanparcoursep66<?php echo $i?>StructurereferenteId' );
			try { $( 'Decisionsaisinebilanparcoursep66<?php echo $i?>ReferentId' ).onchange(); } catch(id) { }

			$('Decisionsaisinebilanparcoursep66<?php echo $i?>Checkcomm').observe( 'change', function() {
				if ($('Decisionsaisinebilanparcoursep66<?php echo $i?>Checkcomm').checked==true) {
					$('Decisionsaisinebilanparcoursep66<?php echo $i?>Commentaire').show();
				}
				else {
					$('Decisionsaisinebilanparcoursep66<?php echo $i?>Commentaire').hide();
				}
			} );

			if ($('Decisionsaisinebilanparcoursep66<?php echo $i?>Checkcomm').checked==true) {
				$('Decisionsaisinebilanparcoursep66<?php echo $i?>Commentaire').show();
			}
			else {
				$('Decisionsaisinebilanparcoursep66<?php echo $i?>Commentaire').hide();
			}

			// Sauvegarde du champ avec les typesorientsprincipales
			/// FIXME : on peut le faire une seule fois ?
			var selectId = 'Decisionsaisinebilanparcoursep66<?php echo $i?>TypeorientprincipaleId';
			var value = $F( selectId );
			if( selects[selectId] == undefined ) {
				selects[selectId] = new Array();
				selects[selectId]['values'] = new Array();
				selects[selectId]['options'] = new Array();
			}

			$$('#' + selectId + ' option').each( function ( option ) {
				selects[selectId]['values'].push( option.value );
				selects[selectId]['options'].push( option.innerHTML );
			} );
			$( 'Decisionsaisinebilanparcoursep66<?php echo $i;?>Decision' ).observe( 'change', function() {
				changeColspanFormAnnuleReporteEps( 'Decisionsaisinebilanparcoursep66<?php echo $i;?>DecisionColumn', 5, 'Decisionsaisinebilanparcoursep66<?php echo $i;?>Decision', [ 'Decisionsaisinebilanparcoursep66<?php echo $i;?>TypeorientId', 'Decisionsaisinebilanparcoursep66<?php echo $i;?>StructurereferenteId', 'Decisionsaisinebilanparcoursep66<?php echo $i;?>ReferentId', 'Decisionsaisinebilanparcoursep66<?php echo $i;?>TypeorientprincipaleId' ] );
				checkDecision( '<?php echo $i;?>', selects[selectId], value );
			});
			changeColspanFormAnnuleReporteEps( 'Decisionsaisinebilanparcoursep66<?php echo $i;?>DecisionColumn', 5, 'Decisionsaisinebilanparcoursep66<?php echo $i;?>Decision', [ 'Decisionsaisinebilanparcoursep66<?php echo $i;?>TypeorientId', 'Decisionsaisinebilanparcoursep66<?php echo $i;?>StructurereferenteId', 'Decisionsaisinebilanparcoursep66<?php echo $i;?>ReferentId', 'Decisionsaisinebilanparcoursep66<?php echo $i;?>TypeorientprincipaleId' ] );
			checkDecision( '<?php echo $i;?>', selects[selectId], value );
		<?php endfor;?>
	});

	function checkDecision( i, options, defaultvalue ) {
		var selectId = 'Decisionsaisinebilanparcoursep66'+i+'TypeorientprincipaleId';
		$$('#' + selectId + ' option').each( function ( option ) {
			$(option).remove();
		} );

		if ( $F( 'Decisionsaisinebilanparcoursep66'+i+'Decision' ) == 'maintien' ) {
			if ( $F( 'Bilanparcours66'+i+'ParenttypeorientId' ) == '<?php echo $typeorientemploiId ?>' ) {
				for( var i = 0 ; i < options['values'].length ; i++ ) {
					if( options['values'][i] == '' || options['values'][i] == '<?php echo $typeorientemploiId ?>' ) {
						$(selectId).insert( new Element( 'option', { 'value': options['values'][i] } ).update( options['options'][i] ) );
					}
				}
			}
			else {
				for( var i = 0 ; i < options['values'].length ; i++ ) {
					if( options['values'][i] == '' || options['values'][i] != '<?php echo $typeorientemploiId ?>' ) {
						$(selectId).insert( new Element( 'option', { 'value': options['values'][i] } ).update( options['options'][i] ) );
					}
				}
			}
		}
		else if ( $F( 'Decisionsaisinebilanparcoursep66'+i+'Decision' ) == 'reorientation' ) {
			if ( $F( 'Bilanparcours66'+i+'ParenttypeorientId' ) != '<?php echo $typeorientemploiId ?>' ) {
				for( var i = 0 ; i < options['values'].length ; i++ ) {
					if( options['values'][i] == '' || options['values'][i] == '<?php echo $typeorientemploiId ?>' ) {
						$(selectId).insert( new Element( 'option', { 'value': options['values'][i] } ).update( options['options'][i] ) );
					}
				}
			}
			else {
				for( var i = 0 ; i < options['values'].length ; i++ ) {
					if( options['values'][i] == '' || options['values'][i] != '<?php echo $typeorientemploiId ?>' ) {
						$(selectId).insert( new Element( 'option', { 'value': options['values'][i] } ).update( options['options'][i] ) );
					}
				}
			}
		}

		var opt = $$('#' + selectId + ' option');
		$( opt ).each( function ( option ) {
			if( $(option).value == defaultvalue ) {
				$(option).selected = 'selected';
			}
		} );
		$( selectId ).simulate( 'change' );
	}
</script>