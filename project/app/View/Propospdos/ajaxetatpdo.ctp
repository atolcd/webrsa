<?php
	if ( !empty( $etatdossierpdo ) ) {
		echo 'Etat du dossier : '.$this->Xhtml->tag( 'strong', __d( 'propopdo', 'ENUM::ETATDOSSIERPDO::'.$etatdossierpdo ) );
	}
?>
