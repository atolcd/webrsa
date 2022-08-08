<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<h1><?php echo $this->pageTitle = __d( 'relancenonrespectsanctionep93', 'Relancesnonrespectssanctionseps93::cohorte' );?></h1>

<?php if( is_array( $this->request->data ) ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->link(
				$this->Xhtml->image(
					'icons/application_form_magnify.png',
					array( 'alt' => '' )
				).' Formulaire',
				'#',
				array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "var form = $$( 'form' ); form = form[0]; $( form ).toggle(); return false;" )
			).'</li>';
		?>
	</ul>
<?php endif;?>
<?php
	$paramDate = array(
		'domain' => 'orientsstructs',
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	);
	// Formulaire
	echo $this->Form->create( null, array( 'id' => 'Search', 'novalidate' => true ) );

	echo $this->Xhtml->tag( 'fieldset', $this->Xhtml->tag( 'legend', 'Recherche par bénéficiaire' ).
		$this->Default2->subform(
			array(
				'Search.Personne.nom' => array(  'label' => __d( 'personne', 'Personne.nom' ), 'required' => false ),
				'Search.Personne.nomnai' => array(  'label' => __d( 'personne', 'Personne.nomnai' ) ),
				'Search.Personne.prenom' => array(  'label' => __d( 'personne', 'Personne.prenom' ), 'required' => false ),
				'Search.Personne.nir' => array(  'label' => __d( 'personne', 'Personne.nir' ) ),
				'Search.Adresse.numcom' => array( 'required' => false, 'label' => __d( 'adresse', 'Adresse.numcom' ) ),
				'Search.Serviceinstructeur.id' => array( 'domain' => 'relancenonrespectsanctionep93', 'label' => __d( 'relancenonrespectsanctionep93', 'Serviceinstructeur.id' ) ),
				'Search.Personne.trancheage' => array('label' => __d( 'personne', 'Personne.trancheage'), 'empty' => true, 'options' => Configure::read('Search.Options.enums.Personne.trancheage')),
				'Search.Personne.trancheagesup' => array( 'label' => __d( 'personne', 'Personne.trancheagesup' ),'empty' => true, 'options' => Configure::read('Search.Options.enums.Personne.trancheagesup')),
				'Search.Personne.trancheageprec' => array('label' => __d( 'personne', 'Personne.trancheageprec' ), 'empty' => true, 'options' => Configure::read('Search.Options.enums.Personne.trancheageprec')),
			),
			array(
				'options' => $options
			)
		)
	);

	echo $this->Xhtml->tag( 'fieldset', $this->Xhtml->tag( 'legend', __d( 'dossier', 'Dossier.matricule.fieldset' ) ).
		$this->Default2->subform(
			array(
				'Search.Dossier.matricule' => array(  'label' => __d( 'dossier', 'Dossier.matricule' ) ),
				'Search.Dossiercaf.nomtitulaire' => array(  'label' => __d( 'dossiercaf', 'Dossiercaf.nomtitulaire' ) ),
				'Search.Dossiercaf.prenomtitulaire' => array(  'label' => __d( 'dossiercaf', 'Dossiercaf.prenomtitulaire' ) ),
			)
		)
	);

	$comparators = array( '<' => '<' ,'>' => '>','<=' => '<=', '>=' => '>=' );

	echo '<fieldset><legend>Présence contrat</legend><div class="input">';
	echo '<fieldset><legend><input name="data[Search][Relance][contrat]" id="SearchRelanceContrat0" value="0" '.( ( @$this->request->data['Search']['Relance']['contrat'] == 0 ) ? 'checked="checked"' : '' ).' type="radio" /><label for="RelanceContrat0">Personne orientée sans contrat</label></legend>'.
		'<div>'.
			$this->Form->input( 'Search.Relance.compare0', array( 'label' => 'Opérateurs', 'type' => 'select', 'options' => $comparators, 'empty' => true ) ).
			$this->Form->input( 'Search.Relance.nbjours0', array( 'label' => 'Nombre de jours depuis l\'orientation<span id="nbjoursmin0"></span>', 'type' => 'text' ) ).
		'</div>'.
		'</fieldset>';
	echo '<fieldset><legend><input name="data[Search][Relance][contrat]" id="SearchRelanceContrat1" value="1" '.( ( @$this->request->data['Search']['Relance']['contrat'] == 1 ) ? 'checked="checked"' : '' ).' type="radio" /><label for="RelanceContrat1">Personne orientée avec contrat</label></legend>'.
		'<div>'.
			$this->Form->input( 'Search.Relance.compare1', array( 'label' => 'Opérateurs', 'type' => 'select', 'options' => $comparators, 'empty' => true ) ).
			$this->Form->input( 'Search.Relance.nbjours1', array( 'label' => 'Nombre de jours depuis la fin du dernier contrat<span id="nbjoursmin1"></span>', 'type' => 'text' ) ).
		'</div>'.
		'</fieldset>';
	echo '</div></fieldset>';

	echo $this->Form->input( 'Search.Relance.numrelance', array( 'legend' => 'Type de relance à réaliser', 'type' => 'radio', 'options' => $options['Relancenonrespectsanctionep93']['numrelance'], 'value' => ( isset( $this->request->data['Search']['Relance']['numrelance'] ) ? @$this->request->data['Search']['Relance']['numrelance'] : 1 ) ) );

	echo '<fieldset><legend>' . __d('orientsstructs', 'Orientstruct.search' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Orientstruct.derniere' => array( 'type' => 'checkbox' )
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => 'orientsstructs' )
		)
		. $this->Default3->subform(
			array(
				'Search.Orientstruct.dernierevalid' => array( 'type' => 'checkbox' )
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => 'orientsstructs' )
		)
		. $this->SearchForm->dateRange( 'Search.Orientstruct.date_valid', $paramDate )
	;

	echo $this->Default3->subform(
		array(
			'Search.Orientstruct.origine' => array('empty' => true),
		),
		array( 'options' => array( 'Search' => $options ), 'domain' => 'orientsstructs' )
	);

	echo $this->Default3->subform(
			array(
				'Search.Orientstruct.typeorient_id' => array('empty' => true, 'required' => false),
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => 'orientsstructs' )
		);

	echo $this->Allocataires->communautesrSelect( 'Orientstruct', array( 'options' => array( 'Search' => $options ), 'label' => __d('orientsstructs', 'Search.Orientstruct.communautesr_id' ) ));

	echo $this->Default3->subform(
			array(
				'Search.Orientstruct.structurereferente_id' => array('empty' => true, 'required' => false),
				'Search.Orientstruct.statut_orient' => array('empty' => true, 'required' => false)
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => 'orientsstructs' )
		)
		. '</fieldset>'
	;


	echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
	?>
		<fieldset>
		<legend><?php echo __d('tag', 'Search.Tag.search_title') ?></legend>

		<?php
			echo $this->Default3->subform(
				array(
					'Search.ByTag.tag_choice' => array('type' => 'checkbox', 'label' => __d('tag', 'Search.Tag.filter_title')),
				),
				array( 'options' => array('domain' => 'tag'))
			);

	?>
		<div id="SearchByTagFieldset">

			<?php echo $this->Allocataires->SearchForm->dateRange( 'Search.Tag.created', array('domain' => 'dossiers') ); ?>

			 <?php
			echo $this->Default3->subform(
				array(
					'Search.Tag.exclusionValeur' => array('type' => 'checkbox', 'label' => __d('tag', 'Search.Tag.exclusionValeur')),
				),
				array( 'options' => array('domain' => 'tag' ))
			);

	?>
			<fieldset>
			<legend><?php echo __d('tag', 'Search.Tag.valeurtag_id') ?></legend>
				<?php
				$i = 0;
				foreach($options['Tag']['valeurtag_id'] as $key => $array){
					echo '<fieldset><legend>'.$key.'</legend>';
					foreach ($array as $value => $label){
						echo $this->Default3->subform(
							array(
								'Search.Tag.valeurtag_id.'.$i => array('type' => 'checkbox', 'value' => $value, 'label' => $label),
							),
							array( 'options' => array('domain' => 'tag' ))
						);
						$i++;
					}
					echo '</fieldset>';
				}
			?>
			</fieldset>

			<?php
			echo $this->Default3->subform(
				array(
					'Search.Tag.exclusionEtat' => array('type' => 'checkbox', 'label' => __d('tag', 'Search.Tag.exclusionEtat')),
				),
				array( 'options' => array('domain' => 'tag' ))
			);

			?>
			<fieldset>
			<legend><?php echo __d('tag', 'Search.Tag.etat') ?></legend>
				<?php
				$i = 0;
				foreach($options['Tag']['etat'] as $value => $label){
					echo $this->Default3->subform(
						array(
							'Search.Tag.etat.'.$i => array('type' => 'checkbox', 'value' => $value, 'label' => $label),
						),
						array( 'options' => array('domain' => 'tag' ))
					);
					$i++;
				}
			?>
			</fieldset>
		</div>
	</fieldset>
	<script type="text/javascript">
	document.observe( 'dom:loaded', function() { try {
		observeDisableFieldsetOnCheckbox( 'SearchByTagTagChoice', 'SearchByTagFieldset', false, true );
	} catch( e ) {
		console.error( e );
	} } );
	</script>
