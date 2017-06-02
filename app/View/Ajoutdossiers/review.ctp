<?php $this->pageTitle = __d( 'ajoutdossier', "Ajoutdossiers::{$this->action}" );?>
<?php echo $this->Form->create( 'Ajoutdossiers', array( 'id' => 'SignupForm', 'novalidate' => true ) ); ?>
	<div class="submit">
		<?php echo $this->Form->submit('Continue', array('div'=>false));?>
		<?php echo $this->Form->submit('Cancel', array('name'=>'Cancel','div'=>false));?>
	</div>
<?php echo $this->Form->end();?>