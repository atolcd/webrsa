<?php echo $this->Xform->input(
	false, array('type' => 'hidden', 'name' => 'data[controller]', "value" => $this->request->data['controller'])
);?>
<table class="tooltips">
	<thead>
		<tr>
			<th>Groupe</th>
			<th>Accès total</th>
			<?php echo '<th>'.implode("</th>\n\t\t\t<th>", $actions)."</th>\n"; ?>
		</tr>
	</thead>
	<tbody>
		<?php
foreach ($groups as $id => $name) {
	$cells = array(h($name));
	
	$cells[] = $this->Xform->input(
		false, array(
			'type' => 'checkbox',
			'class' => 'module_checkbox',
			'name' => 'data[module]['.$id.']',
			'checked' => Hash::get($module, $id),
		)
	);
	
	foreach ($actions as $action) {
		$keyTrad = $controllerNameShort.':'.$action;
		$trad = __d('droit', $keyTrad);
		$traduction = $trad === $keyTrad ? $action : $action.' ('.$trad.')';
		
		$cells[] = $this->Xform->input(
			false, array(
				'type' => 'checkbox',
				'name' => 'data['.$action.']['.$id.']',
				'checked' => Hash::get($data, $action.'.'.$id),
				'title' => h($name).' - '.$traduction,
			)
		);
	}
	
	echo $this->Xhtml->tableCells($cells, array('class' => 'odd'), array('class' => 'even'));
}
		?>
	</tbody>
</table>
<br/>
<div class="input submit center">
	<input type="submit" value="Sauvegarder"/>
</div>