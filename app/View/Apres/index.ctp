<?php
    $this->pageTitle = sprintf( 'APREs liées à %s', $personne['Personne']['nom_complet'] );
    $this->modelClass = Inflector::classify( $this->request->params['controller'] );
?>
<h1><?php echo $this->pageTitle;?></h1>
<?php echo $this->element( 'ancien_dossier' );?>
		<!-- Modification de l'affichage suite a la demande du CG93, on peut créer une APRE même si pas de forfaitaire présente-->
		<?php if( empty( $apres ) ):?>
			<p class="notice">Cette personne ne possède pas encore d'APRE.</p>
		<?php endif;?>

		<!-- Harry - modification de l'affichage suite a la demande du CG93, APRE Complémentaire-->
		<?php if( Configure::read( 'Apre.complementaire.query' ) === true ):?>
		<?php if( ( $aprescomp ) ):?>
			<p class="notice">Cette personne (matricule où nom,prénom) possède une entrée dans la table d'APRE complémentaire. Pour plus d'information voir l'application QUERY.</p>
		<?php endif;?>
		<?php endif;?>

		<?php if( $this->Permissions->checkDossier( 'apres'.Configure::read( 'Apre.suffixe' ), 'add', $dossierMenu ) /*&& ( $apre_forfait > 0 )*/ ):?>
			<ul class="actionMenu">
				<?php
					echo '<li>'.$this->Xhtml->addLink(
						'Ajouter APRE',
						array( 'controller' => 'apres'.Configure::read( 'Apre.suffixe' ), 'action' => 'add', $personne_id )
					).' </li>';
				?>
			</ul>
		<?php endif;?>



<?php if( !empty( $apres ) ):?>
<?php
	if( $alerteMontantAides ) {
		echo $this->Xhtml->tag(
			'p',
			$this->Xhtml->image( 'icons/error.png', array( 'alt' => 'Remarque' ) ).' '.sprintf( 'Cette personne risque de bénéficier de plus de %s € d\'aides complémentaires au cours des %s derniers mois', Configure::read( 'Apre.montantMaxComplementaires' ), Configure::read( 'Apre.periodeMontantMaxComplementaires' ) ),
			array( 'class' => 'error' )
		);
	}
?>
<table class="tooltips">
	<thead>
		<tr>
			<th>Statut de l'APRE</th>
			<th>Date demande APRE</th>
			<th>Etat du dossier</th>
			<th>Montant demandé</th>
			<th>Montant attribué</th>
			<th colspan="5" class="action">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach( $apres as $index => $apre ) {
				$aidesApre = array();
				$buttonEnabled = null;
				$mtforfait = null;
				$mtattribue = null;
				$naturesaide = Set::classicExtract( $apre, "{$this->modelClass}.Natureaide" );

				if( !empty( $naturesaide ) ) {
					foreach( $naturesaide as $natureaide => $nombre ) {
						if( $nombre > 0 ) {
							$aidesApre[] = h( Set::classicExtract( $natureAidesApres, $natureaide ) );
							///Calcul des montants versés pour les aides d'une APRE complémentaire
							$montantaide = Set::classicExtract( $apre, "$natureaide.montantaide" );
							$mtforfait += $montantaide;
						}
					}
				}

				$piecesManquantes = Set::extract( $apre, '/Relanceapre/Piecemanquante/libelle' );

				/**
				**  Mise en place de l'impossibilité de modifier/relancer/imprimer les APREs forfaitaires
				**  +
				**  Conditionnement des éléments à afficher selon le statut de l'APRE
				**/
				$statutApre = Set::classicExtract( $apre, "{$this->modelClass}.statutapre" );
				if( $statutApre == 'C' ) {
					$etat = Set::enum( Set::classicExtract( $apre, "{$this->modelClass}.etatdossierapre" ), $options['etatdossierapre'] );
					$mtforfait = $mtforfait;
					if( Configure::read( 'nom_form_apre_cg' ) == 'cg66' ){
						$mtforfait = Set::classicExtract( $apre, 'Aideapre66.montantaide' );
					}
					$buttonEnabled = true;
					if( $etat == 'Complet' ){
						$buttonEnabledInc = false;
					}
					else{
						$buttonEnabledInc = true;
					}

					// Calcul des montants attribués
					$montantsAttribues = Set::extract( $apre, '/Comiteapre/ApreComiteapre[decisioncomite=ACC]/montantattribue' );
					$mtattribue = ( ( is_array( $montantsAttribues ) && !empty( $montantsAttribues ) ) ? array_sum( $montantsAttribues ) : null );
				}
				else if( $statutApre == 'F' ) {
					$etat = Set::enum( Set::classicExtract( $apre, "{$this->modelClass}.etatdossierapre" ), $options['etatdossierapre'] );
					$mtforfait = Set::classicExtract( $apre, "{$this->modelClass}.mtforfait" );
					$buttonEnabled = false;
					$buttonEnabledInc = false;

					// Calcul des montants attribués
					$mtattribue = $mtforfait;
				}

				$innerTable = '<table id="innerTable'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>N° APRE</th>
							<td>'.h( Set::classicExtract( $apre, "{$this->modelClass}.numeroapre" ) ).'</td>
						</tr>
						<tr>
							<th>Nom/Prénom Allocataire</th>
							<td>'.h( $apre['Personne']['nom'].' '.$apre['Personne']['prenom'] ).'</td>
						</tr>
						<tr>
							<th>Référent APRE</th>
							<td>'.h( Hash::get( $apre, 'Referent.nom_complet' ) ).'</td>
						</tr>
						<tr>
							<th>Natures de la demande</th>
							<td>'.( empty( $aidesApre ) ? null :'<ul><li>'.implode( '</li><li>', $aidesApre ).'</li></ul>' ).'</td>
						</tr>
					</tbody>
				</table>';

				echo $this->Xhtml->tableCells(
					array(
						h( Set::enum( $statutApre, $options['statutapre'] ) ),
						h( date_short( Set::classicExtract( $apre, "{$this->modelClass}.datedemandeapre" ) ) ),
						h( $etat ),
						h( $this->Locale->money( $mtforfait ) ),
						h( $this->Locale->money( $mtattribue ) ),
						$this->Xhtml->viewLink(
							'Voir la demande APRE',
							array( 'controller' => 'apres'.Configure::read( 'Apre.suffixe' ), 'action' => 'view', $apre[$this->modelClass]['id'] ),
							$this->Permissions->checkDossier( 'apres'.Configure::read( 'Apre.suffixe' ), 'view', $dossierMenu )
						),
						$this->Xhtml->editLink(
							'Editer la demande APRE',
							array( 'controller' => 'apres'.Configure::read( 'Apre.suffixe' ), 'action' => 'edit', $apre[$this->modelClass]['id'] ),
							$buttonEnabled,
							$this->Permissions->checkDossier( 'apres'.Configure::read( 'Apre.suffixe' ), 'edit', $dossierMenu )
						),
						$this->Xhtml->relanceLink(
							'Relancer la demande APRE',
							array( 'controller' => 'relancesapres', 'action' => 'add', $apre[$this->modelClass]['id'] ),
							$buttonEnabledInc,
							$this->Permissions->checkDossier( 'relancesapres', 'add', $dossierMenu ) && ( $apre[$this->modelClass]['etatdossierapre'] == 'INC' )
						),
						$this->Xhtml->printLink(
							'Imprimer la demande APRE',
							array( 'controller' => 'apres', 'action' => 'impression', $apre[$this->modelClass]['id'] ),
							$buttonEnabled,
							$this->Permissions->checkDossier( 'apres', 'impression', $dossierMenu )
						),
						$this->Xhtml->fileLink(
							'Fichiers liés',
							array( 'controller' => 'apres'.Configure::read( 'Apre.suffixe' ), 'action' => 'filelink', $apre[$this->modelClass]['id'] ),
							$buttonEnabled && $this->Permissions->checkDossier( 'apres'.Configure::read( 'Apre.suffixe' ), 'filelink', $dossierMenu )
						),
						array( $innerTable, array( 'class' => 'innerTableCell' ) )
					),
					array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
					array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
				);
			}
		?>
	</tbody>
