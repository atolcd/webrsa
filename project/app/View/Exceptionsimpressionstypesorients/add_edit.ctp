<?php
	foreach ($options['origines'] as $value => [$label, $checked]){
		if($value == 'cohorte'){
			$options['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigine']['cohorte'][$value] = $label;
		} else {
			$options['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigine']['horscohorte'][$value] = $label;
		}
	}

	echo $this->Default3->titleForLayout( $this->request->data );

	echo $this->Default3->DefaultForm->create();
	echo '<div id="exceptionorigines">';
		echo $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', __m('Exceptionimpressiontypeorient.origine.C' ) )
			. $this->Form->input( 'Exceptionimpressiontypeorient.ExceptionimpressiontypeorientOrigineC', array( 'label' => false, 'multiple' => 'checkbox' , 'options' => $options['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigine']['cohorte'] ) )
		);
		echo $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', __m('Exceptionimpressiontypeorient.origine.HC' ) )
			. $this->Form->input( 'Exceptionimpressiontypeorient.ExceptionimpressiontypeorientOrigineHC', array( 'label' => false, 'multiple' => 'checkbox' , 'options' => $options['Exceptionimpressiontypeorient']['ExceptionimpressiontypeorientOrigine']['horscohorte'] ) )
		);
	echo '</div>';

	echo $this->Default3->subform(
		$this->Translator->normalize(
			array(
				'Exceptionimpressiontypeorient.id',
				'Exceptionimpressiontypeorient.act' => array( 'type' => 'select', 'options' => $options['Activite']['act'], 'empty' => true ),
				'Exceptionimpressiontypeorient.porteurprojet' => array( 'type' => 'select', 'options' => $options['porteurprojet'],  'empty' => true ),
				'Exceptionimpressiontypeorient.modele_notif',
				'Exceptionimpressiontypeorient.actif' => ['default' => true],
				'Exceptionimpressiontypeorient.typeorient_id' => array( 'type' => 'hidden', 'value' => $typeorient_id),
				'Exceptionimpressiontypeorient.ordre' => array( 'type' => 'hidden', 'value' => $options['ordre']),
			)
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php if ($this->action == 'edit') { ?>
			let originesChecked = '<?php echo implode(',', Hash::extract($this->request->data, 'ExceptionimpressiontypeorientOrigine.{n}.origine')); ?>';
			console.log(originesChecked);
			document.querySelectorAll("#exceptionorigines .checkbox input").forEach( (el) => {
				if(originesChecked.indexOf(el.value) != -1) {
					el.checked = true;
				}
			});
		<?php } else { ?>
			document.querySelectorAll("#exceptionorigines .checkbox input").forEach( (el) => {
				el.checked = true;
			});
		<?php } ?>
	});
</script>