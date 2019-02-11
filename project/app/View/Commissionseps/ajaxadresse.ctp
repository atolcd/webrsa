<?php

	echo '<div class="input text">';
		echo $this->Xhtml->tag(
			'label',
			'Adresse'
		);
		echo Set::classicExtract( $struct, 'Structurereferente.num_voie' ).' '.Set::classicExtract( $struct, 'Structurereferente.type_voie' ).' '.Set::classicExtract( $struct, 'Structurereferente.nom_voie' );
	echo '</div>';

	echo '<div class="input text">';
		echo $this->Xhtml->tag(
			'label',
			'Code Postal'
		);
		echo Set::classicExtract( $struct, 'Structurereferente.code_postal' );
	echo '</div>';

	echo '<div class="input text">';
		echo $this->Xhtml->tag(
			'label',
			'Ville'
		);
		echo Set::classicExtract( $struct, 'Structurereferente.ville' );
	echo '</div>';

?>
