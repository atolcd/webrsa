<?php
	$this->pageTitle = 'Statut de rendez-vous';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Statutrdv', array( 'type' => 'post' ) );
		echo $this->Form->input( 'Statutrdv.id', array( 'type' => 'hidden' ) );
	}
	else {
		echo $this->Form->create( 'Statutrdv', array( 'type' => 'post' ) );
		echo $this->Form->input( 'Statutrdv.id', array( 'type' => 'hidden' ) );
	}
?>

<fieldset>
	<?php
		echo $this->Form->input( 'Statutrdv.libelle', array( 'label' =>  required( __( 'Statut du RDV' ) ), 'type' => 'text' ) );
		if ( Configure::read( 'Cg.departement' ) == 58 ) {
			echo $this->Form->input( 'Statutrdv.provoquepassagecommission', array( 'legend' =>  required( __( 'Provoque un passage en commission ?' ) ), 'fieldset' => false, 'type' => 'radio', 'options' => $provoquepassagecommission ) );
		}
		elseif ( Configure::read( 'Cg.departement' ) == 66 ) {
			echo $this->Form->input( 'Statutrdv.permetpassageepl', array( 'legend' =>  required( __( 'Permet un passage en EPL Audition ?' ) ), 'fieldset' => false, 'type' => 'radio', 'options' => $permetpassageepl ) );
		}
	?>
</fieldset>

	<?php echo $this->Form->submit( 'Enregistrer' );?>
<?php echo $this->Form->end();?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'statutsrdvs',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>