<?php $this->pageTitle = 'Visualisation des Informations financières';?>

<h1><?php echo $this->pageTitle;?></h1>
<div id="ficheInfoFina">
	<table>
		<tbody>
			<tr class="odd">
				<th ><?php echo __d( 'personne', 'Personne.nom_complet' );?></th>
				<td><?php echo $personne['Personne']['nom_complet'];?></td>
			</tr>
			<tr class="even">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.moismoucompta' );?></th>
				<td><?php echo  h( strftime('%B %Y', strtotime( $infofinanciere['Infofinanciere']['moismoucompta'] ) ) );?></td>
			</tr>
			<tr class="odd">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.type_allocation' );?></th>
				<td><?php echo ($type_allocation[$infofinanciere['Infofinanciere']['type_allocation']]);?></td>
			</tr>
			<tr class="even">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.natpfcre' );?></th>
				<td><?php echo ($natpfcre[$infofinanciere['Infofinanciere']['natpfcre']]);?></td>
			</tr>
			<tr class="odd">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.rgcre' );?></th>
				<td><?php echo ($infofinanciere['Infofinanciere']['rgcre']);?></td>
			</tr>
			<tr class="even">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.numintmoucompta' );?></th>
				<td><?php echo ($infofinanciere['Infofinanciere']['numintmoucompta']);?></td>
			</tr>
			<tr class="odd">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.typeopecompta' );?></th>
				<td><?php echo ($typeopecompta[$infofinanciere['Infofinanciere']['typeopecompta']]);?></td>
			</tr>
			<tr class="even">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.sensopecompta' );?></th>
				<td><?php echo ($sensopecompta[$infofinanciere['Infofinanciere']['sensopecompta']]);?></td>
			</tr>
			<tr class="odd">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.mtmoucompta' );?></th>
				<td><?php echo ($infofinanciere['Infofinanciere']['mtmoucompta']);?></td>
			</tr>
			<tr class="even">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.ddregu' );?></th>
				<td><?php echo (  date_short( $infofinanciere['Infofinanciere']['ddregu'] ) );?></td>
			</tr>
			<tr class="odd">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.dttraimoucompta' );?></th>
				<td><?php echo h( date_short( $infofinanciere['Infofinanciere']['dttraimoucompta'] ) );?></td>
			</tr>
			<tr class="even">
				<th ><?php echo __d( 'infofinanciere', 'Infofinanciere.heutraimoucompta' );?></th>
				<td><?php echo (  $this->Locale->date( 'Time::short', $infofinanciere['Infofinanciere']['heutraimoucompta'] ) );?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'infosfinancieres',
			'action'     => 'index',
			$dossier_id
		),
		array(
			'id' => 'Back'
		)
	);
?>