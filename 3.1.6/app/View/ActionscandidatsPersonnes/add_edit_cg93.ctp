<?php
	$domain = "actioncandidat_personne_".Configure::read( 'ActioncandidatPersonne.suffixe' );
	$this->pageTitle = __d( $domain, "ActionscandidatsPersonnes::{$this->action}" );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<?php
	echo $this->Xhtml->tag( 'h1', $this->pageTitle, array( 'class' => 'aere' ) );

	echo $this->Xhtml->tag(
		'p',
		'La fiche de prescription est un document conventionnel partagé qui engage tous les acteurs du PDI',
		array(
			'class' => 'remarque'
		)
	);

	echo $this->Xform->create( 'ActioncandidatPersonne', array( 'id' => 'candidatureform' ) );
	if( Set::check( $this->request->data, 'ActioncandidatPersonne.id' ) ){
		echo $this->Xform->input( 'ActioncandidatPersonne.id', array( 'type' => 'hidden' ) );
	}
?>
<fieldset class="actioncandidat">
	<legend class="actioncandidat" >Prescripteur / Référent</legend>
	<?php
		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.ddaction' => array( 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2, 'empty' => false ),
				'ActioncandidatPersonne.referent_id' => array( 'value' => $referentId ),
				'Personne.id' => array( 'value' => $personne_id, 'type' => 'hidden' )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);

		///Ajax pour les données du référent et de l'organisme auquel il est lié
		echo $this->Ajax->observeField( 'ActioncandidatPersonneReferentId', array( 'update' => 'ActioncandidatPersonneStructurereferente', 'url' => array( 'action' => 'ajaxstruct' ) ) );


		echo $this->Xhtml->tag(
			'div',
			'<b></b>',
			array(
				'id' => 'ActioncandidatPersonneStructurereferente'
			)
		);

		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.motifdemande' => array( 'required' => true, 'domain' => $domain )
			)
		);
	?>
