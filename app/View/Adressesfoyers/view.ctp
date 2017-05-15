<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);
	
	$title = implode(
		' ',
		array(
			$adresse['Adresse']['numvoie'],
			$adresse['Adresse']['libtypevoie'],
			$adresse['Adresse']['nomvoie']
		)
	);

	$this->pageTitle = 'Visualisation de l\'adresse « '.$title.' »';
?>
<h1><?php echo 'Visualisation de l\'adresse « '.$title.' »';?></h1>

<ul class="actionMenu">
	<?php
		if( $this->Permissions->checkDossier( 'adressesfoyers', 'edit', $dossierMenu ) ) {
			echo '<li>'.$this->Xhtml->editLink(
				'Éditer l\'adresse « '.$title.' »',
				array( 'controller' => 'adressesfoyers', 'action' => 'edit', $adresse['Adressefoyer']['id'] ),
				WebrsaAccess::isEnabled($adresse, '/adressesfoyers/edit')
			).' </li>';
		}
	?>
</ul>

<div id="ficheAdresse">
	<h2>Informations adresse</h2>
	<table>
		<tbody>
			<tr class="odd">
				<th><?php echo __d( 'adressefoyer', 'Adressefoyer.rgadr' );?></th>
				<td><?php echo isset( $rgadr[$adresse['Adressefoyer']['rgadr']] ) ? $rgadr[$adresse['Adressefoyer']['rgadr']] : null ;?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'adressefoyer', 'Adressefoyer.dtemm' );?></th>
				<td><?php echo date_short( $adresse['Adressefoyer']['dtemm'] );?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'adressefoyer', 'Adressefoyer.typeadr' );?></th>
				<td><?php echo $typeadr[$adresse['Adressefoyer']['typeadr']];?></td>
			</tr>
		</tbody>
	</table>
	<h2>Adresse</h2>
	<table>
		<tbody>
			<tr class="even">
				<th><?php echo __d( 'adresse', 'Adresse.numvoie' );?></th>
				<td><?php echo $adresse['Adresse']['numvoie'];?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'adresse', 'Adresse.libtypevoie' );?></th>
				<td><?php echo $adresse['Adresse']['libtypevoie'];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'adresse', 'Adresse.nomvoie' );?></th>
				<td><?php echo $adresse['Adresse']['nomvoie'];?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'adresse', 'Adresse.complideadr' );?></th>
				<td><?php echo $adresse['Adresse']['complideadr'];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'adresse', 'Adresse.compladr' );?></th>
				<td><?php echo $adresse['Adresse']['compladr'];?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'adresse', 'Adresse.lieudist' );?></th>
				<td><?php echo $adresse['Adresse']['lieudist'];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'adresse', 'Adresse.numcom' );?></th>
				<td><?php echo $adresse['Adresse']['numcom'];?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'adresse', 'Adresse.codepos' );?></th>
				<td><?php echo $adresse['Adresse']['codepos'];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'adresse', 'Adresse.nomcom' );?></th>
				<td><?php echo $adresse['Adresse']['nomcom'];?></td>
			</tr>
			<tr class="odd">
				<th><?php echo __d( 'adresse', 'Adresse.pays' );?></th>
				<td><?php echo $pays[$adresse['Adresse']['pays']];?></td>
			</tr>
			<tr class="even">
				<th><?php echo __d( 'adresse', 'Adresse.canton' );?></th>
				<td><?php echo $adresse['Adresse']['canton'];?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'adressesfoyers',
			'action'     => 'index',
			$adresse['Adressefoyer']['foyer_id']
		),
		array(
			'id' => 'Back'
		)
	);
?>