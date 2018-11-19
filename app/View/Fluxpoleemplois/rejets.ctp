<table class="dossiers search tooltips" style="width: 100%;">
	<thead>
		<tr>
			<th class="actions">Code rejet</th>
			<th class="actions">Date rejet</th>
			<th class="actions">NIR</th>
			<th class="actions">Nom</th>
			<th class="actions">Nom marital</th>
			<th class="actions">Prénom</th>
			<th class="actions">Date de naissance</th>
		</tr>
	</thead>
	
	<tbody>
<?php
foreach ($donnees as $donnee) {
?>
		<tr class="odd dynamic">
			<td class="data string "><?php echo ($donnee['Fluxpoleemploirejet']['code_rejet']); ?></td>
			<td class="data string "><?php echo ($donnee['Fluxpoleemploirejet']['date_rejet']); ?></td>
			<td class="data string "><?php echo ($donnee['Fluxpoleemploirejet']['individu_nir']); ?></td>
			<td class="data string "><?php echo ($donnee['Fluxpoleemploirejet']['individu_nom_naissance']); ?></td>
			<td class="data string "><?php echo ($donnee['Fluxpoleemploirejet']['individu_nom_marital']); ?></td>
			<td class="data string "><?php echo ($donnee['Fluxpoleemploirejet']['individu_prenom']); ?></td>
			<td class="data string "><?php echo ($donnee['Fluxpoleemploirejet']['individu_date_naissance']); ?></td>
		</tr>
<?php
}
?>
	</tbody>
</table>