<?php
	$this->pageTitle = 'Impression des APREs pour l\'état liquidatif';

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	///Fin pagination


	if( empty( $apres ) ) {
		echo $this->Xhtml->tag( 'p', 'Aucune APRE à sélectionner.', array( 'class' => 'notice' ) );
	}
	else {
		$pagination = $this->Xpaginator->paginationBlock( 'Etatliquidatif', array( $this->request->params['pass'][0] ) );

		$headers = array(
			$this->Xpaginator->sort( 'N° Dossier', 'Dossier.numdemrsa' ),
			$this->Xpaginator->sort( 'N° APRE', 'Apre.numeroapre' ),
			$this->Xpaginator->sort( 'Date de demande APRE', 'Apre.datedemandeapre' ),
			$this->Xpaginator->sort( 'Montant forfaitaire', 'Apre.mtforfait' ),
			$this->Xpaginator->sort( 'Nb enfant - 12ans', 'Apre.nbenf12' ),
			$this->Xpaginator->sort( 'Nom bénéficiaire', 'Personne.nom' ),
			$this->Xpaginator->sort( 'Prénom bénéficiaire', 'Personne.prenom' ),
			$this->Xpaginator->sort( 'Adresse', 'Adresse.nomcom' ),
			'Formation',
			'Bénéficiaire',
			'Tiers prestataire',

		);

		///
		$thead = $this->Xhtml->tag( 'thead', $this->Xhtml->tableHeaders( $headers ) );

		echo $this->Xform->create( 'Etatliquidatif' );
		// FIXME
		echo '<div>'.$this->Xform->input( 'Etatliquidatif.id', array( 'type' => 'hidden', 'value' => $this->request->params['pass'][0] ) ).'</div>';

		/// Corps du tableau
		$rows = array();
		$libelleNatureaide = null;

		foreach( $apres as $i => $apre ) {
			if( $typeapre == 'F' ) {
				$apre['Apre']['allocation'] = $apre['Apre']['mtforfait'];
				$isTiers = false;
				$dest = null;
			}
			else if( $typeapre == 'C' ) {
				$apre['Apre']['allocation'] = $apre['ApreEtatliquidatif']['montantattribue'];
				$aidesApre = array();
				$isTiers = false;
				$modelsFormation = array( 'Formqualif', 'Formpermfimo', 'Permisb', 'Actprof' );
				$natureaide = Set::classicExtract( $apre, 'Apre.nomaide' );
				if( !empty( $natureaide ) ) {
					$aidesApre = $natureaide;
					if( in_array( $natureaide, $modelsFormation ) ){
						$dest = 'tiersprestataire';
						$isTiers = true;
						$libelleNatureaide = __d( 'apre', $natureaide ); // FIXME: traduction
					}
					else{
						$dest = 'beneficiaire';
						$isTiers = false;
						$libelleNatureaide = 'Hors formation';
					}
				}
			}
			else {
				$this->cakeError( 'error500' );
			}

			$apre_id = Set::classicExtract( $apre, 'Apre.id' );
			$rows[] = array(
				Set::classicExtract( $apre, 'Dossier.numdemrsa' ),
				Set::classicExtract( $apre, 'Apre.numeroapre' ),
				$this->Locale->date( 'Date::short', Set::classicExtract( $apre, 'Apre.datedemandeapre' ) ),
				$this->Locale->money( Set::classicExtract( $apre, 'Apre.allocation' ) ),
				Set::classicExtract( $apre, 'Apre.nbenf12' ),
				Set::classicExtract( $apre, 'Personne.nom' ),
				Set::classicExtract( $apre, 'Personne.prenom' ),
				Set::classicExtract( $apre, 'Adresse.nomcom' ),
				$libelleNatureaide,
				$this->Theme->button( 'print', array( 'action' => 'impression', $apre_id, $this->request->params['pass'][0], 'dest' => 'beneficiaire' ) ),
				$this->Theme->button( 'print', array( 'action' => 'impression', $apre_id, $this->request->params['pass'][0], 'dest' => 'tiersprestataire' ), array( 'enabled' =>  $isTiers ) ),
			);
		}
		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $rows, array( 'class' => 'odd' ), array( 'class' => 'even' ) ) );

		echo $pagination;
		echo $this->Xhtml->tag( 'table', $thead.$tbody, array( 'class' => 'nocssicons' ) );
		echo $pagination;

		echo $this->Xform->end();
	}
?>
<?php if( $typeapre == 'F' ) :?>
	<ul class="actionMenu">
		<li><?php
			echo $this->Xhtml->printCohorteLink(
				'Imprimer la cohorte',
				Set::merge(
					array(
						'action' => 'impressions',
						$this->request->params['pass'][0],
					),
					$this->request->params['named'],
					Hash::flatten( $this->request->data, '__' )
				)
			);
		?></li>
	</ul>
<?php endif;?>

<?php echo $this->Default->button( 'back', array( 'action' => 'index' ), array( 'id' => 'Back' ) ); ?>