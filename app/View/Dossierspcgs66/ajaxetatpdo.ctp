<?php
	if ( !empty( $etatdossierpcg ) && !in_array( $etatdossierpcg, array( 'transmisop', 'atttransmisop' ) ) ){
		echo 'Etat du dossier : '.$this->Xhtml->tag( 'strong', __d( 'dossierpcg66', 'ENUM::ETATDOSSIERPCG::'.$etatdossierpcg ) );
	}
	else if ( !empty( $etatdossierpcg ) && $etatdossierpcg == 'atttransmisop' ) {
		echo 'Etat du dossier : '.$this->Xhtml->tag( 'strong', __d( 'dossierpcg66', 'ENUM::ETATDOSSIERPCG::'.$etatdossierpcg ) ).' à '.$this->Xhtml->tag( 'strong', $orgs );
	}
    else if ( !empty( $etatdossierpcg ) && $etatdossierpcg == 'transmisop' ) {
		echo 'Etat du dossier : '.$this->Xhtml->tag( 'strong', __d( 'dossierpcg66', 'ENUM::ETATDOSSIERPCG::'.$etatdossierpcg ) ).' à '.$this->Xhtml->tag( 'strong', $orgs ).' le '.$this->Xhtml->tag( 'strong', date_short( $datetransmission ) );
	}
?>
