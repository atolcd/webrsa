<?php  $this->pageTitle = 'Informations agricoles de la personne';?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $infoagricole ) ):?>
	<p class="notice">Cette personne ne possède pas encore d'informations agricoles.</p>
<?php else:?>
		<table class="tooltips">
			<thead>
				<tr>
					<th>Montant bénéfice agricole</th>
					<th>Régime fiscale agricol</th>
					<th>Date du bénéfice</th>
				</tr>
			</thead>
			<tbody>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( $infoagricole['Infoagricole']['mtbenagri']),
							h( $regfisagri[$infoagricole['Infoagricole']['regfisagri']]),
							h( date_short($infoagricole['Infoagricole']['dtbenagri'] ) ),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			</tbody>
		</table>

		<h2>Aides agricoles</h2>
		<table class="tooltips">
			<thead>
				<tr>
					<th>Année de l'aide</th>
					<th>Libellé de l'aide</th>
					<th>Montant de l'aide</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $infoagricole['Aideagricole'] as $aideagricole ):?>
					<?php
						echo $this->Xhtml->tableCells(
							array(
								h( $aideagricole['annrefaideagri']),
								h( $aideagricole['libnataideagri']),
								h( $aideagricole['mtaideagri'] ),
							),
							array( 'class' => 'odd' ),
							array( 'class' => 'even' )
						);
					?>
				<?php endforeach;?>
			</tbody>
		</table>
<?php endif;?>