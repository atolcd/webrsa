<?php
	echo $this->Default3->titleForLayout();
	
	echo $this->Fileuploader->element('Cui', $fichiers, $record, $options['Cui']['haspiecejointe']);

	echo $this->Observer->disableFormOnSubmit('cuiform');