<?php
	echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
	echo $this->Search->observeDisableFormOnSubmit( 'Search' );

	echo $this->Html->tag( 'div', $this->Form->button( __( 'Search' ) ), array( 'class' => 'submit' ) );
	echo $this->Form->end();

	// Résultats
	if( isset( $results ) ) {
		if( empty( $results ) ) {
			echo $this->Xhtml->tag( 'p', 'Aucun résultat ne correspond à ces critères.', array( 'class' => 'notice' ) );
		}
		else {
			echo $this->Html->tag( 'h2', 'Résultats de la recherche' );
			$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

			if( $this->request->data['Search']['Relance']['contrat'] == 0 ) {
				$pagination = $this->Xpaginator->paginationBlock( 'Orientstruct', $this->passedArgs );
			}
			else {
				$pagination = $this->Xpaginator->paginationBlock( 'Contratinsertion', $this->passedArgs );
			}

			echo $pagination;
			echo $this->Form->create( null, array( 'id' => 'Relancenonrespectsanctionep93Form', 'novalidate' => true ) );

			foreach( Hash::flatten( $this->request->data ) as $key => $data ) {
				if( !preg_match( '/^Relancenonrespectsanctionep93\./', $key ) && !( trim( $data ) == '' ) ) {
				echo $this->Form->input( $key, array( 'type' => 'hidden', 'value' => $data ) );
			}
		}

		echo '<table class="tooltips" style="width: 100%;">
			<thead>
				<tr>
					<th>'.$this->Xpaginator->sort( __d( 'dossier', 'Dossier.matricule' ), 'Dossier.matricule' ).'</th>
					<th>'.$this->Xpaginator->sort( 'Nom / Prénom Allocataire', 'Personne.nom' ).'</th>
					<th>'.$this->Xpaginator->sort( 'NIR', 'Personne.nir' ).'</th>
					<th>'.$this->Xpaginator->sort( 'Nom de commune', 'Adresse.nomcom' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'foyer', 'Foyer.enerreur' ), 'Foyer.enerreur' ).'</th>
					'.( ( $this->request->data['Search']['Relance']['contrat'] == 0 ) ? '<th>'.$this->Xpaginator->sort( 'Date d\'orientation', 'Orientstruct.date_valid' ).'</th>' : '' ).'
					'.( ( $this->request->data['Search']['Relance']['contrat'] == 0 ) ? '<th>'.$this->Xpaginator->sort( 'Date de notification d\'orientation', 'Orientstruct.date_impression' ).'</th>' : '' ).'
					'.( ( $this->request->data['Search']['Relance']['contrat'] == 0 ) ? '<th>'.$this->Xpaginator->sort( 'Nombre de jours depuis la notification d\'orientation', 'Orientstruct.date_impression' ).'</th>' : '' ).'
					'.( ( $this->request->data['Search']['Relance']['contrat'] == 1 ) ? '<th>'.$this->Xpaginator->sort( 'Date de fin du contrat', 'Contratinsertion.df_ci' ).'</th>' : '' ).'
					'.( ( $this->request->data['Search']['Relance']['contrat'] == 1 ) ? '<th>'.$this->Xpaginator->sort( 'Nombre de jours depuis la fin du contrat', 'Contratinsertion.df_ci' ).'</th>' : '' ).'
					'.( ( $this->request->data['Search']['Relance']['numrelance'] == 2 ) ? '<th>'.$this->Xpaginator->sort( 'Date de première relance', 'Relancenonrespectsanctionep93.daterelance' ).'</th>' : '' ).'
					'.( ( $this->request->data['Search']['Relance']['numrelance'] == 3 ) ? '<th>'.$this->Xpaginator->sort( 'Date de seconde relance', 'Relancenonrespectsanctionep93.daterelance' ).'</th>' : '' ).'
					'.( ( $this->request->data['Search']['Relance']['contrat'] == 0 ) ? '<th>'.$this->Xpaginator->sort( 'Date de relance minimale', 'Orientstruct.date_impression' ).'</th>' : '' ).'
					'.( ( $this->request->data['Search']['Relance']['contrat'] == 1 ) ? '<th>'.$this->Xpaginator->sort( 'Date de relance minimale', 'Contratinsertion.df_ci' ).'</th>' : '' ).'
					<th style="width: 19em;">'.__d( 'relancenonrespectsanctionep93', 'Relancenonrespectsanctionep93.daterelance' ).'</th>
					<th style="width: 8em;">'.__d( 'relancenonrespectsanctionep93', 'Relancenonrespectsanctionep93.arelancer' ).'</th>
					<th class="innerTableHeader">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>';
			foreach( $results as $index => $result ) {
				$innerTable = '<table id="innerTable'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>Date naissance</th>
							<td>'.h( date_short( @$result['Personne']['dtnai'] ) ).'</td>
						</tr>
						<tr>
							<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
							<td>'.Hash::get( $result, 'Structurereferenteparcours.lib_struc' ).'</td>
						</tr>
						<tr>
							<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
							<td>'.Hash::get( $result, 'Referentparcours.nom_complet' ).'</td>
						</tr>
					</tbody>
				</table>';

				$row = array(
					h( @$result['Dossier']['matricule'] ),
					h( @$result['Personne']['nom'].' '.@$result['Personne']['prenom'] ),
					h( @$result['Personne']['nir'] ),
					h( @$result['Adresse']['nomcom'] ),
					array( h( @$result['Foyer']['enerreur'] ), array( 'class' => 'foyer_enerreur '.( empty( $result['Foyer']['enerreur'] ) ? 'empty' : null ) ) ),
				);

				if( $this->request->data['Search']['Relance']['contrat'] == 0 ) {
					$row[] = h( date_short( @$result['Orientstruct']['date_valid'] ) );
					$row[] = h( date_short( @$result['Orientstruct']['date_impression'] ) );
					$row[] = h( @$result['Orientstruct']['nbjours'] );
				}
				else {
					$row[] = date_short( @$result['Contratinsertion']['df_ci'] );
					$row[] = h( @$result['Contratinsertion']['nbjours'] );
				}

				if( $this->request->data['Search']['Relance']['numrelance'] >  1 && $this->request->data['Search']['Relance']['numrelance'] <= 3 ) {
					$row[] = date_short( @$result['Relancenonrespectsanctionep93']['daterelance'] );
				}

				$row[] = date_short( @$result['Nonrespectsanctionep93']['datemin'] );

				$row = Set::merge(
					$row,
					array(
						( ( @$this->request->data['Search']['Relance']['numrelance'] > 1 ) ? $this->Form->input( "Relancenonrespectsanctionep93.{$index}.nonrespectsanctionep93_id", array( 'type' => 'hidden', 'value' => @$result['Nonrespectsanctionep93']['id'] ) ) : '' ).
						$this->Form->input( "Relancenonrespectsanctionep93.{$index}.dossier_id", array( 'type' => 'hidden', 'value' => @$result['Dossier']['id'] ) ).
						$this->Form->input( "Relancenonrespectsanctionep93.{$index}.numrelance", array( 'type' => 'hidden', 'value' => @$this->request->data['Search']['Relance']['numrelance'] ) ).
						$this->Form->input( "Relancenonrespectsanctionep93.{$index}.orientstruct_id", array( 'type' => 'hidden', 'value' => @$result['Orientstruct']['id'] ) ).
						$this->Form->input( "Relancenonrespectsanctionep93.{$index}.contratinsertion_id", array( 'type' => 'hidden', 'value' => @$result['Contratinsertion']['id'] ) ).
						$this->Form->input( "Relancenonrespectsanctionep93.{$index}.user_id", array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) ).
						$this->Form->input( "Relancenonrespectsanctionep93.{$index}.daterelance", array( 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y', strtotime( $result['Nonrespectsanctionep93']['datemin'] ) ), 'label' => false ) ),
						$this->Form->input( "Relancenonrespectsanctionep93.{$index}.arelancer", array( 'type' => 'radio', 'options' => array( 'R' => 'Relancer', 'E' => 'En attente' ), 'legend' => false, 'div' => false, 'separator' => '<br />', 'value' => ( isset( $this->request->data['Relancenonrespectsanctionep93'][$index]['arelancer'] ) ? @$this->request->data['Relancenonrespectsanctionep93'][$index]['arelancer'] : 'E' ) ) ),
						array( $innerTable, array( 'class' => 'innerTableCell' ) )
					)
				);

				echo $this->Xhtml->tableCells(
					$row,
					array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
					array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
				);
			}
			echo '</tbody></table>';

			echo '<div class="selectall">';
			echo $this->Form->button( 'Tout Relancer', array( 'type' => 'button', 'onclick' => "return toutChoisir( $( 'Relancenonrespectsanctionep93Form' ).getInputs( 'radio' ), 'R', true );" ) );
			echo $this->Form->button( 'Tout mettre En attente', array( 'type' => 'button', 'onclick' => "return toutChoisir( $( 'Relancenonrespectsanctionep93Form' ).getInputs( 'radio' ), 'E', true );" ) );
			echo '</div>';

			echo $pagination;
			echo $this->Form->submit( __( 'Save' ) );
			echo $this->Form->end();
		}
	}
