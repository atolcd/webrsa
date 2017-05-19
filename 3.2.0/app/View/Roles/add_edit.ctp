<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	};

	echo $this->Default3->titleForLayout( $this->request->data );

	echo $this->Default3->DefaultForm->create();

	echo $this->Default3->subform(
		$this->Translator->normalize(
			array(
				'Role.id',
				'Role.name',
				'Role.actif' => array( 'type' => 'checkbox' )
			)
		),
		array( 'options' => $options )
	);

	$index = 0;
	foreach( $dataUsers as $groupName => $users ) {
		$fields = array(
			'RoleUser.user_id' => array(
				'label' => false,
				'fieldset' => false,
				'multiple' => 'checkbox',
				'class' => 'col3',
				'options' => $users,
				'hiddenField' => false
			)
		);

		echo $this->Html->tag(
			'fieldset',
				$this->Html->tag( 'legend', $groupName )
				.$this->Default3->subform( $this->Translator->normalize( $fields ) ),
			array(
				'id' => 'fieldsetRoleUserUserId'.( $index + 1 )
			)
		);
		$index++;
	}

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFormOnSubmit();
?>
<script type="text/javascript">
//<![CDATA[
	document.observe( 'dom:loaded', function() {
		<?php
			$count = count( $dataUsers );
			for( $index = 0 ; $index < $count ; $index++ ) :
		?>
		insertButtonsCocherDecocher(
			$$( 'fieldset' )[<?php echo $index;?>],
			"fieldset#fieldsetRoleUserUserId<?php echo ( $index + 1);?> input[type=checkbox]"
		);
		<?php endfor;?>
	} );
//]]>
</script>