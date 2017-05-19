<?php
	if( Configure::read( 'Cg.departement') == 66 ) {
		$this->pageTitle = 'Avis techniques';
	}
	else {
		$this->pageTitle = 'Proposition';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'propodecisioncui66', "Propodecisioncui66::{$this->action}" )
	);
?>
<fieldset id="avismne">
	<legend><?php echo 'Avis PRE';?></legend>
		<?php
			echo $this->Xform->create( 'Propodecisioncui66', array( 'id' => 'propodecisioncui66form' ) );
			if( Set::check( $this->request->data, 'Propodecisioncui66.id' ) ){
				echo $this->Xform->input( 'Propodecisioncui66.id', array( 'type' => 'hidden' ) );
			}

			echo $this->Xform->input( 'Propodecisioncui66.cui_id', array( 'type' => 'hidden', 'value' => $cui_id ) );
			echo $this->Xform->input( 'Propodecisioncui66.user_id', array( 'type' => 'hidden', 'value' => $userConnected ) );

			echo $this->Xform->input( 'Propodecisioncui66.structurereferente_id', array( 'type' => 'hidden' ) );
			echo $this->Xform->input( 'Propodecisioncui66.observcui', array( 'label' => __d( 'propodecisioncui66', 'Propodecisioncui66.observcui' ), 'type' => 'textarea', 'rows' => 6)  );
			echo $this->Xform->input( 'Propodecisioncui66.propositioncui', array( 'label' => __d( 'propodecisioncui66', 'Propodecisioncui66.propositioncui' ), 'type' => 'select', 'options' => $options['Propodecisioncui66']['propositioncui'], 'empty' => false ) );
			echo $this->Xform->input( 'Propodecisioncui66.datepropositioncui', array( 'label' => required( __d( 'propodecisioncui66', 'Propodecisioncui66.datepropositioncui' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+2, 'minYear'=>date('Y')-2 , 'empty' => false)  );
		?>
</fieldset>

<fieldset>
	<?php
		echo $this->Default2->subform(
			array(
				'Propodecisioncui66.isavisreferent' => array( 'label' => __d( 'propodecisioncui66', 'Propodecisioncui66.isavisreferent' ), 'type' => 'checkbox' ),
			),
			array(
				'options' => $options['Propodecisioncui66']
			)
		);
	?>
	<fieldset id="avisreferent">
		<legend></legend>
			<?php
				echo $this->Xform->input( 'Propodecisioncui66.observcuireferent', array( 'label' => __d( 'propodecisioncui66', 'Propodecisioncui66.observcuireferent' ), 'type' => 'textarea', 'rows' => 6)  );
				echo $this->Xform->input( 'Propodecisioncui66.propositioncuireferent', array( 'label' => __d( 'propodecisioncui66', 'Propodecisioncui66.propositioncuireferent' ), 'type' => 'select', 'options' => $options['Propodecisioncui66']['propositioncui'], 'empty' => true ) );
				echo $this->Xform->input( 'Propodecisioncui66.datepropositioncuireferent', array( 'label' => required( __d( 'propodecisioncui66', 'Propodecisioncui66.datepropositioncuireferent' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+2, 'minYear'=>date('Y')-2 , 'empty' => false)  );
			?>
	</fieldset>
</fieldset>
<fieldset>
	<?php
		echo $this->Default2->subform(
			array(
				'Propodecisioncui66.isaviselu' => array( 'label' => __d( 'propodecisioncui66', 'Propodecisioncui66.isaviselu' ), 'type' => 'checkbox' ),
			),
			array(
				'options' => $options['Propodecisioncui66']
			)
		);
	?>
	<fieldset id="aviselu">
		<legend></legend>
			<?php
				echo $this->Xform->input( 'Propodecisioncui66.observcuielu', array( 'label' => __d( 'propodecisioncui66', 'Propodecisioncui66.observcuielu' ), 'type' => 'textarea', 'rows' => 6)  );
				echo $this->Xform->input( 'Propodecisioncui66.propositioncuielu', array( 'label' => __d( 'propodecisioncui66', 'Propodecisioncui66.propositioncuielu' ), 'type' => 'select', 'options' => $options['Propodecisioncui66']['propositioncui'], 'empty' => true ) );
				echo $this->Xform->input( 'Propodecisioncui66.datepropositioncuielu', array( 'label' => required( __d( 'propodecisioncui66', 'Propodecisioncui66.datepropositioncuielu' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+2, 'minYear'=>date('Y')-2 , 'empty' => false)  );
			?>
	</fieldset>
</fieldset>

<div class="submit">
<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox(
			'Propodecisioncui66Isaviselu',
			'aviselu',
			false,
			true
		);

		observeDisableFieldsetOnCheckbox(
			'Propodecisioncui66Isavisreferent',
			'avisreferent',
			false,
			true
		);
	});

</script>