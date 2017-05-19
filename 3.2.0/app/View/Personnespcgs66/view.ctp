<?php
	$this->pageTitle =  __d( 'personnepcg66', "Personnespcgs66::{$this->action}" );
?>
<?php
	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Form->create( 'Personnepcg66', array( 'type' => 'post', 'id' => 'personnepcg66form', 'novalidate' => true ) );

	//Liste des différentes situations de la personne
	$listeSituations = Set::extract( $personnepcg66, '/Situationpdo/libelle' );
	$differentesSituations = '';
	foreach( $listeSituations as $key => $situation ) {
		if( !empty( $situation ) ) {
			$differentesSituations .= $this->Xhtml->tag( 'h3', '' ).'<ul><li>'.$situation.'</li></ul>';
		}
	}

	//Liste des différents statuts de la personne
	$listeStatuts = Set::extract( $personnepcg66, '/Statutpdo/libelle' );
	$differentsStatuts = '';
	foreach( $listeStatuts as $key => $statut ) {
		if( !empty( $statut ) ) {
			$differentsStatuts .= $this->Xhtml->tag( 'h3', '' ).'<ul><li>'.$statut.'</li></ul>';
		}
	}
	echo $this->Default2->view(
		$personnepcg66,
		array(
			'Personne.nom_complet' => array( 'type' => 'string', 'value' => '#Personne.qual# #Personne.nom# #Personne.prenom#' ),
			'Situationpdo.libelle' => array( 'value' => $differentesSituations ),
			'Statutpdo.libelle' => array( 'value' => $differentsStatuts ),
		),
		array(
			'class' => 'aere'
		)
	);
?>
<div class="submit">
	<?php
		echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Form->end();?>