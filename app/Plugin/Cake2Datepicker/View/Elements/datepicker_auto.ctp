<?php
	// Detect browser favorite language if $lang is not set (default is en-GB)
	if (empty($lang)) {
		$languages = array();
		$browserLanguages = explode(',', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
		foreach ($browserLanguages as $language) {
			if (preg_match('/^(([\w]+)(?:\-[\w]+){0,1})(?:;q=(.*)){0,1}$/', trim($language), $matches)) {
				if (empty($matches[3])) {
					$matches[3] = '1.0'; // q=1
				}
				if (!isset($languages[$matches[3]])) {
					$languages[$matches[3]] = $matches;
				}
			}
		}
		krsort($languages);
		$filenames = scandir(__DIR__.'/../../webroot/js');
		
		foreach ($languages as $langueage) {
			foreach ($filenames as $filename) {
				if (preg_match('/lang_('.$langueage[1].'|'.$langueage[2].')\.js/i', $filename, $matches)) {
					$lang = $matches[1];
					break;
				}
			}
			if (!empty($lang)) {
				break;
			}
		}
		
		if (empty($lang)) {
			$lang = 'en-GB';
		}
	}
	
	echo $this->Html->css(Configure::read('Cake2Datepicker.config.css'));
	echo $this->Html->css('Cake2Datepicker.style');
	echo $this->Html->script('Cake2Datepicker.protocalendar');
	echo $this->Html->script('Cake2Datepicker.lang_'.$lang);
?>
<script type="text/javascript">
//<![CDATA[
	var Cake2Datepicker = {
		lang: '<?php echo $lang;?>',
		div_input_selector: '<?php echo Configure::read('Cake2Datepicker.config.div_input_selector');?>',
		img_calendar: '<?php echo Router::url(Configure::read('Cake2Datepicker.config.img_calendar'));?>',
		img_remover: '<?php echo Router::url(Configure::read('Cake2Datepicker.config.img_remover'));?>'
	};
//]]>
</script>
<?php
	echo $this->Html->script('Cake2Datepicker.script');