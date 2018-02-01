<?php
	echo $this->Default3->titleForLayout();

	function getActionLink($url) {
		$request = parseSearchUrl( $url, array( 'sessionKey' ) );
		if( true === empty( $request ) ) {
			return null;
		}

		$url = Router::reverse( array( 'url' => null ) + $request );
		if( true === WebrsaPermissions::check( $request['controller'], $request['action'] ) ) {
			return '<a href="'.$url.'">Effectuer&nbsp;l\'action</a>';
		}
		else {
			return '<span class="disabled">Effectuer&nbsp;l\'action</a>';
		}
	}

	$route = array(
		'controller' => 'dashboards',
		'action' => 'reset_cache',
	);

	// Séparation des onglets
	$onglets = array();
	foreach ($roles as $key => $role) {
		foreach ($role['Actionrole'] as $actionrole) {
			$onglet = Hash::get($actionrole, 'Categorieactionrole.name');

			if (!isset($onglets[$onglet][$key]['Role'])) {
				$onglets[$onglet][$key]['Role'] = $role['Role'];
			}

			if (!isset($onglets[$onglet][$key]['Actionrole'])) {
				$onglets[$onglet][$key]['Actionrole'] = array();
			}

			$onglets[$onglet][$key]['Actionrole'][] = $actionrole;
		}
	}

	ksort($onglets);
?>

<div id="tabbedWrapper" class="tabs">
<?php
	foreach ($onglets as $ongletName => $onglet) {
		echo '<div id="'.Inflector::slug( $ongletName ).'">';
		echo $this->Html->tag( 'h2', $ongletName, array( 'class' => 'title' ) );

		foreach ($onglet as $role) {
			$resetUrl = preg_replace('/%3A/', ':', Router::url($route+array(Hash::get($role, 'Role.id'))));

			$reset = WebrsaPermissions::check('dashboards', 'reset_cache')
				? '<p><a href="'.$resetUrl.'">Recalculer les nombres (très longue attente)</a></p>'
				: ''
			;

			echo '<h3>'.Hash::get($role, 'Role.name').'</h3>
				'.$reset.'
				<table>
					<thead>
						<tr>
							<th>Intitulé de l\'action</th>
							<th>Description</th>
							<th>Nombre de résultats</th>
							<th>Dernière mise à jour</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>'
			;

			foreach ((array)Hash::get($role, 'Actionrole') as $key => $action) {
				$class = $key%2 === 0 ? 'odd' : 'even';
				echo '<tr class="'.$class.'">
						<td>'.Hash::get($action, 'name').'</td>
						<td>'.Hash::get($action, 'description').'</td>
						<td class="number">'.Hash::get($action, 'Actionroleresultuser.0.results').'</td>
						<td class="date">'.$this->Default3->DefaultTable->DefaultTableCell->DefaultData->format(Hash::get($action, 'Actionroleresultuser.0.modified'), 'datetime').'</td>
						<td>'.getActionLink(Hash::get($action, 'url')).'</td>
					</tr>'
				;
			}

			echo '</tbody></table><br/><br/>';
		}

		echo '</div>';
	}
?>
</div>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>
<script type="text/javascript">
	//<![CDATA[
	makeTabbed( 'tabbedWrapper', 2 );
	//]]>
</script>




