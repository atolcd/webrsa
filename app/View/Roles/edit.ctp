<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	
	function multipleCheckbox( $View, $path, $options, $class = '' ) {
		$name = model_field($path);
		return $View->Xform->input($path, array(
			'label' => __m($path), 
			'type' => 'select', 
			'multiple' => 'checkbox', 
			'options' => $options[$name[0]][$name[1]],
			'class' => $class
		));
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'role', "Roles::{$this->action}" )
	);

    echo $this->Xform->create();
    echo $this->Default2->subform(
        array(
            'Role.id' => array( 'type' => 'hidden' ),
            'Role.name' => array( 'type' => 'text', 'required' => true ),
			'Role.actif' => array( 'type' => 'checkbox' ),
        ),
		array(
			'options' => $options
		)
    );
	
	foreach ($dataUsers as $groupName => $users) {
		echo $this->Xform->input('RoleUser.user_id', array(
			'label' => $groupName,
			'type' => 'select',
			'multiple' => 'checkbox',
			'hiddenField' => false,
			'before' => '<input type="button" onClick="selectAll(this)" value="Cocher/Décocher tout"/><br/>',
			'options' => $users,
			'class' => 'divideInto3Columns'
		));
		
		/*
		 * 
		 * 
		 * 
		 * TODO : cocher les cases dans le cas d'un edit
		 * 
		 * 
		 * 
		 */
	}

    echo $this->Html->tag(
        'div',
        $this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
        .$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
        array( 'class' => 'submit noprint' )
    );

    echo $this->Xform->end();
?>
<script>
function selectAll(button) {
	button.coche = button.coche === undefined || button.coche === false ? true : false;
	button.up('fieldset').select('input[type="checkbox"]').each(function(box){
		box.checked = button.coche;
	});
}
</script>