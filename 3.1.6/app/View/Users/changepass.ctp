<?php
	$this->pageTitle = 'Changer votre mot de passe';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1><br />

<h2 class="title">Informations personnelles</h2>
<?php
	echo $this->Form->create( 'User', array( 'type' => 'post' ) );
	echo $this->Form->inputs(
		array(
			'fieldset' => false,
			'legend' => false,
			'User.current_password' => array(
				'type' => 'password',
				'value' => '',
				'label' =>  required( __d( 'users', 'User.current_password' ) )
			),
			'User.new_password' => array(
				'type' => 'password',
				'value' => '',
				'label' =>  required( __d( 'users', 'User.new_password' ) )
			),
			'User.new_password_confirmation' => array(
				'type' => 'password',
				'value' => '',
				'label' =>  required( __d( 'users', 'User.new_password_confirmation' ) )
			),
		)
	);

	echo $this->Form->submit( 'Changer' );
	echo $this->Form->end();
?>