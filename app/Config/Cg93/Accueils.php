<?php
	/**
	 * Accueil : blocs d'information à afficher
	 *
	 * Profil :
	 *  - correspond aux profils des groups dans la partie administration
	 *
	 * Blocs :
	 *  - vous pouvez ordonnez les blocs comme vous le souhaitez
	 *  - blocs possibles :
	 * cers
	 * fichesprescription (93)
	 * rendezvous
	 *
	 */
	Configure::write(
		'page.accueil.profil',
		array (
			// Affichage par défaut.
			'by-default' => array (
				'cers' => array (
					'du' => 1, // 0 = début de la semaine, 1 = début de la semaine d'avant, ...
					'au' => 1 // 1 = fin de la semaine, 2 = fin de la semaine d'après, ...
				),
				'fichesprescription' => array ( // Réservé au CD 93
					'limite' => 6 // Nombre de mois antérieur à la date du jour
				),
				'rendezvous' => array (
					'limite' => 1 // Nombre de jour après à la date du jour
				)
			),
		)
	);
?>