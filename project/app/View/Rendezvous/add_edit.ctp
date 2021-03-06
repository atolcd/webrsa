<?php
    if(!isset($messages))
        $messages   =   array();
    if($alertTrancheAge) {
        $messages[__d('rendezvous.po', 'Rendezvous::Alert55Ans')] = 'error';
    }

	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout Rendez-vous';
	}
	else {
		$this->pageTitle = 'Édition Rendez-vous';
	}

	if( Configure::read( 'debug' ) ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	echo $this->element( 'required_javascript' );

	$departement = Configure::read( 'Cg.departement' );
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		dependantSelect( 'RendezvousPermanenceId', 'RendezvousStructurereferenteId' );
		dependantSelect( 'RendezvousReferentId', 'RendezvousStructurereferenteId' );

		<?php
			echo $this->Ajax->remoteFunction(
				array(
					'update' => 'ReferentFonction',
					'url' => array(
						'action' => 'ajaxreffonct',
						Set::extract( $this->request->data, 'Rendezvous.referent_id' )
					)
				)
			);
		?>

		<?php if( Configure::read( 'Cg.departement') == 58 ):?>
			observeDisableFieldsOnCheckbox(
				'RendezvousIsadomicile',
				[
					'RendezvousPermanenceId'
				],
				true
			);
		<?php endif;?>

		<?php if( isset( $options['Thematiquerdv']['Thematiquerdv'] ) && !empty( $options['Thematiquerdv']['Thematiquerdv'] ) ):?>
			<?php foreach( $options['Thematiquerdv']['Thematiquerdv'] as $typerdv_id => $thematiques ):?>
				observeDisableFieldsetOnValue(
					'RendezvousTyperdvId',
					'ThematiquerdvThematiquerdvFieldset<?php echo $typerdv_id;?>',
					[ '<?php echo $typerdv_id;?>' ],
					false,
					true
				);
			<?php endforeach;?>
		<?php endif;?>
	});
</script>

<h1><?php echo $this->pageTitle;?></h1>
<?php
    echo $this->Default3->messages( $messages );//affichage de l'alerte +55ans si nécessaire
	echo $this->Form->create( 'Rendezvous', array( 'type' => 'post', 'novalidate' => true ) );
	echo '<div>'.$this->Form->input( 'Rendezvous.id', array( 'type' => 'hidden' ) ).'</div>';
	echo '<div>'.$this->Form->input( 'Rendezvous.personne_id', array( 'type' => 'hidden', 'value' => $personne_id ) ).'</div>';
?>

