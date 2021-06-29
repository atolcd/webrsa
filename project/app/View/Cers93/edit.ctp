<?php
	$title_for_layout = ( ( $this->action == 'add' ) ? 'Ajout d\'un CER' : 'Modification d\'un CER' );
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		dependantSelect( 'ContratinsertionReferentId', 'ContratinsertionStructurereferenteId' );
	} );
</script>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		<?php
			$ref_id = Set::extract( $this->request->data, 'Contratinsertion.referent_id' );
            echo $this->Ajax->remoteFunction(
                array(
                    'update' => 'StructurereferenteRef',
                    'url' => array(
						'controller' => $this->request->params['controller'],
						'action' => 'ajaxstruct',
					),
					'with' => 'Form.Element.serialize( \'ContratinsertionStructurereferenteId\' )'
                )
            ).';';
            echo $this->Ajax->remoteFunction(
                array(
                    'update' => 'ReferentRef',
                    'url' => array(
						'controller' => $this->request->params['controller'],
						'action' => 'ajaxref',
					),
					'with' => 'Form.Element.serialize( \'ContratinsertionReferentId\' )'
                )
            ).';';
        ?>
    } );
</script>

<?php
	echo $this->Xform->getExtraValidationErrorMessages();

	echo $this->Html->tag( 'h1', $title_for_layout );

	echo $this->Xform->create( null, array( 'inputDefaults' => array( 'domain' => 'contratinsertion' ), 'id' => 'contratinsertion', 'novalidate' => 'novalidate' ) );

	echo $this->Xform->inputs(
		array(
			'fieldset' => false,
			'legend' => false,
			'Contratinsertion.id' => array( 'type' => 'hidden' ),
			'Contratinsertion.personne_id' => array( 'type' => 'hidden', 'value' => $personne_id ),
			'Cer93.id' => array( 'type' => 'hidden' ),
			'Cer93.contratinsertion_id' => array( 'type' => 'hidden' ),
			// Champs non sauvegardés mais nécessaires en cas d'erreur et de renvoi du formulaire
			'Contratinsertion.rg_ci' => array( 'type' => 'hidden' ),
			'Personne.sexe' => array( 'type' => 'hidden' ),
			'Cer93.rolepers' => array( 'type' => 'hidden' ),
			'Cer93.numdemrsa' => array( 'type' => 'hidden' ),
			'Cer93.identifiantpe' => array( 'type' => 'hidden' ),
			'Cer93.user_id' => array( 'type' => 'hidden' ),
			'Cer93.nomutilisateur' => array( 'type' => 'hidden' ),
			'Cer93.structureutilisateur' => array( 'type' => 'hidden' ),
		)
	);
?>
<!-- Bloc 1  -->
<fieldset>
    <legend>Structure établissant le CER</legend>
    <table class="wide noborder cers93">
        <tr>
            <td class="noborder">
                <?php echo $this->Xform->input( 'Contratinsertion.structurereferente_id', array( 'label' => __d( 'cers93', 'Contratinsertion.structurereferente_id' ), 'type' => 'select', 'options' => $options['Contratinsertion']['structurereferente_id'], /*'selected' => $struct_id,*/ 'empty' => true, 'required' => true ) );?>
                <?php echo $this->Ajax->observeField( 'ContratinsertionStructurereferenteId', array( 'update' => 'StructurereferenteRef', 'url' => array( 'action' => 'ajaxstruct' ) ) ); ?>
            </td>
            <td class="noborder">
                <?php echo $this->Xform->input( 'Contratinsertion.referent_id', array( 'label' => __d( 'cers93', 'Contratinsertion.referent_id' ), 'type' => 'select', 'options' => $options['Contratinsertion']['referent_id'], 'empty' => true, 'selected' => ( isset( $this->request->data['Contratinsertion']['structurereferente_id'] ) && isset( $this->request->data['Contratinsertion']['referent_id'] ) ) ? ( $this->request->data['Contratinsertion']['structurereferente_id'].'_'.suffix( $this->request->data['Contratinsertion']['referent_id'] ) ) : null ) );?>
                <?php echo $this->Ajax->observeField( 'ContratinsertionReferentId', array( 'update' => 'ReferentRef', 'url' => array( 'action' => 'ajaxref' ) ) ); ?>
            </td>
        </tr>
        <tr>
            <td class="wide noborder"><div id="StructurereferenteRef"></div></td>

            <td class="wide noborder"><div id="ReferentRef"></div></td>
        </tr>
        <tr>
            <td class="wide noborder">
				<?php
					echo $this->Html->tag( 'p', 'Rang du contrat: '.$this->request->data['Contratinsertion']['rg_ci'] );
				?>
			</td>
        </tr>
    </table>
</fieldset>

<script type="text/javascript">
    function checkDatesToRefresh() {
        if( ( $F( 'ContratinsertionDdCiMonth' ) ) && ( $F( 'ContratinsertionDdCiYear' ) ) && ( radioValue( 'contratinsertion', 'data[Cer93][duree]' ) !== undefined ) ) {
            setDateIntervalCer( 'ContratinsertionDdCi', 'ContratinsertionDfCi', radioValue( 'contratinsertion', 'data[Cer93][duree]' ), false );
        }
    }

    document.observe( "dom:loaded", function() {
        Event.observe( $( 'ContratinsertionDdCiDay' ), 'change', function() {
            checkDatesToRefresh();
        } );
        Event.observe( $( 'ContratinsertionDdCiMonth' ), 'change', function() {
            checkDatesToRefresh();
        } );
        Event.observe( $( 'ContratinsertionDdCiYear' ), 'change', function() {
            checkDatesToRefresh();
        } );

		<?php foreach( $options['Cer93']['duree'] as $duree ): ?>
        Event.observe( $( 'Cer93Duree<?php echo str_replace( ' mois', '' ,$duree );?>' ), 'change', function() {
            checkDatesToRefresh();
        } );
		<?php endforeach;?>
	});
</script>
<script type="text/javascript">
    Event.observe( $( 'ContratinsertionStructurereferenteId' ), 'change', function( event ) {
        $( 'ReferentRef' ).update( '' );
    } );
