<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'un mémo';
	}
	else {
		$this->pageTitle = 'Édition d\'un mémo';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Memo', array( 'type' => 'post' ) );
	}
	else {
		echo $this->Form->create( 'Memo', array( 'type' => 'post' ) );
		echo '<div>';
		echo $this->Form->input( 'Memo.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	echo '<div>';
	echo $this->Form->input( 'Memo.personne_id', array( 'type' => 'hidden', 'value' => $personne_id ) );
	echo '</div>';
?>

<div class="aere">
	<fieldset>
		<?php
			echo $this->Xform->input( 'Memo.name', array( 'type' => 'textarea', 'label' => __d( 'memo', 'Memo.name' ) ) );
		?>
	</fieldset>
</div>
<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>