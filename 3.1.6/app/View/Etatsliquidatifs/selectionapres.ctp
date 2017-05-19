<?php
	$this->pageTitle = 'Sélection des APREs pour l\'état liquidatif';

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	if( empty( $apres ) ) {
		echo $this->Xform->create( 'Etatliquidatif' );
		echo $this->Xhtml->tag( 'p', 'Aucune APRE à sélectionner.', array( 'class' => 'notice' ) );
		$buttons = array();
		$buttons[] = $this->Xform->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		echo $this->Xhtml->tag( 'div', implode( '', $buttons ), array( 'class' => 'submit' ) );
		echo $this->Xform->end();
	}
	else {
		$headers = array(
			'N° Dossier',
			'N° APRE',
			'Date de demande APRE',
			'Montant forfaitaire',
			'Nb enfant - 12ans',
			'Nom bénéficiaire',
			'Prénom bénéficiaire',
			'Adresse',
			'Sélectionner'
		);

		///
		$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $headers ) );

		echo $this->Xform->create( 'Etatliquidatif' );
		// FIXME
		echo '<div>'.$this->Xform->input( 'Etatliquidatif.id', array( 'type' => 'hidden', 'value' => $this->request->params['pass'][0] ) ).'</div>';

		/// Corps du tableau
		$rows = array();
		foreach( $apres as $i => $apre ) {

			if( $typeapre == 'C' ){
				$montant = ( Set::classicExtract( $apre, 'Apre.mtforfait' ) + Set::classicExtract( $apre, 'Apre.montantaverser' ) );
			}
			else if( $typeapre == 'F' ) {
				$montant = Set::classicExtract( $apre, 'Apre.mtforfait' );
			}
			$apre_id = Set::classicExtract( $apre, 'Apre.id' );
			$rows[] = array(
				Set::classicExtract( $apre, 'Dossier.numdemrsa' ),
				Set::classicExtract( $apre, 'Apre.numeroapre' ),
				$this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Apre.datedemandeapre' ) ),
				$this->Locale->money( $montant ),
				Set::classicExtract( $apre, 'Apre.nbenf12' ),
				Set::classicExtract( $apre, 'Personne.nom' ),
				Set::classicExtract( $apre, 'Personne.prenom' ),
				Set::classicExtract( $apre, 'Adresse.nomcom' ),
				$this->Xform->checkbox( "Apre.Apre.$i", array( 'value' => $apre_id, 'checked' => ( in_array( $apre_id, $this->request->data['Apre']['Apre'] ) ), 'class' => 'checkbox' ) )
			);
		}
		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $rows, array( 'class' => 'odd' ), array( 'class' => 'even' ) ) );

		///
		echo $this->Xhtml->tag(
			'ul',
			implode(
				'',
				array(
					$this->Xhtml->tag( 'li', $this->Xhtml->link( 'Tout sélectionner', '#', array( 'onclick' => 'allCheckboxes( true ); return false;' ) ) ),
					$this->Xhtml->tag( 'li', $this->Xhtml->link( 'Tout désélectionner', '#', array( 'onclick' => 'allCheckboxes( false ); return false;' ) ) ),
				)
			)
		);

		echo $this->Xhtml->tag( 'p', sprintf( '%s APREs dans la liste', $this->Locale->number( count( $apres ) ) ) );
		echo $this->Xhtml->tag( 'table', $thead.$tbody );

		$buttons = array();
		$buttons[] = $this->Xform->submit( 'Valider la liste', array( 'div' => false ) );
		$buttons[] = $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
		echo $this->Xhtml->tag( 'div', implode( '', $buttons ), array( 'class' => 'submit' ) );

		echo $this->Xform->end();
	}
?>
<script type="text/javascript">
//<![CDATA[
	function allCheckboxes( checked ) {
		$$('input.checkbox').each( function ( checkbox ) {
			$( checkbox ).checked = checked;
		} );
		return false;
	}
//]]>
</script>