<div class="col-2-accueil">
<h2>
	<?php
		$nbCantons = $results['cantons'];
		$titre = __d('accueils', 'Accueil.canton.titre');
		echo $titre;
	?>
</h2>
<table>
	<?php
		if ($nbCantons > 0) {
	?>
	<thead>
		<tr>
            <th><?php echo __d('accueils', 'Accueil.canton.nonvide.debut')?> </th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="color: red;">
				<?php
					echo '<b>' . $nbCantons . '</b> ' . __d('accueils', 'Accueil.canton.nonvide.fin'). '<br><br>';
					echo '<b>' . $this->Html->link(
							__d('accueils', 'Accueil.action.voir'),
							array(
								'controller' => 'cantons',
								'action' => 'index/Search__Canton__cantonvide:1'
							),
						    array (
								'target' => '_blank',
							)
					) . '</b>';
					echo '<br>';
					echo $this->Default3->actions (
						array (
							'/Cantons/adressesnonassociees' => array(
								'class' => 'exportcsv',
								'title' => __d('cantons', '/Cantons/adressesnonassociees/:title'),
								'text' => __d('cantons', '/Cantons/adressesnonassociees')
							),
							'/Cantons/adressessanscanton' => array(
								'class' => 'exportcsv',
								'title' => __d('cantons', '/Cantons/adressessanscanton/:title'),
								'text' => __d('cantons', '/Cantons/adressessanscanton')
							)
						)
					);
				?>
			</td>
		</tr>
	</tbody>
	<?php
		}
		else {
	?>
	<thead>
		<tr>
			<th><?php echo __d('accueils', 'Accueil.canton.aucun'); ?></th>
		</tr>
	</thead>
	<?php
		}
    ?>
    <tbody>
	</tbody>
</table>
</div>