</script>
<fieldset>
	<legend>État civil</legend>
	 <table class="wide noborder">
        <tr>
            <td class="mediumSize noborder">
                <strong>Statut de la personne : </strong><?php echo Set::enum( Set::classicExtract( $this->request->data, 'Cer93.rolepers' ), $options['Prestation']['rolepers'] ); ?>
                <br />
                <strong>Nom : </strong><?php echo Set::enum( Set::classicExtract( $this->request->data, 'Cer93.qual'), $options['Personne']['qual'] ).' '.Set::classicExtract( $this->request->data, 'Cer93.nom' );?>
                <br />
                <?php if( $this->request->data['Personne']['sexe'] == 2 ):?>
					<strong>Nom de naissance : </strong><?php echo Set::classicExtract( $this->request->data, 'Cer93.nomnai' );?>
					<br />
                <?php endif;?>
                <strong>Prénom : </strong><?php echo Set::classicExtract( $this->request->data, 'Cer93.prenom' );?>
                <br />
                <strong>Date de naissance : </strong><?php echo date_short( Set::classicExtract( $this->request->data, 'Cer93.dtnai' ) );?>
                <br />
                <strong>Adresse : </strong>
				<br /><?php echo nl2br( Set::classicExtract( $this->request->data, 'Cer93.adresse' ) ).'<br />'.Set::classicExtract( $this->request->data, 'Cer93.codepos' ).' '.Set::classicExtract( $this->request->data, 'Cer93.nomcom' );?>
            </td>
            <td class="mediumSize noborder">
                <!-- <strong>N° Service instructeur : </strong>
                <?php
					$libservice = Set::enum( Set::classicExtract( $this->request->data, 'Suiviinstruction.typeserins' ),  $options['Serviceinstructeur']['typeserins'] );
					if( isset( $libservice ) ) {
						echo $libservice;
					}
					else{
						echo 'Non renseigné';
					}
                ?>
                <br />
                <strong>N° demandeur : </strong><?php echo Set::classicExtract( $this->request->data, 'Cer93.numdemrsa' );?>
                <br /> -->
                <strong>Date d'ouverture de droit : </strong><?php echo date_short( Set::classicExtract( $this->request->data, 'Cer93.dtdemrsa' ) );?>
                <br />
                <strong>N° CAF/MSA : </strong><?php echo Set::classicExtract( $this->request->data, 'Cer93.matricule' );?>
                <!--<br />
                <strong>Inscrit au Pôle emploi</strong>
                <?php echo  !empty( $this->request->data['Cer93']['identifiantpe'] ) ? 'Oui' : 'Non' ;?>
				<br />
				 <strong>N° identifiant : </strong><?php echo Set::classicExtract( $this->request->data, 'Cer93.identifiantpe' );?> -->
				<br />
				 <strong>Situation familiale : </strong><?php echo Set::enum( Set::classicExtract( $this->request->data, 'Cer93.sitfam' ), $options['Foyer']['sitfam'] );?>
                <br />
                <strong>Conditions de logement : </strong><?php echo Set::enum( Set::classicExtract( $this->request->data, 'Cer93.natlog' ), $options['Dsp']['natlog'] );?>
            </td>
        </tr>
    </table>

<?php

	// Bloc 2 : Composition du foyer
	if( !empty( $this->request->data['Compofoyercer93'] ) ) {

		// Sauvegarde des informations
		foreach( $this->request->data['Compofoyercer93'] as $index => $compofoyercer93 ) {
			echo $this->Xform->inputs(
				array(
					'fieldset' => false,
					'legend' => false,
					"Compofoyercer93.{$index}.id" => array( 'type' => 'hidden' ),
					"Compofoyercer93.{$index}.cer93_id" => array( 'type' => 'hidden' ),
					"Compofoyercer93.{$index}.qual" => array( 'type' => 'hidden' ),
					"Compofoyercer93.{$index}.nom" => array( 'type' => 'hidden' ),
					"Compofoyercer93.{$index}.prenom" => array( 'type' => 'hidden' ),
					"Compofoyercer93.{$index}.dtnai" => array( 'type' => 'hidden' ),
					"Compofoyercer93.{$index}.rolepers" => array( 'type' => 'hidden' ),
				)
			);
		}

		// Affichage des informations sous forme de tableau
		echo '<table class="mediumSize aere">
			<thead>
				<tr>
					<th>Rôle</th>
					<th>Civilité</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Date de naissance</th>
				</tr>
			</thead>
		<tbody>';
		foreach( $this->request->data['Compofoyercer93'] as $index => $compofoyercer93 ){
			echo $this->Xhtml->tableCells(
				array(
					h( Set::enum( $compofoyercer93['rolepers'], $options['Prestation']['rolepers'] ) ),
					h( Set::enum( $compofoyercer93['qual'], $options['Personne']['qual'] ) ),
					h( $compofoyercer93['nom'] ),
					h( $compofoyercer93['prenom'] ),
					h( $this->Locale->date( 'Date::short', $compofoyercer93['dtnai'] ) )
				),
				array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
				array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
			);
		}
		echo '</tbody></table>';
	}

	echo $this->Xform->inputs(
		array(
			'fieldset' => false,
			'legend' => false,
			// Bloc 2: état cvil
			'Cer93.matricule' => array( 'type' => 'hidden' ),
			'Cer93.dtdemrsa' => array( 'type' => 'hidden' ),
			'Cer93.qual' => array( 'type' => 'hidden' ),
			'Cer93.nom' => array( 'type' => 'hidden' ),
			'Cer93.nomnai' => array( 'type' => 'hidden' ),
			'Cer93.prenom' => array( 'type' => 'hidden' ),
			'Cer93.dtnai' => array( 'type' => 'hidden' ),
			'Cer93.adresse' => array( 'type' => 'hidden' ),
			'Cer93.codepos' => array( 'type' => 'hidden' ),
			'Cer93.nomcom' => array( 'type' => 'hidden' ),
			'Cer93.sitfam' => array( 'type' => 'hidden' ),
			'Cer93.natlog' => array( 'type' => 'hidden' ),
			'Cer93.prevupcd' => array( 'type' => 'hidden' ),
			'Cer93.sujetpcd' => array( 'type' => 'hidden' ),
			'Cer93.incoherencesetatcivil' => array( 'domain' => 'cer93', 'type' => 'textarea' )
		)
	);
?>
</fieldset>

