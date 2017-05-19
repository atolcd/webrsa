<?php $this->pageTitle = 'Utilisateurs';?>

<h1><?php echo $this->pageTitle;?></h1><br />

<?php
	echo $this->Form->create( 'User', array( 'type' => 'post', 'autocomplete' => 'off' ) );

	if( $this->action == 'add' ) {
		echo '<div>';
		echo $this->Form->input( 'User.id', array( 'type' => 'hidden', 'value' => null ) );
		echo '</div>';
	}
	else {
		echo '<div>';
		echo $this->Form->input( 'User.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>

<div id="tabbedWrapper" class="tabs">
	<div id="infos">
		<h2 class="title">Informations personnelles</h2>
		<?php include '_form.ctp'; ?>
	</div>
	<div id="droits">
		<h2 class="title">Droits</h2>
		<?php
			if( $this->action == 'add' ) {
				echo $this->Xhtml->para(null, __( 'Sauvegardez puis &eacute;ditez &agrave; nouveau l\'utilisateur pour modifier ses droits.' ));
				echo $this->Xhtml->para(null, __( 'Les nouveaux utilisateurs h&eacute;ritent des droits des profils auxquels ils sont rattach&eacute;s.' ));
			}
			else {
				echo $this->element('editDroits');
			}
		?>
	</div>
</div>
<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php
	echo $this->Form->end();
	echo $this->Observer->disableFormOnSubmit();
?>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>

<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>