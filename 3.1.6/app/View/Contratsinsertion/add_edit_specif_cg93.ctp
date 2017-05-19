<?php
    if( $this->action == 'add' ) {
        $this->pageTitle = 'Ajout d\'un CER';
    }
    else {
        $this->pageTitle = 'Édition d\'un CER';
    }

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>
<?php
	echo $this->Form->create( 'Contratinsertion', array( 'type' => 'post', 'id' => 'testform' ) );
	echo $this->Form->input( 'Contratinsertion.personne_id', array( 'type' => 'hidden', 'value' => Set::classicExtract( $personne, 'Personne.id' ) ) );

	if( $this->action == 'add' ) {

		echo '<div>';
		echo $this->Form->input( 'Contratinsertion.id', array( 'type' => 'hidden', 'value' => '' ) );
		echo $this->Form->input( 'Contratinsertion.rg_ci', array( 'type' => 'hidden'/*, 'value' => '' */) );
		echo '</div>';
	}
	else {
		echo '<div>';
		echo $this->Form->input( 'Contratinsertion.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}


?>

<script type="text/javascript">
document.observe("dom:loaded", function() {
	observeDisableFieldsOnValue( 'ContratinsertionRgCi', [ 'ContratinsertionTypocontratId' ], 1, true );
});
</script>

<script type="text/javascript">
document.observe("dom:loaded", function() {
	dependantSelect( 'ContratinsertionReferentId', 'ContratinsertionStructurereferenteId' );
});
</script>

<script type="text/javascript">
function checkDatesToRefresh() {
	if( ( $F( 'ContratinsertionDdCiMonth' ) ) && ( $F( 'ContratinsertionDdCiYear' ) ) && ( $F( 'ContratinsertionDureeEngag' ) ) ) {
		setDateIntervalCer( 'ContratinsertionDdCi', 'ContratinsertionDfCi', $F( 'ContratinsertionDureeEngag' ), false );
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

	Event.observe( $( 'ContratinsertionDureeEngag' ), 'change', function() {
		checkDatesToRefresh();
	} );

	// form, radioName, fieldsetId, value, condition, toggleVisibility
	observeDisableFieldsetOnRadioValue(
		'testform',
		'data[Contratinsertion][forme_ci]',
		$( 'Contratsuite' ),
		'C',
		false,
		true
	);

	// form, radioName, fieldsetId, value, condition, toggleVisibility
	observeDisableFieldsetOnRadioValue(
		'testform',
		'data[Contratinsertion][forme_ci]',
		$( 'faitsuitea' ),
		'C',
		false,
		true
	);

	observeDisableFieldsetOnCheckbox(
		'ContratinsertionFaitsuitea',
		'Raisonfaitsuitea',
		false,
		true
	);


	//Autre cas de suspension / radiation
	observeDisableFieldsetOnRadioValue(
		'testform',
		 'data[Contratinsertion][avisraison_suspension_ci]',
		$( 'Suspensionautre' ),
		'A',
		false,
		true
	);

	//Autre cas de suspension / radiation
	observeDisableFieldsetOnRadioValue(
		'testform',
		 'data[Contratinsertion][avisraison_radiation_ci]',
		$( 'Radiationautre' ),
		'A',
		false,
		true
	);



	//Autre cas de suspension / radiation
	observeDisableFieldsetOnRadioValue(
		'testform',
		 'data[Contratinsertion][raison_ci]',
		$( 'Tablesuspension' ),
		'S',
		false,
		false
	);


	//Autre cas de suspension / radiation
	observeDisableFieldsetOnRadioValue(
		'testform',
		 'data[Contratinsertion][raison_ci]',
		$( 'Tableradiation' ),
		'R',
		false,
		false
	);
//         observeDisableFieldsOnRadioValue(
//             'testform',
//             'data[Contratinsertion][raison_ci]',
//             [
//                 'ContratinsertionDateradiationparticulierDay',
//                 'ContratinsertionDateradiationparticulierMonth',
//                 'ContratinsertionDateradiationparticulierYear',
//                 'ContratinsertionAvisraisonRadiationCiD',
//                 'ContratinsertionAvisraisonRadiationCiN',
//                 'ContratinsertionAvisraisonRadiationCiA',
//                 'AutreavisradiationAutreavisradiationEND',
//                 'AutreavisradiationAutreavisradiationRDC',
//                 'AutreavisradiationAutreavisradiationMOA'
//             ],
//             'R',
//             true
//         );
//
//         observeDisableFieldsOnRadioValue(
//             'testform',
//             'data[Contratinsertion][raison_ci]',
//             [
//                 'ContratinsertionAvisraisonSuspensionCiD',
//                 'ContratinsertionAvisraisonSuspensionCiN',
//                 'ContratinsertionAvisraisonSuspensionCiA',
//                 'ContratinsertionDatesuspensionparticulierDay',
//                 'ContratinsertionDatesuspensionparticulierMonth',
//                 'ContratinsertionDatesuspensionparticulierYear',
//                 'AutreavissuspensionAutreavissuspensionEND',
//                 'AutreavissuspensionAutreavissuspensionRDC',
//                 'AutreavissuspensionAutreavissuspensionMOA',
//                 'AutreavissuspensionAutreavissuspensionSTE'
//             ],
//             'S',
//             true
//         );




	<?php
	$ref_id = Set::extract( $this->request->data, 'Contratinsertion.referent_id' );
		echo $this->Ajax->remoteFunction(
			array(
				'update' => 'StructurereferenteRef',
				'url' => array(
					'action' => 'ajaxstruct',
					Set::extract( $this->request->data, 'Contratinsertion.structurereferente_id' )
				)
			)
		).';';
		echo $this->Ajax->remoteFunction(
			array(
				'update' => 'ReferentRef',
				'url' => array(
					'action' => 'ajaxref',
					Set::extract( $this->request->data, 'Contratinsertion.referent_id' )
				)
			)
		).';';
	?>
} );
</script>
<?php /*debug($personne);*/ ?>
<fieldset>
<table class="wide noborder">
	<tr>
		<td class="mediumSize noborder">
			<strong>Statut de la personne : </strong><?php echo Set::extract( $rolepers, Set::extract( $personne, 'Prestation.rolepers' ) ); ?>
			<br />
			<strong>Nom : </strong><?php echo Set::enum( Set::classicExtract( $personne, 'Personne.qual') , $qual ).' '.Set::classicExtract( $personne, 'Personne.nom' );?>
			<br />
			<strong>Prénom : </strong><?php echo Set::classicExtract( $personne, 'Personne.prenom' );?>
			<br />
			<strong>Date de naissance : </strong><?php echo date_short( Set::classicExtract( $personne, 'Personne.dtnai' ) );?>
		</td>
		<td class="mediumSize noborder">
			<strong>N° Service instructeur : </strong>
			<?php
				$libservice = Set::enum( Set::classicExtract( $personne, 'Suiviinstruction.typeserins' ),  $typeserins );
				if( isset( $libservice ) ) {
					echo $libservice;
				}
				else{
					echo 'Non renseigné';
				}
			?>
			<br />
			<strong>N° demandeur : </strong><?php echo Set::classicExtract( $personne, 'Dossier.numdemrsa' );?>
			<br />
			<strong>N° CAF/MSA : </strong><?php echo Set::classicExtract( $personne, 'Dossier.matricule' );?>
			<br />
			<strong>Inscrit au Pôle emploi</strong>
			<?php
				$isPoleemploi = Set::classicExtract( $personne, 'Activite.act' );
				if( $isPoleemploi == 'ANP' )
					echo 'Oui';
				else
					echo 'Non';
			?>
			<br />
			<strong>N° identifiant : </strong><?php echo Set::classicExtract( $personne, 'Personne.idassedic' );?>
		</td>
	</tr>
	<tr>
		<td class="mediumSize noborder">
			<strong>Adresse : </strong><br /><?php echo Set::classicExtract( $personne, 'Adresse.numvoie' ).' '.Set::classicExtract( $personne, 'Adresse.libtypevoie' ).' '.Set::classicExtract( $personne, 'Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $personne, 'Adresse.codepos' ).' '.Set::classicExtract( $personne, 'Adresse.nomcom' );?>
		</td>
		<td class="mediumSize noborder">
			<?php if( Set::extract( $personne, 'Modecontact.0.autorutitel' ) == 'A' ):?>
					<strong>Numéro de téléphone 1 : </strong><?php echo Set::extract( $personne, 'Modecontact.0.numtel' );?>
			<?php endif;?>
			<?php if( Set::extract( $personne, 'Modecontact.1.autorutitel' ) == 'A' ):?>
					<br />
					<strong>Numéro de téléphone 2 : </strong><?php echo Set::extract( $personne, 'Modecontact.1.numtel' );?>
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="mediumSize noborder">
		<?php if( Set::extract( $personne, 'Modecontact.0.autorutiadrelec' ) == 'A' ):?>
			<strong>Adresse mail : </strong><?php echo Set::extract( $personne, 'Modecontact.0.adrelec' );?> <!-- FIXME -->
		<?php endif;?>
		</td>
	</tr>
</table>
</fieldset>

<fieldset>
<legend>Type de Contrat</legend>
	<table class="wide noborder">
		<tr>
			<td class="noborder">
				<?php
					$error = Set::classicExtract( $this->validationErrors, 'Contratinsertion.forme_ci' );
					$class = 'radio'.( !empty( $error ) ? ' error' : '' );

					$thisDataFormeCi = Set::classicExtract( $this->request->data, 'Contratinsertion.forme_ci' );
					if( !empty( $thisDataFormeCi ) ) {
						$valueFormeci = $thisDataFormeCi;
					}
					$input =  $this->Form->input( 'Contratinsertion.forme_ci', array( 'type' => 'radio' , 'options' => $forme_ci, /*'div' => false,*/ 'legend' => required( __d( 'contratinsertion', 'Contratinsertion.forme_ci' )  ), 'value' => $valueFormeci ) );

					echo $this->Xhtml->tag( 'div', $input, array( 'class' => $class ) );
				?>
			</td>
		</tr>
		<tr>
			<td class="noborder" colspan="2">
				<strong>Date d'ouverture du droit ( RMI, API, rSa ) : </strong><?php echo date_short( Set::classicExtract( $personne, 'Dossier.dtdemrsa' ) );?>
			</td>
		</tr>
		<tr>
			<td class="mediumSize noborder">
				<strong>Ouverture de droit ( nombre d'ouvertures ) : </strong><?php echo $numouverturedroit; /*count( Set::extract( $personne, '/Foyer/Dossier/dtdemrsa' ) );*/?>
			</td>
			<td class="mediumSize noborder">
				<strong>rSa majoré</strong>
				<?php
					$soclmajValues = array_unique( Set::extract( $personne, '/Foyer/Dossier/Infofinanciere/natpfcre' ) );
					if( array_intersects( $soclmajValues, array_keys( $soclmaj ) )   )
						echo 'Oui';
					else
						echo 'Non';
				?>
			</td>
		</tr>

		<?php if( Configure::read( 'nom_form_ci_cg' ) == 'cg66' ):?>
			<tr>
				<td class="noborder">
					<?php
						echo $this->Xform->input( 'Contratinsertion.num_contrat', array( 'label' => false , 'type' => 'select', 'options' => $options['num_contrat'], 'empty' => true, 'value' => $tc ) );
//                             echo $tc;
					?>
				</td>
				<td class="noborder">
					<?php
							if( $nbrCi != 0 ) {
								echo '(nombre de renouvellement) : '.( $nbrCi - 1 );
							}
							else {
								echo '(nombre de renouvellement) : 0';
							}
					?>
				</td>
			</tr>
		<?php endif;?>

		<?php if( Configure::read( 'nom_form_ci_cg' ) == 'cg93' ):?>
			<tr>
				<td class="noborder">
					<?php
						echo $this->Xform->input( 'Contratinsertion.num_contrat', array( 'label' => false , 'type' => 'hidden', 'value' => $tc ) );
						echo Set::enum( $tc, $options['num_contrat'] );

					?>
				</td>
				<td class="noborder">
					<?php echo '(nombre de renouvellement) : '.$nbrCi;?>
				</td>
			</tr>
		<?php endif;?>
	</table>
</fieldset>
<fieldset>
	<fieldset class="noborder" id="Contratsuite">
		<table class="wide noborder">
			<tr>
				<td colspan="2" class="noborder center" id="contrat">
					<em>Ce contrat est établi pour : </em>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="noborder">
					<div class="demi"><?php echo $this->Form->input( 'Contratinsertion.type_demande', array( 'label' => 'Raison : ' , 'type' => 'radio', 'div' => false, 'separator' => '</div><div class="demi">', 'options' => $options['type_demande'], 'legend' => false ) );?></div>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="noborder" id="faitsuitea">
		<?php
			echo $this->Xhtml->tag(
				'span',
				$this->Form->input(
					'Contratinsertion.faitsuitea',
					array(
						'type'=>'checkbox',
						'label'=> 'Ce contrat fait suite à'
					)
				)
			);
		?>
	</fieldset>
	<fieldset class="noborder" id="Raisonfaitsuitea">
		<div class="demi">
			<?php echo $this->Form->input( 'Contratinsertion.raison_ci', array( 'label' => 'Raison : ' , 'type' => 'radio', 'div' => false, 'separator' => '</div><div class="demi">', 'options' => $raison_ci, 'legend' => false ) );?>
		</div>
			<table class="wide noborder">
				<tr>
					<td class="noborder">
						<fieldset id="Tablesuspension" class="noborder">
							<table  class="wide noborder">
								<tr>
									<td class="noborder">
										<?php
											if( isset( $situationdossierrsa['Suspensiondroit'][0]['ddsusdrorsa'] ) && !empty( $situationdossierrsa['Suspensiondroit'][0]['ddsusdrorsa'] ) ) {
												echo $this->Xhtml->tag(
													'fieldset',
													'Date de suspension : '.$this->Locale->date( '%d/%m/%Y', $situationdossierrsa['Suspensiondroit'][0]['ddsusdrorsa'] ),
													array(
														'id' => 'dtsuspension',
														'class' => 'noborder'
													)
												);
											}
											else{
												echo 'Date de suspension : '.$this->Form->input( 'Contratinsertion.datesuspensionparticulier', array( 'label' => false, 'type' => 'date' , 'dateFormat' => 'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>date('Y')-1, 'empty' => true ) );
											}
										?>
									</td>
								</tr>
								<tr>
									<td class="noborder">
										<?php
											echo $this->Form->input( 'Contratinsertion.avisraison_suspension_ci', array( 'type' => 'radio', 'separator' => '<br />', 'options' => $avisraison_ci, 'legend' => false,   ) );
										?>
										<fieldset id="Suspensionautre" class="invisible">
											<?php

												$AutreavissuspensionId = Set::classicExtract( $this->request->data, 'Autreavissuspension.id' );
												$ContratinsertionId = Set::classicExtract( $this->request->data, 'Contratinsertion.id' );
												if( $this->action == 'edit' && !empty( $AutreavissuspensionId ) ) {
													echo $this->Form->input( 'Autreavissuspension.id', array( 'type' => 'hidden' ) );
													echo $this->Form->input( 'Autreavissuspension.contratinsertion_id', array( 'type' => 'hidden', 'value' => $ContratinsertionId ) );
												}
												$selected = Set::extract( $this->request->data, '/Autreavissuspension/autreavissuspension' );
												if( empty( $selected ) ){
													$selected = Set::extract( $this->request->data, '/Autreavissuspension/Autreavissuspension' );
												}

												echo $this->Form->input( 'Autreavissuspension.Autreavissuspension', array( 'multiple' => 'checkbox', 'type' => 'select', 'separator' => '<br />', 'options' => $options['autreavissuspension'], 'selected' => $selected, 'label' => false,   ) );
											?>
										</fieldset>
									</td>
								</tr>
							</table>
						</fieldset>
					</td>
					<td class="noborder">
						<fieldset id="Tableradiation" class="noborder">
							<table class="wide noborder">
								<tr>
									<td class="noborder" id="dtradiation">
										<?php
											if( isset( $situationdossierrsa['Situationdossierrsa']['dtclorsa'] ) ) {
												echo $this->Xhtml->tag(
													'span',
													'Date de radiation : '.$this->Locale->date( '%d/%m/%Y', $situationdossierrsa['Situationdossierrsa']['dtclorsa'] ),
													array(
														'id' => 'dtradiation'
													)
												);
											}
											else{
												echo 'Date de radiation'.$this->Form->input( 'Contratinsertion.dateradiationparticulier', array( 'label' => false, 'type' => 'date' , 'dateFormat' => 'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>date('Y')-1, 'empty' => true ) );
											}
										?>
									</td>
								</tr>
								<tr>
									<td class="noborder">
										<?php
											echo $this->Form->input( 'Contratinsertion.avisraison_radiation_ci', array( 'type' => 'radio', 'separator' => '<br />', 'options' => $avisraison_ci, 'legend' => false ) );
										?>
										<fieldset id="Radiationautre" class="invisible">
											<?php

												$AutreavisradiationId = Set::classicExtract( $this->request->data, 'Autreavisradiation.id' );
												$ContratinsertionId = Set::classicExtract( $this->request->data, 'Contratinsertion.id' );
												if( $this->action == 'edit' && !empty( $AutreavisradiationId ) ) {
													echo $this->Form->input( 'Autreavisradiation.id', array( 'type' => 'hidden' ) );
													echo $this->Form->input( 'Autreavisradiation.contratinsertion_id', array( 'type' => 'hidden', 'value' => $ContratinsertionId ) );
												}
												$selected = Set::extract( $this->request->data, '/Autreavisradiation/autreavisradiation' );
												if( empty( $selected ) ){
													$selected = Set::extract( $this->request->data, '/Autreavisradiation/Autreavisradiation' );
												}

												echo $this->Form->input( 'Autreavisradiation.Autreavisradiation', array( 'multiple' => 'checkbox', 'type' => 'select', 'separator' => '<br />', 'options' => $options['autreavisradiation'], 'selected' => $selected, 'label' => false,   ) );
											?>
										</fieldset>
									</td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
			</fieldset>
		</table>
		<table class="noborder">
			<tr>
				<td colspan="2" class="noborder center">
					<em><strong>Lorsque le contrat conditionne l'ouverture du droit, il ne sera effectif qu'après décision <?php echo __d('default'.Configure::read('Cg.departement'), 'du Président du Conseil Général');?></strong></em>
				</td>
			</tr>
		</table>
	</fieldset>


<script type="text/javascript">
document.observe("dom:loaded", function() {

	observeDisableFieldsetOnCheckbox(
		'ContratinsertionFaitsuitea',
		'Raisonfaitsuitea',
		false,
		true
	);
} );
</script>


<fieldset>
<legend>Type d'orientation</legend>
<table class="wide noborder">
	<tr>
		<td class="noborder">
			<?php echo $this->Xform->input( 'Contratinsertion.structurereferente_id', array( 'label' => 'Nom de l\'organisme de suivi', 'type' => 'select', 'options' => $structures, 'selected' => $struct_id, 'empty' => true, 'required' => true ) );?>
			<?php echo $this->Ajax->observeField( 'ContratinsertionStructurereferenteId', array( 'update' => 'StructurereferenteRef', 'url' => array( 'action' => 'ajaxstruct' ) ) ); ?>
		</td>
		<td class="noborder">
			<?php echo $this->Xform->input( 'Contratinsertion.referent_id', array('label' => 'Nom du référent chargé du suivi :', 'type' => 'select', 'options' => $referents, 'empty' => true, 'selected' => $struct_id.'_'.$referent_id ) );?>
			<?php echo $this->Ajax->observeField( 'ContratinsertionReferentId', array( 'update' => 'ReferentRef', 'url' => array( 'action' => 'ajaxref' ) ) ); ?>
		</td>
	</tr>
	<tr>
		<td class="wide noborder"><div id="StructurereferenteRef"></div></td>

		<td class="wide noborder"><div id="ReferentRef"></div></td>
	</tr>
</table>

</fieldset>

<script type="text/javascript">
Event.observe( $( 'ContratinsertionStructurereferenteId' ), 'change', function( event ) {
	$( 'ReferentRef' ).update( '' );
} );
</script>



<fieldset class="loici">
<p>
	Loi N°2008-1249 du 1er Décembre, généralisant le revenu de solidarité active et réformant les politiques d'engagement réciproque : <strong>Contrat librement débattu avec engagements réciproques</strong> ( articles L.263.35 et L.262.36 )<br />
	<strong>Respect du Contrat</strong> ( Article L-262-37 1° et 2° ) :<br />
	<em>"Sauf décision prise au regard de la situation particulière du bénéficiaire, le versement du revenu de solidarité active est suspendu, en tout ou partie, par <?php echo __d('default'.Configure::read('Cg.departement'), 'le Président du Conseil Général');?> :<br />
	lorsque, du fait du bénéficiaire et sans motif légitime, le projet personnalisé d'accès à l'emploi ou l'un des contrats mentionnés aux articles L.262-35 et L.262-36 ne sont pas établis dans les délais prévus ou ne sont pas renouvelés.<br />
	lorsque, sans motif légitime, les dispositions du projet personnalisé d'accès à l'emploi ou les stipulations de l'un des contrats mentionnés aux articles L.262-35 et L.262-36 ne sont pas respectés par le bénéficiaire."<br />
	</em>
	<strong>Lorsque le bénéficiaire ne respecte pas les conditions de ce contrat, l'organisme signataire le signale <?php echo __d('default'.Configure::read('Cg.departement'), 'au Président du Conseil Général');?>.</strong>
</p>
</fieldset>

<!--  CER de la version 2.0rc9 remis en place suite à la demande du CG93 du 09 Mars 2011 -->
<fieldset>
<legend> CONTRATS D'INSERTION </legend>
	<?php echo $this->Form->input( 'Contratinsertion.dd_ci', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.dd_ci' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+10, 'minYear'=>date('Y')-10 , 'empty' => true)  );?>
	<?php echo $this->Form->input( 'Contratinsertion.duree_engag', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.duree_engag' ) ), 'type' => 'select', 'options' => $duree_engag, 'empty' => true )  ); ?>
	<?php echo $this->Form->input( 'Contratinsertion.df_ci', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.df_ci' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+10, 'minYear'=>date('Y')-10 , 'empty' => true ) ) ;?>
</fieldset>

<fieldset >
<legend> FORMATION ET EXPERIENCE </legend>
<?php echo $this->Form->input( 'Dsp.id', array( 'label' => false, 'div' => false,  'type' => 'hidden' ) );?>
<?php echo $this->Form->input( 'Dsp.personne_id', array( 'label' => false, 'div' => false, /*'value' => Set::classicExtract( $personne, 'Personne.id' ), */ 'type' => 'hidden' ) );?>
<?php echo $this->Form->input( 'Dsp.nivetu', array( 'label' => __d( 'dsp', 'Dsp.nivetu' ), 'options' => $nivetus, 'empty' => true ) );?>
<?php echo $this->Form->input( 'Contratinsertion.diplomes', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.diplomes' ), 'type' => 'textarea', 'rows' => 3)  ); ?>
<?php echo $this->Form->input( 'Contratinsertion.expr_prof', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.expr_prof' ), 'type' => 'textarea', 'rows' => 3)  ); ?>
<?php echo $this->Form->input( 'Contratinsertion.form_compl', array( 'label' =>  __d( 'contratinsertion', 'Contratinsertion.form_compl' ), 'type' => 'textarea', 'rows' => 3)  ); ?>
</fieldset>
<fieldset>
<legend> PARCOURS D'INSERTION ANTERIEUR </legend>
	<?php
		echo $this->Widget->booleanRadio( 'Contratinsertion.actions_prev', array( 'legend' => required( __d( 'contratinsertion', 'Contratinsertion.actions_prev' ) ) ) );
	?>

	<?php echo $this->Form->input( 'Contratinsertion.obsta_renc', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.obsta_renc' ), 'type' => 'textarea', 'rows' => 3)  ); ?>
</fieldset>
<fieldset>
<legend> PROJET ET ACTIONS D'INSERTION </legend>
	<?php echo $this->Form->input( 'Contratinsertion.objectifs_fixes', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.objectifs_fixes' ), 'type' => 'textarea', 'rows' => 3)  ); ?>

	<?php
		echo $this->Form->input( 'Action.id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Action.code', array( 'label' => __d( 'action', 'Action.code_action' ), 'type' => 'text', 'empty' => true, 'maxlength' => 2 )  );
		echo $this->Form->input( 'Contratinsertion.engag_object', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.engag_object' ), 'type' => 'select', 'options' => $actions, 'empty' => true )  );
	?>
	<?php
		///FIXME
		$contratinsertion_id = Set::extract( $this->request->data, 'Actioninsertion.contratinsertion_id' );
		if( $this->action == 'edit' && !empty( $contratinsertion_id ) ) :?>
		<?php echo $this->Form->input( 'Actioninsertion.contratinsertion_id', array( 'label' => false, 'div' => false,  'type' => 'hidden' ) );?>
	<?php endif;?>
	<?php
		echo $this->Form->input( 'Actioninsertion.dd_action', array( 'label' => __d( 'action', 'Action.dd_action' ), 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear'=>date('Y')+10, 'minYear'=>date('Y')-10 , 'empty' => true )  );
		echo $this->Form->input( 'Contratinsertion.commentaire_action', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.commentaire_action' ), 'type' => 'textarea', 'rows' => 3 )  );
	?>
	<?php
		echo $this->Widget->booleanRadio( 'Contratinsertion.emp_trouv', array( 'legend' => required( __d( 'contratinsertion', 'Contratinsertion.emp_trouv' ) )) );
	?>
	Si oui, veuillez préciser :
	<?php echo $this->Form->input( 'Contratinsertion.sect_acti_emp', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.sect_acti_emp' ) ), 'type' => 'select', 'options' => $sect_acti_emp, 'empty' => true )  ); ?>
	<?php echo $this->Form->input( 'Contratinsertion.emp_occupe', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.emp_occupe' ) ), 'type' => 'select', 'options' => $emp_occupe, 'empty' => true )  ); ?>
	<?php echo $this->Form->input( 'Contratinsertion.duree_hebdo_emp', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.duree_hebdo_emp' ) ), 'type' => 'select', 'options' => $duree_hebdo_emp, 'empty' => true )  ); ?>
	<?php echo $this->Form->input( 'Contratinsertion.nat_cont_trav', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.nat_cont_trav' ) ), 'type' => 'select', 'options' => $nat_cont_trav, 'empty' => true )  ); ?>
	<?php echo $this->Form->input( 'Contratinsertion.duree_cdd', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.duree_cdd' ) ), 'type' => 'select', 'options' => $duree_cdd, 'empty' => true )  ); ?>
</fieldset>

<fieldset>
	<?php echo $this->Form->input( 'Contratinsertion.nature_projet', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.nature_projet' ) ), 'type' => 'textarea', 'rows' => 6)  ); ?>
	<?php echo $this->Form->input( 'Contratinsertion.lieu_saisi_ci', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.lieu_saisi_ci' ) ), 'type' => 'text', 'maxlength' => 50 )  ); ?><br />
	<?php echo $this->Form->input( 'Contratinsertion.date_saisi_ci', array( 'label' => required( __d( 'contratinsertion', 'Contratinsertion.date_saisi_ci' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+10, 'minYear'=>date('Y')-10 , 'empty' => true)  ); ?>
	<?php
		echo $this->Xhtml->tag(
			'div',
			$this->Xhtml->tag( 'span', 'Le bénéficiaire :', array( 'class' => 'label' ) ).
			$this->Xhtml->tag( 'span', $personne['Personne']['nom'].' '.$personne['Personne']['prenom'], array( 'class' => 'input' ) ),
			array( 'class' => 'input text' )
		);
	?>
</fieldset>

<script type="text/javascript">
document.observe( "dom:loaded", function() {
	Event.observe( $( 'ActionCode' ), 'keyup', function() {
		var value = $F( 'ActionCode' );
		if( value.length == 2 ) { // FIXME: in_array
			$$( '#ContratinsertionEngagObject option').each( function ( option ) {
				if( $( option ).value == value ) {
					$( option ).selected = 'selected';
				}
			} );
		}
	} );

	observeDisableFieldsOnRadioValue(
		'testform',
		'data[Contratinsertion][actions_prev]',
		[ 'ContratinsertionObstaRenc' ],
		'N',
		true
	);

	observeDisableFieldsOnRadioValue(
		'testform',
		'data[Contratinsertion][emp_trouv]',
		[ 'ContratinsertionSectActiEmp', 'ContratinsertionEmpOccupe', 'ContratinsertionDureeHebdoEmp', 'ContratinsertionNatContTrav', 'ContratinsertionDureeCdd' ],
		'O',
		true
	);

	observeDisableFieldsOnValue(
		'ContratinsertionNatContTrav',
		[ 'ContratinsertionDureeCdd' ],
		'TCT3',
		false
	);
} );
</script>

<fieldset class="cnilci">
<p>
	<em>Conformément à la loi "Informatique et liberté" n°78-17 du 06 janvier 1978 relative à l'informatique, aux fichiers et aux libertés nous nous engageons à prendre toutes les précautions afin de préserver la sécurité de ces informations et notamment empêcher qu'elles soient déformées, endommagées ou communiquées à des tiers. Les coordonnées informations liées à l'adresse, téléphone et mail seront utilisées uniquement pour permettre la prise de contact, dans le cadre du parcours d'engagement réciproque.</em>
</p>
</fieldset>
<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>
