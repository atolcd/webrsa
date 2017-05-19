<?php
	$this->pageTitle = 'Objet du rendez-vous';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Typerdv', array( 'type' => 'post' ) );
		echo $this->Form->input( 'Typerdv.id', array( 'type' => 'hidden' ) );
	}
	else {
		echo $this->Form->create( 'Typerdv', array( 'type' => 'post' ) );
		echo $this->Form->input( 'Typerdv.id', array( 'type' => 'hidden' ) );
	}
?>

<fieldset>
	<?php
		echo $this->Xform->input( 'Typerdv.libelle', array( 'label' =>  required( __d( 'rendezvous', 'Rendezvous.lib_rdv' ) ), 'type' => 'text' ) );
		echo $this->Xform->input( 'Typerdv.modelenotifrdv', array( 'label' =>  required( __d( 'typerdv', 'Typerdv.modelenotifrdv' ) ), 'type' => 'text' ) );
// 		if ( Configure::read( 'Cg.departement' ) == 58 ) {
// 			echo $this->Xform->input( 'Typerdv.nbabsencesavpassageep', array( 'label' =>  required( __d( 'typerdv', 'Typerdv.nbabsencesavpassageep' ) ), 'type' => 'text' ) );
// 			echo $this->Xform->input( 'Typerdv.motifpassageep', array( 'label' =>  __d( 'typerdv', 'Typerdv.motifpassageep' ), 'type' => 'text' ) );
// 		}
		if ( Configure::read( 'Cg.departement' ) == 66 ) {
			echo $this->Xform->input( 'Typerdv.nbabsaveplaudition', array( 'label' =>  required( __d( 'typerdv', 'Typerdv.passageeplaudition' ) ), 'type' => 'text' ) );
		}
	?>
</fieldset>

	<?php echo $this->Form->submit( 'Enregistrer' );?>
<?php echo $this->Form->end();?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'typesrdv',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>