<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'themeapre66', "Themesapres66::{$this->action}" )
	)
?>
<?php
	echo $this->Default->form(
		array(
			'Themeapre66.name' => array('required' => true),
		)
	);
?>