<?php
	//Bloc 3 : Vérification des droits
	echo $this->Xform->inputs(
		array(
			'fieldset' => true,
			'legend' => 'Vérification des droits',
			'Cer93.inscritpe' => array( 'domain' => 'cer93', 'type' => 'radio', 'options' => $options['Cer93']['inscritpe'], 'empty' => false ),
			'Cer93.cmu' => array( 'domain' => 'cer93', 'type' => 'radio', 'options' => $options['Cer93']['cmu'], 'empty' => false ),
			'Cer93.cmuc' => array( 'domain' => 'cer93', 'type' => 'radio', 'options' => $options['Cer93']['cmuc'], 'empty' => false )
		)
	);


?>
<fieldset id="FormationEtExperience">
	<legend>Formation et expérience</legend>
	<?php
		// bloc 4 : Formation et expérience
		echo $this->Xform->input( 'Cer93.nivetu', array( 'domain' => 'cer93', 'type' => 'select', 'empty' => true, 'options' => $options['Cer93']['nivetu'] ) );
	?>

	<fieldset>
		<legend>Diplômes (scolaires, universitaires et/ou professionnels)</legend>
		<ul class="actionMenu">
			<li><a href="#" onclick="try { addDynamicTrInputs( 'Diplomecer93', gabaritDiplomecer93 ); } catch (e) { console.log( e ); } return false;">Ajouter</a></li>
		</ul>
		<table id="Diplomecer93">
			<thead>
				<tr>
					<th>Intitulé du diplôme</th>
					<th>Année d'obtention</th>
					<th>Précisez si obtenu à l'étranger</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if( !empty( $this->request->data['Diplomecer93'] ) ) {
						foreach( $this->request->data['Diplomecer93'] as $index => $diplomecer93 ) {
							echo $this->Html->tableCells(
								array(
									$this->Xform->input( "Diplomecer93.{$index}.id", array( 'type' => 'hidden', 'label' => false ) )
									.$this->Xform->input( "Diplomecer93.{$index}.cer93_id", array( 'type' => 'hidden', 'label' => false ) )
									.$this->Xform->input( "Diplomecer93.{$index}.name", array( 'type' => 'text', 'label' => false ) ),
									$this->Xform->input( "Diplomecer93.{$index}.annee", array( 'type' => 'select', 'label' => false, 'options' => array_range( date( 'Y' ), 1960 ), 'empty' => true ) ),
									$this->Xform->input( "Diplomecer93.{$index}.isetranger", array( 'type' => 'checkbox', 'label' => false, 'empty' => true ) ),
									$this->Html->link( 'Supprimer', '#', array( 'onclick' => "deleteDynamicTrInputs( 'Diplomecer93', {$index} );return false;" ) ),
								)
							);
						}
					}
				?>
			</tbody>
		</table>
	</fieldset>

	<fieldset>
		<legend>Expériences professionnelles significatives</legend>
		<ul class="actionMenu">
			<li><a href="#" onclick="try { addDynamicTrInputs( 'Expprocer93', gabaritExpprocer93 ); } catch (e) { console.log( e ); } return false;">Ajouter</a></li>
		</ul>
		<table id="Expprocer93" class="tooltips">
			<thead>
				<tr>
					<th>Recherche rapide</th>
					<th>Code famille</th>
					<th>Code domaine</th>
					<th>Code métier</th>
					<th>Appellation métier</th>
					<th>Nature du contrat</th>
					<th>Année de début</th>
					<th colspan="2">Durée</th>
					<th>Action</th>
					<th class="innerTableHeader noprint">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if( !empty( $this->request->data['Expprocer93'] ) ) {
						foreach( $this->request->data['Expprocer93'] as $index => $expprocer93 ) {
							$innerTable = '<table id="innerTableExpprocer93'.$index.'" class="innerTable">
								<tbody>
									<tr>
										<th>'.__d( 'cer93', 'Cer93.metierexerce_id' ).'</th>
										<td>'.value( $options['Expprocer93']['metierexerce_id'], Hash::get( $this->request->data, "Expprocer93.{$index}.metierexerce_id" ) ).'</td>
									</tr>
									<tr>
										<th>'.__d( 'cer93', 'Cer93.secteuracti_id' ).'</th>
										<td>'.value( $options['Expprocer93']['secteuracti_id'], Hash::get( $this->request->data, "Expprocer93.{$index}.secteuracti_id" ) ).'</td>
									</tr>
								</tbody>
							</table>';

							echo $this->Html->tableCells(
								array(
									$this->Xform->input( "Expprocer93.{$index}.id", array( 'type' => 'hidden', 'label' => false ) )
									.$this->Xform->input( "Expprocer93.{$index}.cer93_id", array( 'type' => 'hidden', 'label' => false ) )
									.$this->Xform->input( "Expprocer93.{$index}.metierexerce_id", array( 'type' => 'hidden', 'label' => false ) )
									.$this->Xform->input( "Expprocer93.{$index}.secteuracti_id", array( 'type' => 'hidden', 'label' => false ) )
									.$this->Xform->input( "Expprocer93.{$index}.Entreeromev3.id", array( 'type' => 'hidden' ) )
									.$this->Xform->input( "Expprocer93.{$index}.Entreeromev3.romev3", array( 'type' => 'text', 'label' => false ) ),
									$this->Xform->input( "Expprocer93.{$index}.Entreeromev3.familleromev3_id", array( 'type' => 'select', 'label' => false, 'options' => $options['Catalogueromev3']['familleromev3_id'], 'empty' => true ) ),
									$this->Xform->input( "Expprocer93.{$index}.Entreeromev3.domaineromev3_id", array( 'type' => 'select', 'label' => false, 'options' => $options['Catalogueromev3']['domaineromev3_id'], 'empty' => true ) ),
									$this->Xform->input( "Expprocer93.{$index}.Entreeromev3.metierromev3_id", array( 'type' => 'select', 'label' => false, 'options' => $options['Catalogueromev3']['metierromev3_id'], 'empty' => true ) ),
									$this->Xform->input( "Expprocer93.{$index}.Entreeromev3.appellationromev3_id", array( 'type' => 'select', 'label' => false, 'options' => $options['Catalogueromev3']['appellationromev3_id'], 'empty' => true ) ),
									$this->Xform->input( "Expprocer93.{$index}.naturecontrat_id", array( 'type' => 'select', 'label' => false, 'options' => $options['Naturecontrat']['naturecontrat_id'], 'empty' => true ) ),
									$this->Xform->input( "Expprocer93.{$index}.anneedeb", array( 'type' => 'select', 'label' => false, 'options' => array_range( date( 'Y' ), 1960 ), 'empty' => true ) ),
									$this->Xform->input( "Expprocer93.{$index}.nbduree", array( 'type' => 'text', 'label' => false ) ),
									$this->Xform->input( "Expprocer93.{$index}.typeduree", array( 'type' => 'select', 'label' => false, 'options' => $options['Expprocer93']['typeduree'], 'empty' => true, 'domain' => 'cer93' ) ),
									$this->Html->link( 'Supprimer', '#', array( 'onclick' => "deleteDynamicTrInputs( 'Expprocer93', {$index} );return false;" ) ),
									array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
								)
							);
						}
					}
				?>
			</tbody>
		</table>
	</fieldset>
	<?php
		echo $this->Xform->inputs(
			array(
				'fieldset' => false,
				'legend' => false,
				'Cer93.autresexps' => array( 'domain' => 'cer93', 'type' => 'textarea' ),
				'Cer93.isemploitrouv' => array( 'legend' => required( __d( 'cer93', 'Cer93.isemploitrouv' ) ), 'domain' => 'cer93', 'type' => 'radio', 'options' => $options['Cer93']['isemploitrouv'] ),
				// Ajout d'un champ caché pour pouvoir supprimer Emptrouvromev3.id lorsque le fieldset est fermé
				'Cer93.emptrouvromev3_id' => array( 'type' => 'hidden', 'value' => Hash::get( $this->request->data, 'Emptrouv.id' ) )
			)
		);
	?>
	<fieldset id="emploitrouv" class="noborder">
		<fieldset>
			<legend>Si oui, veuillez préciser :</legend>
			<?php
				echo $this->Romev3->fieldset( 'Emptrouvromev3', array( 'options' => array( 'Emptrouvromev3' => $options['Catalogueromev3'] ), 'required' => true ) );

				$secteuracti_id = Hash::get( $this->request->data, 'Cer93.secteuracti_id' );
				$metierexerce_id = Hash::get( $this->request->data, 'Cer93.metierexerce_id' );

				if( !empty( $secteuracti_id ) || !empty( $metierexerce_id ) ) {
					echo $this->Html->tag(
						'fieldset',
						$this->Html->tag( 'legend', 'Emploi trouvé (codes INSEE)' )
						.$this->Default3->subform(
							array(
								'Cer93.secteuracti_id' => array(
									'view' => true,
									'type' => 'text',
									'hidden' => true,
									'options' => (array)$options['Expprocer93']['secteuracti_id']
								),
								'Cer93.metierexerce_id' => array(
									'view' => true,
									'type' => 'text',
									'hidden' => true,
									'options' => (array)$options['Expprocer93']['metierexerce_id']
								),
							),
							array( 'domain' => 'cer93' )
						)
					);
				}
				else {
					echo $this->Xform->inputs(
						array(
							'fieldset' => false,
							'legend' => false,
							'Cer93.secteuracti_id' => array( 'type' => 'hidden' ),
							'Cer93.metierexerce_id' => array( 'type' => 'hidden' )
						)
					);
				}

				echo $this->Xform->inputs(
					array(
						'fieldset' => false,
						'legend' => false,
						'Cer93.dureehebdo' => array( 'domain' => 'cer93', 'type' => 'select', 'options' => $options['dureehebdo'], 'empty' => true, 'required' => true ),
						'Cer93.naturecontrat_id' => array( 'domain' => 'cer93', 'type' => 'select', 'options' => $options['Naturecontrat']['naturecontrat_id'], 'empty' => true, 'required' => true )
					)
				);

				echo $this->Xform->input( 'Cer93.dureecdd', array( 'domain' => 'cer93', 'type' => 'select', 'empty' => true, 'options' => $options['dureecdd'], 'required' => true ) );
			?>
		</fieldset>
	</fieldset>
	<script type="text/javascript">
		document.observe( "dom:loaded", function() {
			observeDisableFieldsetOnRadioValue(
				'contratinsertion',
				'data[Cer93][isemploitrouv]',
				$( 'emploitrouv' ),
				'O',
				false,
				true
			);
			<?php if( !empty( $naturecontratDuree ) ):?>
				observeDisableFieldsOnValue(
					'Cer93NaturecontratId',
					[ 'Cer93Dureecdd' ],
					[ '<?php echo implode( "', '", $naturecontratDuree ); ?>' ],
					false,
					true
				);
			<?php endif;?>
			$("Emptrouvromev3Appellationromev3Id").observe( 'click', function() {
				var elementparent = document.getElementById("Emptrouvromev3Appellationromev3Id").parentNode;
				elementparent.classList.add("loading");
				setTimeout(function(){
					elementparent.classList.remove("loading");
				}, 2000);
			} );
		});

	</script>
	<!-- Fin bloc 4 -->
