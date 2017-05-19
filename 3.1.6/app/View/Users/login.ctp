<?php
	$this->pageTitle = 'Connexion';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( isset( $success ) ): ?>
	<p class="success"><?php echo $success; ?></p>
<?php else: ?>
	<?php if( isset( $error ) ): ?>
		<p class="error"><?php echo $error; ?></p>
	<?php endif; ?>

	<?php echo $this->Form->create( 'User', array( 'action' => 'login' ) ); ?>
		<?php echo $this->Form->input( 'username', array( 'label' => 'Identifiant' ) ); ?>
		<?php echo $this->Form->input( 'password', array( 'label' => 'Mot de passe' ) ); ?>
		<?php echo $this->Form->submit( 'Connexion' ); ?>
	<?php echo $this->Form->end(); ?>
<?php endif; ?>

<?php if( Configure::read( 'Password.mail_forgotten' ) ): ?>
	<div id="forgottenpass">
		<?php echo $this->Html->link( 'Mot de passe oublié ?', array( 'controller' => 'users', 'action' => 'forgottenpass' ) );?>
	</div>
<?php endif; ?>

<?php echo $this->Observer->disableFormOnSubmit( 'UserLoginForm', 'Connexion en cours ...' ); ?>