<?php
	echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );

	echo $this->Default3->titleForLayout( $personne );

	echo $this->Xform->create( 'Questionnaireb7pdv93', array( 'id' => 'questionnairesb7pdvs93form' ) );
?>
<fieldset>
	<legend><?php echo __d ('questionnairesb7pdvs93', 'Questionnaireb7pdv93.generalites') ?></legend>
	<?php
		echo $this->Form->input(
			'Questionnaireb7pdv93.typeemploi',
			array(
				'options' => $options['Typeemploi'],
				'empty' => '' ,
				'label' => required (__d ('questionnairesb7pdvs93', 'Questionnaireb7pdv93.typeemploi'))
			)
		);
	?>
	<?php
		echo $this->Form->input(
			'Questionnaireb7pdv93.dureeemploi',
			array(
				'options' => $options['Dureeemploi'],
				'empty' => '',
				'label' => required (__d ('questionnairesb7pdvs93', 'Questionnaireb7pdv93.dureeemploi'))
			)
		);
	?>
	<?php
		echo $this->Default3->subform(
			array(
				'Questionnaireb7pdv93.dateemploi' => array(
					'dateFormat' => 'MY',
					'minYear' => date('Y') - 39,
					'maxYear' => date('Y') + 1,
				)
			)
		);
	?>
</fieldset>

<?php
	// Codes ROME V3 Expérience professionnelle significative
	echo $this->Romev3->fieldset(
		'Expproromev3',
		array(
			'options' => $options,
			'required' => true
		)
	);
?>

<div class="submit">
	<?php echo $this->Form->submit( __d ('default', 'Save'), array( 'div' => false ) );?>
	<?php echo $this->Form->submit( __d ('default', 'Cancel'), array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php
	echo $this->Form->end();
?>