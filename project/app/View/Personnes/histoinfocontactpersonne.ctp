<?php
    if( Configure::read( 'debug' ) > 0 ) {
        echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
    }

	$title = "{$infocontactpersonne[0]['Personne']['qual']} {$infocontactpersonne[0]['Personne']['nom']} {$infocontactpersonne[0]['Personne']['prenom']}";
    $this->pageTitle = 'Historique des coordonnées de « '.$title.' »';

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
?>
<h1><?php echo $this->pageTitle;?></h1>

<ul class="actions">
<?php
	echo '<li class="action">'.$this->Xhtml->link(
		__m('Histocoordonnees::back'),
		array( 'controller' => 'personnes', 'action' => 'view', $infocontactpersonne[0]['Personne']['id'] ),
		array(
			'title' => __m('Histocoordonnees::back::title', $title),
			'enabled' => WebrsaAccess::isEnabled($infocontactpersonne[0], '/Personnes/view'),
			'class' => 'personnes coordonnees infocontact'
		)
	).' </li>';
?>
</ul>

<?php
	echo $this->Default3->index(
		$infocontactpersonne,
		$this->Translator->normalize(
			array(
				'Infocontactpersonne.fixe',
				'Infocontactpersonne.mobile',
				'Infocontactpersonne.email',
				'Infocontactpersonne.modified'=> array( 'type' => 'date' )
			)
		),
		array(
			'domain' => 'infocontactpersonne',
			'options' => $options,
			'paginate' => false,
		)
	);?>