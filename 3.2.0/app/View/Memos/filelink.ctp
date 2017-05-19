<?php
	$this->pageTitle =  __d( 'memo', "Memos::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Memo', $fichiers, $memo, $options['Memo']['haspiecejointe'] );
?>