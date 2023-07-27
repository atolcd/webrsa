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
					<th width="25%"><?= __d('modecontact', 'tel'); ?></th>
					<th width="40%"><?= __d('modecontact', 'mail'); ?></th>
					<th width="25%"><?= __d('modecontact', 'date'); ?></th>
					<th width="10%"><?= __d('modecontact', 'actions'); ?></th>
				</thead>
				<tbody>
					<?php foreach($modescontactfoyer as $key => $m):?>
						<tr class= <?php echo ($key%2 == 0) ? "even" : "odd" ?> >
							<td><?= $m['Modecontact']['numtel']?></td>
							<td><?= $m['Modecontact']['adrelec']?></td>
							<td><?= $m['Modecontact']['modified']?></td>
							<td><a href='/Modescontact/view/<?= $m['Modecontact']['id']?>'><img src="/img/icons/zoom.png"><?= __d('modecontact', 'voir'); ?></a></td>
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

		const ongletfoyer = document.querySelectorAll("a[href='#foyer']");
		const ongletDEM = document.querySelectorAll("a[href='#DEM']");
		const ongletCJT = document.querySelectorAll("a[href='#CJT']");

		//Si on arrive directement sur un onglet autre que le premier
		if (<?= $onglet?> === DEM) {
			ongletfoyer[0].classList.remove("active");
			ongletDEM[0].classList.add("active");

			document.getElementById('foyer').style.display = "none";
			document.getElementById('DEM').style = "";

			if (ongletCJT.length) {
				ongletCJT[0].classList.remove("active");
				document.getElementById('CJT').style.display = "none";
			}
		} else if(<?= $onglet?> === CJT) {
			ongletfoyer[0].classList.remove("active");
			ongletDEM[0].classList.remove("active");
			ongletCJT[0].classList.add("active");

			document.getElementById('foyer').style.display = "none";
			document.getElementById('DEM').style.display = "none";
			document.getElementById('CJT').style = "";
		}



		tab = [
			[
				[ongletfoyer[0], 'foyer'],
				[[ongletDEM[0], 'DEM']]
			],
			[
				[ongletDEM[0], 'DEM'],
				[[ongletfoyer[0], 'foyer']]
			]
		];

		if (ongletCJT.length) {
			tab[0][1].push([ongletCJT[0], 'CJT']);
			tab[1][1].push([ongletCJT[0], 'CJT']);
			tab.push([[ongletCJT[0], 'CJT'], [[ongletDEM[0], 'DEM'], [ongletfoyer[0], 'foyer']]]);
		}


		//Pour basculer d'onglet en onglet
		//On doit court-circuiter le fonctionnement de base pour arriver sur un onglet précis et
		//non pas le premier onglet de la liste
		tab.forEach(
			element => element[0][0].onclick = function() {
				element[0][0].classList.add("active");
				document.getElementById(element[0][1]).style = "";
				element[1].forEach(function(e){
					e[0].classList.remove("active");
					document.getElementById(e[1]).style.display = "none";
				});
			}
		);
	});

</script>
