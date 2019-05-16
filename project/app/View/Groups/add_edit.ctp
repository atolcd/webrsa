<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}

	echo $this->Default3->titleForLayout( $this->request->data );

	echo ('<br>');
	echo $this->Default3->DefaultForm->create();
?>

<div id="tabbedWrapper" class="tabs">
	<div id="infos">
		<h2 class="title">Informations</h2>
		<fieldset>
			<?php
				echo $this->Default3->subform(
					$this->Translator->normalize(
						array(
							'Group.id',
							'Group.name',
							'Group.code',
							'Group.parent_id' => array( 'empty' => true )
						)
					),
					array(
						'options' => array(
							'Group' => array(
								'parent_id' => $groups
							)
						)
					)
				);
			?>
		</fieldset>
	</div>

	<div id="droits">
		<h2 class="title">Permissions</h2>
		<?php echo $this->element('permissions', compact('acos', 'parentPermissions'));?>
	</div>
</div>
<?php
	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

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