</fieldset>
<!--  Bloc 5 -->
<?php $sujetpcd = array();?>
<?php if( $this->request->data['Contratinsertion']['rg_ci'] <= 1 ): ?>
	<p class="notice">Ce CER est le premier.</p>
<?php else: ?>
<fieldset id="bilanpcd"><legend>Bilan du contrat précédent</legend>

	<h4>Le précédent contrat portait sur </h4>
	<?php if( !empty( $this->request->data['Cer93']['sujetpcd'] ) ):?>
		<?php
			$sujetpcd = unserialize( $this->request->data['Cer93']['sujetpcd'] );
			echo $this->Cer93->sujetspcds2( $sujetpcd );
		?>
	<?php else:?>
		<p class="notice">Aucune information renseignée</p>
	<?php endif;?>
	<?php
		// Sujet précédent, complément d'informations ROME v.3
		$sujetromev3 = (array)Hash::get( $sujetpcd, 'Sujetromev3' );
		if( !empty( $sujetromev3 ) ) {
			echo $this->Romev3->fieldsetView( 'Sujetromev3', $sujetpcd, array( 'legend' => 'Le précédent contrat portait sur l\'emploi (ROME v.3)' ) );
		}

		//Il a été prévu (champ prevu du bloc 6)
		echo $this->Xform->fieldValue( 'Cer93.prevupcd', Set::classicExtract( $this->request->data, 'Cer93.prevupcd' ), true, 'textarea' );

		//Bloc 5 : Bilan du précédent contrat
		echo $this->Xform->input( 'Cer93.bilancerpcd', array( 'domain' => 'cer93', 'type' => 'textarea' ) );
	?>
