<h1><?php echo $this->pageTitle = 'Liste des décisions des dossiers d\'EP';?></h1>

<?php if( empty( $dossierseps ) ):?>
	<p class="notice">Il n'y a pas encore de décision visible.</p>
<?php else:?>
	<?php
		$pagination = $this->Xpaginator->paginationBlock( 'Dossierep', $this->passedArgs );

		echo $pagination;
	?>
	<table>
		<thead>
			<tr>
				<th><?php echo $this->Xpaginator->sort( 'Dossier EP', 'Dossierep.id' );?></th>
				<th>Nom du demandeur</th>
				<th>Adresse</th>
				<th>Date de naissance</th>
				<th><?php echo $this->Xpaginator->sort( 'Thème du dossier EP', 'Dossierep.themeep' );?></th>
				<th>Date de décision</th>
				<th>Décision</th>
			</tr>
		</thead>
		<tbody>
	<?php
		foreach( $dossierseps as $i => $dossierep ) {
			// FIXME: voir contrôleur -> plus générique
			$decision = null;
			$datedecision = null;

			// CG 66
			if( $dossierep['Dossierep']['themeep'] == 'defautsinsertionseps66' ) {
				$decision = Set::enum(
					@$dossierep['Defautinsertionep66']['Decisiondefautinsertionep66'][0]['decision'],
					$decisions['Defautinsertionep66']
				);
				$datedecision = @$dossierep['Defautinsertionep66']['Decisiondefautinsertionep66'][0]['modified'];
			}
			else if( $dossierep['Dossierep']['themeep'] == 'saisinesbilansparcourseps66' ) {
				$decision = Set::enum(
					@$dossierep['Saisinebilanparcoursep66']['Decisionsaisinebilanparcoursep66'][0]['decision'],
					$decisions['Saisinebilanparcoursep66']
				);
				$datedecision = @$dossierep['Saisinebilanparcoursep66']['Decisionsaisinebilanparcoursep66'][0]['modified'];
			}
			else if( $dossierep['Dossierep']['themeep'] == 'saisinespdoseps66' ) {
				$decision = @$dossierep['Saisinepdoep66']['Decisionsaisinepdoep66'][0]['Decisionpdo']['libelle'];
				$datedecision = @$dossierep['Saisinepdoep66']['Decisionsaisinepdoep66'][0]['modified'];
			}
			// CG 93
			else if( $dossierep['Dossierep']['themeep'] == 'reorientationseps93' ) {
				$decision = Set::enum(
					@$dossierep['Reorientationep93']['Decisionreorientationep93'][0]['decision'],
					$decisions['Reorientationep93']
				);
				$datedecision = @$dossierep['Reorientationep93']['Decisionreorientationep93'][0]['modified'];
			}
			else if( $dossierep['Dossierep']['themeep'] == 'nonrespectssanctionseps93' ) {
				$decision = Set::enum(
					@$dossierep['Nonrespectsanctionep93']['Decisionnonrespectsanctionep93'][0]['decision'],
					$decisions['Nonrespectsanctionep93']
				);
				$datedecision = @$dossierep['Nonrespectsanctionep93']['Decisionnonrespectsanctionep93'][0]['modified'];
			}

			echo $this->Xhtml->tableCells(
				array(
					$dossierep['Dossierep']['id'],
					implode( ' ', array( $dossierep['Personne']['qual'], $dossierep['Personne']['nom'], $dossierep['Personne']['prenom'] ) ),
					implode( ' ', array( $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['numvoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['libtypevoie'], $dossierep['Personne']['Foyer']['Adressefoyer'][0]['Adresse']['nomvoie'] ) ),
					$this->Locale->date( __( 'Locale->date' ), $dossierep['Personne']['dtnai'] ),
					Set::enum( $dossierep['Dossierep']['themeep'], $options['Dossierep']['themeep'] ),
					$this->Locale->date( __( 'Locale->date' ), $datedecision ),
					$decision
				)
			);
		}
	?>
		</tbody>
	</table>
	<?php echo $pagination;?>
<?php endif;?>