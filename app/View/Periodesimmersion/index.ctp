<?php
	$this->pageTitle = 'Périodes d\'immersion';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'periodeimmersion', "Periodesimmersion::{$this->action}" )
	);
?>
<ul class="actionMenu">
	<?php
		echo '<li>'.$this->Xhtml->addLink(
			'Mise en oeuvre d\'une période d\'immersion',
			array( 'action' => 'add', $cui_id ),
			$this->Permissions->checkDossier( 'periodesimmersion', 'add', $dossierMenu )
		).' </li>';
	?>
</ul>
<?php
	$listeoptions = $options;
	unset( $options );
	$options['Periodeimmersion'] = $listeoptions;

	echo $this->Default2->index(
		$periodesimmersion,
		array(
			'Periodeimmersion.datedebperiode',
			'Periodeimmersion.datefinperiode',
			'Periodeimmersion.nomentaccueil',
			'Periodeimmersion.objectifimmersion',
			'Periodeimmersion.datesignatureimmersion'
		),
		array(
			'actions' => array(
				'Periodesimmersion::edit',
				'Periodesimmersion::gedooo',
				'Periodesimmersion::delete'
			),
			'options' => $options
		)
	);
?>
<?php echo $this->Xform->create( 'Periodeimmersion' );?>
<div class="submit">
	<?php
		echo $this->Xform->submit( 'Retour au CUI', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Xform->end(); ?>