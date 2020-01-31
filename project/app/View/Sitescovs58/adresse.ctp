<?php
	echo $this->Default3->titleForLayout($sitecov58);
?>
<br>
<ul class="actions">
	<li class="action">
		<?php
			echo $this->Xhtml->link (
				__d ('sitescovs58', '/Parametrages/index'),
				array ('controller' => 'sitescovs58', 'action' => 'index'),
				array (
					'class' => 'back link',
					'enabled' => true
				)
			);
		?>
    </li>
    <li class="action">
		<?php
			echo $this->Xhtml->link (
				__d ('canton', 'Canton.onglet.general'),
				array ('controller' => 'sitescovs58', 'action' => 'edit', $id),
				array (
					'class' => 'edit link',
					'enabled' => true
				)
			);
		?>
    </li>
    <li class="action">
		<?php
			echo $this->Xhtml->link (
				__d ('canton', 'Canton.onglet.adresse'),
				array ('controller' => 'sitescovs58', 'action' => 'adresse', $id),
				array (
					'class' => 'edit link',
					'enabled' => true
				)
			);
		?>
    </li>
</ul>

<h2><?php echo __d ('canton', 'Canton.titre.adresses.associees').' ('.count ($cantonSitecov58s).')' ?></h2>
<table id="TableSitescovs58Adresse" class="sitescovs58 adresse" style="width: 100%;">
	<thead>
		<tr>
			<th id="TableSitescovs58AdresseColumnCantonCanton"><?php echo __d ('canton', 'Canton.canton') ?></th>
			<th id="TableSitescovs58AdresseColumnZonegeographiqueLibelle"><?php echo __d ('canton', 'Zonegeographique.libelle') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonNumvoie"><?php echo __d ('canton', 'Canton.numvoie') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonLibtypevoie"><?php echo __d ('canton', 'Canton.libtypevoie') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonNomvoie"><?php echo __d ('canton', 'Canton.nomvoie') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonNomcom"><?php echo __d ('canton', 'Canton.nomcom') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonCodepos"><?php echo __d ('canton', 'Canton.codepos') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonNumcom"><?php echo __d ('canton', 'Canton.numcom') ?></th>
			<th class="actions" id="TableSitescovs58AdresseColumnActions"><?php echo __d ('canton', 'Canton.actions') ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($cantonSitecov58s as $key => $canton) {
		$ligne = 'even';
		if ($key%2 == 1) {
			$ligne = 'odd';
		}
	?>
		<tr class="<?php echo $ligne; ?>">
			<td class="data string "><?php echo $canton['Canton']['canton']; ?></td>
			<td class="data string "><?php echo $canton['Zonegeographique']['libelle']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['numvoie']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['libtypevoie']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['nomvoie']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['nomcom']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['codepos']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['numcom']; ?></td>
			<td class="action">
				<a href="/Sitescovs58/adresse/<?php echo $id; ?>/<?php echo $canton['Canton']['id']; ?>/desassocier/" title="<?php echo __d ('canton', 'Canton.desassocier') ?>" class="cantons delete" onclick="if (confirm(&quot;Désassocier ?&quot;)) { return true; } return false;"><?php echo __d ('canton', 'Canton.desassocier') ?></a>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>

<br><br>

<?php
	// Formulaire
	$searchFormId = 'CantonSitecov58IndexForm';
	$actions =  array(
		'/Cantons/index/#toggleform' => array(
			'class' => 'search',
			'onclick' => "$( '{$searchFormId}' ).toggle(); return false;",
			'text' => __d ('canton', 'Canton.formulaire')
		)
	);

	echo $this->Default3->actions( $actions );
	echo $this->Default3->form(
		$this->Translator->normalize(
			array(
				'Search.Canton.canton' => array( 'required' => false ),
				'Search.Canton.nomcom' => array( 'required' => false ),
				'Search.Canton.zonegeographique_id' => array( 'empty' => true, 'required' => false ),
				'Search.Canton.codepos' => array( 'required' => false ),
				'Search.Canton.numcom' => array( 'required' => false ),
			)
		),
		array(
			'buttons' => array( 'Search', 'Reset' ),
			'options' => array( 'Search' => $options ),
			'id' => $searchFormId,
			'class' => isset( $results ) ? 'folded' : 'unfolded'
		)
	);

	echo $this->Observer->disableFormOnSubmit();
?>

<h2><?php echo __d ('canton', 'Canton.titre.adresses.a.associer') ?></h2>
<table id="TableSitescovs58AdresseaAssocier" class="sitescovs58 adresse" style="width: 100%;">
	<thead>
		<tr>
			<th id="TableSitescovs58AdresseColumnCantonCanton"><?php echo __d ('canton', 'Canton.canton') ?></th>
			<th id="TableSitescovs58AdresseColumnZonegeographiqueLibelle"><?php echo __d ('canton', 'Zonegeographique.libelle') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonNumvoie"><?php echo __d ('canton', 'Canton.numvoie') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonLibtypevoie"><?php echo __d ('canton', 'Canton.libtypevoie') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonNomvoie"><?php echo __d ('canton', 'Canton.nomvoie') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonNomcom"><?php echo __d ('canton', 'Canton.nomcom') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonCodepos"><?php echo __d ('canton', 'Canton.codepos') ?></th>
			<th id="TableSitescovs58AdresseColumnCantonNumcom"><?php echo __d ('canton', 'Canton.numcom') ?></th>
			<th class="actions" id="TableSitescovs58AdresseColumnActions"><?php echo __d ('canton', 'Canton.actions') ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($results as $key => $canton) {
		$ligne = 'even';
		if ($key%2 == 1) {
			$ligne = 'odd';
		}
	?>
		<tr class="<?php echo $ligne; ?>">
			<td class="data string "><?php echo $canton['Canton']['canton']; ?></td>
			<td class="data string "><?php echo $canton['Zonegeographique']['libelle']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['numvoie']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['libtypevoie']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['nomvoie']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['nomcom']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['codepos']; ?></td>
			<td class="data string "><?php echo $canton['Canton']['numcom']; ?></td>
			<td class="action">
				<a href="/Sitescovs58/adresse/<?php echo $id; ?>/<?php echo $canton['Canton']['id']; ?>/associer/" title="<?php echo __d ('canton', 'Canton.associer') ?>" class="cantons add"><?php echo __d ('canton', 'Canton.associer') ?></a>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>