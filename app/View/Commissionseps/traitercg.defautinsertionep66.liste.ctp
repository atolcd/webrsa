<?php
echo '<table id="Decisiondefautinsertionep66" class="tooltips">
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup />
		<colgroup span="4" style="border-right: 5px solid #235F7D;border-left: 5px solid #235F7D;" />
		<colgroup />
		<thead>
			<tr>
				<th rowspan="2">Nom du demandeur</th>
				<th rowspan="2">Adresse</th>
				<th rowspan="2">Date de naissance</th>
				<th rowspan="2">Date d\'orientation</th>
				<th rowspan="2">Orientation actuelle</th>
				<th rowspan="2">Structure</th>
				<th rowspan="2">Motif saisine</th>
				<th rowspan="2">Avis EPL</th>
				<th colspan="4">Décision CD</th>
				<th rowspan="2">Observations</th>
				<th rowspan="2">Action</th>
				<th class="innerTableHeader noprint">Avis EP</th>
			</tr>
			<tr>
				<th>Décision</th>
				<th>Type d\'orientation</th>
				<th>Structure référente</th>
				<th>Référent</th>
			</tr>
		</thead>
	<tbody>';

	$hiddenDossiers = array( 'Decisiondefautinsertionep66' => array() );

	foreach( $dossiers[$theme]['liste'] as $i => $dossierep ) {
		if( isset( $this->request->data['Decisiondefautinsertionep66'][$i]['field_type'] ) && $this->request->data['Decisiondefautinsertionep66'][$i]['field_type'] == 'hidden' ) {
			$this->request->data['Decisiondefautinsertionep66'][$i]['etape'] = 'cg';
			$this->request->data['Decisiondefautinsertionep66'][$i]['user_id'] = $this->Session->read( 'Auth.User.id' );

			$indexDecision = count( $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'] ) - 1;
			$idDecisionDefaut = null;
			
			/**
			 * TODO : à retirer en 3.2.8
			 * Garder pour vérification conflit 3.2.7
			 */
			if (isset ($dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66']) && $indexDecision > 0) {
			//if (isset ($dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'])) {
				$idDecisionDefaut = Set::classicExtract( $dossierep, "Passagecommissionep.0.Decisiondefautinsertionep66.{$indexDecision}.id" );
			}
			$this->request->data['Decisiondefautinsertionep66'][$i]['id'] = $idDecisionDefaut;

			$hiddenDossiers['Decisiondefautinsertionep66'][$i] = $this->request->data['Decisiondefautinsertionep66'][$i];
		}
		else {

			$examenaudition = Set::enum( @$dossierep['Defautinsertionep66']['Bilanparcours66']['examenaudition'], $options['Defautinsertionep66']['type'] );
			if( !empty( $dossierep['Defautinsertionep66']['Bilanparcours66']['examenauditionpe'] ) ){
				$examenaudition = Set::enum( @$dossierep['Defautinsertionep66']['Bilanparcours66']['examenauditionpe'], $options['Bilanparcours66']['examenauditionpe'] );
			}

			$multiple = ( count( $dossiersAllocataires[$dossierep['Personne']['id']] ) > 1 ? 'multipleDossiers' : null );

			$decisionep = $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][0];
			$avisEp = implode( ' - ', Hash::filter( (array)array( Set::enum( @$decisionep['decisionsup'], $options['Decisiondefautinsertionep66']['decisionsup'] ), Set::enum( @$decisionep['decision'], $options['Decisiondefautinsertionep66']['decision'] ), @$listeTypesorients[@$decisionep['typeorient_id']], @$listeStructuresreferentes[@$decisionep['structurereferente_id']], @$listeReferents[@$decisionep['referent_id']] ) ) );
			$avisEpCommentaire = Set::classicExtract( $decisionep, "commentaire" );

			$decisionep = $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'][count($dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'])-1];

			$innerTable = "<table id=\"innerTableDecisiondefautinsertionep66{$i}\" class=\"innerTable\">
				<tbody>
					<tr>
						<th>Observations de l'EP</th>
						<td>".$avisEpCommentaire."</td>
					</tr>
				</tbody>
			</table>";

			$hiddenFields = $this->Form->input( "Decisiondefautinsertionep66.{$i}.user_id", array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) );

			$indexDecision = count( $dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'] ) - 1;
			$idDecisionDefaut = null;

			/**
			 * TODO : à retirer en 3.2.8
			 * Garder pour vérification conflit 3.2.7
			 */
			if (isset ($dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66']) && $indexDecision > 0) {
			//if (isset ($dossierep['Passagecommissionep'][0]['Decisiondefautinsertionep66'])) {
				$idDecisionDefaut = Set::classicExtract( $dossierep, "Passagecommissionep.0.Decisiondefautinsertionep66.{$indexDecision}.id" );
			}
			$idDefautinsertionep66 = null;
			if (isset ($dossierep['Defautinsertionep66']) && $indexDecision > 0) {
			//if (isset ($dossierep['Defautinsertionep66'])) {
				$idDefautinsertionep66 = Set::classicExtract( $dossierep, "Defautinsertionep66.id" );
			}

			echo $this->Xhtml->tableCells(
				array(
					implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
					implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['codepos'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomcom'] ) ),
					$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
					$this->Locale->date( __( 'Locale->date' ), @$dossierep['Defautinsertionep66']['Orientstruct']['date_valid'] ),
					@$dossierep['Defautinsertionep66']['Orientstruct']['Typeorient']['lib_type_orient'],
					@$dossierep['Defautinsertionep66']['Orientstruct']['Structurereferente']['lib_struc'],

					$examenaudition,

					$this->Form->input( "Defautinsertionep66.{$i}.id", array( 'type' => 'hidden', 'value' => $idDefautinsertionep66 ) ).
					$this->Form->input( "Defautinsertionep66.{$i}.dossierep_id", array( 'type' => 'hidden', 'value' => $dossierep['Dossierep']['id'] ) ).
					$this->Form->input( "Decisiondefautinsertionep66.{$i}.id", array( 'type' => 'hidden', 'value' => $idDecisionDefaut ) ).
					$this->Form->input( "Decisiondefautinsertionep66.{$i}.etape", array( 'type' => 'hidden', 'value' => 'cg' ) ).
					$this->Form->input( "Decisiondefautinsertionep66.{$i}.passagecommissionep_id", array( 'type' => 'hidden' ) ).

					$avisEp,

					array(
						$this->Form->input( "Decisiondefautinsertionep66.{$i}.decision", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $options['Decisiondefautinsertionep66']['decision'], 'value' => $decisionep['decision'] ) ).
						$this->Form->input( "Decisiondefautinsertionep66.{$i}.decisionsup", array( 'type' => 'select', 'label' => false, 'empty' => true, 'options' => $options['Decisiondefautinsertionep66']['decisionsup'], 'value' => $decisionep['decisionsup'] ) ),
						array( 'id' => "Decisiondefautinsertionep66{$i}DecisionColumn", 'class' => ( !empty( $this->validationErrors['Decisiondefautinsertionep66'][$i]['decision'] ) || !empty( $this->validationErrors['Decisiondefautinsertionep66'][$i]['decisionsup'] ) ? 'error' : '' ) )
					),
					array(
						$this->Form->input( "Decisiondefautinsertionep66.{$i}.typeorient_id", array( 'label' => false, 'options' => $typesorients, 'empty' => true, 'value' => $decisionep['typeorient_id'] ) ),
						( !empty( $this->validationErrors['Decisiondefautinsertionep66'][$i]['typeorient_id'] ) ? array( 'class' => 'error' ) : array() )
					),
					array(
						$this->Form->input( "Decisiondefautinsertionep66.{$i}.structurereferente_id", array( 'label' => false, 'options' => $structuresreferentes, 'empty' => true, 'type' => 'select', 'value' => $decisionep['typeorient_id'].'_'.$decisionep['structurereferente_id'] ) ),
						( !empty( $this->validationErrors['Decisiondefautinsertionep66'][$i]['structurereferente_id'] ) ? array( 'class' => 'error' ) : array() )
					),
					array(
						$this->Form->input( "Decisiondefautinsertionep66.{$i}.referent_id", array( 'label' => false, 'options' => $referents, 'empty' => true, 'type' => 'select', 'value' => $decisionep['structurereferente_id'].'_'.$decisionep['referent_id'] ) ),
						( !empty( $this->validationErrors['Decisiondefautinsertionep66'][$i]['referent_id'] ) ? array( 'class' => 'error' ) : array() )
					),
	// 				array( $this->Form->input( "Decisiondefautinsertionep66.{$i}.raisonnonpassage", array( 'label' => false, 'type' => 'textarea' ) ), array( 'colspan' => '3' ) ),
					$this->Form->input( "Decisiondefautinsertionep66.{$i}.commentaire", array( 'label' =>false, 'type' => 'textarea', 'value' => $decisionep['commentaire'] ) ).
					$hiddenFields,
					array( $this->Xhtml->link( 'Voir', array( 'controller' => 'dossiers', 'action' => 'view', $dossierep['Personne']['Foyer']['dossier_id'] ), array( 'class' => 'external' ) ), array( 'class' => 'button view' ) )
				),
				array( 'class' => "odd {$multiple}" ),
				array( 'class' => "even {$multiple}" )
			);
		}
	}
	echo '</tbody></table>';

	if( !empty( $hiddenDossiers ) ) {
		echo '<div>';
		foreach( $hiddenDossiers['Decisiondefautinsertionep66'] as $i => $hiddenDossier ) {
			foreach( $hiddenDossier as $field => $value ) {
				echo $this->Xform->input( "Decisiondefautinsertionep66.{$i}.{$field}", array( 'type' => 'hidden' ) );
			}
		}
		echo '</div>';
	}
