<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->element( 'dossier_menu', array( 'personne_id' => $personne_id ) );
?>

<div class="with_treemenu">
	<?php
		echo $this->Xhtml->tag(
			'h1',
			$this->pageTitle = __d( 'decisionpropopdo', "Decisionspropospdos::{$this->action}" )
		);

		echo $this->Xform->create( 'Decisionpropopdo', array( 'id' => 'decisionpropopdoform' ) );
		if( Set::check( $this->request->data, 'Decisionpropopdo.id' ) ){
			echo $this->Xform->input( 'Decisionpropopdo.id', array( 'type' => 'hidden' ) );
		}
		echo $this->Xform->input( 'Decisionpropopdo.propopdo_id', array( 'type' => 'hidden', 'value' => $propopdo_id ) );
	?>

	<fieldset><legend>Proposition de décision</legend>
		<fieldset id="Decision" class="invisible">
			<?php
				echo $this->Default2->subform(
					array(
						'Decisionpropopdo.datedecisionpdo' => array( 'label' =>  ( __( 'Date de décision de la PDO' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>2009, 'empty' => false ),
						'Decisionpropopdo.decisionpdo_id' => array( 'label' =>  ( __( 'Décision du Conseil Général' ) ), 'type' => 'select', 'options' => $decisionpdo, 'required' => true, 'empty' => true )
					),
					array(
						'options' => $options
					)
				);

				echo $this->Default2->subform(
					array(
						'Decisionpropopdo.commentairepdo' => array( 'label' =>  'Proposition : ', 'type' => 'textarea', 'rows' => 3 ),
					),
					array(
						'options' => $options
					)
				);

				echo $this->Default2->subform(
					array(
						'Decisionpropopdo.hasreponseep' => array( 'label' =>  '<strong>Réponse à l\'EP </strong>', 'type' => 'checkbox' ),
					),
					array(
						'options' => $options
					)
				);
			?>
			<fieldset id="reponseep" class="invisible">
				<?php
					echo $this->Default2->subform(
					array(
						'Decisionpropopdo.decisionreponseep' => array( 'type' => 'radio', 'separator' => '<br />',  'options' => $options['decisionreponseep'] ),
						'Decisionpropopdo.accordepaudition' => array( 'type' => 'radio', 'options' => $options['accordepaudition'] ),
						'Decisionpropopdo.commentairereponseep',
						'Decisionpropopdo.datereponseep' => array( 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear' => date('Y')+5, 'minYear' => 2009, 'empty' => true )
					),
					array(
						'options' => $options
					)
				);
				?>
			</fieldset>
		</fieldset>

	</fieldset>

	<fieldset><legend>Avis technique</legend>
			<?php
				echo $this->Default2->subform(
					array(
						'Decisionpropopdo.avistechnique' => array( 'label' => false, 'type' => 'radio', 'options' => $options['avistechnique'] ),
					),
					array(
						'options' => $options
					)
				);
			?>
			<fieldset id="avistech" class="noborder">
				<?php
					echo $this->Default2->subform(
					array(
						'Decisionpropopdo.commentaireavistechnique',
						'Decisionpropopdo.dateavistechnique' => array( 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear' => date('Y')+5, 'minYear' => 2009, 'empty' => false )
					),
					array(
						'options' => $options
					)
				);
				?>
			</fieldset>
	</fieldset>

	<fieldset><legend>Validation de la proposition</legend>
			<?php
				echo $this->Default2->subform(
					array(
						'Decisionpropopdo.validationdecision' => array( 'label' => false, 'type' => 'radio', 'options' => $options['validationdecision'] ),
					),
					array(
						'options' => $options
					)
				);
			?>
			<fieldset id="validpropo" class="noborder">
				<?php
					echo $this->Default2->subform(
					array(
						'Decisionpropopdo.commentairedecision',
						'Decisionpropopdo.datevalidationdecision' => array( 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear' => date('Y')+5, 'minYear' => 2009, 'empty' => false )
					),
					array(
						'options' => $options
					)
				);
				?>
			</fieldset>
	</fieldset>

	<?php
		echo "<div class='submit'>";
			echo $this->Form->submit('Enregistrer', array('div'=>false));
			echo $this->Form->button( 'Retour', array( 'type' => 'button', 'onclick'=>"location.replace('".Router::url( array( 'controller' => 'propospdos', 'action' => 'edit', $propopdo_id ) )."')" ) );
		echo "</div>";

		echo $this->Form->end();
	?>

	<?php echo $this->Xform->end();?>
</div>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox(
			'DecisionpropopdoHasreponseep',
			'reponseep',
			false,
			true
		);
		observeDisableFieldsetOnRadioValue(
			'decisionpropopdoform',
			'data[Decisionpropopdo][avistechnique]',
			$( 'avistech' ),
			['1','0'],
			false,
			true
		);
		observeDisableFieldsetOnRadioValue(
			'decisionpropopdoform',
			'data[Decisionpropopdo][validationdecision]',
			$( 'validpropo' ),
			['1','0'],
			false,
			true
		);
	} );
</script>