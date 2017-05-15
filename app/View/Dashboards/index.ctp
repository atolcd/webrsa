<?php
	echo $this->Default3->titleForLayout();
	
	function getActionLink($url, $controller, $action) {
		if (WebrsaPermissions::check($controller, $action)) {
			return '<a href="'.$url.'">Effectuer&nbsp;l\'action</a>';
		} else {
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

<ul class="ui-tabs-nav">
	<li class="tab">
		<a href="#">
<?php
	echo implode('</a></li><li class="tab"><a href="#">', array_keys($onglets));
?>
		</a>
	</li>
</ul>

<div id="tabbedWrapper" class="tabs">
<?php
	foreach ($onglets as $ongletName => $onglet) {
		echo '<div class="tab" id="'.$ongletName.'">';
		
		foreach ($onglet as $role) {
			$resetUrl = preg_replace('/%3A/', ':', Router::url($route+array(Hash::get($role, 'Role.id'))));

			$reset = WebrsaPermissions::check('dashboards', 'reset_cache') 
				? '<a href="'.$resetUrl.'">Recalculer les nombres (très longue attente)</a>' 
				: ''
			;

			echo '<h3>'.Hash::get($role, 'Role.name').'</h3>
				'.$reset.'
				<table>
					<thead>
						<th>Intitulé de l\'action</th>
						<th>Description</th>
						<th>Nombre de résultats depuis le '.Hash::get($role, 'Role.date_count').'</th>
						<th>Action</th>
					</thead>
					<tbody>'
			;

			foreach ((array)Hash::get($role, 'Actionrole') as $key => $action) {
				$class = $key%2 === 0 ? 'odd' : 'even';
				echo '<tr class="'.$class.'">
						<td>'.Hash::get($action, 'name').'</td>
						<td>'.Hash::get($action, 'description').'</td>
						<td>'.Hash::get($action, 'count').'</td>
						<td>'.getActionLink(Hash::get($action, 'url'), Hash::get($action, 'controller'), Hash::get($action, 'action')).'</td>
					</tr>'
				;
			}

			echo '</tbody></table><br/><br/>';
		}
		
		echo '</div>';
	}
?>
</div>
<script type="text/javascript">
	$$('div.tab').each(function(div) {
		div.hide();
	});
	
	$$('ul.ui-tabs-nav li.tab a').each(function(a) {
		a.observe('click', function(event) {
			event.preventDefault();
			
			$$('ul.ui-tabs-nav li.tab a').each(function(a) {
				a.removeClassName('active');
			});
			
			event.target.addClassName('active');
			
			$$('div.tab').each(function(div) {
				div.hide();
			});
			
			$(event.target.innerHTML.trim()).show();
		});
	});
	$$('ul.ui-tabs-nav li.tab a').first().dispatchEvent(new Event('click'));
</script>
	
	
	
	
	