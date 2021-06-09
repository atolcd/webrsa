<?php
	$this->pageTitle = __m('Fluxpoleemplois.updateEtat.titre');
?>
<h1><?php echo $this->pageTitle;?></h1>
<?php
	echo '<p>' . __m('Fluxpoleemplois.updateEtat.etatActuel') . '<b>' . $etatActuel . '</b></p>';

	echo $this->Form->create( 'Modifetatpe', array( 'type' => 'post', 'novalidate' => true ) );
	echo $this->Form->input( 'Modifetatpe.lib_etatpe', array( 'label' => required( __m('Fluxpoleemplois.lib_etatpe') ), 'type' => 'select', 'options' => $options['etatpe'], 'empty' => true, 'required' => true  ) );
	echo $this->Form->input( 'Modifetatpe.lib_motif', array( 'label' => required( __m('Fluxpoleemplois.lib_motif') ), 'type' => 'select', 'options' => $options['motifs'], 'empty' => true, 'required' => true  ) );
?>
	<div class="submit">
	<?php echo $this->Form->submit( __d('default', 'Form::Save'), array( 'div' => false ) );?>
	<?php echo $this->Form->submit( __d('default', 'Form::Cancel'), array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();