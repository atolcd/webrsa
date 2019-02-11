<div class="titressejour form">
<?php echo $this->Form->create('Titresejour', array('novalidate' => true));?>
	<fieldset>
 		<legend><?php echo __('Add Titresejour');?></legend>
	<?php
		echo $this->Form->input('personne_id');
		echo $this->Form->input('dtentfra');
		echo $this->Form->input('nattitsej');
		echo $this->Form->input('menttitsej');
		echo $this->Form->input('ddtitsej');
		echo $this->Form->input('dftitsej');
		echo $this->Form->input('numtitsej');
		echo $this->Form->input('numduptitsej');
	?>
	</fieldset>
<?php echo $this->Form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Xhtml->link(__( 'List Titressejour' ), array('action' => 'index'));?></li>
	</ul>
</div>