</fieldset>
<?php endif; ?>
<!--  Fin bloc 5 -->

<fieldset><legend>Projet pour ce nouveau contrat</legend>
		<?php
		// Bloc 6 : Projet pour ce nouveau contrat
		echo $this->Xform->input( 'Cer93.prevu', array( 'domain' => 'cer93', 'type' => 'textarea', 'required' => true ) );

		// HABTM spécial, avec des select liés aux cases à cocher
		$validationErrorSujetcer93Sujetcer93 = ( isset( $this->validationErrors['Sujetcer93']['Sujetcer93'] ) && !empty( $this->validationErrors['Sujetcer93']['Sujetcer93'] ) );
		?>
		<div class="input checkboxes<?php if( $validationErrorSujetcer93Sujetcer93 ) { echo ' error'; }?>">
		<?php
		echo '<fieldset><legend>';
			echo required( $this->Default2->label( 'Sujetcer93.Sujetcer93', array( 'domain' => 'cer93' ) ) );
		echo '</legend>';

		if( $validationErrorSujetcer93Sujetcer93 ) {
			echo "<div class='error-message'>".$this->validationErrors['Sujetcer93']['Sujetcer93'][0]."</div>";
		}

		$selectedSujetcer93 = array();
		if( !empty( $this->request->data['Sujetcer93']['Sujetcer93'] ) ) {
			$selectedSujetcer93 = Set::extract( '/Sujetcer93/Sujetcer93/sujetcer93_id', $this->request->data );
			$keys = array_keys( $this->request->data['Sujetcer93']['Sujetcer93'] );
			$selectedSujetcer93 = array_combine( $keys, $selectedSujetcer93 );
		}
		echo $this->Xform->input( "Sujetcer93.Sujetcer93", array( 'type' => 'hidden', 'value' => '' ) );
		$i = 0;

		// Activation / désactivation de la partie "Votre contrat porte sur l'emploi (ROME v.3)" en fonciton des réponses à "Votre contrat porte sur"
		$activationPath = Configure::read( 'Cer93.Sujetcer93.Romev3.path' );
		$activationValues = (array)Configure::read( 'Cer93.Sujetcer93.Romev3.values' );

		$activationSujetcer93 = ( 'Sujetcer93.Sujetcer93.{n}.sujetcer93_id' === $activationPath );
		$activationSoussujetcer93 = ( 'Sujetcer93.Sujetcer93.{n}.soussujetcer93_id' === $activationPath );
		$activationIds = array();

		echo '<ul class="liste_sujets">';
		foreach( $options['Sujetcer93']['sujetcer93_id'] as $idSujet => $nameSujet ) {
			$array_key = array_search( $idSujet, $selectedSujetcer93 );
			$checked = ( ( $array_key !== false ) ? 'checked' : '' );
			$soussujetcer93_id = null;
			$commentaireautre = null;

			$valeurparsoussujetcer93_id = null;
			if( $checked ) {
				if( isset( $this->request->data['Sujetcer93']['Sujetcer93'][$array_key]['soussujetcer93_id'] ) ) {
					$soussujetcer93_id = $this->request->data['Sujetcer93']['Sujetcer93'][$array_key]['soussujetcer93_id'];
				}
				else if( isset( $this->request->data['Sujetcer93']['Sujetcer93'][$array_key]['commentaireautre'] ) ) {
					$commentaireautre = $this->request->data['Sujetcer93']['Sujetcer93'][$array_key]['commentaireautre'];
				}

				if( isset( $this->request->data['Sujetcer93']['Sujetcer93'][$array_key]['valeurparsoussujetcer93_id'] ) ) {
					$valeurparsoussujetcer93_id = suffix( $this->request->data['Sujetcer93']['Sujetcer93'][$array_key]['valeurparsoussujetcer93_id'] );
					$valeurparsoussujetcer93_id = "{$soussujetcer93_id}_{$valeurparsoussujetcer93_id}";
				}
			}

			// Niveau 1
			echo '<li>';

			$domPath = "Sujetcer93.Sujetcer93.{$idSujet}.sujetcer93_id";
			if( $activationSujetcer93 && in_array( $idSujet, $activationValues ) ) {
				$activationIds[] = $this->Html->domId( $domPath );
			}

			echo $this->Xform->input( $domPath, array( 'name' => "data[Sujetcer93][Sujetcer93][{$i}][sujetcer93_id]", 'label' => $nameSujet, 'type' => 'checkbox', 'value' => $idSujet, 'hiddenField' => false, 'checked' => $checked ) );

			// Niveau 2
			if( !empty( $soussujetscers93[$idSujet] ) ) {
				echo '<ul><li>'; // Niveau 2

				$domPath = "Sujetcer93.Sujetcer93.{$idSujet}.soussujetcer93_id";
				if( $activationSoussujetcer93 && in_array( $idSujet, $activationValues ) ) {
					$activationIds[] = $this->Html->domId( $domPath );
				}

				echo $this->Xform->input( $domPath, array( 'name' => "data[Sujetcer93][Sujetcer93][{$i}][soussujetcer93_id]", 'label' => false, 'type' => 'select', 'options' => $soussujetscers93[$idSujet], 'empty' => true, 'value' => $soussujetcer93_id ) );

				$interSoussujet = array_intersect( array_keys( $soussujetscers93[$idSujet] ), $autresoussujet_isautre_id );
				if( !empty( $interSoussujet ) ) {
					$autresoussujet = null;
					if( !empty( $array_key ) || ( $array_key == 0 ) ) {
						if( isset( $this->request->data['Sujetcer93']['Sujetcer93'][$array_key]['autresoussujet'] ) ) {
							$autresoussujet = $this->request->data['Sujetcer93']['Sujetcer93'][$array_key]['autresoussujet'];
						}
					}
					echo $this->Xform->input( "Sujetcer93.Sujetcer93.{$idSujet}.autresoussujet", array( 'name' => "data[Sujetcer93][Sujetcer93][{$i}][autresoussujet]", 'label' => false, 'type' => 'text', 'value' => $autresoussujet ) );
				}

				// Niveau 3
				if( !empty( $valeursparsoussujetscers93[$idSujet] ) ) {
					$correspondChilParent[$this->Html->domId( "Sujetcer93.Sujetcer93.{$idSujet}.valeurparsoussujetcer93_id" )] = $this->Html->domId( "Sujetcer93.Sujetcer93.{$idSujet}.soussujetcer93_id" );
					echo '<ul><li>'; // Niveau 3
					echo $this->Xform->input( "Sujetcer93.Sujetcer93.{$idSujet}.valeurparsoussujetcer93_id", array( 'name' => "data[Sujetcer93][Sujetcer93][{$i}][valeurparsoussujetcer93_id]", 'label' => false, 'type' => 'select', 'options' => $valeursparsoussujetscers93[$idSujet], 'empty' => true, 'value' => $valeurparsoussujetcer93_id ) );
					// Ajout
					$inter = array_intersect( array_keys( $valeursparsoussujetscers93[$idSujet] ), $autrevaleur_isautre_id );
					if( !empty( $inter ) ) {
						$autrevaleur = null;
						if( !empty( $array_key ) || ( $array_key == 0 ) ) {
							if( isset( $this->request->data['Sujetcer93']['Sujetcer93'][$array_key]['autrevaleur'] ) ) {
								$autrevaleur = $this->request->data['Sujetcer93']['Sujetcer93'][$array_key]['autrevaleur'];
							}
						}
						echo $this->Xform->input( "Sujetcer93.Sujetcer93.{$idSujet}.autrevaleur", array( 'name' => "data[Sujetcer93][Sujetcer93][{$i}][autrevaleur]", 'label' => false, 'type' => 'text', 'value' => $autrevaleur ) );
					}
					echo '</li></ul>'; // Niveau 3
				}
				echo '</li></ul>'; // Niveau 2
			}
			else {
				echo $this->Xform->input( "Sujetcer93.Sujetcer93.{$idSujet}.commentaireautre", array( 'name' => "data[Sujetcer93][Sujetcer93][{$i}][commentaireautre]", 'label' => false, 'type' => 'text', 'value' => $commentaireautre ) );
			}
			echo '</li>'; // Niveau 1
			$i++;
		}
		echo '</ul>';
		if( !empty( $sujetscers93enregistres ) ) {
			echo $this->Html->tag(
				'fieldset',
				$this->Html->tag( 'legend', 'Valeurs précédemment sélectionnées mais désacivées' )
				.$this->Cer93->sujets2( $sujetscers93enregistres, array( 'hidden' => true ) )
			);
		}
		echo '</fieldset>';
		echo '</div>';

		// Activation / désactivation de la partie "Votre contrat porte sur l'emploi (ROME v.3)" en fonciton des réponses à "Votre contrat porte sur"
		echo $this->Xform->input( 'Sujetromev3.id', array( 'type' => 'hidden', 'id' => false ) ); // TODO: Cer93.sujetromev3_._id

		if( !empty( $activationPath ) && !empty( $activationValues ) ) {
			echo $this->Romev3->fieldset( 'Sujetromev3', array( 'options' => array( 'Sujetromev3' => $options['Catalogueromev3'] ), 'required' => true ) );

			// 1. Si le chemin est Sujetcer93.Sujetcer93.{n}.sujetcer93_id, alors c'est un select
			if( $activationPath === 'Sujetcer93.Sujetcer93.{n}.sujetcer93_id' ) {
				foreach( $activationIds as $master ) {
					echo $this->Observer->disableFieldsetOnCheckbox( $master, 'Sujetromev3FieldsetId', false, true );
				}
			}

			// 2. Si le chemin est Sujetcer93.Sujetcer93.{n}.soussujetcer93_id, alors ce sont des cases à cocher
			if( $activationPath === 'Sujetcer93.Sujetcer93.{n}.soussujetcer93_id' ) {
				foreach( $activationIds as $master ) {
					echo $this->Observer->disableFieldsetOnValue( $master, 'Sujetromev3FieldsetId', $activationValues, false, true );
				}
			}
		}
	?>
