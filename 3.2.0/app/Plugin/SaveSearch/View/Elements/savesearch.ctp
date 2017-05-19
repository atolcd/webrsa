<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'popup' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	
	$options_savesearch['Savesearch'] = array(
		'isforgroup' => array(0 => 'Non', 1 => 'Oui'),
		'isformenu' => array(0 => 'Non', 1 => 'Oui'),
	);
?>

<div id="savesearch_popup" style="display: none;">
	<div id="popups2" style="z-index: 1000;">
		<div id="popup_1">
			<div class="hideshow">
				<div class="fade" style="z-index: 31"></div>
				<div class="popup_block">
					<div class="popup">
						<a href="#" onclick="$('savesearch_popup').hide(); return false;"><?php echo $this->Xhtml->image('icon_close.png', array('class' => 'cntrl', 'alt' => 'close')); ?></a>
						<div id="popup-content1"><?php
							echo $this->Form->create(null, 
								array(
									'type' => 'post', 
									'url' => array( 'controller' => 'savesearchs', 'action' => 'save'), 
									'id' => 'savesearch_popup_form'
								)
							);
							
							$urlToSave = $this->request->base 
								? preg_replace('/^'.preg_quote($this->request->base, '/').'/', '', $this->here) 
								: $this->here
							; 
						
							$inputs = array(
								'Savesearch.controller' => array('type' => 'hidden', 'value' => $this->request->params['controller']),
								'Savesearch.action' => array('type' => 'hidden', 'value' => $this->request->params['action']),
								'Savesearch.url' => array('type' => 'hidden', 'value' => $urlToSave),

								'Savesearch.name' => array('type' => 'text', 'required' => true),
							);
							
							if ($this->Permissions->check('savesearchs', 'save_group')) {
								$inputs['Savesearch.isforgroup'] = array('type' => 'radio', 'value' => 0);
							}
							
							if (Configure::read('Module.Savesearch.mon_menu.enabled')) {
								$inputs['Savesearch.isformenu'] = array('type' => 'radio', 'value' => 0);
							}
							
							echo $this->Default->subform(
								$inputs,
								array('options' => $options_savesearch)
							);
							
							echo '<div class="center">'
								.$this->Form->button('Sauvegarder', array('type' => 'submit'))
								.'</div>'
							;
							
							echo $this->Form->end();
							echo $this->Observer->disableFormOnSubmit('savesearch_popup_form');
						?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>