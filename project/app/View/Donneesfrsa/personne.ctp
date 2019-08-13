<?php
	$defaultParams = array('paginate' => false, 'options' => $options);
	$noData = $this->Xhtml->tag('p', __d ('personne', 'Personne.nodata'), array('class' => 'notice'));

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));

	echo $this->element('ancien_dossier');

?>
<br><br>
<ul id="" class="ui-tabs-nav">
<?php
foreach ($personnes_list as $personne) {
	$label = '(&nbsp;'.Hash::get($personne, 'Prestation.rolepers').'&nbsp;) '.$personne['Personne']['nom_complet'];
	$class = Hash::get($personne, 'Personne.id') === (integer)$this->request->params['pass'][0] ? 'active' : '';
	echo '<li class="tab">'.$this->Xhtml->link(
		$label, array('controller' => 'donneesfrsa', 'action' => 'personne', $personne['Personne']['foyer_id'], $personne['Personne']['id']), array('class' => $class), false, false
	).'</li>';
} ?>
</ul>

<div id="tabbedWrapper" class="tabs">
	<div style="" class="tab">

<?php
	echo '<br><h1>'.__d ('personne', 'Personne.informations').'</h1>';
	echo '<br><br><h2>'.__d ('personne', 'Personne.langues').'</h2>';
	if (!empty($personneslangues)) {
		echo $this->Default3->index(
			$personneslangues,
			array(
				'Personnelangue.maternelles',
				'Personnelangue.francais_niveau',
				'Personnelangue.francais_niveau_validation',
				'Personnelangue.niveaux_professionnels'
			),
			array('domain' => 'personnelangue') + $defaultParams
		);
	} else {
		echo $noData;
	}
	echo '<br><br><h2>'.__d ('personne', 'Personne.experiences').'</h2>';
	if (!empty($personnesfrsadiplomexper)) {
		echo $this->Default3->index(
			$personnesfrsadiplomexper,
			array(
				'Personnefrsadiplomexper.nivetu',
				'Personnefrsadiplomexper.diplome',
				'Personnefrsadiplomexper.expprof',
				'Personnefrsadiplomexper.formations',
				'Personnefrsadiplomexper.permisb',
				'Personnefrsadiplomexper.autreexpersavoir'
			),
			array('domain' => 'personnefrsadiplomexper') + $defaultParams
		);
	} else {
		echo $noData;
	}
?>
	</div>
</div>
<script>
	// Affichage des blocs d'informations
	$$('div.information').each(function(info){
		info.observe('click', function(event){
			$(event.target.getAttribute('for')).toggle();
		});
	});

	// Permet de faire le lien facilement au survol entre un foreign_key et l'id correspondant
	$$('table td.highlight').each(function(td){
		td.up('tr').observe('mouseover', function(event){
			var baseid = event.target.up('tr').select('td.highlight')[0].innerHTML,
				highlight = false,
				count = 0;

			$$('table td.highlight').each(function(td2){
				if (td2.up('tr').select('td.highlight')[0].innerHTML === baseid) {
					count++;
				}
			});
			if (count > 1) {
				$$('table td.highlight').each(function(td2){
					var tr = td2.up('tr');

					if (tr.select('td.highlight')[0].innerHTML === baseid) {
						tr.addClassName('highlighted');
					}
				});
			}
		});
		td.up('tr').observe('mouseout', function(event){
			var baseid = event.target.up('tr').select('td.highlight')[0].innerHTML;

			$$('table td.highlight').each(function(td2){
				var tr = td2.up('tr');

				if (tr.select('td.highlight')[0].innerHTML === baseid) {
					tr.removeClassName('highlighted');
				}
			});
		});
	});
</script>