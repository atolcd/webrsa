<?php  $this->pageTitle = 'Détails des droits RSA';?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $detaildroitrsa ) ):?>
	<p class="notice">Ce dossier ne possède pas encore de détails sur les droits.</p>

<?php else:?>
	<table class="aere">
		<thead>
			<tr>
				<th>Domicile fixe</th>
				<th>Code origine de la demande</th>
				<th>Date début calcul</th>
				<th>Date de fin calcul</th>
				<th>Montant revenu minimum</th>
				<th>Montant revenu garanti</th>
				<th>Montant ressources mensuelles</th>
				<th>Montant total</th>
			</tr>
		</thead>
		<tbody>
			<?php
				echo $this->Xhtml->tableCells(
					array(
						h( $topsansdomfixe[$detaildroitrsa['Detaildroitrsa']['topsansdomfixe']]),
						h( $oridemrsa[$detaildroitrsa['Detaildroitrsa']['oridemrsa']]),
						h( $this->Locale->date( 'Date::short', $detaildroitrsa['Detaildroitrsa']['ddelecal'] ) ),
						h( $this->Locale->date( 'Date::short', $detaildroitrsa['Detaildroitrsa']['dfelecal'] ) ),
						h( $detaildroitrsa['Detaildroitrsa']['mtrevminigararsa'] ),
						h( $detaildroitrsa['Detaildroitrsa']['mtrevgararsa'] ),
						h( $detaildroitrsa['Detaildroitrsa']['mtressmenrsa'] ),
						h( $detaildroitrsa['Detaildroitrsa']['mttotdrorsa'] ),
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			?>
		</tbody>
	</table>

	<h2>Détails des calculs</h2>
	<table class="tooltips">
		<thead>
			<tr>
				<th>Nature de la prestation</th>
				<th>Sous nature de la prestation</th>
				<th>Date de début du calcul de la sous nature de la prestation</th>
				<th>Date de fin du calcul de la sous nature de la prestation</th>
				<th>Montant du dernier mois de calcul du droit au RSA</th>
				<th>Date du dernier mois de calcul du droit au RSA</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $detaildroitrsa['Detailcalculdroitrsa'] as $detailcalcul ):?>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( $natpf[$detailcalcul['natpf']]),
							h( $sousnatpf[$detailcalcul['sousnatpf']]),
							h( $this->Locale->date( 'Date::short', $detailcalcul['ddnatdro'] ) ),
							h( $this->Locale->date( 'Date::short', $detailcalcul['dfnatdro'] ) ),
							h( $detailcalcul['mtrsavers'] ),
							h( $this->Locale->date( 'Date::short', $detailcalcul['dtderrsavers'] ) ),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				?>
			<?php endforeach;?>
		</tbody>
	</table>
<?php endif;?>