?>

<?php if( isset( $results ) ):?>
	<script type="text/javascript">
		<?php foreach( $results as $index => $result ):?>
		observeDisableFieldsOnRadioValue(
			'Relancenonrespectsanctionep93Form',
			'data[Relancenonrespectsanctionep93][<?php echo $index;?>][arelancer]',
			[
				'Relancenonrespectsanctionep93<?php echo $index;?>DaterelanceDay',
				'Relancenonrespectsanctionep93<?php echo $index;?>DaterelanceMonth',
				'Relancenonrespectsanctionep93<?php echo $index;?>DaterelanceYear'
			],
			'E',
			false
		);
		<?php endforeach;?>

		function checkRadiosBySelector( selector ) {
			var radios = $$( selector );
			$( radios ).each( function( radio ) {
				$( radio ).click();
			} );
		}
	</script>
<?php endif;?>

<script type="text/javascript">
	// Ne désactive que la valeur
	function disableFieldsOnRadioValue2( form, radioName, fieldsIds, value, condition ) {
		var v = $( form ).getInputs( 'radio', radioName );
		var currentValue = undefined;
		$( v ).each( function( radio ) {
			if( radio.checked ) {
				currentValue = radio.value;
			}
		} );

		var disabled = !( ( currentValue == value ) == condition );

		fieldsIds.each( function ( fieldId ) {
			var field = $( fieldId );
			if( !disabled ) {
				field.enable();
			}
			else {
				field.disable();
			}
		} );
	}

	function observeDisableFieldsOnRadioValue2( form, radioName, fieldsIds, value, condition ) {
		disableFieldsOnRadioValue2( form, radioName, fieldsIds, value, condition );

		var v = $( form ).getInputs( 'radio', radioName );
		var currentValue = undefined;
		$( v ).each( function( radio ) {
			$( radio ).observe( 'change', function( event ) {
				disableFieldsOnRadioValue2( form, radioName, fieldsIds, value, condition );
			} );
		} );
	}

	var form = $$( 'form' );
	form = form[0];

	<?php if( isset( $results ) ):?>$( form ).hide();<?php endif;?>

	observeDisableFieldsOnRadioValue(
		form,
		'data[Search][Relance][contrat]',
		[ 'SearchRelanceCompare0', 'SearchRelanceNbjours0' ],
		'1',
		false
	);

	observeDisableFieldsOnRadioValue(
		form,
		'data[Search][Relance][contrat]',
		[ 'SearchRelanceCompare1', 'SearchRelanceNbjours1' ],
		'0',
		false
	);

	document.observe("dom:loaded", function() {
		[ $('SearchRelanceContrat0'), $('SearchRelanceContrat1'), $('SearchRelanceNumrelance1'), $('SearchRelanceNumrelance2'), $('SearchRelanceNumrelance3') ].each( function(field) {
			field.observe('change', function() {
				updateNbJours(findContrat(), findRelance());
			} );
		} );
		updateNbJours(findContrat(), findRelance());
	});

	function findRelance() {
		if ($('SearchRelanceNumrelance1').checked==true)
			return 1;
		else if ($('SearchRelanceNumrelance2').checked==true)
			return 2;
		else if ($('SearchRelanceNumrelance3').checked==true)
			return 3;
	}

	function findContrat() {
		if ($('SearchRelanceContrat0').checked==true)
			return 0;
		else if ($('SearchRelanceContrat1').checked==true)
			return 1;
	}

	function updateNbJours(contrat, relance) {
		var nbJoursMin = 0;
		if (contrat == 0) {
			if (relance == 1)
				nbJoursMin = parseInt('<?php echo Configure::read( "Nonrespectsanctionep93.relanceOrientstructCer1" );?>');
			else if (relance == 2)
				nbJoursMin = parseInt('<?php echo Configure::read( "Nonrespectsanctionep93.relanceOrientstructCer1" );?>') + parseInt('<?php echo Configure::read( "Nonrespectsanctionep93.relanceOrientstructCer2" );?>');
			else if (relance == 3)
				nbJoursMin = parseInt('<?php echo Configure::read( "Nonrespectsanctionep93.relanceOrientstructCer1" );?>') + parseInt('<?php echo Configure::read( "Nonrespectsanctionep93.relanceOrientstructCer2" );?>') + parseInt('<?php echo Configure::read( "Nonrespectsanctionep93.relanceOrientstructCer3" );?>');

			$('nbjoursmin0').update(' ('+nbJoursMin+' jours minimum)');
			$('nbjoursmin1').update('');
		}
		else {
			if (relance == 1)
				nbJoursMin = parseInt('<?php echo Configure::read( "Nonrespectsanctionep93.relanceCerCer1" );?>');
			else if (relance == 2)
				nbJoursMin = parseInt('<?php echo Configure::read( "Nonrespectsanctionep93.relanceCerCer1" );?>') + parseInt('<?php echo Configure::read( "Nonrespectsanctionep93.relanceCerCer2" );?>');

			if (nbJoursMin > 0)
				$('nbjoursmin1').update(' ('+nbJoursMin+' jours minimum)');
			else
				$('nbjoursmin1').update(' (merci de choisir un type de relance)');
			$('nbjoursmin0').update('');
		}
	}

	observeDisableFieldsOnCheckbox(
		'SearchOrientstructDerniere',
		[
			'SearchOrientstructDernierevalid',
		],
		true
	);
	observeDisableFieldsOnCheckbox(
		'SearchOrientstructDernierevalid',
		[
			'SearchOrientstructDerniere',
		],
		true
	);
</script>