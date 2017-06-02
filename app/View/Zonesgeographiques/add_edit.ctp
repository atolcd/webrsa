<?php
	$this->pageTitle = 'Zones géographiques';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

	<h1><?php echo $this->pageTitle;?></h1>

	<?php
		if( $this->action == 'add' ) {
			echo $this->Form->create( 'Zonegeographique', array( 'type' => 'post' ) );
			echo $this->Form->input( 'Zonegeographique.id', array( 'type' => 'hidden', 'value' => '' ) );
		}
		else {
			echo $this->Form->create( 'Zonegeographique', array( 'type' => 'post' ) );
			echo $this->Form->input( 'Zonegeographique.id', array( 'type' => 'hidden' ) );
		}
	?>

	<fieldset>
		<?php echo $this->Form->input( 'Zonegeographique.libelle', array( 'label' => required( __( 'libelle' ) ), 'type' => 'text' ) );?>
		<?php echo $this->Form->input( 'Zonegeographique.codeinsee', array( 'label' => required( __( 'codeinsee' ) ), 'type' => 'text', 'maxLength' => 5 ) );?>
	</fieldset>

	<div class="submit">
		<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
		<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
	</div>
	<?php echo $this->Form->end();?>

<div class="clearer"><hr /></div>