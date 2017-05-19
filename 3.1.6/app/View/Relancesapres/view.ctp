<?php $this->pageTitle = 'Relances pour l\'APRE';?>
<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'une relance';
	}
	else {
		$this->pageTitle = 'Relance d\'APRE ';
		$foyer_id = $this->request->data['Personne']['foyer_id'];
	}
?>
<h1><?php echo 'Relance d\'APRE  ';?></h1>

<div id="ficheCI">
	<table>
		<tbody>
			<tr class="even">
				<th><?php echo __( 'N° dossier APRE');?></th>
				<td><?php echo Set::classicExtract( $apre, 'Apre.numeroapre' );?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __( 'Nom / Prénom bénéficiare' );?></th>
				<td><?php echo ( $apre['Personne']['nom'].' '.$apre['Personne']['prenom'] );?></td>
			</tr>
			<tr class="even">
				<th><?php echo __( 'Date de relance' );?></th>
				<td><?php echo date_short( Set::classicExtract( $relanceapre, 'Relanceapre.daterelance' ) );?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __( 'Etat du dossier APRE' );?></th>
				<td><?php echo Set::enum( Set::classicExtract( $relanceapre, 'Relanceapre.etatdossierapre' ), $options['etatdossierapre'] );?></td>
			</tr>
			<tr class="even">
				<th><?php echo __( 'Commentaire' );?></th>
				<td><?php echo Set::classicExtract( $relanceapre, 'Relanceapre.commentairerelance' );?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __( 'Pièces manquantes' );?></th>
				<td><?php
						$piecesAbsentes = array();
						$piecesPresentesLibelle = Set::classicExtract( $apre, 'Pieceapre.{n}.id' );

						foreach(  $piecesPresentesLibelle as $pieceapre ) {
							if(  !empty( $pieceapre ) )  {
								$piecesAbsentes[] = Set::classicExtract( $piecesapre, $pieceapre );
							}
						};
						echo ( empty( $piecesAbsentes ) ? null :'<ul><li>'.implode( '</li><li>', $piecesAbsentes ).'</li></ul>' );
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>