<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	};

	echo $this->Default3->titleForLayout( $this->request->data );

	echo $this->Default3->form(
		$this->Translator->normalize(
			array(
				'Canton.id',
				'Canton.canton',
				'Canton.zonegeographique_id' => array( 'empty' => true ),
				'Canton.numvoie',
				'Canton.libtypevoie' => array( 'empty' => true ),
				'Canton.nomvoie',
				'Canton.nomcom',
				'Canton.codepos',
				'Canton.numcom'
			)
		),
		array(
			'options' => $options
		)
	);

	echo $this->Observer->disableFormOnSubmit();
?>
<div class="notice-accueil" style="padding-left: 40%">
	<h2><?php echo __d ('cantons', 'Canton.liste'); ?></h2>
	<br>
<?php
	foreach ($cantons as $canton) {
?>
	<div><?php echo $canton['Canton']['canton']; ?></div>
<?php
	}
?>
</div>