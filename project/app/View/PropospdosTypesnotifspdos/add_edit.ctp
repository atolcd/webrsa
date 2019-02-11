<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout traitement';
	}
	else {
		$this->pageTitle = 'Édition traitement';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->element( 'dossier_menu', array( 'id' => $dossier_id ) );
?>

<div class="with_treemenu">
	<h1><?php echo $this->pageTitle;?></h1>

	<?php
		if( $this->action == 'add' ) {
			echo $this->Form->create( 'PropopdoTypenotifpdo', array( 'type' => 'post', 'novalidate' => true ) );
		}
		else {
			echo $this->Form->create( 'PropopdoTypenotifpdo', array( 'type' => 'post', 'novalidate' => true ) );
			echo '<div>';
			echo $this->Form->input( 'PropopdoTypenotifpdo.id', array( 'type' => 'hidden' ) );
			echo '</div>';
		}
	?>

	<div class="aere">
		<fieldset>
			<legend>Détails PDO</legend>
				<?php echo $this->Form->input( 'PropopdoTypenotifpdo.propopdo_id', array( 'label' => false, 'type' => 'hidden' ) ) ;?>
			<?php echo $this->Form->input( 'PropopdoTypenotifpdo.typenotifpdo_id', array( 'label' =>  ( __( 'Type de notification' ) ), 'type' => 'select', 'options' => $typenotifpdo, 'empty' => true ) );?>
				<?php echo $this->Form->input( 'PropopdoTypenotifpdo.datenotifpdo', array( 'label' =>  ( __( 'Date de notification' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=> date('Y')-1, 'empty' => false ) );?>
		</fieldset>
	</div>

	<div class="submit">
		<?php echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );?>
		<?php echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
	</div>
	<?php echo $this->Xform->end();?>
</div>
<div class="clearer"><hr /></div>