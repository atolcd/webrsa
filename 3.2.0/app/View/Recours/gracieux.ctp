<?php
	$this->pageTitle = 'Recours gracieux';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $gracieux ) ):?>
	<p class="notice">Ce dossier ne possède pas de recours gracieux.</p>

<?php else:?>
	<?php  echo $this->Form->create( 'Indus', array( 'type' => 'post', 'novalidate' => true ));?>
		<h2>Généralités</h2>
			<?php echo $this->Form->input( 'Recours.type_recours', array( 'label' => false, 'type' => 'radio', 'options' => array( 'G' => 'Gracieux', 'C' => 'Contentieux' ), 'legend' => 'Type de recours' ) ); ?>
			<?php echo $this->Form->input( 'Recours.date_recours', array( 'label' =>  ( __( 'date_recours' ) ), 'type' => 'date', 'dateFormat'=> 'DMY', 'maxYear'=>date('Y')+10, 'minYear'=> date('Y')-10 , 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.comment', array( 'label' => 'Commentaires commission', 'type' => 'textarea' ) ); ?>
<hr />
		<h2>Commission de Recours Amiable (CRA)</h2>
			<?php echo $this->Form->input( 'Recours.datecommission', array( 'label' =>  ( __( 'datecommission' ) ), 'type' => 'date', 'dateFormat'=> 'DMY', 'maxYear'=>date('Y')+10, 'minYear'=>date('Y')-10 , 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.typecommission2', array( 'label' => __( 'typecommission2' ), 'type' => 'select', 'options' => $commission, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.decision', array( 'label' => __( 'decision' ), 'type' => 'select', 'options' => $decisionrecours, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.avis', array( 'label' => __( 'avis' ), 'type' => 'textarea' ) ); ?>
<hr />
		<h2>Décision PCG</h2>
			<?php echo $this->Form->input( 'Recours.typecommission', array( 'label' => __( 'typecommission' ), 'type' => 'select', 'options' => $commission, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.date_commission', array( 'label' =>  ( __( 'date_commission' ) ), 'type' => 'date', 'dateFormat'=> 'DMY', 'maxYear'=>date('Y')+10, 'minYear'=>date('Y')-10 , 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.decision2', array( 'label' => __( 'decision2' ), 'type' => 'select', 'options' => $decisionrecours, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.motif', array( 'label' => __( 'motif' ), 'type' => 'select', 'options' => $motifrecours, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Recours.avis2', array( 'label' => __( 'avis2' ), 'type' => 'textarea' ) ); ?>

		<?php echo $this->Form->submit( 'Enregistrer' );?>
	<?php echo $this->Form->end();?>
<?php endif;?>