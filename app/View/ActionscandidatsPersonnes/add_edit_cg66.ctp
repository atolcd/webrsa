<?php
	$domain = "actioncandidat_personne_".Configure::read( 'ActioncandidatPersonne.suffixe' );
	$this->pageTitle = __d( $domain, "ActionscandidatsPersonnes::{$this->action}" );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>


<?php
	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	echo $this->Xform->create( 'ActioncandidatPersonne', array( 'id' => 'candidatureform' ) );
	if( Set::check( $this->request->data, 'ActioncandidatPersonne.id' ) ){
		echo $this->Xform->input( 'ActioncandidatPersonne.id', array( 'type' => 'hidden' ) );
	}
?>
<fieldset id="infocandidature">
	<legend>Informations de candidature</legend>
	<?php

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

        echo '<fieldset id="formationregion" class="noborder">';
        echo $this->Default->subform(
            array(
//                'Progfichecandidature66.Progfichecandidature66' => array( 'label' => __d( 'progfichecandidature66', 'Progfichecandidature66.name' ), 'type' => 'select', 'multiple' => 'checkbox', 'empty' => false, 'options' => $progsfichescandidatures66 ),
                'ActioncandidatPersonne.progfichecandidature66_id' => array( 'label' => __d( 'progfichecandidature66', 'Progfichecandidature66.name' ), 'type' => 'radio', 'empty' => false, 'options' => $progsfichescandidatures66 ),
				'ActioncandidatPersonne.valprogfichecandidature66_id',
                'ActioncandidatPersonne.formationregion',
                'ActioncandidatPersonne.nomprestataire'
            ),
            array(
                'options' => $options,
                'domain' => $domain
            )
        );
        echo '</fieldset>';


		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.referent_id' => array( 'value' => $referentId ),
				'Personne.id' => array( 'value' => $personne_id, 'type' => 'hidden' ),
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);

		echo $this->Ajax->observeField( 'ActioncandidatPersonneActioncandidatId', array( 'update' => 'ActioncandidatPartenairePartenaireId', 'url' => array( 'action' => 'ajaxpart' ) ) );

		echo $this->Xhtml->tag(
			'div',
			' ',
			array(
				'id' => 'ActioncandidatPartenairePartenaireId'
			)
		);

		echo $this->Xhtml->tag(
			'div',
			' ',
			array(
				'id' => 'ActioncandidatPrescripteurReferentId'
			)
		);

		echo $this->Ajax->observeField( 'ActioncandidatPersonneReferentId', array( 'update' => 'ActioncandidatPrescripteurReferentId', 'url' => array( 'action' => 'ajaxreferent' ) ) );

	?>
