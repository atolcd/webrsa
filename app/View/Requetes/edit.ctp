<div class="requetes form">
<?php echo $this->Form->create('Requete'); ?>
	<fieldset>
		<legend><?php echo __('Edit Requete'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('nom');
		echo $this->Form->input('typereq');
		echo $this->Form->input('description');
		echo $this->Form->input('sql_select');
		echo $this->Form->input('sql_condition');
		echo $this->Form->input('sql_option');
		echo $this->Form->input('isactif');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Requete.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Requete.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Requetes'), array('action' => 'index')); ?></li>
	</ul>
</div>
