<div class="titressejour index">
<h2><?php echo __('Titressejour');?></h2>
<p>
<?php
echo $this->Paginator->counter(array(
'format' => __( 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%' )
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $this->Paginator->sort('id');?></th>
	<th><?php echo $this->Paginator->sort('personne_id');?></th>
	<th><?php echo $this->Paginator->sort('dtentfra');?></th>
	<th><?php echo $this->Paginator->sort('nattitsej');?></th>
	<th><?php echo $this->Paginator->sort('menttitsej');?></th>
	<th><?php echo $this->Paginator->sort('ddtitsej');?></th>
	<th><?php echo $this->Paginator->sort('dftitsej');?></th>
	<th><?php echo $this->Paginator->sort('numtitsej');?></th>
	<th><?php echo $this->Paginator->sort('numduptitsej');?></th>
	<th class="actions"><?php echo __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($titressejour as $titresejour):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $titresejour['Titresejour']['id']; ?>
		</td>
		<td>
			<?php echo $titresejour['Titresejour']['personne_id']; ?>
		</td>
		<td>
			<?php echo $titresejour['Titresejour']['dtentfra']; ?>
		</td>
		<td>
			<?php echo $titresejour['Titresejour']['nattitsej']; ?>
		</td>
		<td>
			<?php echo $titresejour['Titresejour']['menttitsej']; ?>
		</td>
		<td>
			<?php echo $titresejour['Titresejour']['ddtitsej']; ?>
		</td>
		<td>
			<?php echo $titresejour['Titresejour']['dftitsej']; ?>
		</td>
		<td>
			<?php echo $titresejour['Titresejour']['numtitsej']; ?>
		</td>
		<td>
			<?php echo $titresejour['Titresejour']['numduptitsej']; ?>
		</td>
		<td class="actions">
			<?php echo $this->Xhtml->link(__( 'View' ), array('action' => 'view', $titresejour['Titresejour']['id'])); ?>
			<?php echo $this->Xhtml->link(__( 'Edit' ), array('action' => 'edit', $titresejour['Titresejour']['id'])); ?>
			<?php echo $this->Xhtml->link(__( 'Delete', true), array('action' => 'delete', $titresejour['Titresejour']['id']), null, sprintf(__('Are you sure you want to delete # %s?' ), $titresejour['Titresejour']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $this->Paginator->prev('<< '.__( 'previous' ), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $this->Paginator->numbers();?>
	<?php echo $this->Paginator->next(__( 'next' ).' >>', array(), null, array('class' => 'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Xhtml->link(__( 'New Titresejour' ), array('action' => 'add')); ?></li>
	</ul>
</div>