</fieldset>
<!-- Javascript pour les sujetscers93 -->
<script type="text/javascript">
//<![CDATA[
	<?php foreach( array_keys( $options['Sujetcer93']['sujetcer93_id'] ) as $key ) :?>
	observeDisableFieldsOnCheckbox(
		'Sujetcer93Sujetcer93<?php echo $key;?>Sujetcer93Id',
		['Sujetcer93Sujetcer93<?php echo $key;?>Soussujetcer93Id', 'Sujetcer93Sujetcer93<?php echo $key;?>Valeurparsoussujetcer93Id', 'Sujetcer93Sujetcer93<?php echo $key;?>Commentaireautre', 'Sujetcer93Sujetcer93<?php echo $key;?>Autrevaleur'],
		false,
		true
	);
	<?php endforeach;?>

	<?php if( !empty( $correspondChilParent ) ):?>
		<?php foreach( $correspondChilParent as $childId => $parentId ):?>
			dependantSelect( '<?php echo $childId;?>', '<?php echo $parentId;?>' );
		<?php endforeach;?>
	<?php endif;?>
//]]>
</script>
<!-- Javascript pour les soussujetscers93 -->
<script type="text/javascript">
//<![CDATA[
	document.observe( "dom:loaded", function() {
		<?php foreach( array_keys( $options['Sujetcer93']['sujetcer93_id'] ) as $key ):?>
			<?php if( in_array( $key, $sujets_ids_soussujets_autres ) ):?>

			// FIXME : Arnaud
			observeDisableFieldsOnValue(
				'Sujetcer93Sujetcer93<?php echo $key;?>Soussujetcer93Id',
				['Sujetcer93Sujetcer93<?php echo $key;?>Valeurparsoussujetcer93Id'],
				['<?php echo implode( "', '", $autresoussujet_isautre_id );?>', ''],
				true,
				true
			);
			// FIXME

			observeDisableFieldsOnValue(
				'Sujetcer93Sujetcer93<?php echo $key;?>Soussujetcer93Id',
				['Sujetcer93Sujetcer93<?php echo $key;?>Autresoussujet'],
				['<?php echo implode( "', '", $autresoussujet_isautre_id );?>'],
				false,
				true
			);
			<?php endif;?>
		<?php endforeach;?>
	});