</fieldset>
<fieldset id="infocandidat">
	<legend><strong>Personne orientée / allocataire</strong></legend>
	<table class="wide noborder">
		<tr>
			<td class="mediumSize noborder">
				<strong>Statut de la personne : </strong><?php echo Set::extract( $options['Prestation']['rolepers'], Set::extract( $personne, 'Prestation.rolepers' ) ); ?>
				<br />
				<strong>Nom : </strong><?php echo Set::enum( Set::classicExtract( $personne, 'Personne.qual') , $options['Personne']['qual'] ).' '.Set::classicExtract( $personne, 'Personne.nom' );?>
				<br />
				<strong>Prénom : </strong><?php echo Set::classicExtract( $personne, 'Personne.prenom' );?>
				<br />
				<strong>Date de naissance : </strong><?php echo date_short( Set::classicExtract( $personne, 'Personne.dtnai' ) );?>
				<br />
				<strong>Adresse : </strong><br /><?php echo Set::classicExtract( $personne, 'Adresse.numvoie' ).' '.Set::classicExtract( $personne, 'Adresse.libtypevoie' ).' '.Set::classicExtract( $personne, 'Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $personne, 'Adresse.codepos' ).' '.Set::classicExtract( $personne, 'Adresse.nomcom' );?>
			</td>
			<td class="mediumSize noborder">
				<strong>N° Service instructeur : </strong>
				<?php
					$libservice = Set::enum( Set::classicExtract( $personne, 'Suiviinstruction.typeserins' ),  $options['Suiviinstruction']['typeserins'] );
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
				<strong>Bénéficiaire du RSA depuis le (entrée dans le dispositif RSA/RMI): </strong><?php echo date_short( Set::classicExtract( $personne, 'Dossier.dtdemrsa' ) );?>
				<br />
				<strong>N° CAF/MSA : </strong><?php echo Set::classicExtract( $personne, 'Dossier.matricule' );?>
				<br />
				<?php
					if( Hash::get( $personne, 'Historiqueetatpe.etat' ) == 'inscription' ) {
						$inscritPe = 'Oui';
						$identifiantPe = Hash::get( $personne, 'Historiqueetatpe.identifiantpe' );
					}
					else {
						$inscritPe = 'Non';
						$identifiantPe = null;
					}
				?>
				<strong>Inscrit au Pôle emploi</strong>
				<?php echo h( $inscritPe );?>
				<br />
				<strong>N° identifiant : </strong><?php echo h( $identifiantPe );?>
				<br />
				<br />
				<strong>Chargé d'insertion: </strong><?php echo Set::classicExtract( $personne, 'Referent.nom_complet' );?>
				<br />
				<strong>N° de téléphone du chargé d'insertion: </strong><?php echo Set::classicExtract( $personne, 'Referent.numero_poste' );?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="mediumSize noborder">
				<strong>Tél. fixe : </strong>
				<?php
					$numtelfixe = Set::classicExtract( $personne, 'Personne.numfixe' );
					if( !empty( $numtelfixe ) ) {
						echo Set::extract( $personne, 'Personne.numfixe' );
					}
					else{
						echo $this->Xform->input( 'Personne.numfixe', array( 'label' => false, 'type' => 'text' ) );

					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="mediumSize noborder">
                <strong>Tél. portable : </strong>
				<?php
					$numtelport = Set::extract( $personne, 'Personne.numport' );
					if( !empty( $numtelport ) ) {
						echo Set::extract( $personne, 'Personne.numport' );
					}
					else{
						echo $this->Xform->input( 'Personne.numport', array( 'label' => false, 'type' => 'text' ) );
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="mediumSize noborder">
				<strong>Adresse mail : </strong>
				<?php
					$email = Set::extract( $personne, 'Personne.email' );
					if( !empty( $email ) ) {
						echo Set::extract( $personne, 'Personne.email' );
					}
					else{
						echo $this->Xform->input( 'Personne.email', array( 'label' => false, 'type' => 'text' ) );
					}
				?>
			</td>
		</tr>
		<tr>
			<td class="mediumSize noborder" colspan="2">
				<strong>Bénéficiaire du : </strong>
				<?php
					$socle = Hash::get( $personne, 'Detailcalculdroitrsa.natpf_socle' );
					$activite = Hash::get( $personne, 'Detailcalculdroitrsa.natpf_activite' );
					$majore = Hash::get( $personne, 'Detailcalculdroitrsa.natpf_majore' );

					echo 'RSA Socle: '.$this->Xhtml->boolean( $socle );
					echo ', RSA majoré: '.$this->Xhtml->boolean( $majore );
					echo ', RSA socle + activité: '.$this->Xhtml->boolean( $socle && $activite );
				?>
			</td>
		</tr>
		<tr>
			<td class="mediumSize noborder">
				<strong>Niveau de diplôme et/ou qualification : </strong>
			</td>
			<td class="mediumSize noborder">
				<?php echo value( $options['Cer93']['nivetu'], Hash::get( $personne, 'Cer93.nivetu' ) ); ?>
			</td>
		</tr>
		<tr>
			<td class="mediumSize noborder">
				<strong>Contrat d’engagement réciproque (CER) : </strong>
				<?php echo value( $options['Cer93']['positioncer'], Hash::get( $personne, 'Cer93.positioncer' ) ); ?>
			</td>
			<td class="mediumSize noborder">
				<strong>Du : </strong>
				<?php echo date_short( Hash::get( $personne, 'Contratinsertion.dd_ci' ) ); ?>
				<strong>Au : </strong>
				<?php echo date_short( Hash::get( $personne, 'Contratinsertion.df_ci' ) ); ?>
			</td>
		</tr>
	</table>
</fieldset>

<fieldset class="actioncandidat">
	<legend class="actioncandidat" >Partenaire / Prestataire</legend>
	<?php
		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.rendezvouspartenaire' => array( 'type' => 'hidden', 'value'=> '0' ),
				'ActioncandidatPersonne.horairerdvpartenaire' => array(
					'type' => 'datetime',
					'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2,
					'timeFormat'=>24, 'interval' => 5,
					'empty' => true,
					'required' => false
				),
			),
			array(
				'options' => $options,
				'domain' => $domain
			)
		);

		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.pieceallocataire' => array( 'legend' => 'L\'allocataire est invité à se munir : ', 'type' => 'radio', 'separator' => '<br />', 'options' => $options['ActioncandidatPersonne']['pieceallocataire'] ),
				'ActioncandidatPersonne.autrepiece' => array( 'label' => false )
			)
		);

	?>
</fieldset>

