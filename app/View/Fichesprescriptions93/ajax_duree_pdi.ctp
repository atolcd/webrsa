<?php
	if( $this->request->data !== false ) {
		echo $this->Form->input( 'Ficheprescription93.duree_action', array( 'type' => 'hidden', 'id' => false ) );
		$options = array( 'label' => __d( 'fichesprescriptions93', 'Ficheprescription93.duree_action' ) );
		echo $this->Default3->DefaultForm->fieldValue( 'Ficheprescription93.duree_action', $options );
	}
?>