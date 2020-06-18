<?php
	$this->pageTitle = __m('Dossier.motifchgmtetat');
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>
<?php
	echo $this->Form->create( 'Motifsetatdossier', array( 'type' => 'post', 'novalidate' => true ) );
	echo $this->Form->input( 'Situationdossierrsa.etatdosrsa', array( 'label' =>  __m('Dossier.etatdosrsa'), 'type' => 'select', 'options' => $options['etatdosrsa'], 'empty' => true, 'required' => true  ) );
	echo $this->Form->input( 'Motifetatdossier.lib_motif', array( 'label' =>  __m('Dossier.lib_motif'), 'type' => 'select', 'options' => $options['motifs'], 'empty' => true, 'required' => true  ) );
?>
	<div class="submit">
	<?php echo $this->Form->submit( __d('default', 'Form::Save'), array( 'div' => false ) );?>
	<?php echo $this->Form->submit( __d('default', 'Annuler'), array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>