?>

<script type="text/javascript">
	/**
	 * @var {object} typeDecision - {"valeur decision": ["orientation actuelle", "orientation future"], ...}
	 */
	var typeDecision = <?php echo json_encode(Configure::read('Commissionseps.defautinsertionep66.decision.type'));?>,
		isEmploi = [<?php echo implode(', ', (array)Configure::read('Commissionseps.defautinsertionep66.isemploi'));?>],
		decision = [],
		decisionsup = [],
		typeorient = [],
		typeorient_id = [],
		i
	;
	
	/**
	 * Permet de savoir si un typeorient_id est de la catégorie emploi
	 * 
	 * @param {integer} typeorient_id
	 * @returns {Boolean}
	 */
	function idIsEmploi(typeorient_id) {
		for (var i=0; i<isEmploi.length; i++) {
			if (typeorient_id === isEmploi[i]) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Retire les options qui ne correspondent pas à l'orientation actuelle de l'allocataire
	 * 
	 * @param {DOM} decision
	 * @param {integer} typeorient_id
	 */
	function setAvailableDecision(decision, typeorient_id) {
		var options = decision.select('option'),
			isEmploi = idIsEmploi(typeorient_id);
	
		if (typeorient_id === null) {
			return;
		}
		
		for (var i=0; i<options.length; i++) {
			if (typeDecision[options[i].value] === undefined) {
				continue;
			}
			
			if ((isEmploi && typeDecision[options[i].value][0] === 'social')
					|| (!isEmploi && typeDecision[options[i].value][0] === 'emploi')) {
				options[i].hide();
			}
		}
	}
	
	/**
	 * Selectionne le premier element visible d'un select
	 * 
	 * @param {DOM} select
	 */
	function selectFirstNonHiddenOption(select) {
		var i, options = select.select('option'), first = true;
		
		for (i=0; i<options.length; i++) {
			if (options[i].visible() && options[i].value !== '') {
				options[i].selected = first;
				if (first) {
					select.setValue(options[i].value);
					first = false;
				}
			} else {
				options[i].selected = false;
			}
		}
	}
	
	/**
	 * Permet de masquer les options ne correspondent pas à une décision
	 * 
	 * @param {DOM} decisionSelect
	 * @param {DOM} typeorientSelect
	 * @global {object} typeDecision
	 */
	function setAvailableTypeOrient(decisionSelect, typeorientSelect) {
		var isEmploi, options, change = false;
		
		if (typeDecision[decisionSelect.getValue()] !== undefined) {
			isEmploi = typeDecision[decisionSelect.getValue()][1] === 'emploi';
			options = typeorientSelect.select('option');
		} else {
			return ;
		}
		
		for (var i=0; i<options.length; i++) {
			if ((isEmploi && !idIsEmploi(parseInt(options[i].value, 10)))
					|| (!isEmploi && idIsEmploi(parseInt(options[i].value, 10)))) {
				options[i].hide();
				if (typeorientSelect.getValue() === options[i].value) {
					change = true;
				}
			} else {
				options[i].show();
			}
		}
		
		if (change) {
			selectFirstNonHiddenOption(typeorientSelect);
			typeorientSelect.simulate('change');
		}
		
		removeEmptyOptgroup(typeorientSelect);
	}
	
	/**
	 * Fonction utilisé à l'appel de page et dans le cas d'un evenement change sur decision
	 * 
	 * @param {DOM} decision
	 * @param {DOM} decisionsup
	 * @param {DOM} typeorient
	 * @param {integer} typeorient_id
	 */
	function observableLogic(decision, decisionsup, typeorient, typeorient_id) {
		// Affichage de la décision suplémentaire
		if (inArray(decision.getValue(), ['reorientationprofverssoc', 'reorientationsocversprof', 'maintienorientsoc'])) {
			decisionsup.show();
		} else {
			decisionsup.hide();
		}
		
		// Colspan en cas de report ou d'annulation
		if (inArray(decision.getValue(), ['reporte', 'annule'])) {
			$(decision.id+'Column').setAttribute('colspan', 4);
			$(decision.id+'Column').up('tr').select('div.disabled').each(function(div){
				div.up('td').hide();
			});
		} else {
			$(decision.id+'Column').removeAttribute('colspan');
			$(decision.id+'Column').up('tr').select('div.disabled').each(function(div){
				div.up('td').show();
			});
		}
		
		setAvailableDecision(decision, typeorient_id);
		setAvailableTypeOrient(decision, typeorient);
	}
	
	document.observe("dom:loaded", function() {
		<?php foreach( array_keys($dossiers[$theme]['liste']) as $i):
			if( isset( $this->request->data['Decisiondefautinsertionep66'][$i]['field_type'] ) && $this->request->data['Decisiondefautinsertionep66'][$i]['field_type'] == 'hidden' ) {
				continue;
			}
			$typeorient_id = Hash::get($dossiers, "{$theme}.liste.{$i}.Defautinsertionep66.Orientstruct.Typeorient.id");
			?>
			i = <?php echo $i;?>;
			decision[i] = $('Decisiondefautinsertionep66<?php echo $i;?>Decision');
			decisionsup[i] = $('Decisiondefautinsertionep66<?php echo $i;?>Decisionsup');
			typeorient[i] = $('Decisiondefautinsertionep66<?php echo $i;?>TypeorientId');
			typeorient_id[i] = <?php echo $typeorient_id ? $typeorient_id : 'null'?>;
			
			dependantSelect('Decisiondefautinsertionep66<?php echo $i?>StructurereferenteId', 'Decisiondefautinsertionep66<?php echo $i?>TypeorientId');
			dependantSelect('Decisiondefautinsertionep66<?php echo $i?>ReferentId', 'Decisiondefautinsertionep66<?php echo $i?>StructurereferenteId');

			observeDisableElementsOnValues(
				[
					'Decisiondefautinsertionep66<?php echo $i;?>TypeorientId',
					'Decisiondefautinsertionep66<?php echo $i;?>StructurereferenteId',
					'Decisiondefautinsertionep66<?php echo $i;?>ReferentId'
				],
				[
					{element: decision[i], value: 'reorientationprofverssoc', operator: '!='},
					{element: decision[i], value: 'reorientationsocversprof', operator: '!='},
					{element: decision[i], value: 'maintienorientsoc', operator: '!='}
				],
				false,
				false
			);
	
			decision[i].observe('change', function(){ 
				observableLogic(decision[<?php echo $i;?>], decisionsup[<?php echo $i;?>], typeorient[<?php echo $i;?>], typeorient_id[<?php echo $i;?>]); 
			});
			observableLogic(decision[i], decisionsup[i], typeorient[i], typeorient_id[i]);
		<?php endforeach;?>
	});
</script>