</table>
<?php endif;?>

<br />

<?php if( !empty( $apres ) ):?>

	<h2>Liste des relances</h2>
	<?php if( empty( $relancesapres ) ):?>
		<p class="notice">Cette personne ne possède pas encore de relances.</p>
	<?php endif;?>

	<?php if( !empty( $apres ) && !empty( $relancesapres ) ):?>
	<table class="tooltips">
		<thead>
			<tr>
				<th>N° Apre</th>
				<th>Date de relance</th>
				<th>Liste des pièces manquantes</th>
				<th>Commentaire</th>
				<th colspan="3" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach( $relancesapres as $relanceapre ) {
					$piecesAbsentes = Set::extract( $relanceapre, '/Relanceapre/Piecemanquante/libelle' );
					$piecesManquantesAides = Set::classicExtract( $relanceapre, "{$this->modelClass}.Piece.Manquante" );

					$textePiecesManquantes = '';
					foreach( $piecesManquantesAides as $model => $pieces ) {
						if( !empty( $pieces ) ) {
							$textePiecesManquantes .= $this->Xhtml->tag( 'h3', __d( 'apre', $model ) ).'<ul><li>'.implode( '</li><li>', $pieces ).'</li></ul>';
						}
					}

					$piecesRestantes = '';
					$piecesRelances = Set::classicExtract( $relanceapre, 'Relanceapre.listepiecemanquante' );
					$piecesRestantes .= $this->Xhtml->tag( 'h3', '' ).$piecesRelances;

					echo $this->Xhtml->tableCells(
						array(
							h( Set::classicExtract( $relanceapre, "{$this->modelClass}.numeroapre" ) ),
							h( date_short( Set::classicExtract( $relanceapre, 'Relanceapre.daterelance' ) ) ),
							$piecesRestantes,
							h( Set::classicExtract( $relanceapre, 'Relanceapre.commentairerelance' ) ),
							$this->Xhtml->editLink(
								'Editer la relance',
								array( 'controller' => 'relancesapres', 'action' => 'edit', Set::classicExtract( $relanceapre, 'Relanceapre.id' ) ),
								( $this->Permissions->checkDossier( 'relancesapres', 'edit', $dossierMenu ) && Set::classicExtract( $relanceapre, 'Relanceapre.id' ) == $lastrelance_id  )
							),
							$this->Xhtml->printLink(
								'Imprimer la notification de relance',
								array( 'controller' => 'relancesapres', 'action' => 'impression', Set::classicExtract( $relanceapre, 'Relanceapre.id' ) ),
								$this->Permissions->checkDossier( 'relancesapres', 'impression', $dossierMenu )
							)
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}
			?>
		</tbody>
	</table>
	<?php  endif;?>
<?php  endif;?>