<?php
	$this->pageTitle = 'Comité d\'examen APRE';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	echo $this->Xform->create( 'Comiteapre', array( 'type' => 'post' ) );

	if( $this->action == 'edit' ) {
		echo '<div>';
		echo $this->Xform->input( 'Comiteapre.id', array( 'label' => false, 'type' => 'hidden' ) );
		echo '</div>';
	}
?>

	<fieldset>
		<?php echo $this->Xform->input( 'Comiteapre.datecomite', array( 'label' => required( __( 'Date du comité' ) ), 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 1 ) );?>
		<?php echo $this->Xform->input( 'Comiteapre.heurecomite', array( 'label' => required( __( 'Heure du comité' ) ), 'type' => 'time', 'timeFormat' => '24', 'minuteInterval'=> 5, 'hourRange' => array( 8, 19 ), 'empty' => true ) );?>
		<?php echo $this->Xform->input( 'Comiteapre.lieucomite', array( 'label' => required( __( 'Lieu du comité' ) ), 'type' => 'text' ) );?>
		<?php echo $this->Xform->input( 'Comiteapre.intitulecomite', array( 'label' => required( __( 'Intitulé du comité' ) ), 'type' => 'text' ) );?>
		<?php echo $this->Xform->input( 'Comiteapre.observationcomite', array( 'label' => __( 'Observation du comité' ), 'type' => 'text' ) );?>
	</fieldset>
	<?php echo $this->Xform->submit( 'Enregistrer' );?>
<?php echo $this->Xform->end();?>