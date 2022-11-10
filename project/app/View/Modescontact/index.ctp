<?php
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->Default3->titleForLayout($this->request->data);
?>
	<br><br>
	<div id="tabbedWrapper" class="tabs">
		<div id="foyer">
			<h2 class="title">Foyer</h2>
			<h2><?= __d('modecontact', 'foyer.titre'); ?></h2>
			<table width="60%">
				<thead>
					<th width="25%"><?= __d('modecontact', 'date'); ?></th>
					<th width="25%"><?= __d('modecontact', 'tel'); ?></th>
					<th width="50%"><?= __d('modecontact', 'mail'); ?></th>
				</thead>
				<tbody>
					<?php foreach($modescontactfoyer as $key => $m):?>
						<tr class= <?php echo ($key%2 == 0) ? "even" : "odd" ?> >
							<td><?= $m['Modecontact']['modified']?></td>
							<td><?= $m['Modecontact']['numtel']?></td>
							<td><?= $m['Modecontact']['adrelec']?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div id="DEM">
			<h2 class="title"><?= $demandeur['qual'] . " " . $demandeur['nom'] . " " . $demandeur['prenom'] . " (" . $demandeur['rolepers'] . ")"?></h2>
			<?php
				echo $this->element(
					'coordonnees',
					[
						'manuel' => $saisiemanuelleDEM,
						'caf' => $fluxcontactDEM,
						'personne' => $demandeur
					]
	);
			?>
		</div>
		<!-- Si il y a un conjoint, on affiche un onglet -->
		<?php if(isset($conjoint)): ?>
		<div id="CJT">
			<h2 class="title"><?= $conjoint['qual'] . " " . $conjoint['nom'] . " " . $conjoint['prenom'] . " (" . $conjoint['rolepers'] . ")"?></h2>
			<?php
				echo $this->element(
					'coordonnees',
					[
						'manuel' => $saisiemanuelleCJT,
						'caf' => $fluxcontactCJT,
						'personne' => $conjoint
					]
	);
?>
		</div>
		<?php endif; ?>
</div>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		makeTabbed( 'tabbedWrapper', 2 );


		if(<?= $onglet?> != 'foyer') {
			var ongletfoyer = document.querySelectorAll("a[href='#foyer']");
			var ongletDEM = document.querySelectorAll("a[href='#DEM']");
			var ongletCJT = document.querySelectorAll("a[href='#CJT']");

			ongletfoyer[0].classList.remove("active");
			onglet<?= $onglet?>[0].classList.add("active");

			document.getElementById('foyer').style.display = "none";
			document.getElementById('<?=$onglet?>').style = "";
		}
	});
</script>
