<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'une personne';
	}
	else {
		$title = implode(
			' ',
			array(
				$this->request->data['Personne']['qual'],
				$this->request->data['Personne']['nom'],
				$this->request->data['Personne']['prenom'] )
		);

		$this->pageTitle = 'Édition de la personne « '.$title.' »';
		$foyer_id = $this->request->data['Personne']['foyer_id'];
	}
?>

<h1><?php echo $this->pageTitle;?></h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Personne', array( 'type' => 'post', 'novalidate' => true ));
	}
	else {
		echo $this->Form->create( 'Personne', array( 'type' => 'post', 'novalidate' => true ) );
		echo '<div>';
		echo $this->Form->input( 'Personne.id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Prestation.id', array( 'type' => 'hidden', 'div' => 'div' ) );
		echo '</div>';
	}
?>
<div>
	<?php echo $this->Form->input( 'Personne.foyer_id', array( 'type' => 'hidden', 'div' => 'div', 'value' => $foyer_id ) );?>
	<?php echo $this->Form->input( 'Prestation.natprest', array( 'type' => 'hidden', 'value' => 'RSA' ) );?>
	<?php echo $this->Form->input( 'Prestation.rolepers', array( 'label' => __d( 'prestation', 'Prestation.rolepers' ), 'type' => 'select', 'empty' => true ) );?>
</div>
<?php require  '_form.ctp' ;?>


<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>