</fieldset>
<fieldset id="infocandidat">
	<legend>Informations du candidat</legend>
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
				<strong>N° CAF/MSA : </strong><?php echo Set::classicExtract( $personne, 'Dossier.matricule' );?>
				<br />
				<strong>Date d'ouverture du droit : </strong><?php echo date_short( Hash::get( $personne, 'Dossier.dtdemrsa' ) );?>
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
			</td>
		</tr>
		<tr>
			<td class="mediumSize noborder">
				<strong>Adresse : </strong><br /><?php echo Set::classicExtract( $personne, 'Adresse.numvoie' ).' '.Set::classicExtract( $personne, 'Adresse.libtypevoie' ).' '.Set::classicExtract( $personne, 'Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $personne, 'Adresse.codepos' ).' '.Set::classicExtract( $personne, 'Adresse.nomcom' );?>
			</td>
		<tr>
			<td colsapn="2" class="mediumSize noborder">
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
	</table>
</fieldset>

	<fieldset id="poursuitesuivi"><legend></legend>
	<?php
		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.poursuitesuivicg' => array( 'type' => 'checkbox' , 'label' => 'Poursuite suivi CG' )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);
	?>
	</fieldset>
<fieldset id="motifdemande">
	<legend><?php echo required( "Motif de la demande (donner des précisions sur le parcours d'insertion et les motifs de la prescription)" ); ?></legend>
		<?php
			echo $this->Default->subform(
				array(
					'ActioncandidatPersonne.motifdemande' => array( 'label' => false, 'required' => false )
				),
				array(
					'domain' => $domain
				)
			);
		?>
</fieldset>
<fieldset id="mobilite">
	<legend>Mobilité</legend>
	<?php
		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.mobile' => array( 'type' => 'radio' , 'legend' => 'Etes-vous mobile ?', 'div' => false, 'options' => array( '0' => 'Non', '1' => 'Oui' ) ),
//				'ActioncandidatPersonne.naturemobile' => array( 'label' => 'Nature de la mobilité', 'options' => $options['ActioncandidatPersonne']['naturemobile'], 'empty' => true ),
//				'ActioncandidatPersonne.typemobile'=> array( 'label' => 'Type de mobilité ' ),
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);
	?>
    <fieldset id="sous_mobilite" class="noborder">
        <legend></legend>
        <?php
            echo $this->Default->subform(
                array(
                    'ActioncandidatPersonne.naturemobile' => array( 'label' => 'Nature de la mobilité', 'options' => $options['ActioncandidatPersonne']['naturemobile'], 'empty' => true ),
                    'ActioncandidatPersonne.typemobile'=> array( 'label' => 'Type de mobilité ' ),
                ),
                array(
                    'domain' => $domain,
                    'options' => $options
                )
            );
        ?>
    </fieldset>
</fieldset>

<fieldset id="rdv">
	<legend>Rendez-vous</legend>
	<?php
		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.rendezvouspartenaire' => array( 'type' => 'radio' , 'legend' => 'Rendez-vous', 'div' => false, 'options' => array( '0' => 'Non', '1' => 'Oui' ) )
			),
			array(
				'options' => $options,
				'domain' => $domain
			)
		);
	?>
    <fieldset id="sous_rendezvous" class="noborder">
        <legend></legend>
        <?php
            echo $this->Default->subform(
                array(
                    'ActioncandidatPersonne.horairerdvpartenaire' => array(
                        'type' => 'datetime',
                        'label' => 'Rendez-vous fixé le ',
                        'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2,
                        'timeFormat' => 24,
                        'hourRange' => array( 8, 19 ),
                        'interval' => 5,
                        'empty' => true
                    ),
                    'ActioncandidatPersonne.lieurdvpartenaire' => array( 'type' => 'text' , 'label' => 'Lieu du rendez-vous ' )
                ),
                array(
                    'domain' => $domain,
                    'options' => $options
                )
            );
        ?>
    </fieldset>
</fieldset>

<fieldset id="engagement" class="loici">
	<p>
		<strong>Engagement:</strong><br />
		<em>Je m’engage à me rendre disponible afin d’être présent à la prestation ou au rendez vous qui me sera fixé. En cas de force majeure, je m’engage à prévenir le référent chargé de mon suivi.<br />
		Je suis informé(e) que dans le cas où je ne donnerai pas suite à ce rendez-vous sans motif valable, <strong>cela pourra entraîner la suspension du versement de mon allocataion rSa</strong>.<br />
		</em>
	</p>
	<?php
		echo $this->Default->subform(
			array(
				'ActioncandidatPersonne.datesignature' => array( 'dateFormat' => 'DMY', 'empty' => false )
			),
			array(
				'domain' => $domain
			)
		);
	?>
</fieldset>

