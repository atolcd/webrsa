<?php $this->pageTitle = 'Détails des indus';?>

<h1><?php echo $this->pageTitle;?></h1>


<?php if( empty( $infofinanciere ) ):?>
	<p class="notice">Ce dossier ne possède pas d'indus.</p>

<?php else:?>
<h2>Généralités</h2>
	<table class="smallHeader aere">
		<tbody>
				<tr class="odd">
					<th class="aere">Type de l'indu</th>
					<td><?php echo $natpfcre[$infofinanciere['Infofinanciere']['natpfcre']];?></td>
				</tr>
				<tr class="even">
					<th class="aere">Motif de l'indu</th>
					<td><?php echo $typeopecompta[$infofinanciere['Infofinanciere']['typeopecompta']];?></td>
				</tr>
				<tr class="odd">
					<th class="aere">Date de l'indu</th>
					<td><?php echo $this->Locale->date( 'Date::short', $infofinanciere['Infofinanciere']['moismoucompta'] );?></td>
				</tr>
				<tr class="even">
					<th class="aere">Date transfert CG</th>
					<td><?php echo $this->Locale->date( 'Date::short', $infofinanciere['Infofinanciere']['dttraimoucompta'] );?></td>
				</tr>
		</tbody>
	</table>

<h2>Montants</h2>
	<table class="aere">
		<thead>
			<tr class="odd">
				<th></th>
				<th>RSA "socle"</th>
				<th>RSA "chapeau"</th>
			</tr>
		</thead>
		<tbody>
			<tr class="even">
				<th class="aere">Montant initial de l'indu</th>
				<td class="number"><?php echo $this->Locale->money( $infofinanciere[0]['mt_indus_constate'] );?></td>
				<td><?php /*echo $this->Locale->money( $infofinanciere[0]['mt_indus_constate'] );*/?></td>
			</tr>
			<tr class="odd">
				<th class="aere">Recouvrement par la CAF</th>
				<td class="number"><?php echo $this->Locale->money( $infofinanciere[0]['mt_remises_indus'] );?></td>
				<td><?php /*echo $this->Locale->money( $infofinanciere[0]['mt_indus_constate'] );*/?></td>
			</tr>
			<tr class="even">
				<th class="aere">Remise accordée par la CAF</th>
				<td class="number"><?php echo $this->Locale->money( $infofinanciere[0]['mt_remises_indus'] );?></td>
				<td><?php /*echo $this->Locale->money( $infofinanciere[0]['mt_indus_constate'] );*/?></td>
			</tr>
			<tr class="odd">
				<th class="aere">Montant transféré</th>
				<td class="number"><?php echo $this->Locale->money( $infofinanciere[0]['mt_indus_transferes_c_g'] );?></td>
				<td><?php /*echo $this->Locale->money( $infofinanciere[0]['mt_indus_constate'] );*/?></td>
			</tr>
			<tr class="even">
				<th class="aere">Remise CG</th>
				<td class="number"><?php echo $this->Locale->money( $infofinanciere[0]['mt_remises_indus'] );?></td>
				<td></td>
			</tr>
			<tr class="odd">
				<th class="aere">Solde final de l'indu</th>
				<td class="number"><?php echo $this->Locale->money( $infofinanciere[0]['mt_indus_transferes_c_g'] - $infofinanciere[0]['mt_remises_indus'] );?></td>
				<td></td>
			</tr>
		</tbody>
	</table>
<?php endif;?>

	<ul class="actionMenu">
		<li>
			<?php
				echo $this->Xhtml->recgraLink(
					'Recours gracieux',
					array( 'controller' => 'recours', 'action' => 'gracieux', $infofinanciere['Dossier']['id'] ),
					$this->Permissions->checkDossier( 'recours', 'gracieux', $dossierMenu )
				);
			?>
		</li>
		<li>
			<?php
				echo $this->Xhtml->recconLink(
					'Recours contentieux',
					array( 'controller' => 'recours', 'action' => 'contentieux', $infofinanciere['Dossier']['id'] ),
					$this->Permissions->checkDossier( 'recours', 'contentieux', $dossierMenu )
				);
			?>
		</li>
	</ul>
	<?php
		echo $this->Default->button(
			'back',
			array(
				'controller' => 'indus',
				'action'     => 'index',
				$dossier_id
			),
			array(
				'id' => 'Back'
			)
		);
	?>