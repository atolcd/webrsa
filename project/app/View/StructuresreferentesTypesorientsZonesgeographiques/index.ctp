<?= $this->Default3->titleForLayout(); ?>
<?= $this->Default3->actions(['/parametrages/index' => array('class' => 'back')]); ?>
<?php
echo '<ul class="actionMenu">'
		.'<li>'
		. $this->Xhtml->exportLink(
			__m('exportcsv'),
			array( 'controller' => 'StructuresreferentesTypesorientsZonesgeographiques', 'action' => 'exportcsv_index' )
		)
		.'</li>'
	.'</ul>';
?>
<table id="TableStructuresreferentesTypesorientsZonesgeographiquesIndex" class="thematiquesrdvs index">
	<thead>
		<tr>
			<th><?= __m('zonegeographique.libelle')?></th>
			<?php
				//pour chaque type orient enfant on ajoute une colonne
				foreach ($typesorients as $typeorient){
					echo '<th>'.$typeorient.'</th>';
				}
			?>
			<th><?= __m('actions')?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$class = 'odd';
			//pour chaque ville, on crée une ligne et on remplit si on a des infos
			foreach ($villes as $keyville => $ville){
				echo '<tr class='.$class.'>';
					echo '<td>'.$ville.'</td>';
					foreach ($typesorients as $keytypeorient => $typeorient){
						echo '<td>'.$structuresreferentes[$keyville][$keytypeorient].'</td>';
					}
					echo '<td class="action"><a href="/StructuresreferentesTypesorientsZonesgeographiques/edit/'.$keyville.'" title="'.__m('modifier.title').'" class="edit">'.__m('modifier').'</a></td>';
				echo '</tr>';

				$class = $class == 'odd' ? 'even' : 'odd';
			}
		?>
	</tbody>

</table>
<?= $this->Default3->actions(['/parametrages/index' => array('class' => 'back')]); ?>