<div class="aere">
	<fieldset>
		<?php
			echo $this->Form->input( 'Rendezvous.structurereferente_id', array( 'label' =>  required( $departement == 93 ? 'Structure proposant le RDV' : __d( 'rendezvous', 'Rendezvous.lib_struct' ) ), 'type' => 'select', 'options' => $options['Rendezvous']['structurereferente_id'], 'empty' => true ) );
			echo $this->Form->input( 'Rendezvous.referent_id', array( 'label' =>  ( $departement == 93 ? 'Personne proposant le RDV' : 'Nom de l\'agent / du référent' ), 'type' => 'select', 'options' => $options['Rendezvous']['referent_id'], 'empty' => true ) );
			///Ajax
			echo $this->Ajax->observeField( 'RendezvousReferentId', array( 'update' => 'ReferentFonction', 'url' => array( 'action' => 'ajaxreffonct' ) ) );

			echo $this->Xhtml->tag(
				'div',
				'<b></b>',
				array(
					'id' => 'ReferentFonction'
				)
			);

			/// Ajout d'une case à cocher permettant de déterminer si le RDV se déroulera chez l'allocataire pour le CG58
			if( Configure::read( 'Cg.departement') == 58 ){
				echo $this->Form->input( 'Rendezvous.isadomicile', array( 'label' => 'Visite à domicile', 'type' => 'checkbox' ) );
			}

			///Ajout d'une permanence liée à une structurereferente
			echo $this->Form->input( 'Rendezvous.permanence_id', array( 'label' => 'Permanence liée à la structure', 'type' => 'select', 'options' => $options['Rendezvous']['permanence_id'], 'empty' => true ) );

			echo $this->Form->input( 'Rendezvous.typerdv_id', array( 'label' =>  required( __d( 'rendezvous', 'Rendezvous.lib_rdv' ) ), 'type' => 'select', 'options' => $options['Rendezvous']['typerdv_id'], 'empty' => true ) );

			// Thématiques du RDV
			if( isset( $options['Thematiquerdv']['Thematiquerdv'] ) && !empty( $options['Thematiquerdv']['Thematiquerdv'] ) ) {
				foreach( $options['Thematiquerdv']['Thematiquerdv'] as $typerdv_id => $thematiques ) {
					if( $departement == 93 && $typerdv_id == Configure::read( 'Rendezvous.Typerdv.collectif_id' ) ) {
						$input = $this->Form->input(
							'Thematiquerdv.Thematiquerdv',
							array(
								'id' => "ThematiquerdvThematiquerdv{$typerdv_id}",
								'type' => 'select',
								'multiple' => 'radio',
								'options' => $thematiques,
								'label' => 'Thématique',
								'empty' => true,
							)
						);
					}
					else {
						$input = $this->Xform->input(
							'Thematiquerdv.Thematiquerdv',
							array(
								'id' => "ThematiquerdvThematiquerdv{$typerdv_id}",
								'type' => 'select',
								'multiple' => 'checkbox',
								'options' => $thematiques,
								'label' => 'Thématiques',
								'empty' => false,
							)
						);
					}
					echo $this->Xhtml->tag(
						'fieldset',
						$input,
						array(
							'id' => "ThematiquerdvThematiquerdvFieldset{$typerdv_id}",
							'class' => 'invisible',
						)
					);
				}
			}

			if( Configure::read( 'Cg.departement') == 58 ){
				echo $this->Form->input( 'Rendezvous.statutrdv_id', array( 'label' =>  ( __d( 'rendezvous', 'Rendezvous.statutrdv' ) ), 'type' => 'select', 'options' => $options['Rendezvous']['statutrdv_id'], 'empty' => true ) );
				echo $this->Form->input( 'Rendezvous.rang', array( 'label' =>  required( __d( 'rendezvous', 'Rendezvous.rang', true ) ), 'type' => 'text' ) );
			}
			else{
				echo $this->Form->input( 'Rendezvous.statutrdv_id', array( 'label' =>  required( __d( 'rendezvous', 'Rendezvous.statutrdv' ) ), 'type' => 'select', 'options' => $options['Rendezvous']['statutrdv_id'], 'empty' => true ) );
			}

			echo $this->Form->input( 'Rendezvous.daterdv', array( 'label' =>  required( __d( 'rendezvous', 'Rendezvous.daterdv' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear' => date('Y')+1, 'minYear' => 2009 ) );

			echo $this->Xform->input( 'Rendezvous.heurerdv', array( 'label' =>  required( __d( 'rendezvous', 'Rendezvous.heurerdv' ) ), 'type' => 'time', 'timeFormat' => '24','minuteInterval'=> 5,  'empty' => true, 'hourRange' => array( 8, 19 ), 'style' => 'margin-bottom: 0.5em;'  ) );

			echo $this->Form->input( 'Rendezvous.objetrdv', array( 'label' =>  ( __d( 'rendezvous', 'Rendezvous.objetrdv' ) ), 'type' => 'textarea', 'style' => 'margin-bottom: 1.5em;' ) );

			echo $this->Form->input( 'Rendezvous.commentairerdv', array( 'label' =>  ( __d( 'rendezvous', 'Rendezvous.commentairerdv' ) ), 'type' => 'textarea' ) );

			echo $this->Form->input( 'Rendezvous.arevoirle', array( 'label' => 'A revoir le ', 'type' => 'date', 'dateFormat' => 'MY', 'maxYear' => date('Y')+3, 'minYear' => date('Y'), 'empty' => true ) );
		?>
	</fieldset>
</div>
<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>