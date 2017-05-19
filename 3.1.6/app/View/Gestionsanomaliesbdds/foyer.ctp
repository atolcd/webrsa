<?php /*$urlParams = $this->request->params['named'];*/ ?>
<h1><?php echo $this->pageTitle = 'Résolution des problèmes de personnes au sein d\'un foyer';?></h1>

<?php echo $this->element( 'required_javascript' );?>

<h2>Informations concernant le foyer</h2>
<?php
	$informations = array(
		$this->Gestionanomaliebdd->foyerErreursPrestationsAllocataires( $foyer, false ),
		$this->Gestionanomaliebdd->foyerPersonnesSansPrestation( $foyer, false ),
		$this->Gestionanomaliebdd->foyerErreursDoublonsPersonnes( $foyer, false ),
		( $foyer['Dossier']['locked'] ? $this->Xhtml->image( 'icons/lock.png', array( 'alt' => '', 'title' => 'Dossier verrouillé' ) ) : null ),
	);
	$informations = Hash::filter( (array)$informations );

	if( !empty( $informations ) ) {
		echo '<ul>';
		foreach( $informations as $information ) {
			echo "<li>{$information}</li>";
		}
		echo '</ul>';
	}
?>

<ul>
	<li><?php echo $this->Xhtml->link( 'Voir', array( 'controller' => 'personnes', 'action' => 'index', $this->request->params['pass'][0] ) );?></li>
	<?php foreach( $methodes as $m ):?>
		<?php $m = strtolower( $m );?>
		<li><?php echo $this->Xhtml->link( "Comparaison {$m}", array( $this->request->params['pass'][0], 'Gestionanomaliebdd__methode' => $m ) );?></li>
	<?php endforeach;?>
</ul>

<h2>Personnes du foyer</h2>
<?php
	echo $this->Default2->index(
		$personnes,
		array(
			'Personne.nom',
			'Personne.nomnai',
			'Personne.prenom',
			'Personne.prenom2',
			'Personne.prenom3',
			'Personne.dtnai',
			'Personne.nir',
            'Personne.idassedic',
            'Personne.nomcomnai',
			'Prestation.natprest',
			'Prestation.rolepers',
		),
		array(
			'options' => $options,
			'domain' => 'gestionanomaliebdd',
			'id' => 'GestionsanomaliesbddsPersonnesFoyer',
			'actions' => array(
				'Dossiers::view' => array(
					'label' => 'Voir',
					'url' => array( 'controller' => 'personnes', 'action' => 'view', '#Personne.id#' ),
					'class' => 'external',
					'disabled' => !$this->Permissions->checkDossier( 'personnes', 'view', $dossierMenu )
				),
				'Dossiers::edit' => array(
					'label' => 'Modifier',
					'url' => array( 'controller' => 'personnes', 'action' => 'edit', '#Personne.id#' ),
					'class' => 'external',
					'disabled' => !$this->Permissions->checkDossier( 'personnes', 'edit', $dossierMenu )
				),
			)
		)
	);
?>

<h2>Personnes posant problème au sein du foyer</h2>
<?php
	echo $this->Default2->index(
		$problemes,
		array(
			'Personne.nom',
			'Personne.nomnai',
			'Personne.prenom',
			'Personne.prenom2',
			'Personne.prenom3',
			'Personne.dtnai',
			'Personne.nir',
            'Personne.idassedic',
            'Personne.nomcomnai',
			'Prestation.natprest',
			'Prestation.rolepers',
		),
		array(
			'options' => $options,
			'domain' => 'gestionanomaliebdd',
			'actions' => array(
				'Dossiers::correction' => array(
					'label' => 'Corriger',
					'url' => array( 'controller' => $this->request->params['controller'], 'action' => 'personnes', '#Personne.foyer_id#', '#Personne.id#' ),
					'disabled' => !$this->Permissions->checkDossier( $this->request->params['controller'], 'personnes', $dossierMenu )
				),
			),
			'id' => 'GestionsanomaliesbddsProblemesFoyer'
		)
	);
?>