<fieldset class="actioncandidat">
	<legend class="actioncandidat" >Effectivité de la prescription</legend>
	<?php
		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.bilanvenu' => array( 'type' => 'radio', 'separator' => '<br />',  'legend' => 'La personne s\'est présentée' ),
				'ActioncandidatPersonne.bilanrecu' => array( 'type' => 'radio', 'separator' => '<br />',  'legend' => 'La personne a été reçue' ),
				'ActioncandidatPersonne.daterecu' => array( 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2, 'empty' => true ),
				'ActioncandidatPersonne.personnerecu' => array( 'type' => 'text' ),
				'ActioncandidatPersonne.presencecontrat' => array( 'type' => 'select', 'label' => 'Avec son contrat d\'Engagement Réciproque' ),
				'ActioncandidatPersonne.bilanretenu' => array( 'type' => 'radio', 'separator' => '<br />', 'legend' => 'La personne a été retenue' )
			),
			array(
				'options' => $options,
				'domain' => $domain
			)
		);

		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.personne_id' => array( 'value' => $personne_id, 'type' => 'hidden' ),
				'ActioncandidatPersonne.actioncandidat_id' => array( 'type' => 'select', 'options' => $actionsfiche )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);

		///Ajax pour les données de l'action entreprise et de son partenaire lié
		echo $this->Ajax->observeField( 'ActioncandidatPersonneActioncandidatId', array( 'update' => 'ActioncandidatPartenairePartenaireId', 'url' => array( 'action' => 'ajaxpart' ) ) );
		echo $this->Xhtml->tag(
			'div',
			'<b></b>',
			array(
				'id' => 'ActioncandidatPartenairePartenaireId',
				'class' => 'aere'
			)
		);

		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.integrationaction' => array( 'type' => 'radio', 'separator' => '<br />',  'legend' => 'La personne a intégré l\'action ?' ),
				'ActioncandidatPersonne.precisionmotif',
				'ActioncandidatPersonne.dfaction' => array( 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2, 'empty' => true )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);
	?>
</fieldset>

<fieldset>
	<?php
		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.datesignature' => array( 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2, 'empty' => false )
			),
			array(
				'domain' => $domain
			)
		);
	?>
</fieldset>

<div class="submit">
	<?php
		echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Xform->end();?>


<script type="text/javascript">
	document.observe( "dom:loaded", function() {



		<?php
			echo $this->Ajax->remoteFunction(
				array(
					'update' => 'ActioncandidatPartenairePartenaireId',
					'url' => array( 'action' => 'ajaxpart', Set::extract( $this->request->data, 'ActioncandidatPersonne.actioncandidat_id' ) )
				)
			).';';

// 			echo $this->Ajax->remoteFunction(
// 				array(
// 					'update' => 'ActioncandidatPersonneStructurereferente',
// 					'url' => array( 'action' => 'ajaxstruct', Set::extract( $this->request->data, 'ActioncandidatPersonne.referent_id' ) )
// 				)
// 			).';';

// 			echo $this->Ajax->remoteFunction(
// 				array(
// 					'update' => 'StructureData',
// 					'url' => array( 'action' => 'ajaxreffonct', Set::extract( $this->request->data, 'Rendezvous.referent_id' ) )
// 				)
// 			).';';
		?>


		<?php
			if( ( $this->action == 'add' ) && !empty( $referentId ) ) {
				echo $this->Ajax->remoteFunction(
					array(
						'update' => 'ActioncandidatPersonneStructurereferente',
						'url' => array( 'action' => 'ajaxstruct', $referentId )
					)
				);
			}
			else {

				echo $this->Ajax->remoteFunction(
					array(
						'update' => 'ActioncandidatPersonneStructurereferente',
						'url' => array( 'action' => 'ajaxstruct', Set::extract( $this->request->data, 'ActioncandidatPersonne.referent_id' ) )
					)
				);
			}
		?>

			dependantSelect(
				'RendezvousReferentId',
				'RendezvousStructurereferenteId'
			);

		///Bilan si personne reçue
		observeDisableFieldsOnRadioValue(
			'candidatureform',
			'data[ActioncandidatPersonne][bilanrecu]',
			[
				'ActioncandidatPersonneDaterecuDay',
				'ActioncandidatPersonneDaterecuMonth',
				'ActioncandidatPersonneDaterecuYear',
				'ActioncandidatPersonnePersonnerecu',
				'ActioncandidatPersonnePresencecontrat'
			],
			'N',
			false
		);

		///Bilan si personne reçue
		observeDisableFieldsOnRadioValue(
			'candidatureform',
			'data[ActioncandidatPersonne][pieceallocataire]',
			[
				'ActioncandidatPersonneAutrepiece'
			],
			'AUT',
			true
		);

	} );
</script>