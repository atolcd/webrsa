<?php  $this->pageTitle = 'Identification du flux';?>

	<h1><?php echo $this->pageTitle;?></h1>

	<?php if( empty( $identflux ) ):?>
		<p class="notice">Aucun flux reçu.</p>
	<?php else: ?>
		<table class="tooltips">
			<thead>
				<tr>
					<th>Code application partenaire</th>
					<th>N° de version</th>
					<th>Type de flux</th>
					<th>Nature du flux</th>
					<th>Date de création du flux</th>
					<th>Heure de création du flux</th>
					<th>Date de référence</th>
				</tr>
			</thead>
			<tbody>
					<?php
						foreach( $identflux as $ident ) {
							echo $this->Xhtml->tableCells(
								array(
									h( $ident['Identificationflux']['applieme'] ),
									h( $ident['Identificationflux']['numversionapplieme'] ),
									h( $ident['Identificationflux']['typeflux'] ),
									h( $ident['Identificationflux']['natflux'] ),
									h( date_short( $ident['Identificationflux']['dtcreaflux'] ) ) ,
									h( $ident['Identificationflux']['heucreaflux'] ),
									h( date_short( $ident['Identificationflux']['dtref'] ) ) ,
								),
								array( 'class' => 'odd' ),
								array( 'class' => 'even' )
							);
						}
					?>
			</tbody>
		</table>
	<?php  endif;?>
<div class="clearer"><hr /></div>