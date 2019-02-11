<fieldset>
	<legend><?php echo 'Sauvegardes existantes';?></legend>
	<?php
	echo $this->Xform->input('Savesearch.dispo', 
		array('label' => 'Sauvegarde', 'type' => 'select', 'empty' => true, 'options' => $moduleSavesearchDispo) 
	);
	
	if (!Configure::read('Module.Savesearch.mon_menu.enabled')) {
		 echo $this->Xhtml->link('Modifier', array('controller' => 'savesearchs', 'action' => 'index'));
	}
	?>
	<div class="center"><input type="button" value="Charger" id="ModuleSavesearchLoadButton"/></div>
</fieldset>
<script>
	$('ModuleSavesearchLoadButton').observe('click', function() {
		if ($('SavesearchDispo').getValue() === '') {
			return;
		}
		
		new Ajax.Request('<?php echo Router::url( array( 'controller' => 'savesearchs', 'action' => 'ajax_geturl' ) ); ?>/', {
			asynchronous:true, 
			evalScripts:true, 
			parameters: {
				'id': $('SavesearchDispo').getValue()
			}, 
			requestHeaders: {Accept: 'application/json'},
			onComplete:function(request, json) {
				document.location.href = json;
			}
		});
	});
	
</script>