<?php if( $this->action == 'edit' ):?>

	<p class="center"><em><strong>A remplir par le partenaire :</strong></em></p>
	<fieldset class="partenaire bilan">
		<?php
			echo $this->Xhtml->tag(
				'dl',
				'Bilan d\'accueil : '.REQUIRED_MARK
			);

			echo $this->Default->subform(
				array(
					'ActioncandidatPersonne.bilanvenu' => array( 'required' => true, 'type' => 'radio', 'separator' => '<br />',  'legend' => false, 'style' => 'padding:0;' ),
					'ActioncandidatPersonne.bilanretenu' => array( 'required' => true, 'type' => 'radio', 'separator' => '<br />', 'legend' => false ),
				),
				array(
					'domain' => $domain,
					'options' => $options
				)
			);

			echo $this->Default->subform(
				array(
					'ActioncandidatPersonne.infocomplementaire',
					'ActioncandidatPersonne.datebilan' => array( 'dateFormat' => 'DMY', 'empty' => false )
				),
				array(
					'domain' => $domain,
					'options' => $options
				)
			);
		?>
	</fieldset>
	<fieldset id="blocsortie">
		<?php
			echo $this->Default2->subform(
				array(
					'ActioncandidatPersonne.issortie' => array( 'label' =>  'Sortie', 'type' => 'checkbox' ),
					),
				array(
					'options' => $options
				)
			);
		?>
		<fieldset id="issortie" class="invisible">
			<?php
				echo $this->Default->subform(
					array(
						'ActioncandidatPersonne.sortiele' => array(  'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2  ),
						'ActioncandidatPersonne.motifsortie_id' => array( 'type' => 'select', 'empty' => true )
					),
					array(
						'domain' => $domain,
						'options' => $options
					)
				);
			?>
		</fieldset>
	</fieldset>


<?php endif;?>
<div class="submit">
	<?php
		echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Xform->end();?>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {


		// Mise en disabled des champs lors du passage du formulaire en édition

		<?php
			echo $this->Ajax->remoteFunction(
				array(
					'update' => 'ActioncandidatPartenairePartenaireId',
					'url' => array( 'action' => 'ajaxpart', Set::extract( $this->request->data, 'ActioncandidatPersonne.actioncandidat_id' ) )
				)
			);
		?>;
		<?php
			if( ( $this->action == 'add' ) && !empty( $referentId ) ) {
				echo $this->Ajax->remoteFunction(
					array(
						'update' => 'ActioncandidatPrescripteurReferentId',
						'url' => array( 'action' => 'ajaxreferent', $referentId )
					)
				);
			}
			else {
				echo $this->Ajax->remoteFunction(
					array(
						'update' => 'ActioncandidatPrescripteurReferentId',
						'url' => array( 'action' => 'ajaxreferent', Set::extract( $this->request->data, 'ActioncandidatPersonne.referent_id' ) )
					)
				);
			}
		?>

        observeDisableFieldsetOnRadioValue(
            'candidatureform',
            'data[ActioncandidatPersonne][mobile]',
            $( 'sous_mobilite' ),
            '1',
            false,
            true
        );

        observeDisableFieldsetOnRadioValue(
            'candidatureform',
            'data[ActioncandidatPersonne][rendezvouspartenaire]',
            $( 'sous_rendezvous' ),
            '1',
            false,
            true
        );

        // On affiche le la case à cocher poursuite suivi CG si l'ID
        // de l'action sélectionnée fait partie des IDs actions paramétrés
        observeDisableFieldsetOnValue(
			'ActioncandidatPersonneActioncandidatId',
			$( 'poursuitesuivi' ),
			['<?php echo implode( "', '", Configure::read( "ActioncandidatPersonne.Actioncandidat.typeregionPoursuitecgId" ) );?>'],
			false,
			true
		);

       // On affiche le fieldset Mobilité uniquement si l'action sélectionnée
        // ne fait pas partie des IDs actions paramétrés
        observeDisableFieldsetOnValue(
			'ActioncandidatPersonneActioncandidatId',
			$( 'rdv' ),
			['<?php echo implode( "', '", array_diff( array_keys( $actionsfiche ), Configure::read( "ActioncandidatPersonne.Actioncandidat.typeregionId" ) ) );?>'],
			false,
            true
		);


        // On affiche le fieldset RDV uniquement si l'action sélectionnée
        // ne fait pas partie des IDs actions paramétrés
        observeDisableFieldsetOnValue(
			'ActioncandidatPersonneActioncandidatId',
			$( 'mobilite' ),
			['<?php echo implode( "', '", array_diff( array_keys( $actionsfiche ), Configure::read( "ActioncandidatPersonne.Actioncandidat.typeregionId" ) ) );?>'],
			false,
            true
		);


//		observeDisableFieldsOnRadioValue(
//			'candidatureform',
//			'data[ActioncandidatPersonne][rendezvouspartenaire]',
//			[
//                'ActioncandidatPersonneHorairerdvpartenaireDay',
//                'ActioncandidatPersonneHorairerdvpartenaireMonth',
//                'ActioncandidatPersonneHorairerdvpartenaireYear',
//                'ActioncandidatPersonneHorairerdvpartenaireHour',
//                'ActioncandidatPersonneHorairerdvpartenaireMin',
//                'ActioncandidatPersonneLieurdvpartenaire',
//                'ActioncandidatPersonnePersonnerdvpartenaire'
//			],
//			[undefined,null,''],
//			false
//		);
//
//		observeDisableFieldsOnRadioValue(
//			'candidatureform',
//			'data[ActioncandidatPersonne][mobile]',
//			[
//				'ActioncandidatPersonneTypemobile',
//				'ActioncandidatPersonneNaturemobile'
//			],
//			[undefined, null, ''],
//			false
//		);

        // On affiche le la case à cocher poursuite suivi CG si l'ID
        // de l'action sélectionnée fait partie des IDs actions paramétrés
        observeDisableFieldsetOnValue(
			'ActioncandidatPersonneActioncandidatId',
			$( 'formationregion' ),
			['<?php echo implode( "', '", Configure::read( "ActioncandidatPersonne.Actioncandidat.typeregionId" ) );?>'],
			false,
			true
		);

		<?php  if( $this->action == 'edit' ):?>

			observeDisableFieldsetOnRadioValue(
				'candidatureform',
				'data[ActioncandidatPersonne][bilanvenu]',
				$( 'blocsortie' ),
				'VEN',
				false,
				true
			);

			observeDisableFieldsetOnRadioValue(
				'candidatureform',
				'data[ActioncandidatPersonne][bilanretenu]',
				$( 'blocsortie' ),
				'RET',
				false,
				true
			);

			observeDisableFieldsetOnCheckbox(
				'ActioncandidatPersonneIssortie',
				'issortie',
				false,
				true
			);

			observeDisableFieldsOnRadioValue(
				'candidatureform',
				'data[ActioncandidatPersonne][bilanvenu]',
				[
					'ActioncandidatPersonneBilanretenu_',
					'ActioncandidatPersonneBilanretenuRET',
					'ActioncandidatPersonneBilanretenuNRE'
				],
				'VEN',
				true
			);

		<?php endif;?>

		/**
		 * Retourne l'id du nom du programme Région
		 * @returns {string}
		 */
		function getProgrammeValue() {
			var value = null;
			$$('#formationregion input[name="data[ActioncandidatPersonne][progfichecandidature66_id]"]').each(function(element){
				if (element.getValue() !== null && element.getValue() !== '') {
					value = element.getValue();
					throw $break;
				}
			});
			
			return value;
		}
		
		/**
		 * Cache les options dans Valeur de l'action région qui ne correspondent pas au programme choisi
		 * @param {string} idProgramme
		 */
		function hideValeurProgramme( idProgramme ) {
			$('ActioncandidatPersonneValprogfichecandidature66Id').select('option').each(function(option){
				if (option.value === '' || option.value.indexOf(idProgramme+'_') === 0) {
					option.show();
				}
				else {
					option.hide();
				}
			});
		}
		
		hideValeurProgramme( getProgrammeValue() );
		$$('#formationregion input[name="data[ActioncandidatPersonne][progfichecandidature66_id]"]').each(function(element){
			element.observe('change', function(){
				$('ActioncandidatPersonneValprogfichecandidature66Id').setValue('');
				hideValeurProgramme( getProgrammeValue() );
			});
		});
	} );
</script>
<!--/************************************************************************/ -->