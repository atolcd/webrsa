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
            <th><?php echo __d('accueils', 'Accueil.canton.nonvide.debut') . ' ' . $nbCantons . ' ' . __d('accueils', 'Accueil.canton.nonvide.fin'); ?> </th>
            <th>
            <?php
            echo $this->Html->link(
                __d('accueils', 'Accueil.action.voir'),
                    array(
                        'controller' => 'cantons',
                        'action' => 'index/Search__Canton__cantonvide:1'
                    )
                );
            ?>
            </th>
		</tr>
	</thead>
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