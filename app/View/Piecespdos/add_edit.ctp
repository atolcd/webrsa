<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<?php echo $this->element( 'dossier_menu', array( 'id' => Set::extract( $pdo, 'Propopdo.dossier_id' ) ) );?>

<?php $this->pageTitle = 'Pieces PDOs';?>

<div class="with_treemenu">
	<h1><?php echo $this->pageTitle;?></h1>

	<?php
		if( $this->action == 'add' ) {
			echo $this->Form->create( 'Piecepdo', array( 'type' => 'post', 'novalidate' => true ) );
		}
		else {
			echo $this->Form->create( 'Piecepdo', array( 'type' => 'post', 'novalidate' => true ) );
		}
	?>

	<fieldset>
		<?php echo $this->Form->input( 'Piecepdo.propopdo_id', array( 'label' => false, 'type' => 'hidden', 'value' => Set::classicExtract( $pdo, 'Propopdo.id' ) ) );?>
		<?php echo $this->Form->input( 'Piecepdo.libelle', array( 'label' => required( __( 'Intitulé de la pièce' ) ), 'type' => 'text' ) );?>
		<?php echo $this->Form->input( 'Piecepdo.dateajout', array( 'label' => required( __( 'Date de l\'ajout' ) ), 'type' => 'date', 'dateFormat' => 'DMY' ) );?>
	</fieldset>

		<?php echo $this->Form->submit( 'Enregistrer' );?>
	<?php echo $this->Form->end();?>
</div>
<div class="clearer"><hr /></div>