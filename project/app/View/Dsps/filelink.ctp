<?php
	$this->pageTitle =  __d( 'dsp_rev', "DspsRevs::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'DspRev', $fichiers, $dsprev, $optionsrevs['haspiecejointe'] );
?>