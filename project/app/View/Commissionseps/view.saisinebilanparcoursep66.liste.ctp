<?php
	$class = Inflector::classify( $theme );
	echo "<div id=\"$class\"><h3 class=\"title\">".__d( 'dossierep',  'ENUM::THEMEEP::'.Inflector::tableize( $theme ) )."</h3>";

	if( in_array( 'dossierseps::choose', $etatsActions[$commissionep['Commissionep']['etatcommissionep']] ) ) {
		echo '<ul class="actionMenu">';
		echo '<li>'.$this->Xhtml->affecteLink(
			'Affecter les dossiers',
			array( 'controller' => 'dossierseps', 'action' => 'choose', Set::classicExtract( $commissionep, 'Commissionep.id' ), '#' => $theme )
		).' </li>';
		echo '</ul>';
	}
	else {
		echo '<li><span class="disabled"> Affecter les dossiers </span></li>';
	}

	if( empty( $dossiers[$theme] ) ) {
		echo '<p class="notice">Il n\'existe aucun dossier de cette thématique associé à cette commission d\'EP.</p>';
	}
	else {
?>
<?php
/**
 * TODO : à retirer en 3.2.8
 * Garder pour vérification conflit 3.2.7
 */
//<form action="<?php echo Router::url( array( 'controller' => $controller, 'action' => 'view/'.$commissionep['Commissionep']['id']."/sort:Passagecommissionep.heureseance/direction:asc/#tabbedWrapper,dossiers,Defautinsertionep66" ) );?>" method="post" id="FormRequestmaster">
?>
<form action="<?php echo Router::url( array( 'controller' => $controller, 'action' => 'view/'.$commissionep['Commissionep']['id']."/sort:Passagecommissionep.heureseance/direction:asc/#tabbedWrapper,dossiers,Saisinebilanparcoursep66" ) );?>" method="post" id="FormRequestmaster">

<?php
		$touteslesheuresdepassage = array ();
		foreach ($dossierseps as $key => $value) {
			$touteslesheuresdepassage[$value['Passagecommissionep']['id']] = $value['Passagecommissionep']['heureseance'];
		}

		echo $this->Default2->index(
			$dossiers[$theme],
			array(
				'Personne.qual',
				'Personne.nom',
				'Personne.prenom',
				'Personne.dtnai',
				'Adresse.nomcom',
				'Dossierep.created',
				'Dossierep.themeep',
				'Passagecommissionep.etatdossierep',
				'Passagecommissionep.heureseance' => array (
					'input' => 'heureseance',
					'type' => 'text',
					'hidden' => 'Passagecommissionep.id',
					'dateseance' => $commissionep['Commissionep']['dateseance'],
					'touteslesheuresdepassage' => $touteslesheuresdepassage,
				),
				'Foyer.enerreur' => array( 'type' => 'string', 'sort' => false, 'class' => 'foyer_enerreur' ),
			),
			array(
				'actions' => array(
					'Dossierseps::view' => array( 'label' => 'Voir', 'url' => array( 'controller' => 'historiqueseps', 'action' => 'index', '#Personne.id#' ), 'class' => 'external' )
				),
				'options' => $options,
				'id' => $theme,
				'trClass' => $trClass,
                'paginate' => 'Dossierep'
			)
		);
	}
?>
	<?php echo $this->Xform->submit( 'Enregistrer les heures de passage', array ( 'name' => 'enregistreheureseance') );?>
</form>
<?php
	echo "</div>";
?>