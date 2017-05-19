<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'une COV';
	}
	else {
		$this->pageTitle = 'Modification d\'une COV';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$searchFormId = 'Covs58AddEditForm';
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	echo $this->Form->create( 'Cov58', array( 'type' => 'post', 'id' => $searchFormId, 'novalidate' => true ) );

	echo $this->Default2->subform(
		array(
			'Cov58.id' => array( 'type'=>'hidden' ),
			'Cov58.sitecov58_id' => array( 'type' => 'select', 'empty' => true, 'required' => true ),
			'Cov58.datecommission' => array( 'dateFormat' => __( 'Locale->dateFormat', true ), 'timeFormat' => __( 'Locale->timeFormat' ), 'interval' => 15, 'required' => true, 'maxYear' => date('Y') + 1, 'minYear' => date('Y') - 1 ),
			'Cov58.observation' => array( 'type'=>'textarea' )
		)
	);
	if( $this->action == 'edit' ){
		echo $this->Default2->subform( array( 'Cov58.etatcov' => array( 'type'=>'hidden' ) ) );
	}

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Form->end();
	echo $this->Observer->disableFormOnSubmit( $searchFormId );
?>