//]]>
</script>
<!-- Javascript pour les valeursparsoussujetscers93 -->
<script type="text/javascript">
//<![CDATA[
	document.observe( "dom:loaded", function() {
		<?php foreach( array_keys( $options['Sujetcer93']['sujetcer93_id'] ) as $key ):?>
			<?php if( in_array( $key, $sujets_ids_valeurs_autres ) ):?>
			observeDisableFieldsOnValue(
				'Sujetcer93Sujetcer93<?php echo $key;?>Valeurparsoussujetcer93Id',
				['Sujetcer93Sujetcer93<?php echo $key;?>Autrevaleur'],
				['<?php echo implode( "', '", $autrevaleur_isautre_id );?>'],
				false,
				true
			);
			<?php endif;?>
		<?php endforeach;?>
	});
//]]>
</script>

<!-- Ajout -->

<?php
	//Bloc 7 : Durée proposée
	echo $this->Xform->input( 'Cer93.duree', array( 'legend' => required( 'Ce contrat est proposé pour une durée de ' ), 'domain' => 'cer93', 'type' => 'radio', 'options' => $options['Cer93']['duree'] ) );

	//Bloc 8 : Projet pour ce nouveau contrat
	echo $this->Xform->input( 'Cer93.pointparcours', array( 'domain' => 'cer93', 'type' => 'select', 'options' => $options['Cer93']['pointparcours'], 'empty' => true, 'required' => true ) );

	echo $this->Xform->input( 'Cer93.datepointparcours', array( 'domain' => 'cer93', 'type' => 'date', 'dateFormat' => 'DMY', 'empty' => true ) );
?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsOnValue(
			'Cer93Pointparcours',
			[
				'Cer93DatepointparcoursDay',
				'Cer93DatepointparcoursMonth',
				'Cer93DatepointparcoursYear'
			],
			[ 'aladate' ],
			false,
			true
		);
	});
</script>
<?php


	//Bloc 9 : Partie réservée au professionnel en charge du contrat
	echo $this->Xform->fieldValue( 'Cer93.structureutilisateur', Set::classicExtract( $this->request->data, 'Cer93.structureutilisateur' ), 'cers93' );
	echo $this->Xform->fieldValue( 'Cer93.nomutilisateur', Set::classicExtract( $this->request->data, 'Cer93.nomutilisateur' ) );

	echo $this->Xform->input( 'Cer93.pourlecomptede', array( 'domain' => 'cer93', 'type' => 'text' ) );
	echo $this->Xform->input( 'Cer93.observpro', array( 'domain' => 'cer93', 'type' => 'textarea' ) );

	echo $this->Xform->inputs(
		array(
			'fieldset' => false,
			'legend' => false,
			'Contratinsertion.dd_ci' => array(
				'domain' => 'contratinsertion',
				'type' => 'date',
				'empty' => true,
				'minYear' => date( 'Y' , strtotime(Configure::read('Cer93.dateCER.dtdebutMin'))),
				'maxYear' => date( 'Y' ) +1,
				'dateFormat' => 'DMY',
				'required' => true
			),
			'Contratinsertion.df_ci' => array( 'domain' => 'contratinsertion','type' => 'date', 'empty' => true, 'dateFormat' => 'DMY', 'required' => true ),
			'Contratinsertion.date_saisi_ci' => array( 'label' => 'Contrat saisi le', 'type' => 'date', 'dateFormat' => 'DMY', 'required' => true )
		)
	);
?>

<?php
	echo $this->Html->tag(
		'div',
		 $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
		.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
		array( 'class' => 'submit noprint' )
	);

	echo $this->Xform->end();
	echo $this->Observer->disableFormOnSubmit( 'contratinsertion' );

	function cers93_html_cleanup( $string ) {
		return str_replace( "'", "\\'", preg_replace( '/[[:space:]]+/', ' ', $string ) );
	}
?>

