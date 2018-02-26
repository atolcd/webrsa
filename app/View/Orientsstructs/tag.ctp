<fieldset>
	<legend><?php echo __d('dossiers', 'Search.Tag.search_title') ?></legend>
	<div class="input checkbox">
		
		<input type="checkbox" name="data[Search][ByTag][tag_choice]" value="1" id="SearchByTagChoice" <?php echo isset ($this->request->data['Search']['ByTag']['tag_choice']) ? 'checked="checked"' : ''  ?> />
		<label for="SearchByTagChoice"><?php echo __d('dossiers', 'Search.Tag.filter_title') ?></label>
	</div>
	<div id="SearchByTagFieldset">

	<div class="checkbox">
		<input name="data[Search][Tag][exclusionValeur][]" value="1" id="SearchTagValeurtagExclusion" type="checkbox" <?php echo isset ($this->request->data['Search']['Tag']['exclusionValeur']) ? 'checked="checked"' : ''  ?> />
		<label for="SearchTagValeurtagExclusion">Exclusion des valeurs</label>
	</div>
<?php
	echo $this->Xform->multipleCheckbox('Search.Tag.valeurtag_id', $options);
?>
	<div class="checkbox">
		<input name="data[Search][Tag][exclusionEtat][]" value="1" id="SearchTagValeurtagEtat" type="checkbox" <?php echo isset ($this->request->data['Search']['Tag']['exclusionEtat']) ? 'checked="checked"' : ''  ?> />
		<label for="SearchTagValeurtagEtat">Exclusion des états</label>
	</div>
<?php
	echo $this->Xform->multipleCheckbox('Search.Tag.etat', $options);
?>
	</div>
</fieldset>
<script type="text/javascript">
//<![CDATA[
document.observe( 'dom:loaded', function() { try {
	observeDisableFieldsetOnCheckbox( 'SearchByTagChoice', 'SearchByTagFieldset', false, true );
} catch( e ) {
	console.error( e );
} } );
//]]>
</script>