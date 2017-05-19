<?php
	$text = 'XML : '.$nrsa;
	$this->pageTitle = $text.'<br></br>';

	echo $this->Html->tag( 'h1', $this->pageTitle );
	echo nl2br(htmlEntities($rejet));

?>