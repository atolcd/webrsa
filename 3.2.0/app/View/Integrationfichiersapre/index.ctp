<?php
	$this->pageTitle = 'Journal d\'intégration des fichiers CSV pour l\'APRE';
	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
?>

<?php if( !empty( $integrationfichiersapre ) ):?>
	<?php
		$pagination = $this->Xpaginator->paginationBlock( 'Integrationfichierapre', $this->passedArgs );
	?>

	<?php echo $pagination;?>
	<table>
		<thead>
			<tr>
				<th><?php echo $this->Xpaginator->sort( 'Date d\'intégration', 'Integrationfichierapre.date_integration' );?></th>
				<th><?php echo $this->Xpaginator->sort( 'À traiter', 'Integrationfichierapre.nbr_atraiter' );?></th>
				<th><?php echo $this->Xpaginator->sort( 'Traité', 'Integrationfichierapre.nbr_succes' );?></th>
				<th><?php echo $this->Xpaginator->sort( 'En erreur', 'Integrationfichierapre.nbr_erreurs' );?></th>
				<th><?php echo $this->Xpaginator->sort( 'Fichier', 'Integrationfichierapre.fichier_in' );?></th>
				<th class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach( $integrationfichiersapre as $integration ) {
					echo $this->Xhtml->tableCells(
						array(
							$this->Locale->date( 'Datetime::short', Set::classicExtract( $integration, 'Integrationfichierapre.date_integration' ) ),
							$this->Locale->number( Set::classicExtract( $integration, 'Integrationfichierapre.nbr_atraiter' ) ),
							$this->Locale->number( Set::classicExtract( $integration, 'Integrationfichierapre.nbr_succes' ) ),
							$this->Locale->number( Set::classicExtract( $integration, 'Integrationfichierapre.nbr_erreurs' ) ),
							h( Set::classicExtract( $integration, 'Integrationfichierapre.fichier_in' ) ),
							$this->Xhtml->link(
								'Télécharger rejet',
								array( 'controller' => 'integrationfichiersapre', 'action' => 'download', Set::classicExtract( $integration, 'Integrationfichierapre.id' ) )
							),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}
			?>
		</tbody>
	</table>
	<?php echo $pagination;?>
<?php else:?>
	<p class="notice">Aucune intégration de fichier CSV pour l'APRE n'a encore été effectuée.</p>
<?php endif;?>