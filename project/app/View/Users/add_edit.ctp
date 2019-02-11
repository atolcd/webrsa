<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}

	echo $this->Default3->titleForLayout( $this->request->data );

	$formId = Inflector::camelize( Inflector::singularize($this->request->params['controller'])."_{$this->request->params['action']}_form" );

	echo $this->Form->create( 'User', array( 'type' => 'post', 'autocomplete' => 'off', 'id' => $formId, 'novalidate' => true ) );

	if( $this->action == 'add' ) {
		echo '<div>';
		echo $this->Form->input( 'User.id', array( 'type' => 'hidden', 'value' => null ) );
		echo '</div>';
	}
	else {
		echo '<div>';
		echo $this->Form->input( 'User.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>

<div id="tabbedWrapper" class="tabs">
	<div id="infos">
		<h2 class="title">Informations</h2>
		<?php require '_form.ctp'; ?>
	</div>
	<div id="droits">
		<h2 class="title">Permissions</h2>
		<?php echo $this->element('permissions', compact('acos', 'parentPermissions'));?>
	</div>
</div>
<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php
	echo $this->Form->end();
	echo $this->Observer->disableFormOnSubmit();
?>
<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>
<?php
	echo $this->element(
		'modalbox',
		array(
			'modalid' => 'loading-wait',
			'modalmessage' => null,
			'modalclose' => false,
			'modalcontent' => $this->Html->tag( 'p', $this->Html->image( 'loading.gif' ).' Chargement des permissions en cours' )
		)
	);
?>