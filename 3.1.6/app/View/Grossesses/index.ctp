<?php  $this->pageTitle = 'Grossesses de la personne';?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $grossesse ) ):?>
	<p class="notice">Cette personne n'a pas eu de grossesses.</p>

<?php else:?>
		<table class="tooltips">
			<thead>
				<tr>
					<th>Date de début de grossesse</th>
					<th>Date de fin de grossesse</th>
					<th>Date de déclaration de grossesse</th>
					<th>Nature de l'interruption</th>

				</tr>
			</thead>
			<tbody>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( date_short( $grossesse['Grossesse']['ddgro'] ) ),
							h( date_short( $grossesse['Grossesse']['dfgro'] ) ),
							h( date_short( $grossesse['Grossesse']['dtdeclgro'] ) ),
							h( value( $natfingro, Set::classicExtract( $grossesse, 'Grossesse.natfingro' ) ) ),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			</tbody>
		</table>
<?php endif;?>