<script type="text/javascript">
	<!--//--><![CDATA[//><!--
		var gabaritDiplomecer93 = '<tr><td><?php
			$fields = $this->Xform->input( 'Diplomecer93.%line%.id', array( 'type' => 'hidden', 'label' => false ) )
				.$this->Xform->input( 'Diplomecer93.%line%.cer93_id', array( 'type' => 'hidden', 'label' => false ) )
				.$this->Xform->input( 'Diplomecer93.%line%.name', array( 'type' => 'text', 'label' => false ) );
			echo str_replace( "'", "\\'", $fields );
		?></td><td><?php
			$fields = $this->Xform->input( 'Diplomecer93.%line%.annee', array( 'type' => 'select', 'options' => array_range( date( 'Y' ), 1960 ), 'label' => false, 'empty' => true ) );
			echo cers93_html_cleanup( $fields );
		?></td><td><?php
			$fields = $this->Xform->input( 'Diplomecer93.%line%.isetranger', array( 'type' => 'checkbox', 'label' => false ) );
			echo cers93_html_cleanup( $fields );
		?></td><td><a href="#" onclick="deleteDynamicTrInputs( \'Diplomecer93\', %line% );return false;">Supprimer</a></td></tr>';

		var gabaritExpprocer93 = '<tr><td><?php
			$fields = $this->Xform->input( 'Expprocer93.%line%.id', array( 'type' => 'hidden', 'label' => false ) )
				.$this->Xform->input( 'Expprocer93.%line%.cer93_id', array( 'type' => 'hidden', 'label' => false ) )
				// ROME v.3
				.$this->Xform->input( 'Expprocer93.%line%.Entreeromev3.id', array( 'type' => 'hidden', 'label' => false ) )
				.$this->Xform->input( "Expprocer93.%line%.Entreeromev3.romev3", array( 'type' => 'text', 'label' => false ) );
			echo cers93_html_cleanup( $fields );
		?></td><td><?php
			$fields = $this->Xform->input( 'Expprocer93.%line%.Entreeromev3.familleromev3_id', array( 'type' => 'select', 'label' => false, 'options' => $options['Catalogueromev3']['familleromev3_id'], 'empty' => true ) );
			echo cers93_html_cleanup( $fields );
		?></td><td><?php
			$fields = $this->Xform->input( 'Expprocer93.%line%.Entreeromev3.domaineromev3_id', array( 'type' => 'select', 'label' => false, 'options' => $options['Catalogueromev3']['domaineromev3_id'], 'empty' => true ) );
			echo cers93_html_cleanup( $fields );
		?></td><td><?php
			$fields = $this->Xform->input( 'Expprocer93.%line%.Entreeromev3.metierromev3_id', array( 'type' => 'select', 'label' => false, 'options' => $options['Catalogueromev3']['metierromev3_id'], 'empty' => true ) );
			echo cers93_html_cleanup( $fields );
		?></td><td><?php
			$fields = $this->Xform->input( 'Expprocer93.%line%.Entreeromev3.appellationromev3_id', array( 'type' => 'select', 'label' => false, 'options' => $options['Catalogueromev3']['appellationromev3_id'], 'empty' => true ) );
			echo cers93_html_cleanup( $fields );
		?></td><td><?php
			$fields = $this->Xform->input( 'Expprocer93.%line%.naturecontrat_id', array( 'type' => 'select', 'label' => false, 'options' => $options['Naturecontrat']['naturecontrat_id'], 'empty' => true ) );
			echo cers93_html_cleanup( $fields );
		?></td><td><?php
			$fields = $this->Xform->input( 'Expprocer93.%line%.anneedeb', array( 'type' => 'select', 'label' => false, 'options' => array_range( date( 'Y' ), 1960 ), 'empty' => true ) );
			echo cers93_html_cleanup( $fields );
		?></td><td><?php
			$fields = $this->Xform->input( 'Expprocer93.%line%.nbduree', array( 'type' => 'text', 'label' => false ) );
			echo str_replace( "'", "\\'", $fields );
		?></td><td><?php
			$fields = $this->Xform->input( 'Expprocer93.%line%.typeduree', array( 'type' => 'select', 'label' => false, 'options' => $options['Expprocer93']['typeduree'], 'empty' => true ) );
			echo cers93_html_cleanup( $fields );
		?></td><td><a href="#" onclick="deleteDynamicTrInputs( \'Expprocer93\', %line% );return false;">Supprimer</a></td></tr>';
	//--><!]]>
</script>

<script type="text/javascript">
	<!--//--><![CDATA[//><!--
		function addDynamicTrInputs( tableId, gabarit ) {
			var index = 0;
			$$( '#' + tableId + ' tbody tr > td:nth-child(1) > input:nth-child(1)' ).each( function( input ) {
				var i = parseInt( input.name.replace( new RegExp( '^.*\\]\\[([0-9]+)\\]\\[.*$', 'gi' ), '$1' ) );
				if( i >= index ) {
					index = i + 1;
				}
			} );
			var regexp = new RegExp( '%line%', 'gi' ),
				line = gabarit.replace( regexp, index );
			$$( '#' + tableId + ' tbody' )[0].insert( { 'top': line } );

			// Uniquement pour le tableau "Expériences professionnelles significatives"
			if( tableId === 'Expprocer93' ) {
				dependantSelect( 'Expprocer93%line%Entreeromev3Domaineromev3Id'.replace( regexp, index ), 'Expprocer93%line%Entreeromev3Familleromev3Id'.replace( regexp, index ) );
				dependantSelect( 'Expprocer93%line%Entreeromev3Metierromev3Id'.replace( regexp, index ), 'Expprocer93%line%Entreeromev3Domaineromev3Id'.replace( regexp, index ) );
				dependantSelect( 'Expprocer93%line%Entreeromev3Appellationromev3Id'.replace( regexp, index ), 'Expprocer93%line%Entreeromev3Metierromev3Id'.replace( regexp, index ) );

				// Ajout du champ permettant l'autocomplétion
				( function() {
					var autocompleteId = 'Expprocer93%line%Entreeromev3Romev3'.replace( regexp, index );
						ajax_parameters = { 'url': '<?php echo Router::url( array( 'controller' => 'cataloguesromesv3', 'action' => 'ajax_appellation' ) );?>', 'prefix': '', 'fields': [ autocompleteId ], 'min': '3', 'delay': '500' };
					$( autocompleteId ).writeAttribute( 'autocomplete', 'off' );
					Event.observe( $( autocompleteId ), 'keyup', function(event) { ajax_action( event, ajax_parameters ); } );
				}() );
			}
		}

		function deleteDynamicTrInputs( tableId, index ) {
			var lineNr = -1;
			$$( '#' + tableId + ' tbody tr > td:nth-child(1) > input:nth-child(1)' ).each( function( input, l ) {
				var i = parseInt( input.name.replace( new RegExp( '^.*\\]\\[([0-9]+)\\]\\[.*$', 'gi' ), '$1' ) );
				if( i == index ) {
					lineNr = l;
				}
			} );

			if( lineNr != -1 ) {
				$$( '#' + tableId + ' tbody tr' )[parseInt(lineNr)].remove();
			}
		}
	//--><!]]>
</script>

<?php
	if( !empty( $this->request->data['Expprocer93'] ) ) {
		foreach( array_keys( $this->request->data['Expprocer93'] ) as $index ) {
			// Listes déroulantes liées
			echo $this->Observer->dependantSelect(
				array(
					"Expprocer93.{$index}.Entreeromev3.familleromev3_id" => "Expprocer93.{$index}.Entreeromev3.domaineromev3_id",
					"Expprocer93.{$index}.Entreeromev3.domaineromev3_id" => "Expprocer93.{$index}.Entreeromev3.metierromev3_id",
					"Expprocer93.{$index}.Entreeromev3.metierromev3_id" => "Expprocer93.{$index}.Entreeromev3.appellationromev3_id",
				)
			);

			// Champ recherche rapide
			// TODO: à faire pour les lignes dynamiques aussi
			echo $this->Ajax2->observe(
				array(
					"Expprocer93.{$index}.Entreeromev3.romev3" => array( 'event' => 'keyup' )
				),
				array(
					'url' => array( 'controller' => 'cataloguesromesv3', 'action' => 'ajax_appellation' ),
					'onload' => false
				)
			);
		}
	}