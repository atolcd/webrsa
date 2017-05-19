<?php
	/**
	 * Menu "Recherches" > "Par DSPs (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Dsps.search',
		array(
			// 1. Filtres de recherche
			'filters' => array(
				// 1.1 Valeurs par défaut des filtres de recherche
				'defaults' => array(
					'Dossier' => array(
						// Case à cocher "Uniquement la dernière demande RSA pour un même allocataire"
						'dernier' => '1',
						// Case à cocher "Filtrer par date de demande RSA"
						'dtdemrsa' => '0',
						// Du (inclus)
						'dtdemrsa_from' => date_sql_to_cakephp( date( 'Y-m-d', strtotime( '-1 week' ) ) ),
						// Au (inclus)
						'dtdemrsa_to' => date_sql_to_cakephp( date( 'Y-m-d', strtotime( 'now' ) ) ),
					),
					'Situationdossierrsa' => array(
						'etatdosrsa_choice' => '0',
						'etatdosrsa' => array( '0','2', '3', '4' )
					)
				),
				// 1.2 Restriction des valeurs qui apparaissent dans les filtres de recherche
				'accepted' => array(),
				// 1.3 Ne pas afficher ni traiter certains filtres de recherche
				'skip' => array()
			),
			// 2. Recherche
			'query' => array(
				// 2.1 Restreindre ou forcer les valeurs renvoyées par le filtre de recherche
				'restrict' => array(),
				// 2.2 Conditions supplémentaires optionnelles
				'conditions' => array(),
				// 2.3 Tri par défaut
				'order' => array()
			),
			// 3. Nombre d'enregistrements par page
			'limit' => 10,
			// 4. Lancer la recherche au premier accès à la page ?
			'auto' => false,
			// 5. Résultats de la recherche
			'results' => array(
				// 5.1 Ligne optionnelle supplémentaire d'en-tête du tableau de résultats
				'header' => array(
					array( 'Dossier' => array( 'colspan' => 3 ) ),
					array( 'Accompagnement et difficultés' => array( 'colspan' => 3 ) ),
					array( 'Code ROME' => array( 'colspan' => 4 ) ),
					array( 'Hors code ROME' => array( 'colspan' => 4 ) ),
					array( ' ' => array( 'class' => 'action noprint' ) ),
					array( ' ' => array( 'style' => 'display: none' ) ),
				),
				// 5.2 Colonnes du tableau de résultats
				'fields' => array(
					'Personne.email',//@fixme
					'Modecontact.adrelec',//@fixme
					// Nom de l'allocataire
					'Personne.nom_complet_court',
					// Commune de l'allocataire
					'Adresse.nomcom',
					// N° CAF
					'Dossier.matricule',
					// Difficultés sociales
					'Donnees.difsoc' => array(
						'type' => 'list'
					),
					// Domaine d'accompagnement individuel
					'Donnees.nataccosocindi' => array(
						'type' => 'list'
					),
					// Obstacles à la recherche d'emploi
					'Donnees.difdisp' => array(
						'type' => 'list'
					),
					// domaine de la dernière activité
					'Deractromev3.domaineromev3',
					// métier de la dernière activité
					'Deractromev3.metierromev3',
					// domaine de l'emploi recherché
					'Actrechromev3.domaineromev3',
					// métier de l'emploi recherché
					'Actrechromev3.metierromev3',
					// Secteur dernière activité
					'Donnees.libsecactderact',
					// Dernière activité
					'Donnees.libderact',
					// Secteur activité recherchée
					'Donnees.libsecactrech',
					// Activité recherchée
					'Donnees.libemploirech',
					'/Dsps/view_revs/#DspRev.id#' => array(
						'class' => 'view',
						'condition' => 'trim("#DspRev.id#") !== ""'
					),
					'/Dsps/view/#Personne.id#' => array(
						'class' => 'view',
						'condition' => 'trim("#DspRev.id#") === ""'
					)
				),
				// 5.3 Infobulle optionnelle du tableau de résultats
				'innerTable' => array(
					'Personne.dtnai',
					'Adresse.numcom',
					'Personne.nir',
					'Situationdossierrsa.etatdosrsa',
					'Donnees.nivetu',
					'Donnees.hispro',
					'Structurereferenteparcours.lib_struc' => array(
						'label' => __d( 'search_plugin_93', 'Structurereferenteparcours.lib_struc' )
					),
					'Referentparcours.nom_complet' => array(
						'label' => __d( 'search_plugin_93', 'Referentparcours.nom_complet' )
					),
					'Deractromev3.familleromev3',
					'Deractromev3.appellationromev3',
					'Actrechromev3.familleromev3',
					'Actrechromev3.appellationromev3'
				)
			),
			// 6. Temps d'exécution, mémoire maximum, ...
			'ini_set' => array()
		)
	);

	/**
	 * Export CSV,  menu "Recherches" > "Par DSPs (nouveau)"
	 */
	Configure::write(
		'ConfigurableQuery.Dsps.exportcsv',
		array(
			// 1. Filtres de recherche, on reprend la configuration de la recherche
			'filters' => Configure::read( 'ConfigurableQuery.Dsps.search.filters' ),
			// 2. Recherche, on reprend la configuration de la recherche
			'query' => Configure::read( 'ConfigurableQuery.Dsps.search.query' ),
			// 3. Résultats de la recherche
			'results' => array(
				'fields' => array(
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Situationdossierrsa.etatdosrsa',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Dossier.matricule',
					'Detaildroitrsa.natpf',
					'Calculdroitrsa.toppersdrodevorsa',
					'Memo.name',
					'Personne.age',
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Donnees.libsecactrech',
					'Donnees.libemploirech',
					//intitulés des structures référentes
					'Structurereferenteparcours.lib_struc' => array(
						'label' => __d( 'search_plugin_93', 'Structurereferenteparcours.lib_struc' )
					),
					'Referentparcours.nom_complet' => array(
						'label' => __d( 'search_plugin_93', 'Referentparcours.nom_complet' )
					),
					'Donnees.difsoc' => array(
						'type' => 'list'
					),
					'Donnees.nataccosocindi' => array(
						'type' => 'list'
					),
					'Donnees.difdisp' => array(
						'type' => 'list'
					),
					'Deractromev3.familleromev3',
					'Deractromev3.domaineromev3',
					'Deractromev3.metierromev3',
					'Deractromev3.appellationromev3',
					'Actrechromev3.familleromev3',
					'Actrechromev3.domaineromev3',
					'Actrechromev3.metierromev3',
					'Actrechromev3.appellationromev3',
					'Personne.numfixe' => array( 'label' => 'Num de telephone fixe' ),
					'Personne.numport' => array( 'label' => 'Num de telephone portable' ),
					'Personne.email' => array( 'label' => 'Adresse mail' ),
					'Dsp.libcooraccosocindi',
					'Dsp.nivetu',
					'Dsp.nivdipmaxobt',
					'Dsp.annobtnivdipmax',
					'Dsp.topqualipro',
					'Dsp.libautrqualipro',
					'Dsp.topcompeextrapro',
					'Dsp.libcompeextrapro',
					'Dsp.topengdemarechemploi',
					'Dsp.inscdememploi',
					'Dsp.topprojpro',
					'Dsp.topmoyloco',
					'Dsp.toppermicondub',
					'Dsp.topautrpermicondu',
					'Dsp.libautrpermicondu',
					'DspRev.modified'
				)
			),
			// 4. Temps d'exécution, mémoire maximum, ...
			'ini_set' => Configure::read( 'ConfigurableQuery.Dsps.search.ini_set' ),
		)
	);

	// -------------------------------------------------------------------------

	/**
	 * Liste des champs devant apparaître dans les résultats de la recherche par DSP:
	 *	- Dsps.index.fields contient les champs de chaque ligne du tableau de résultats
	 *	- Dsps.index.innerTable contient les champs de l'infobulle de chaque ligne du tableau de résultats
	 *	- Dsps.exportcsv contient les champs de chaque ligne du tableau à télécharger au format CSV
	 *
	 * Voir l'onglet "Environnement logiciel" > "WebRSA" > "Champs spécifiés dans
	 * le webrsa.inc" de la vérification de l'application.
	 *
	 * @deprecated since 3.0.00
	 */
	Configure::write(
		'Dsps',
		array(
			'index' => array(
				'fields' => array(
					// Nom de l'allocataire
					'Personne.nom_complet_court',
					// Commune de l'allocataire
					'Adresse.nomcom',
					// N° CAF
					'Dossier.matricule',
					// Difficultés sociales
					'Donnees.difsoc',
					// Domaine d'accompagnement individuel
					'Donnees.nataccosocindi',
					// Obstacles à la recherche d'emploi
					'Donnees.difdisp',
					// domaine de la dernière activité
					'Deractromev3.domaineromev3',
					// métier de la dernière activité
					'Deractromev3.metierromev3',
					// domaine de l'emploi recherché
					'Actrechromev3.domaineromev3',
					// métier de l'emploi recherché
					'Actrechromev3.metierromev3',
					// Secteur dernière activité
					'Donnees.libsecactderact',
					// Dernière activité
					'Donnees.libderact',
					// Secteur activité recherchée
					'Donnees.libsecactrech',
					// Activité recherchée
					'Donnees.libemploirech'
				),
				'innerTable' => array(
					'Personne.dtnai',
					'Adresse.numcom',
					'Personne.nir',
					'Situationdossierrsa.etatdosrsa',
					'Donnees.nivetu',
					'Donnees.hispro',
					//intitulés des structures référentes
					'Structurereferenteparcours.lib_struc' => array(
						'label' => __d( 'search_plugin_93', 'Structurereferenteparcours.lib_struc' )
					),
					'Referentparcours.nom_complet' => array(
						'label' => __d( 'search_plugin_93', 'Referentparcours.nom_complet' )
					),
					'Deractromev3.familleromev3',
					'Deractromev3.appellationromev3',
					'Actrechromev3.familleromev3',
					'Actrechromev3.appellationromev3'
				),
				'header' => array(
					array( 'Dossier' => array( 'colspan' => 3 ) ),
					array( 'Accompagnement et difficultés' => array( 'colspan' => 3 ) ),
					array( 'Code ROME' => array( 'colspan' => 4 ) ),
					array( 'Hors code ROME' => array( 'colspan' => 4 ) ),
					array( ' ' => array( 'class' => 'action noprint' ) ),
					array( ' ' => array( 'style' => 'display: none' ) ),
				)
			),
			'exportcsv1' => array(
				'Dossier.numdemrsa',
				'Dossier.matricule',
				'Situationdossierrsa.etatdosrsa',
				'Personne.qual',
				'Personne.nom',
				'Personne.prenom',
				'Dossier.matricule',
				'Detaildroitrsa.natpf',
				'Calculdroitrsa.toppersdrodevorsa',
				'Memo.name',
				'Personne.age',
				'Adresse.numvoie',
				'Adresse.libtypevoie',
				'Adresse.nomvoie',
				'Adresse.complideadr',
				'Adresse.compladr',
				'Adresse.codepos',
				'Adresse.nomcom',
				'Donnees.libsecactrech',
				'Donnees.libemploirech',
				//intitulés des structures référentes
				'Structurereferenteparcours.lib_struc' => array(
					'label' => __d( 'search_plugin_93', 'Structurereferenteparcours.lib_struc' )
				),
				'Referentparcours.nom_complet' => array(
					'label' => __d( 'search_plugin_93', 'Referentparcours.nom_complet' )
				),
				'Donnees.difsoc',
				'Donnees.nataccosocindi',
				'Donnees.difdisp',
				'Deractromev3.familleromev3',
				'Deractromev3.domaineromev3',
				'Deractromev3.metierromev3',
				'Deractromev3.appellationromev3',
				'Actrechromev3.familleromev3',
				'Actrechromev3.domaineromev3',
				'Actrechromev3.metierromev3',
				'Actrechromev3.appellationromev3',
				'Personne.numfixe' => array( 'label' => 'Num de telephone fixe' ),
				'Personne.numport' => array( 'label' => 'Num de telephone portable' ),
				'Personne.email' => array( 'label' => 'Adresse mail' ),
				'Dsp.libcooraccosocindi',
				'Dsp.nivetu',
				'Dsp.nivdipmaxobt',
				'Dsp.annobtnivdipmax',
				'Dsp.topqualipro',
				'Dsp.libautrqualipro',
				'Dsp.topcompeextrapro',
				'Dsp.libcompeextrapro',
				'Dsp.topengdemarechemploi',
				'Dsp.inscdememploi',
				'Dsp.topprojpro',
				'Dsp.topmoyloco',
				'Dsp.toppermicondub',
				'Dsp.topautrpermicondu',
				'Dsp.libautrpermicondu',
				'DspRev.modified',
			)
		)
	);

	/**
	 * Mise en évidence de certains champs du formulaire des Dsps
	 */
	Configure::write(
		'Evidence.Dsps.add',
		array(
			'fields' => array(
				// Bloc "Rencontrez vous des difficultés sociales ?"
				// Difficultés de santé : Renseignez la ligne 1 du tableau B3
				// Difficultés administratives : Renseignez la ligne 6 du tableau B3
				// Difficultés linguistique : Renseignez la ligne 7 du tableau B3
				// Difficultés de surendettement : Renseignez la ligne 5 du tableau B3
				'#Detaildifsoc0DspId',
				// -------------------------------------------------------------
				// Difficultés de logement : Renseignez la ligne 2 du tableau B3
				'#Detaildiflog0DspId',
				// -------------------------------------------------------------
				// Difficultés familiales : Renseignez la ligne 3 du tableau B3
				'#Detailaccosocfam0DspId',
				// -------------------------------------------------------------
				// Difficultés de modes de garde : Renseignez la ligne 4 du tableau B3
				'#Detaildifdisp0DspId',
				// -------------------------------------------------------------
				// Difficultés de qualification professionnelle : Renseignez la ligne 9 du tableau B3
				'#DspNivetu',
				// -------------------------------------------------------------
				// Difficultés d’accès à l’emploi : Renseignez la ligne 10 du tableau B3
				'#DspTopengdemarechemploi',
				// -------------------------------------------------------------
				// Autres : Renseignez la ligne 11 du tableau B3
				'#Detailaccosocindi0DspId',
			),
			'options' => array(
				// Difficultés de santé : Renseignez la ligne 1 du tableau B3
				'input[id^=Detaildifsoc][id$=Difsoc][type=checkbox][value=0402]' => array(
					'title' => 'Difficultés de santé : Renseignez la ligne 1 du tableau B3'
				),
				'input[id^=Detaildifsoc][id$=Difsoc][type=checkbox][value=0403]' => array(
					'title' => 'Difficultés de santé : Renseignez la ligne 1 du tableau B3'
				),
				// Difficultés administratives : Renseignez la ligne 6 du tableau B3
				'input[id^=Detaildifsoc][id$=Difsoc][type=checkbox][value=0405]' => array(
					'title' => 'Difficultés administratives : Renseignez la ligne 6 du tableau B3'
				),
				// Difficultés linguistique : Renseignez la ligne 7 du tableau B3
				'input[id^=Detaildifsoc][id$=Difsoc][type=checkbox][value=0404]' => array(
					'title' => 'Difficultés linguistique : Renseignez la ligne 7 du tableau B3'
				),
				// Difficultés de surendettement : Renseignez la ligne 5 du tableau B3
				'input[id^=Detaildifsoc][id$=Difsoc][type=checkbox][value=0406]' => array(
					'title' => 'Difficultés de surendettement : Renseignez la ligne 5 du tableau B3'
				),
				// -------------------------------------------------------------
				// Difficultés de logement : Renseignez la ligne 2 du tableau B3
				'input[id^=Detaildiflog][id$=Diflog][type=checkbox][value=1004]' => array(
					'title' => 'Difficultés de logement : Renseignez la ligne 2 du tableau B3'
				),
				'input[id^=Detaildiflog][id$=Diflog][type=checkbox][value=1005]' => array(
					'title' => 'Difficultés de logement : Renseignez la ligne 2 du tableau B3'
				),
				'input[id^=Detaildiflog][id$=Diflog][type=checkbox][value=1006]' => array(
					'title' => 'Difficultés de logement : Renseignez la ligne 2 du tableau B3'
				),
				'input[id^=Detaildiflog][id$=Diflog][type=checkbox][value=1007]' => array(
					'title' => 'Difficultés de logement : Renseignez la ligne 2 du tableau B3'
				),
				'input[id^=Detaildiflog][id$=Diflog][type=checkbox][value=1008]' => array(
					'title' => 'Difficultés de logement : Renseignez la ligne 2 du tableau B3'
				),
				'input[id^=Detaildiflog][id$=Diflog][type=checkbox][value=1009]' => array(
					'title' => 'Difficultés de logement : Renseignez la ligne 2 du tableau B3'
				),
				// -------------------------------------------------------------
				// Difficultés familiales : Renseignez la ligne 3 du tableau B3
				'input[id^=Detailaccosocfam][id$=Nataccosocfam][type=checkbox][value=0412]' => array(
					'title' => 'Difficultés familiales : Renseignez la ligne 3 du tableau B3'
				),
				// -------------------------------------------------------------
				// Difficultés de modes de garde : Renseignez la ligne 4 du tableau B3
				'input[id^=Detaildifdisp][id$=Difdisp][type=checkbox][value=0502]' => array(
					'title' => 'Difficultés de modes de garde : Renseignez la ligne 4 du tableau B3'
				),
				'input[id^=Detaildifdisp][id$=Difdisp][type=checkbox][value=0503]' => array(
					'title' => 'Difficultés de modes de garde : Renseignez la ligne 4 du tableau B3'
				),
				'input[id^=Detaildifdisp][id$=Difdisp][type=checkbox][value=0504]' => array(
					'title' => 'Difficultés de modes de garde : Renseignez la ligne 4 du tableau B3'
				),
				// -------------------------------------------------------------
				// Difficultés de qualification professionnelle : Renseignez la ligne 9 du tableau B3
				'select[id=DspNivetu] option[value=1206]' => array(
					'title' => 'Difficultés de qualification professionnelle : Renseignez la ligne 9 du tableau B3'
				),
				'select[id=DspNivetu] option[value=1207]' => array(
					'title' => 'Difficultés de qualification professionnelle : Renseignez la ligne 9 du tableau B3'
				),
				// -------------------------------------------------------------
				// Difficultés d’accès à l’emploi : Renseignez la ligne 10 du tableau B3
				'select[id=DspTopengdemarechemploi] option[value=0]' => array(
					'title' => 'Difficultés d’accès à l’emploi : Renseignez la ligne 10 du tableau B3'
				),
				// -------------------------------------------------------------
				// Autres : Renseignez la ligne 11 du tableau B3
				'input[id^=Detailaccosocindi][id$=Nataccosocindi][type=checkbox][value=0420]' => array(
					'title' => 'Autres : Renseignez la ligne 11 du tableau B3'
				)

			)
		)
	);
	Configure::write( 'Evidence.Dsps.edit', Configure::read( 'Evidence.Dsps.add' ) );
?>