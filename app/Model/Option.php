<?php
	/**
	 * Code source de la classe Option.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Option possède des méthodes permettant d'obtenir les "enums",
	 * c'est-à-dire un array avec en clé la valeur stockée en base et en valeur
	 * une traduction, pour différents champs de la base de données.
	 *
	 * @deprecated since version 3.1
	 * @package app.Model
	 */
	class Option extends AppModel
	{
		/**
		 * Nom de la classe.
		 *
		 * @var string
		 */
		public $name = 'Option';

		/**
		 * Cette classe n'est pas liée à une table de la base de données.
		 *
		 * @var boolean
		 */
		public $useTable = false;

		/**
		 * Liste des libellés de type de voie.
		 *	- membreseps.typevoie (OK)
		 *	- partenaires.typevoie (OK)
		 *	- permanences.typevoie (OK)
		 *	- servicesinstructeurs.type_voie (OK)
		 *	- structuresreferentes.type_voie (OK)
		 *	- tiersprestatairesapres.typevoie (OK)
		 *	- users.typevoie (OK)
		 *  --------------------------------------------------------------------
		 *	- Cui.typevoieemployeur (@deprecated)
		 *	- Periodeimmersion.typevoieentaccueil (@deprecated)
		 *
		 *	@todo remplacer la méthode typevoie dès lors que tous les modèles /
		 * tables auront été mis à jour
		 *
		 * @var array
		 */
		protected $_libtypevoie = array(
			'Abbaye' => 'Abbaye',
			'Ancien chemin' => 'Ancien chemin',
			'Agglomération' => 'Agglomération',
			'Aire' => 'Aire',
			'Allée' => 'Allée',
			'Anse' => 'Anse',
			'Arcade' => 'Arcade',
			'Ancienne route' => 'Ancienne route',
			'Autoroute' => 'Autoroute',
			'Avenue' => 'Avenue',
			'Bastion' => 'Bastion',
			'Bas chemin' => 'Bas chemin',
			'Boucle' => 'Boucle',
			'Boulevard' => 'Boulevard',
			'Béguinage' => 'Béguinage',
			'Berge' => 'Berge',
			'Bois' => 'Bois',
			'Barriere' => 'Barriere',
			'Bourg' => 'Bourg',
			'Bastide' => 'Bastide',
			'Butte' => 'Butte',
			'Cale' => 'Cale',
			'Camp' => 'Camp',
			'Carrefour' => 'Carrefour',
			'Carriere' => 'Carriere',
			'Carre' => 'Carre',
			'Carreau' => 'Carreau',
			'Cavée' => 'Cavée',
			'Campagne' => 'Campagne',
			'Chemin' => 'Chemin',
			'Cheminement' => 'Cheminement',
			'Chez' => 'Chez',
			'Charmille' => 'Charmille',
			'Chalet' => 'Chalet',
			'Chapelle' => 'Chapelle',
			'Chaussée' => 'Chaussée',
			'Château' => 'Château',
			'Chemin vicinal' => 'Chemin vicinal',
			'Cité' => 'Cité',
			'Cloître' => 'Cloître',
			'Clos' => 'Clos',
			'Col' => 'Col',
			'Colline' => 'Colline',
			'Corniche' => 'Corniche',
			'Côte(au)' => 'Côte(au)',
			'Cottage' => 'Cottage',
			'Cour' => 'Cour',
			'Camping' => 'Camping',
			'Cours' => 'Cours',
			'Castel' => 'Castel',
			'Contour' => 'Contour',
			'Centre' => 'Centre',
			'Darse' => 'Darse',
			'Degré' => 'Degré',
			'Digue' => 'Digue',
			'Domaine' => 'Domaine',
			'Descente' => 'Descente',
			'Ecluse' => 'Ecluse',
			'Eglise' => 'Eglise',
			'Enceinte' => 'Enceinte',
			'Enclos' => 'Enclos',
			'Enclave' => 'Enclave',
			'Escalier' => 'Escalier',
			'Esplanade' => 'Esplanade',
			'Espace' => 'Espace',
			'Etang' => 'Etang',
			'Faubourg' => 'Faubourg',
			'Fontaine' => 'Fontaine',
			'Forum' => 'Forum',
			'Fort' => 'Fort',
			'Fosse' => 'Fosse',
			'Foyer' => 'Foyer',
			'Ferme' => 'Ferme',
			'Galerie' => 'Galerie',
			'Gare' => 'Gare',
			'Garenne' => 'Garenne',
			'Grand boulevard' => 'Grand boulevard',
			'Grand ensemble' => 'Grand ensemble',
			'Groupe' => 'Groupe',
			'Groupement' => 'Groupement',
			'Grand(e) rue' => 'Grand(e) rue',
			'Grille' => 'Grille',
			'Grimpette' => 'Grimpette',
			'Hameau' => 'Hameau',
			'Haut chemin' => 'Haut chemin',
			'Hippodrome' => 'Hippodrome',
			'Halle' => 'Halle',
			'HLM' => 'HLM',
			'Ile' => 'Ile',
			'Immeuble' => 'Immeuble',
			'Impasse' => 'Impasse',
			'Jardin' => 'Jardin',
			'Jetée' => 'Jetée',
			'Lieu dit' => 'Lieu dit',
			'Levée' => 'Levée',
			'Lotissement' => 'Lotissement',
			'Mail' => 'Mail',
			'Manoir' => 'Manoir',
			'Marche' => 'Marche',
			'Mas' => 'Mas',
			'Métro' => 'Métro',
			'Maison forestiere' => 'Maison forestiere',
			'Moulin' => 'Moulin',
			'Montée' => 'Montée',
			'Musée' => 'Musée',
			'Nouvelle route' => 'Nouvelle route',
			'Petite avenue' => 'Petite avenue',
			'Palais' => 'Palais',
			'Parc' => 'Parc',
			'Passage' => 'Passage',
			'Passe' => 'Passe',
			'Patio' => 'Patio',
			'Pavillon' => 'Pavillon',
			'Porche - petit chemin' => 'Porche - petit chemin',
			'Périphérique' => 'Périphérique',
			'Petite impasse' => 'Petite impasse',
			'Parking' => 'Parking',
			'Place' => 'Place',
			'Plage' => 'Plage',
			'Plan' => 'Plan',
			'Placis' => 'Placis',
			'Passerelle' => 'Passerelle',
			'Plaine' => 'Plaine',
			'Plateau(x)' => 'Plateau(x)',
			'Passage à niveau' => 'Passage à niveau',
			'Pointe' => 'Pointe',
			'Pont(s)' => 'Pont(s)',
			'Portique' => 'Portique',
			'Port' => 'Port',
			'Poterne' => 'Poterne',
			'Pourtour' => 'Pourtour',
			'Pré' => 'Pré',
			'Promenade' => 'Promenade',
			'Presqu\'île' => 'Presqu\'île',
			'Petite route' => 'Petite route',
			'Parvis' => 'Parvis',
			'Peristyle' => 'Peristyle',
			'Petite allée' => 'Petite allée',
			'Porte' => 'Porte',
			'Petite rue' => 'Petite rue',
			'Quai' => 'Quai',
			'Quartier' => 'Quartier',
			'Rue' => 'Rue',
			'Raccourci' => 'Raccourci',
			'Raidillon' => 'Raidillon',
			'Rempart' => 'Rempart',
			'Résidence' => 'Résidence',
			'Ruelle' => 'Ruelle',
			'Rocade' => 'Rocade',
			'Roquet' => 'Roquet',
			'Rampe' => 'Rampe',
			'Rond point' => 'Rond point',
			'Rotonde' => 'Rotonde',
			'Route' => 'Route',
			'Sentier' => 'Sentier',
			'Square' => 'Square',
			'Station' => 'Station',
			'Stade' => 'Stade',
			'Tour' => 'Tour',
			'Terre plein' => 'Terre plein',
			'Traverse' => 'Traverse',
			'Terrain' => 'Terrain',
			'Tertre(s)' => 'Tertre(s)',
			'Terrasse(s)' => 'Terrasse(s)',
			'Val(lée)(lon)' => 'Val(lée)(lon)',
			'Vieux chemin' => 'Vieux chemin',
			'Venelle' => 'Venelle',
			'Village' => 'Village',
			'Via' => 'Via',
			'Villa' => 'Villa',
			'Voie' => 'Voie',
			'Vieille route' => 'Vieille route',
			'Zone artisanale' => 'Zone artisanale',
			'Zone d\'aménagement concerte' => 'Zone d\'aménagement concerte',
			'Zone d\'aménagement différé' => 'Zone d\'aménagement différé',
			'Zone industrielle' => 'Zone industrielle',
			'Zone' => 'Zone',
			'Zone à urbaniser en priorité' => 'Zone à urbaniser en priorité'
		);

		/**
		 * Enums pour le champ detailsressourcesmensuelles.abaneu
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function abaneu() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Detailressourcemensuelle')->enum('abaneu')
			return array(
				'A' => 'Abattement',
				'N' => 'Neutralisation'
			);
		}

		/**
		 * Enums pour les champs
		 *	- dsps.accosocfam
		 *	- dsps_revs.accosocfam
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function accosocfam() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dsp')->enum('accosocfam')
			return array(
				'O' => 'Oui',
				'N' => 'Non',
				'P' => 'Pas de réponse'
			);
		}

		/**
		 * Enums pour le champ activites.act
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function act() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Activite')->enum('act')
			$acts = array(
				'AAP' => 'Activité en atelier protégé',
				'ABA' => 'Chômeur-alloc de base',
				'ABS' => 'Absent foyer',
				'ADA' => 'Chômeur aud ou pare abattement',
				'ADN' => 'Chômeur aud neutralisation',
				'AFA' => 'Aide familial agricole',
				'AFC' => 'Chômeur-alloc formation reclass',
				'AFD' => 'Chômeur-alloc fin de droit',
				'AIN' => 'Chômeur-alloc insertion',
				'AMA' => 'Assistante maternelle agréé',
				'AMT' => 'Mi-temps suite plein temps',
				'ANI' => 'Activité + chômeur non indemnisé',
				'ANP' => 'Inscrit à l\'ANPE',
				'APP' => 'Apprenti',
				'ASP' => 'Chômeur-alloc speciale',
				'ASS' => 'Chômeur-alloc solidarité specifique',
				'CAC' => 'Cessation activité pour enfant',
				'CAP' => 'Chômeur et activité > 55% du smic',
				'CAR' => 'Délai de carence ASSEDIC',
				'CAT' => 'Activité centre aide travail',
				'CBS' => 'C.A.T.  : absent du foyer',
				'CCV' => 'Congé conventionnel',
				'CDA' => 'Chômeur aud abat. + activite',
				'CDN' => 'Chômeur aud neut. + activite',
				'CEA' => 'C.E.S. maintien abattement',
				'CEN' => 'C.E.S. maintien neutralisation',
				'CES' => 'Contrat emploi solidarité',
				'CGP' => 'Congé payé',
				'CHA' => 'Chôm. + activité',
				'CHO' => 'Chômeur sans justificatif',
				'CHR' => 'Chômeur',
				'CIA' => 'Contrat insertion/activité /dom',
				'CIS' => 'Contrat insertion + salarié dom',
				'CJT' => 'Conjoint collaborateur d\'E.T.I.',
				'CLD' => 'C.A.T. : longue maladie',
				'CNI' => 'Chômeur non indemnisé',
				'CPL' => 'Chômeur partiel',
				'CSA' => 'C.E.S. et salarié(e)',
				'CSS' => 'Congé sans solde',
				'DEG' => 'Dégagé obligation scolaire',
				'DNL' => 'Sal. non rem. durée légale',
				'DSF' => 'Déclaration situation non fournie',
				'EBO' => 'Etudiant boursier R.M.I.',
				'ETI' => 'Régime Général travailleur non salarié',
				'ETS' => 'Etudiant salarié',
				'ETU' => 'Etudiant',
				'EXP' => 'Régime Agricole travailleur non salarié',
				'EXS' => 'Expl. agricole en ces/dom',
				'FDA' => 'Fonct. publique chôm. aud abat.',
				'FDN' => 'Fonct. publique chôm. aud neut.',
				'GSA' => 'Gérant salarié',
				'HAN' => 'Infirme / handicapé',
				'IAD' => 'Instruction à domicile',
				'INF' => 'Malade/Handicap non scolaire',
				'INP' => 'Inapte',
				'INT' => 'Travailleur intermittent',
				'INV' => 'Pension invalidité',
				'JNF' => 'Justificatif non fourni pour apprenti',
				'MAL' => 'Malade',
				'MAR' => 'Marin pêcheur',
				'MAT' => 'Congé maternité ou paternité',
				'MLD' => 'Maladie longue durée',
				'MMA' => 'Enfant maintenu maternelle',
				'MMC' => 'Mal. maternité et chômage abat.',
				'MMI' => 'Mal. maternité et chômage neut.',
				'MNE' => 'Mort né viable ou non viable',
				'MOA' => 'Membre org. comm. en activité',
				'MOC' => 'Membre org. comm. sans activité',
				'NAS' => 'Inassidu',
				'NCH' => 'Plus de droit / Non à charge',
				'NOB' => 'Non soumis obligation scolaire',
				'PIL' => 'Stagiaire prog insert locale',
				'PRE' => 'Pré retraite',
				'RAC' => 'Réduction activité (C.A.T.)',
				'RAT' => 'Rente AT',
				'RET' => 'Retraite',
				'RMA' => 'Titulaire contrat CIMA/CAV',
				'RSA' => 'Retraite(e) militaire < 60ans',
				'SAB' => 'Congé sabattique',
				'SAC' => 'Sans activité refus ANPE',
				'SAL' => 'Salarié(e)',
				'SAV' => 'Sans activité motif COTOREP',
				'SCI' => 'Malade/Handicap scolarisé',
				'SCO' => 'Scolaire',
				'SFP' => 'Stage formation professionnelle',
				'SIN' => 'Activité inconnue',
				'SNA' => 'Service national actif',
				'SNR' => 'Stage non remunéré et R.M.I.',
				'SSA' => 'Sans activité',
				'SUR' => 'Bénéf. rente de survivant A.T.',
				'TSA' => 'Travailleur saisonnier',
				'VRP' => 'Voyageur représentant placier',
			);

			natcasesort( $acts );

			return $acts;
		}

		/**
		 * Enums pour le champ informationseti.acteti
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function acteti() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('acteti')
			return array(
				'C' => 'Commerçant',
				'A' => 'Artisan',
				'L' => 'Profession libérale',
				'E' => 'Entrepreneur'
			);
		}

		/**
		 * Enums pour le champ identificationsflux.applieme
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function applieme() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Identificationflux')->enum('applieme')
			return array(
				'CRI' => 'Cristal Cnaf',
				'AGO' => 'Agora Ccmsa',
				'NRI' => '@IRMI Cnaf',
				'NRA' => '@RSA Cnaf',
				'IOD' => 'IODAS GFI',
				'GEN' => 'GENESIS SIRUS-BULL',
				'IAS' => 'IAS JVS implicit',
				'PER' => 'Peceaveal INFODB',
				'54' => ' Logiciel du CG 54'
			);
		}

		/**
		 * Enums pour le champ modescontact.autorutiadrelec
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function autorutiadrelec() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Modecontact')->enum('autorutiadrelec')
			return array(
				'A' => 'Accord d\'utilisation',
				'I' => 'Inconnu',
				'R' => 'Refus d\'utilisation'
			);
		}

		/**
		 * Enums pour le champ modescontact.autorutitel
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function autorutitel() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Modecontact')->enum('autorutitel')
			return array(
				'A' => 'Accord d\'utilisation',
				'I' => 'Inconnu',
				'R' => 'Refus d\'utilisation'
			);
		}

		/**
		 * Enums pour le champ condsadmins.aviscondadmrsa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function aviscondadmrsa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Condadmin')->enum('aviscondadmrsa')
			return array(
				'D' => 'Avis demandé au CG',
				'A' => 'Accord du CG',
				'R' => 'Refus du CG',
				"S" => 'Si avis demandé au CG sans suspension'
			);
		}

		/**
		 * Enums pour le champ derogations.avisdero
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function avisdero() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Derogation')->enum('avisdero')
			return array(
				'D' => 'Avis demandé au CG',
				'O' => 'Accord du CG',
				'N' => 'Refus du CG',
				'A' => 'Ajourné'
			);
		}

		/**
		 * Enums pour le champ avispcgdroitsrsa.avisdestpairsa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function avisdestpairsa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Avispcgdroitrsa')->enum('avisdestpairsa')
			return array(
				'D' => 'Avis demandé au CG',
				'A' => 'Accord du CG',
				'R' => 'Refus du CG'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.aviseqpluri
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function aviseqpluri() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Contratinsertion')->enum('aviseqpluri')
			return array(
				'R' => 'Réorientation',
				'M' => 'Maintien de l\'orientation'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.avisraison_ci
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function avisraison_ci() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Contratinsertion')->enum('avisraison_ci')
			return array(
				'D' => 'Défaut de conclusion',
				'N' => 'Non respect du contrat',
				'A' => 'Autre'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function categorie() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Historiqueetatpe')->enum('code')
			return array(
				'1' => 'Personnes sans emploi, immédiatement disponibles, tenues d\'accomplir des actes positifs de recherche d\'emploi, à la recherche d\'un emploi en CDI à plein temps.',
				'2' => 'Personnes sans emploi, tenues d\'accomplir des actes positifs de recherche d\'emploi, à la recherche d\'un emploi en CDI à temps partiel.',
				'3' => 'Personnes sans emploi, tenues d\'accomplir des actes positifs de recherche d\'emploi, à la recherche d\'un emploi en CDD, temporaire ou saisonnier, y compris de très courte durée.',
				'4' => 'Personnes sans emploi, non immédiatement disponibles, à la recherche d\'un emploi ( arrêt maladie de plus de 15 jours, formation de plus de 40 heures...).',
				'5' => 'Personnes pourvues d\'un emploi, à la recherche d\'un autre emploi ( salarié en préavis effectué ou non, CAE, bénévoles...).',
				'6' => 'Personnes non immédiatement disponibles, à la recherche d\'un autre emploi en CDI à temps plein, tenues d\'accomplir des actes positifs de recherche d\'emploi.',
				'7' => 'Personnes non immédiatement disponibles, à la recherche d\'un emploi en CDI à temps partiel, tenues d\'accomplir des actes positifs de recherche d\'emploi.',
				'8' => 'Personnes non immédiatement disponibles, à la recherche d\'un autre emploi en CDI, temporaire ou saisonnier, y compris de très courte durée, tenues d\'accomplir des actes positifs de recherche d\'emploi.',
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function commission() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'V' => 'Commission de validation',
				'D' => 'Commission de décision',
				'P' => 'Commission pluridisciplinaire'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function couvsoc() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Dsp.topcouvsoc -> valeurs possible : 0 || 1
			return array(
				'O' => 'Oui',
				'N' => 'Non',
				'P' => 'Pas de réponse'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function creareprisentrrech() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'O' => 'Oui',
				'N' => 'Non',
				'P' => 'Pas de réponse'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function decisionpdo() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'P' => 'En attente d\'ouverture',
				'I' => 'Instruction en cours',
				'O' => 'Droit ouvert',
				'R' => 'Rejeté',
				'A' => 'Radié',
				'S' => 'Suspendu'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function decisionrecours() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'P' => 'Pas de décision',
				'A' => 'Accord',
				'R' => 'Refus',
				'J' => 'Ajourné'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.decision_ci
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function decision_ci() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Contratinsertion')->enum('decision_ci')
			if( Configure::read( 'Cg.departement' ) != 93 ){
				return array(
					'E' => 'En attente de décision',
					'V' => 'Validation le',
					'N' => 'Non validé',
					'A' => 'Annulé'
				);
			}
			else{
				return array(
					'E' => 'En attente de décision',
					'V' => 'Validation à compter du',
					'A' => 'Annulé',
					'R' => 'Rejet'
				);
			}
		}

		/**
		 * Enums pour les champs
		 *	- dsps.demarlog
		 *	- dsps_revs.demarlog
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function demarlog() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dsp')->enum('demarlog')
			return array(
				'1101' => 'Accès à un logement',
				'1102' => 'Maintien dans le logement',
				'1103' => 'Aucune'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function dipfra() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'F' => 'Français',
				'E' => 'Etranger'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function dif() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'<=' => '<=',
				'=>' => '=>',
				'<' => '<',
				'>' => '>'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function domideract() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Dsp.topdomideract -> valeurs possible : 0 || 1
			return array(
				'O' => 'Oui',
				'N' => 'Non',
				'P' => 'Pas de réponse'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function drorsarmiant() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Dsp.topdrorsarmiant -> valeurs possible : 0 || 1

			return array(
				'O' => 'Oui',
				'N' => 'Non',
				'P' => 'Pas de réponse'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function drorsarmianta2() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Dsp.drorsarmianta2 -> valeurs possible : O || N || S
			return array(
				'O' => 'Oui',
				'N' => 'Non',
				'P' => 'Pas de réponse'
			);
		}

		/**
		 * Enums pour les champs
		 *	- dsps.duractdomi
		 *	- dsps_revs.duractdomi
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function duractdomi() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dsp')->enum('duractdomi')
			return array(
				'2104' => 'Moins d\'un an',
				'2105' => 'De 1 à 3 ans',
				'2106' => 'De 4 à 6 ans',
				'2107' => 'Plus de 6 ans'
			);
		}

		/**
		 * Enums pour les champs
		 *	- bilansparcours66.duree_engag
		 *	- contratsinsertion.duree_engag
		 *	- proposcontratsinsertioncovs58.duree_engag
		 *	- decisionsproposcontratsinsertioncovs58.duree_engag
		 *
		 * @return array
		 */
		public function duree_engag() {
			$function = 'duree_engag_cg'.Configure::read( 'Cg.departement' );
			if (method_exists($this, $function)) {
				return $this->{$function}();
			} else {
				return $this->duree_engag_default();
			}
		}

		/**
		 * Enums generique
		 *	- contratsinsertion.duree_engag
		 *	- proposcontratsinsertioncovs58.duree_engag
		 *	- decisionsproposcontratsinsertioncovs58.duree_engag
		 *
		 * @return array
		 */
		public function duree_engag_default() {
			return array(
				'3' => '3 mois',
				'6' => '6 mois',
				'9' => '9 mois',
				'12' => '12 mois',
				'18' => '18 mois',
				'24' => '24 mois'
			);
		}

		/**
		 * Enums pour les champs du CG 58
		 *	- contratsinsertion.duree_engag
		 *	- proposcontratsinsertioncovs58.duree_engag
		 *	- decisionsproposcontratsinsertioncovs58.duree_engag
		 *
		 * @return array
		 */
		public function duree_engag_cg58() {
			return array(
				'3' => '3 mois',
				'6' => '6 mois',
				'9' => '9 mois',
				'12' => '12 mois',
				'18' => '18 mois',
				'24' => '24 mois'
			);
		}

		/**
		 * Enums pour les champs du CG 66
		 *	- bilansparcours66.duree_engag
		 *	- contratsinsertion.duree_engag
		 *
		 * @return array
		 */
		public function duree_engag_cg66() {
			return array(
				'3' => '3 mois',
				'6' => '6 mois',
				'9' => '9 mois',
				'12' => '12 mois'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.duree_engag du CG 93
		 *
		 * @return array
		 */
		public function duree_engag_cg93() {
			return array(
				'3' => '3 mois',
				'6' => '6 mois',
				'9' => '9 mois',
				'12' => '12 mois',
				'18' => '18 mois',
				'24' => '24 mois'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.duree_engag du CG 976
		 *
		 * @return array
		 */
		public function duree_engag_cg976() {
			return array(
				'3' => '3 mois',
				'6' => '6 mois',
				'9' => '9 mois',
				'12' => '12 mois'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.duree_cdd
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function duree_cdd() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Contratinsertion')->enum('duree_cdd')
			return array(
				'DT1' => 'Temps plein',
				'DT2' => 'Temps partiel',
				'DT3' => 'Mi-temps'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.duree_hebdo_emp
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function duree_hebdo_emp() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Contratinsertion')->enum('duree_hebdo_emp')
			return array(
				'DHT1' => 'Moins de 35h',
				'DHT2' => '35h',
				'DHT3' => 'Entre 35h et 48h'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function elopersdifdisp() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'O' => 'Oui',
				'N' => 'Non',
				'P' => 'Pas de réponse'
			);
		}

		/**
		 * Enums pour le champ creancesalimentaires.engproccrealim
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function engproccrealim() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Creancealimentaire')->enum('engproccrealim')
			return array(
				'O' => 'Procédure engagée',
				'N' => 'Pas de procédure engagée',
				'R' => 'Refus d\'engagement de procédure'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.emp_occupe
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function emp_occupe() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Contratinsertion')->enum('emp_occupe')
			return array(
				'10' => 'Agriculteurs (salariés de leur exploitation)',
				'21' => 'Artisans (salariés de leur entreprise)',
				'22' => 'Commerçants et assimilés (salariés de leur entreprise)',
				'23' => 'Chefs d\'entreprise de 10 salariés ou plus (salariés de leur entreprise)',
				'31' => 'Professions libérales (exercées sous statut de salarié)',
				'33' => 'Cadres de la fonction publique',
				'34' => 'Professeurs, professions scientifiques',
				'35' => 'Professions de l\'information, des arts et des spectacles',
				'37' => 'Cadres administratifs et commerciaux d\'entreprises',
				'38' => 'Ingénieurs et cadres techniques d\'entreprises',
				'42' => 'Professeurs des écoles, instituteurs et professions assimilées',
				'43' => 'Professions intermédiaires de la santé et du travail social',
				'44' => 'Clergé, religieux',
				'45' => 'Professions intermédiaires administratives de la fonction publique',
				'46' => 'Professions intermédiaires administratives et commerciales des entreprises',
				'47' => 'Techniciens (sauf techniciens tertiaires)',
				'48' => 'Contremaîtres, agents de maîtrise (maîtrise administrative exclue)',
				'52' => 'Employés civils et agents de service de la fonction publique',
				'53' => 'Agents de surveillance',
				'54' => 'Employés administratifs d\'entreprise',
				'55' => 'Employés de commerce',
				'56' => 'Personnels des services directs aux particuliers',
				'62' => 'Ouvriers qualifiés de type industriel',
				'63' => 'Ouvriers qualifiés de type artisanal',
				'64' => 'Chauffeurs',
				'65' => 'Ouvriers qualifiés de la manutention, du magasinage et du transport',
				'67' => 'Ouvriers non qualifiés de type industriel',
				'68' => 'Ouvriers non qualifiés de type artisanal',
				'69' => 'Ouvriers agricoles et assimilés'
			);
		}

		/**
		 * Enums pour le champ creancesalimentaires.etatcrealim
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function etatcrealim() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Creancealimentaire')->enum('etatcrealim')
			return array(
				'SA' => 'Sanction appliquée',
				'DD' => 'Dispense demande',
				'AT' => 'Attente décision allocataire',
				'DS' => 'Dispense avec sanction',
				'SF' => 'Présence d\'ASF',
				'PS' => 'Pas de sanction',
				'DA' => 'Dispense accord',
				'PE' => 'Procédure engagée',
				'DR' => 'Dispense refus',
				'RM' => 'Ex-RMI',
				'MS' => 'Maintien sanction',
				'SI' => 'Sanction immédiate',
				'RE' => 'Refus engagement',
				'TR' => 'Tiers recueillant',
				'AA' => 'Aucune décision allocataire'
			);
		}

		/**
		 * Enums pour les champs
		 *	- historiquesdroits.etatdosrsa
		 *	- situationsallocataires.etatdosrsa
		 *	- situationsdossiersrsa.etatdosrsa
		 *
		 * Retourne la liste des états de dossier.
		 * Peut-être filtré par une liste de clés d'états de dossier.
		 *
		 * @param array $etatsDemandes
		 * @return array liste des états à afficher.
		 * @deprecated since version 3.1
		 * @example $this->Option->etatdosrsa( $this->Situationdossierrsa->etatAttente() )
		 */
		public function etatdosrsa($etatsDemandes=array()) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);

			$etats = array(
				'Z' => 'Non défini',
				'0'  => 'Nouvelle demande en attente de décision CG pour ouverture du droit',
				'1'  => 'Droit refusé',
				'2'  => 'Droit ouvert et versable',
				'3'  => 'Droit ouvert et suspendu (le montant du droit est calculable, mais l\'existence du droit est remis en cause)',
				'4'  => 'Droit ouvert mais versement suspendu (le montant du droit n\'est pas calculable)',
				'5'  => 'Droit clos',
				'6'  => 'Droit clos sur mois antérieur ayant eu des créances transferées ou une régularisation dans le mois de référence pour une période antérieure.'
			);

			if( empty($etatsDemandes) ) {
				return $etats;
			}
			else {
				$return = array();
				foreach( $etatsDemandes as $etatDemande ) {
					if( isset( $etats[$etatDemande] ) ) {
						$return[$etatDemande] = $etats[$etatDemande];
					}
				}
				return $return;
			}
		}

		/**
		 * Enums pour le champ suivisinstruction.suiirsa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function suiirsa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Suiviinstruction')->enum('suiirsa')
			return array(
				'01' => 'Données administratives',
				'11' => 'Données socio-profesionnelles du demandeur',
				'12' => 'Données socio-profesionnelles du conjoint',
				'13' => 'Nouvelles Données socio-professionnelles du demandeur',
				'14' => 'Nouvelles Données socio-professionnelles du conjoint',
				'21' => 'Données parcours du demandeur',
				'22' => 'Données parcours du conjoint',
				'23' => 'Nouvelles Données parcours du demandeur',
				'24' => 'Nouvelles Données parcours du conjoint',
				'31' => 'Données orientation du demandeur',
				'32' => 'Données orientation du conjoint',
				'33' => 'Nouvelles Données orientation du demandeur',
				'34' => 'Nouvelles Données orientation du conjoint',
			);
		}

		/**
		 * Enums pour le champ evenements.fg
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function fg() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Evenement')->enum('fg')
			return array(
				'SUS' => 'suspension',
				'DESALL' => 'désignation allocataire',
				'SITPRO' => 'situation professionnelle',
				'INTGRO' => 'interruption grossesse',
				'ETACIV' => 'état civil',
				'SITENFAUT' => 'situation enfant/aut',
				'RESTRIRSA' => 'ressources trimestrielles RSA',
				'SITFAM' => 'situation famille',
				'DECDEMPCG' => 'décision du Président du CG',
				'CARRSA' => 'caractéristiques RSA',
				'PROPCG' => 'proposition au Président CG',
				'HOSPLA'  => 'hospitalisation placement',
				'CIRMA' => 'Cirma ou Cav',
				'SUIRMA' => 'Suivi de Cirma ou de Cav',
				'RECPEN' => 'récépissé pension',
				'TITPEN'  => 'titre de pension',
				'REA' => 'réaffiliation (Fait générateur générique)',
				'DERPRE'  => 'Dérogation du Président du CG',
				'ABANEURES'  => 'abattement ou neuratisation de ressource',
				'DEMRSA'  => 'demande de RSA (Fait générateur générique)',
				'CREALI' => 'créance alimentaire',
				'ASF'  => 'demande ASF',
				'EXCPRE' => 'exclusion Prestation',
				'ADR' => 'Adresse',
				'RAD' => 'Radiation du dossier',
				'MUT' => 'Mutation du dossier',
				'JUSRSAJEU' => 'Justificatif RSA Jeune',
				'AIDFAM' => 'Aide familiale (DOM)',
				'ENTDED' => 'Entrant en droits et devoirs',
				'JUSACT' => 'Justification de l\'activité (DOM)',
				'SURPONEXP' => 'Surface pondérée exploitation (DOM)',
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function fonction_pers() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'ADM' => 'Administrateur',
				'VAL' => 'Validateur',
				'AGE' => 'Agent simple'
			);
		}

		/**
		 * Enums pour les champs
		 *	- cers93.formeci
		 *	- histoschoixcers93.formeci
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function formeci() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Cer93')->enum('formeci')
			return array(
				'S' => 'Simple',
				'C' => 'Complexe'
			);
		}

		/**
		 * Enums pour les champs
		 *	- contratsinsertion.forme_ci
		 *	- proposcontratsinsertioncovs58.forme_ci
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function forme_ci() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Contratinsertion')->enum('forme_ci')

			if( Configure::read( 'nom_form_ci_cg' ) == 'cg66' ) {
				return array( 'S' => 'Simple', 'C' => 'Particulier' );
			}

			return array( 'S' => 'Simple', 'C' => 'Complexe' );
		}

		/**
		 * Enums pour les champs
		 *	- dsps.hispro
		 *	- dsps_revs.hispro
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function hispro() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dsp')->enum('hispro')
			return array(
				'1901' => 'Vous avez toujours travaillé',
				'1902' => 'Vous travaillez par intermittence',
				'1903' => 'Vous avez déjà exercé une activité professionnelle',
				'1904' => 'Vous n\'avez jamais travaillé'
			);
		}

		/**
		 * Enums pour le champ actionsinsertion.lib_action
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function lib_action() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Actioninsertion')->enum('lib_action')
			return array(
				'A' => 'Aide',
				'P' => 'Prestation'
			);
		}

		/**
		 * Enums pour le champ modescontact.matetel
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function matetel() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Modecontact')->enum('matetel');
			return array(
				'FAX' => 'Fax seul',
				'TEL' => 'Téléphone seul',
				'TFA' => 'Téléphone/Fax'
			);
		}

		/**
		 * Enums pour le champ situationsdossiersrsa.moticlorsa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function moticlorsa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Situationdossierrsa')->enum('moticlorsa')
			return array(
				'PCG' => 'Cloture suite décision '.__d('default'.Configure::read('Cg.departement'), 'du Président du Conseil Général'),
				'ECH' => 'Cloture suite à échéance (4 mois sans droits) ',
				'EFF' => 'Cloture suite à l\'annulation de la bascule RMI/API',
				'MUT' => 'Cloture suite à mutation du dossier dans un autre organisme',
				'RGD' => 'Cloture pour regroupement de dossier',
				'RFD' => 'radié fin de droit',
				'RAU' => 'radié autre motif',
				'RST' => 'radié option RSTA Dom',
				'RSO' => 'radié option RSO Dom',
			);
		}

		/**
		 * Enums pour le champ creancesalimentaires.motidiscrealim
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function motidiscrealim() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Creancealimentaire')->enum('motidiscrealim')
			return array(
				'AVA' => 'Avantage en nature autre que le logement',
				'LOG' => 'Logement fourni par les parents',
				'PAM' => 'Pension à l\'amiable',
				'PHE' => 'Parent hors d\'état ou décédé',
				'DCG' => 'Dispense CG',
				'AUT' => 'Autre motif de dispense'
			);
		}

		/**
		 * Enums pour les champs
		 *	- decisionssaisinespdoseps66.motifpdo
		 *	- propospdos.motifpdo
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function motifpdo() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED); ///FIXME: ajout pour les PDO mais à voir
			// ClassRegistry::init('Propopdo')->enum('motifpdo')
			return array(
				'E' => 'En attente de justificatif',
				'A' => 'Admissible',
				'N' => 'Non admissible'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function motidempdo() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED); ///FIXME: ajout pour les PDO mais à voir
			// Origine inconnue
			return array(
				'C' => 'Changement de situation',
				'P' => 'Perte d\'emploi'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function motidemrsa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'0101' => 'Fin de droits ASSEDIC',
				'0102' => 'Fin de droits AAH',
				'0103' => 'Fin d\'indemnités journalières (maternité)',
				'0104' => 'Fin d\'indemnités journalières (maladie et accidents du travail)',
				'0105' => 'Attente de pension vieillesse ou invalidité, ou d\'allocation handicap',
				'0106' => 'Personne isolée avec grossesse ou enfants à charge de moins de 6 ans',
				'0107' => 'Faibles ressources',
				'0108' => 'Cessation d\'activité',
				'0109' => 'Fin d\'études',
			);
		}

		/**
		 * Enums pour le champ suspensionsdroits.motisusdrorsa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function motisusdrorsa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Suspensiondroit')->enum('motisusdrorsa')
			return array(
				'DA' => 'Suspension Dossier => Situation de famille',
				'DB' => 'Suspension Dossier => Ressources',
				'DC' => 'Suspension Dossier => Enquête administrative',
				'DD' => 'Suspension Dossier => Enquête sociale',
				'DE' => 'Suspension Dossier => Abs imprimé campagne contrôle',
				'DF' => 'Suspension Dossier => Absence avis changement CAF',
				'DG' => 'Suspension Dossier => Décès Madame',
				'DH' => 'Suspension Dossier => Décès Monsieur',
				'DI' => 'Suspension Dossier => Autre motif',
				'DJ' => 'Suspension Dossier => Présence paiemt réimp/arrêté',
				'DK' => 'Suspension Dossier => Abs réponse contrôle ASSEDIC',
				'DL' => 'Suspension Dossier => N\'habite plus adresse indiquée',
				'DM' => 'Suspension Dossier => Résidence inconnue',
				'DN' => 'Suspension Dossier => Diverg. droits SS susp anc.mod',
				'DO' => 'Suspension Dossier => Diverg. droits AV susp anc.mod',
				'DP' => 'Suspension Dossier => Contrôlee ASF hors d\'état',
				'GF' => 'Suspension Groupe Prestation => Situation de famille',
				'GR' => 'Suspension Groupe Prestation => Contrôle activité ressources',
				'GA' => 'Suspension Groupe Prestation => Enquête administrative',
				'GS' => 'Suspension Groupe Prestation => Enquête sociale',
				'GC' => 'Suspension Groupe Prestation => Abs. imprimé campagne contrôle',
				'GI' => 'Suspension Groupe Prestation => Imprimé chang. CAF non fourni',
				'GX' => 'Suspension Groupe Prestation => Autre motif',
				'GE' => 'Suspension Groupe Prestation => Forfait ETI non fourni',
				'GJ' => 'Suspension Groupe Prestation => RSA=> suspension PCG',
				'GK' => 'Suspension Groupe Prestation => RSA=> contrat insertion',
				'GL' => 'Suspension Groupe Prestation => RSA=> action non engagée'
			);
		}

		/**
		 * Enums pour le champ suspensionsversements.motisusversrsa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function motisusversrsa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Suspensionversement')->enum('motisusversrsa')
			return array(
				'01' => 'Ressources trop élévées',
				'02' => 'Moins de 25 ans et personne à charge',
				'03' => 'Activité non conforme',
				'04' => 'Titre de sejour non valide',
				'05' => 'RSA inférieur au seuil',
				'06' => 'Déclaration Trimestrielle Ressources non fournie',
				'09' => 'Résidence non conforme',
				'31' => 'Prestation exclue affil partielle',
				'34' => 'Régime non conforme',
				'35' => 'Demande avantage vielliesse absent ou tardif',
				'36' => 'Titre de séjour absent',
				'85' => 'Pas d\'allocataire (si allocataire décédé par exemple)',
				'97' => 'Bénéficiaires AAH réduite',
				'AB' => 'Allocataire absent du foyer',
				'CV' => 'Attente décision PCG (le droit reste théorique jusqu\'au retour)',
				'CG' => 'Application Sanction',
				//ajout suite à l'arrivée du RSAJeune
				'CZ' => 'Activité antérieure insuffisante',
				'DA' => 'Activité antérieure absente',
				'DB' => 'Etudiant rémunération insuff.',
				'DC' => 'Activité antérieure non conforme'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.nat_cont_trav
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function nat_cont_trav() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Contratinsertion')->enum('nat_cont_trav')
			return array(
				'TCT1' => 'Travailleur indépendant',
				'TCT2' => 'CDI',
				'TCT3' => 'CDD',
				'TCT4' => 'Contrat de travail temporaire (Intérim)',
				'TCT5' => 'Contrat de professionnalisation',
				'TCT6' => 'Contrat d\'apprentissage',
				'TCT7' => 'Contrat Initiative Emploi (CIE)',
				'TCT8' => 'Contrat d\'Accompagnement dans l\'Emploi (CAE)',
				'TCT9' => 'Chèque Emploi Service Universel (CESU)'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function nationalite() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Personne')->enum('nati')
			return array(
				'A' => 'Autre nationalité',
				'C' => 'Ressortissant CEE ou Suisse',
				'F' => 'Française'
			);
		}

		/**
		 * Enums pour les champs
		 *	- cers93.natlog
		 *	- dsps.natlog
		 *	- dsps_revs.natlog
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function natlog() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dsp')->enum('natlog')
			return array(
				'0901' => 'Logement autonome : habitat individuel',
				'0902' => 'Logement autonome : habitat collectif',
				'0903' => 'Logement d\'urgence : foyer d\'urgence',
				'0904' => 'Logement d\'urgence : CHRS (Centre d\'Hébergement et de Réinsertion Sociale)',
				'0905' => 'Logement d\'urgence : hôtel social',
				'0906' => 'Autre logement d\'urgence',
				'0907' => 'Logement temporaire : appartement relais',
				'0908' => 'Logement temporaire : bail glissant',
				'0909' => 'Logement temporaire : par parent ou tiers',
				'0910' => 'Logement temporaire : caravane, bateau,...',
				'0911' => 'Logement temporaire : résidence sociale',
				'0912' => 'Logement temporaire : sans hébergement',
				'0913' => 'Logement temporaire : autre situation'
			);
		}

		/**
		 * Enums pour le champ infosfinancieres.natpfcre
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function natpfcre( $type = null ) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);

			$natindu = array(
				'totsocl' => array(
					'RSD' => 'Rsa socle',
					'INK' => 'Indu sur rsa socle ',
					'ITK' => 'Indu sur rsa socle  transféré ou reçu d\'une autre Caf ou Msa ',
					'ISK' => 'Indu sur rsa socle subrogé',
					'ACD' => 'ACD',
					'ASD' => 'Acompte sur droit rsa. (le droit est constaté et ouvert)'
				),
				'soclmaj' => array(
					'RSI' => 'Rsa socle majoration parent isolé',
					'INL' => 'Indu sur rsa socle majoré',
					'ITL' => 'Indu sur rsa socle majoré transféré ou reçu d\'une autre Caf ou Msa '
				),
				'localrsa' => array(
					'RSB' => 'Rsa socle local',
					'RCB' => 'Rsa activité local',
					'INM' => 'Indu sur rsa socle local ou rSa activite local',
					'ITM' => 'Indu sur rsa socle local ou rSa activite local transféré ou reçu d\'une autre Caf ou Msa '
				),
				'indutotsocl' => array(
					'INK' => 'Indu sur rsa socle ',
					'ITK' => 'Indu sur rsa socle  transféré ou reçu d\'une autre Caf ou Msa ',
					'ISK' => 'Indu sur rsa socle subrogé',
				),
				'alloccompta' => array(
					'RSD' => 'Rsa socle',
					'RSI' => 'Rsa socle majoration parent isolé',
					'RSB' => 'Rsa socle local',
					'RCB' => 'Rsa activité local',
					'ASD' => 'Acompte sur droit rsa. (le droit est constaté et ouvert)',
					'VSD' => 'Avance sur droit rsa (suite absence DTRSa ou dans l\'attente de l\'ouverture du droit)',
					'INK' => 'Indu sur rsa socle ',
					'INL' => 'Indu sur rsa socle majoré',
					'INM' => 'Indu sur rsa socle local ou rSa activite local',
					'ITK' => 'Indu sur rsa socle  transféré ou reçu d\'une autre Caf ou Msa ',
					'ITL' => 'Indu sur rsa socle majoré transféré ou reçu d\'une autre Caf ou Msa ',
					'ITM' => 'Indu sur rsa socle local ou rSa activite local transféré ou reçu d\'une autre Caf ou Msa ',
					'ISK' => 'Indu sur rsa socle subrogé',
				),
				'indutransferecg' => array(
					'INK' => 'Indu sur rsa socle ',
					'INL' => 'Indu sur rsa socle majoré',
					'INM' => 'Indu sur rsa socle local ou rSa activite local',
					'ITK' => 'Indu sur rsa socle  transféré ou reçu d\'une autre Caf ou Msa ',
					'ITL' => 'Indu sur rsa socle majoré transféré ou reçu d\'une autre Caf ou Msa ',
					'ITM' => 'Indu sur rsa socle local ou rSa activite local transféré ou reçu d\'une autre Caf ou Msa ',
				),
				'annulationfaible' => array(
					'INK' => 'Indu sur rsa socle ',
					'INL' => 'Indu sur rsa socle majoré',
					'INM' => 'Indu sur rsa socle local ou rSa activite local',
					'ITK' => 'Indu sur rsa socle  transféré ou reçu d\'une autre Caf ou Msa ',
					'ITL' => 'Indu sur rsa socle majoré transféré ou reçu d\'une autre Caf ou Msa ',
					'ITM' => 'Indu sur rsa socle local ou rSa activite local transféré ou reçu d\'une autre Caf ou Msa ',
					'ISK' => 'Indu sur rsa socle subrogé',
					'INN' => 'Indu RCD RCI',
					'ITN' => 'Indu RCD RCI transféré',
					'INP' => 'Indu RSU RCU',
					'ITP' => 'Indu RSU RCU transféré'
				),
				'autreannulation' => array(
					'INK' => 'Indu sur rsa socle ',
					'INL' => 'Indu sur rsa socle majoré',
					'INM' => 'Indu sur rsa socle local ou rSa activite local',
					'ITK' => 'Indu sur rsa socle  transféré ou reçu d\'une autre Caf ou Msa ',
					'ITL' => 'Indu sur rsa socle majoré transféré ou reçu d\'une autre Caf ou Msa ',
					'ITM' => 'Indu sur rsa socle local ou rSa activite local transféré ou reçu d\'une autre Caf ou Msa ',
					'ISK' => 'Indu sur rsa socle subrogé',
				)
			);

			switch( $type ){
				case 'totalloccompta':
				case 'soclmaj':
				case 'localrsa':
				case 'alloccompta':
				case 'indutransferecg':
				case 'annulationfaible':
				case 'autreannulation':
					return $natindu[$type];
				default:
					$result = array();
					$keys = array_keys( $natindu );
					foreach( $keys as $key ) {
						$result = Set::merge( $result, $natindu[$key] );
					}
					return $result;
			}

			return array(
				/*AllocCompta*/
				'RSD' => 'Rsa socle',
				'RSI' => 'Rsa socle majoration parent isolé',
				'RSB' => 'Rsa socle local',
				'RCB' => 'Rsa activité local',
				'ASD' => 'Acompte sur droit rsa. (le droit est constaté et ouvert)',
				'VSD' => 'Avance sur droit rsa (suite absence DTRSa ou dans l\'attente de l\'ouverture du droit)',
				/*Indusconstates*/ /*Remises indus*/    /* Autres annulations*/
				/*IndustransférésCG*/   /* Annulation faible montant*/
				'INK' => 'Indu sur rsa socle ',
				'INL' => 'Indu sur rsa socle majoré',
				'INM' => 'Indu sur rsa socle local ou rSa activite local',
				'ITK' => 'Indu sur rsa socle  transféré ou reçu d\'une autre Caf ou Msa ',
				'ITL' => 'Indu sur rsa socle majoré transféré ou reçu d\'une autre Caf ou Msa ',
				'ITM' => 'Indu sur rsa socle local ou rSa activite local transféré ou reçu d\'une autre Caf ou Msa ',
				/*IndustransférésCG*/
				'ISK' => 'Indu sur rsa socle subrogé',
				/*Indusconstates*/ /*Remises indus*/    /* Autres annulations*/
				/*AllocCompta*/
				'INN' => 'Indu RCD RCI',
				'ITN' => 'Indu RCD RCI transféré',
				'INP' => 'Indu RSU RCU',
				'ITP' => 'Indu RSU RCU transféré'
				/* Annulation faible montant*/
			);
		}

		/**
		 * Enums pour le champ detailsressourcesmensuelles.natress
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function natress() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Detailressourcemensuelle')->enum('natress')
			return array(
				'000' => 'Ressources nulles',
				'001' => 'Salaires sans abattement supplementaire frais p',
				'002' => 'Frais professionnels reels deductibles',
				'003' => 'Salaires avec abattement supplementaire frais p',
				'004' => 'Abattement supplementaire pour frais profession',
				'005' => 'Salaires percus a l\'etranger',
				'006' => 'Revenus exceptionnels d\'activite salarie',
				'007' => 'Cav / cirma',
				'009' => 'Chomage partiel (technique)',
				'010' => 'Allocations de chomage',
				'011' => 'Indem. maladie/maternite/pater.',
				'012' => 'Accident travail/maladie prof.',
				'013' => 'Indemnites maternite/partenite/adoption',
				'014' => 'Autres ijss (maladie, at, mp)',
				'020' => 'Pre-retraite',
				'021' => 'Pension d\'invalidite',
				'022' => 'Pension de vieillesse imposable',
				'023' => 'Contrat d\'epargne - handicape',
				'024' => 'Rente viagere a titre gratuit',
				'025' => 'Allocation de veuvage',
				'026' => 'Pensions alimentaires recues',
				'027' => 'Rente viagere onereux - tiers',
				'028' => 'Pension vieill. non imposable',
				'029' => 'Majoration pension/retraite non imposable',
				'030' => 'Revenu des professions non salariees',
				'031' => 'Revenu profes non salar. non fixe ou inconnu',
				'032' => 'Forfait agricole',
				'033' => 'Forfait agricole non fixe',
				'034' => 'Revenu eti non cga ni micro-bic',
				'040' => 'Revenus fonciers et immobiliers',
				'041' => 'Autres revenus imposables',
				'042' => 'Ressources de l\'ex-conjoint (pinna)',
				'043' => 'Revenus soumis a prelevement liberatoire',
				'050' => 'Eval forf (salaires) ttes prest',
				'051' => 'Eval forf (eti) ttes prest',
				'052' => 'Evaluation forfaitaire (cat)',
				'053' => 'Evaluat. forfait. (salaires) / apl',
				'054' => 'Evaluation forfaitaire eti/ apl',
				'055' => 'Evaluation forfaitaire (esat g.r. 01/2007)',
				'060' => 'Pension alimentaire versee',
				'061' => 'Frais de garde',
				'062' => 'Deficit profes. annee de ref.',
				'063' => 'Deficit foncier',
				'064' => 'Csg deductible revenus patrim.',
				'065' => 'Cotisations volontaires ss',
				'066' => 'Frais de tutelle deductibles',
				'070' => 'Rente accident de travail  a titre personnel',
				'071' => 'Pension militaire invalidite',
				'072' => 'Pension victime de guerre',
				'080' => 'Salarie o.d (x 12)',
				'082' => 'Salarie autre renouvellement (x 12)',
				'083' => 'Salarie 1er renouvellement (x 12)',
				'085' => 'Eti od (profession non salariee)',
				'087' => 'Eti autre renouvellement (profess. non salariee)',
				'088' => 'Eti 1er renouvellement (profess. non salariee)',
				'200' => 'Revenus d\'activite d\'insertion (hors cre, ces)',
				'201' => 'Remuneration stage formation',
				'203' => 'Secours ou aides financieres reguliers',
				'204' => 'Indemnites representatives de frais',
				'205' => 'Revenu eti/marin pecheur/exploit agricole-rmi',
				'206' => 'Pf versees par un autre organisme',
				'207' => 'Nombre de repas rmi',
				'211' => 'Abattement / neutralisation rmi en montant',
				'212' => 'Bourse d\'etudes',
				'213' => 'Nombre asf fictives rmi',
				'214' => 'Montant asf fictive rmi',
				'215' => 'Revenus d\'activite d\'insertion (cre, ces)',
				'216' => 'Nombre d\'heures travaillees',
				'300' => 'Montant revenu sans pf pour api',
				'301' => 'Montant pf caf cedante - api',
				'302' => 'Mt forfait caf cedante - api',
				'303' => 'Montant allocation veuvage pour api',
				'305' => 'Avantages fictifs (p.a.,...)',
				'306' => 'Revenu createur d\'entreprise',
				'400' => 'Mont. (proport.) mensuel pension',
				'402' => 'Garantie de ressources',
				'403' => 'Salaire direct (en pourcentage smic)',
				'404' => 'Complement de remuneration',
				'405' => 'Salaire direct (en euros)',
				'406' => 'Maintien avi (oheix)',
				'407' => 'Maintien garantie de ressources (oheix)',
				'408' => 'Maintien salaire oheix (pourcent.)',
				'409' => 'Maintien cplt remun. (oheix)',
				'410' => 'Maintien salaire oheix (euros)',
				'500' => 'Montant pf etrangeres percues',
				'600' => 'Revenu d\'activite aged',
				'602' => 'Revenu trimestriel aged',
				'777' => 'Autres revenus pour le rso',
				'888' => 'Ressources effacees sur demande allocataire',
				'999' => 'Refus declarer ressources superieures plafond'
			);
		}

		/**
		 * Enums pour le champ grossesses.natfingro
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function natfingro() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Grossesse')->enum('natfingro')
			return array(
				'D' => 'Départ de madame du foyer',
				'I' => 'Interruption de grossesse',
				'M' => 'Enfant mort -né sans déclaration à l\'état civil',
				'N' => 'Naissance',
				'R' => 'Dossier radié sans connaissance des suites de la grossesse',
				'F' => 'Fin de grossesse non justifiée'
			);
		}

		/**
		 * Enums pour le champ suspensionsdroits.natgroupfsus
		 *
		 * Ajout suite à l'arrivée du RSAJeune
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function natgroupfsus() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Suspensiondroit')->enum('natgroupfsus')
			return array(
				'RSA' => 'RSA socle+activité',
				'RSX' => 'RSA socle uniquement',
				'RCX' => 'RSA activité uniquement',
				'DIF' => 'PF différentielles',
				'HOS' => 'PF hospitalisation',
				'ISO' => 'PF isolement'
			);
		}

		/**
		 * Enums pour le champ detailscalculsdroitsrsa.natpf
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function natpf( $natpfDemandees =array() ) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Detailcalculdroitrsa')->enum('natpf')
			$natpfs = array(
				'RSD' => 'RSA Socle (Financement sur fonds Conseil général)',
				'RSI' => 'RSA Socle majoré (Financement sur fonds Conseil général)',
				'RSU' => 'RSA Socle Etat Contrat aidé  (Financement sur fonds Etat)',
				'RSB' => 'RSA Socle Local (Financement sur fonds Conseil général)',
				'RCD' => 'RSA Activité (Financement sur fonds Etat)',
				'RCI' => 'RSA Activité majoré (Financement sur fonds Etat)',
				'RCU' => 'RSA Activité Etat Contrat aidé (Financement sur fonds Etat)',
				'RCB' => 'RSA Activité Local (Financement sur fonds Conseil général)',
				//ajout suite à l'arrivée du RSAJeune
				'RSJ' => 'RSA socle Jeune (Financement sur fonds Etat)',
				'RCJ' => 'RSA activité Jeune (Financement sur fonds Etat)',
				// TODO: dans la configuration ?
				'RSD,RCD' => 'RSA Socle et activité',
				//'RSJ,RCJ' => 'RSA Jeune Socle et activité',
				'RSD-RCD' => 'RSA Socle uniquement',
				'RCD-RSD' => 'RSA Activité uniquement',
			);

			if( empty($natpfDemandees) ) {
				return $natpfs;
			}
			else {
				$return = array();
				foreach( $natpfDemandees as $natpfDemandee ) {
					if( isset( $natpfs[$natpfDemandee] ) ) {
						$return[$natpfDemandee] = $natpfs[$natpfDemandee];
					}
				}
				return $return;
			}
		}

		/**
		 * Enums pour le champ modescontact.nattel
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function nattel() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Modecontact')->enum('nattel');
			return array(
				'D' => 'Domicile',
				'T' => 'Travail'
			);
		}

		public function natureAidesApres() {
			return array(
				'Formqualif'     => 'Formations individuelles qualifiantes',
				'Formpermfimo'   => 'Formation Permis Poids Lourd + FIMO',
				'Actprof'        => 'Action de professionnalisation des contrats aides et SIAE',
				'Permisb'        => 'Permis de conduire B',
				'Amenaglogt'     => 'Aide à l\'installation',
				'Acccreaentr'    => 'Accompagnement à la création d\'entreprise',
				'Acqmatprof'     => 'Acquisition de matériels professionnels',
				'Locvehicinsert' => 'Aide à la location d\'un véhicule d\'insertion'
			);
		}

		/**
		 * Enums pour le champ dossiers.numorg
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function numorg() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dossier')->enum('numorg')
			return array(
			'011' => 'CAF DE BOURG EN BRESSE',
			'021' => 'CAF DE SAINT QUENTIN',
			'022' => 'CAF DE SOISSONS',
			'031' => 'CAF DE MOULINS',
			'041' => 'CAF DE DIGNE-LES-BAINS',
			'051' => 'CAF DE GAP',
			'061' => 'CAF DE NICE',
			'071' => 'CAF D\'ANNONAY',
			'072' => 'CAF D\'AUBENAS',
			'081' => 'CAF DE CHARLEVILLE MEZIERES',
			'091' => 'CAF DE FOIX',
			'101' => 'CAF DE TROYES',
			'111' => 'CAF DE CARCASSONNE',
			'121' => 'CAF DE RODEZ',
			'131' => 'CAF DE MARSEILLE',
			'141' => 'CAF DE CAEN',
			'151' => 'CAF D\'AURILLAC',
			'161' => 'CAF D\'ANGOULEME',
			'171' => 'CAF DE LA ROCHELLE',
			'172' => 'CAISSE MARITIME D\'AF PECHE MARITIME',
			'181' => 'CAF DE BOURGES',
			'191' => 'CAF DE BRIVE',
			'201' => 'CAF D\'AJACCIO',
			'202' => 'CAF DE BASTIA',
			'211' => 'CAF DE DIJON',
			'221' => 'CAF DE SAINT BRIEUC',
			'231' => 'CAF DE GUERET',
			'241' => 'CAF DE PERIGUEUX',
			'251' => 'CAF DE BESANCON',
			'252' => 'CAF DE MONTBELIARD',
			'261' => 'CAF DE VALENCE',
			'271' => 'CAF D\'EVREUX',
			'281' => 'CAF DE CHARTRES',
			'291' => 'CAF DE BREST',
			'292' => 'CAF DE QUIMPER',
			'301' => 'CAF DE NIMES',
			'311' => 'CAF DE TOULOUSE',
			'321' => 'CAF D\'AUCH',
			'331' => 'CAF DE BORDEAUX',
			'341' => 'CAF DE BEZIERS',
			'342' => 'CAF DE MONTPELLIER',
			'351' => 'CAF DE RENNES',
			'361' => 'CAF DE CHATEAUROUX',
			'371' => 'CAF DE TOURS',
			'381' => 'CAF DE GRENOBLE',
			'382' => 'CAF DE VIENNE',
			'391' => 'CAF DE SAINT CLAUDE',
			'401' => 'CAF DE MONT DE MARSAN',
			'411' => 'CAF DE BLOIS',
			'421' => 'CAF DE ROANNE',
			'422' => 'CAF DE SAINT ETIENNE',
			'431' => 'CAF DU PUY',
			'441' => 'CAF DE NANTES',
			'451' => 'CAF D\'ORLEANS',
			'461' => 'CAF DE CAHORS',
			'471' => 'CAF D\'AGEN',
			'481' => 'CAF DE MENDE',
			'491' => 'CAF D\'ANGERS',
			'492' => 'CAF DE CHOLET',
			'501' => 'CAF D\'AVRANCHES',
			'511' => 'CAF DE REIMS',
			'521' => 'CAF DE CHAUMONT',
			'531' => 'CAF DE LAVAL',
			'541' => 'CAF DE NANCY',
			'551' => 'CAF DE BAR LE DUC',
			'561' => 'CAF DE VANNES',
			'571' => 'CAF DE METZ',
			'581' => 'CAF DE NEVERS',
			'591' => 'CAF D\'ARMENTIERES',
			'592' => 'CAF DE CAMBRAI',
			'593' => 'CAF DE DOUAI',
			'594' => 'CAF DE DUNKERQUE',
			'595' => 'CAF DE LILLE',
			'596' => 'CAF DE MAUBEUGE',
			'597' => 'CAF DE ROUBAIX',
			'599' => 'CAF DE VALENCIENNES',
			'601' => 'CAF DE BEAUVAIS',
			'602' => 'CAF DE CREIL',
			'611' => 'CAF D\'ALENCON',
			'621' => 'CAF D\'ARRAS',
			'622' => 'CAF DE CALAIS',
			'631' => 'CAF DE CLERMONT FERRAND',
			'641' => 'CAF DE BAYONNE',
			'642' => 'CAF DE PAU',
			'651' => 'CAF DE TARBES',
			'661' => 'CAF DE PERPIGNAN',
			'671' => 'CAF DE STRASBOURG',
			'681' => 'CAF DE MULHOUSE',
			'691' => 'CAF DE LYON',
			'692' => 'CAF DE VILLEFRANCHE SUR SAONE',
			'701' => 'CAF DE VESOUL',
			'711' => 'CAF DE MACON',
			'721' => 'CAF DU MANS',
			'731' => 'CAF DE CHAMBERY',
			'741' => 'CAF D\'ANNECY',
			'751' => 'CAF DE PARIS',
			'752' => 'CAF de PARIS - NAVIG. INTERIEURE',
			'754' => 'CAF de PARIS - MARINS DU COMMERCE',
			'761' => 'CAF DE DIEPPE',
			'762' => 'CAF D\'ELBEUF',
			'763' => 'CAF DU HAVRE',
			'764' => 'CAF DE ROUEN',
			'771' => 'CAF DE MELUN',
			'781' => 'CAF DE ST QUENTIN EN YVELINES',
			'791' => 'CAF DE NIORT',
			'801' => 'CAF D\'AMIENS',
			'811' => 'CAF D\'ALBI',
			'821' => 'CAF DE MONTAUBAN',
			'831' => 'CAF DE TOULON',
			'841' => 'CAF D\'AVIGNON',
			'851' => 'CAF DE LA ROCHE SUR YON',
			'861' => 'CAF DE POITIERS',
			'871' => 'CAF DE LIMOGES',
			'881' => 'CAF D\'EPINAL',
			'891' => 'CAF D\'AUXERRE',
			'901' => 'CAF DE BELFORT',
			'911' => 'CAF D\'EVRY',
			'921' => 'CAF DE NANTERRE',
			'931' => 'CAF DE ROSNY SOUS BOIS',
			'941' => 'CAF DE CRETEIL',
			'951' => 'CAF DE CERGY PONTOISE',
			'971' => 'CAF DE POINTE A PITRE',
			'972' => 'CAF DU LAMENTIN',
			'973' => 'CAF DE CAYENNE',
			'974' => 'CAF DE SAINT DENIS-DE-LA-REUNION',
			'976' => 'MAYOTTE'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function obstemploidifdisp() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'O' => 'Oui',
				'N' => 'Non',
				'P' => 'Pas de réponse'
			);
		}

		/**
		 * Enums pour le champ detailsdroitsrsa.oridemrsa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function oridemrsa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Detaildroitrsa')->enum('oridemrsa')
			return array(
				'DEM' => 'Le droit au Rsa fait suite à une demande de RSA',
				'RMI' => 'Le droit au rSa est issu de la conversion d\'un droit RMI',
				'API' => 'Le droit au rSa est issu de la conversion d\'un droit API'
			);
		}

		/**
		 * Enums pour le champ creancesalimentaires.orioblalim
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function orioblalim() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Creancealimentaire')->enum('orioblalim')
			return array(
				'CJT' => 'Obligation ex-conjoint',
				'PAR' => 'Obligation parent(s)'
			);
		}

		/**
		 * Enums pour le champ allocationssoutienfamilial.parassoasf
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function parassoasf() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Allocationsoutienfamilial')->enum('parassoasf')
			return array(
				'P' => 'Père',
				'M' => 'Mère'
			);
		}

		/**
		 * Enums pour le champ adresses.pays
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function pays() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Adresse')->enum('pays')
			return array(
				'FRA' => 'France',
				'HOR' => 'Hors de France'
			);
		}

		/**
		 * Enums pour le champ activites.paysact
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function paysact() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Activite')->enum('paysact')
			return array(
				'FRA' => 'France',
				'LUX' => 'Luxembourg',
				'CEE' => 'Communauté Européenne (sauf France, et  Luxembourg)',
				'ACE' => 'Assimilé à la Communauté Européenne',
				'CNV' => 'Pays avec convention sauf CEE',
				'AUT' => 'Autres pays'
			);
		}

		/**
		 * Enums pour le champ personnes.pieecpres
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function pieecpres() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Personne')->enum('pieecpres')
			return array(
				'E' => 'Pièce d\'état civil',
				'P' => 'Certificat de perte'
			);
		}

		/**
		 * @return array
		 */
		public function printed() {
			return array(
				'' => 'Imprimé/Non imprimé',
				'I' => 'Imprimé',
				'N' => 'Non imprimé'
			);
		}


		/**
		 * Enums pour les champs
		 *	- cers93.qual
		 *	- composfoyerscers93.qual
		 *	- contactspartenaires.qual
		 *	- membreseps.qual
		 *	- participantscomites.qual
		 *	- personnes.qual
		 *	- referents.qual
		 *	- situationsallocataires.qual
		 *	- suivisaidesapres.qual
		 *
		 * @return array
		 */
		public function qual() {
			return array(
				'MME' => 'Madame',
				'MR' => 'Monsieur'
			);
		}

		/**
		 * @return array
		 */
		public function quinzaine() {
			return array(
				'1' => 'Première quinzaine',
				'2' => 'Deuxième quinzaine'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.raison_ci
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function raison_ci() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Contratinsertion')->enum('raison_ci')
			return array(
				'S' => 'Suspension',
				'R' => 'Radiation'
			);
		}

		/**
		 * Enums pour le champ activites.reg
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function reg() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Activite')->enum('reg')
			return array(
				'AA' => 'ARTISTE/AUTEUR/COMPOSITEUR',
				'AD' => 'PF DUES PAR ADMINIS. (NON RETRAITE)',
				'AG' => 'AGRICOLE',
				'AL' => 'PF DUES PAR ADMINIS. SAUF AL',
				'AM' => 'ADMINIS. DROIT PF CAF (NON RETRAITE)',
				'CL' => 'COLLECT. LOCALE/HOPIT (NON RETRAITE)',
				'EF' => 'EDF - GDF',
				'EN' => 'EDUCATION NATIONALE',
				'FP' => 'FONCTION PUBLIC HORS EDUC. NAT.',
				'FT' => 'FRANCE TELECOM',
				'GE' => 'GENERAL',
				'MC' => 'MARIN DE COMMERCE',
				'MI' => 'MINES - REGIME MINIER',
				'MO' => 'MINES - REGIME GENERAL',
				'NI'  => 'NAVIGATION INTERIEURE',
				'PM' => 'PECHE',
				'PT'  => 'LA POSTE',
				'RE' => 'RETRAITE ETAB. INDUSTRIEL ETAT',
				'RL'  => 'RETRAITE COLLECT. LOCALE/HOPIT.',
				'RP' => 'PERSONNEL RATP',
				'SN'  => 'S.N.C.F.'
			);
		}

		/**
		 * Enums pour le champ infosagricoles.regfisagri
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function regfisagri() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Infoagricole')->enum('regfisagri')
			return array(
				'F' => 'Montant forfaitaire',
				'R' => 'Montant réél'
			);
		}

		/**
		 * Enums pour le champ informationseti.regfiseti
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function regfiseti() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('regfiseti')
			return array(
				'R' => 'Réel',
				'S' => 'Simple',
				'M' => 'Micro'
			);
		}

		/**
		 * Enums pour le champ informationseti.regfisetia1
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function regfisetia1() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('regfisetia1')
			return array(
				'R' => 'Réel',
				'S' => 'Simple',
				'M' => 'Micro'
			);
		}

		/**
		 * Enums pour le champ adressesfoyers.rgadr
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function rgadr() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Adresse')->enum('rgadr')
			return array(
				'01' => 'Dernière adresse',
				'02' => 'Avant-dernière adresse',
				'03' => 'Avant-avant-dernière adresse'
			);
		}

		/**
		 * Enums pour le champ prestations.rolepers
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function rolepers() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Prestation')->enum('rolepers')
			return array(
				'DEM' => 'Demandeur du RSA',
				'CJT' => 'Conjoint du demandeur',
				'ENF' => 'Enfant',
				'AUT' => 'Autre personne',
				'RDO' => 'Responsable du dossier'
			);
		}

		/**
		 * Enums pour le champ contratsinsertion.sect_acti_emp
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function sect_acti_emp() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Contratinsertion')->enum('sect_acti_emp')
			return array(
				'A' => 'Agriculture, sylviculture et pêche',
				'B' => 'Industries extractives',
				'C' => 'Industrie manufacturière',
				'D' => 'Production et distribution d\'électricité, de gaz, de vapeur et d\'air conditionné',
				'E' => 'Production et distribution d\'eau ; assainissement, gestion des déchets et dépollution',
				'F' => 'Construction',
				'G' => 'Commerce ; réparation d\'automobiles et de motocycles',
				'H' => 'Transports et entreposage',
				'I' => 'Hébergement et restauration',
				'J' => 'Information et communication',
				'K' => 'Activités financières et d\'assurance',
				'L' => 'Activités immobilières',
				'M' => 'Activités spécialisées, scientifiques et techniques',
				'N' => 'Activités de services administratifs et de soutien',
				'O' => 'Administration publique',
				'P' => 'Enseignement',
				'Q' => 'Santé humaine et action sociale',
				'R' => 'Arts, spectacles et activités récréatives',
				'S' => 'Autres activités de services',
				'T' => 'Activités des ménages en tant qu\'employeurs; activités indifférenciées des ménages en tant que producteurs de biens et services pour usage propre',
				'U' => 'Activités extra-territoriales'
			);
		}

		/**
		 * Enums pour le champ infosfinancieres.sensopecompta
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function sensopecompta() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Infofinanciere')->enum('sensopecompta')
			return array(
				'AJ' => 'Ajout du montant dans l\'acompte',
				'DE' => 'Déduction du montant dans l\'acompte'
			);
		}

		/**
		 * Enums pour les champs
		 *	- histoaprecomplementaires.sexe
		 *	- personnes.sexe
		 *	- situationsallocataires.sexe
		 *
		 * @return array
		 */
		public function sexe() {
			return array(
				'1' => 'Homme',
				'2' => 'Femme'
			);
		}

		/**
		 * Enums pour le champ allocationssoutienfamilial.sitasf
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function sitasf() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Allocationsoutienfamilial')->enum('sitasf')
			return array(
				'DC' => 'HORS D\'ETAT',
				'NR' => 'ENFANT NON RECONNU',
				'HB' => 'HORS D\'ETAT',
				'OE' => 'ABANDON - SANS JUGEMENT',
				'PA' => 'ABANDON - JUGEMENT SANS PENSION',
				'TP' => 'ABANDON - PENSION FIXEE',
				'AS' => 'SITUATION NON DROIT',
				'AD' => 'ALLOCATION ADOPTION',
				'RS' => 'ASF SUITE A RSA'
			);
		}

		/**
		 * Enums pour les champs
		 *	- bilansparcours66.sitfam
		 *	- cers93.sitfam
		 *	- contratsinsertion.sitfam
		 *	- foyers.sitfam
		 *	- situationsallocataires.sitfam
		 *
		 * @return array
		 */
		public function sitfam() {
			return array(
				'ABA' => 'Disparu (jugement d\'absence)',
				'CEL' => 'Célibataire',
				'DIV' => 'Divorcé(e)',
				'ISO' => 'Isolement après vie maritale ou PACS',
				'MAR' => 'Mariage',
				'PAC' => 'PACS',
				'RPA' => 'Reprise vie commune sur PACS',
				'RVC' => 'Reprise vie maritale',
				'RVM' => 'Reprise mariage',
				'SEF' => 'Séparation de fait',
				'SEL' => 'Séparation légale',
				'VEU' => 'Veuvage',
				'VIM' => 'Vie maritale'
			);
		}

		/**
		 * Enums pour le champ detailscalculsdroitsrsa.sousnatpf
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function sousnatpf() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Detailcalculdroitrsa')->enum('sousnatpf')
			return array(
				'RSDN1' => 'RSA Socle -25 avec enfants à charge ou grossesse',
				'RSDN2' => 'RSA Socle +25 ans',
				'RSIN1' => 'RSA Socle majoré',
				'RSUN1' => 'RSA Socle Etat Contrat aidé majoré',
				'RSUN2' => 'RSA Socle Etat Contrat aidé -25ans',
				'RSUN3' => 'RSA Socle Etat Contrat aidé +25ans',
				'RSUN4' => 'RSA Socle Etat Jeune',
				'RSBN1' => 'RSA Socle Local majoré',
				'RSBN2' => 'RSA Socle Local -25 ans',
				'RSBN3' => 'RSA Socle Local +25ans',
				'RSJN1' => 'RSA Socle Jeune',
				'RCDN1' => 'RSA Activité -25 avec enfants à charge ou grossesse',
				'RCDN2' => 'RSA Activité +25 ans',
				'RCIN1' => 'RSA Activité majoré',
				'RCUN1' => 'RSA Activité Etat Contrat aidé majoré',
				'RCUN2' => 'RSA Activité Etat Contrat aidé -25ans',
				'RCUN3' => 'RSA Activité Etat Contrat aidé +25ans',
				'RCUN4' => 'RSA Activité Etat Jeune',
				'RCBN1' => 'RSA Activité Local majoré',
				'RCBN2' => 'RSA Activité Local -25 ans',
				'RCBN3' => 'RSA Activité Local +25ans',
				'RCJN1' => 'RSA activité Jeune',
				'RSID1' => 'RSA socle majoré DOM',
				'RCID1' => 'RSA activité majoré DOM',
				'RSDD1' => 'RSA socle -25ans DOM avec enfants à charge ou grossesse',
				'RSDD2' => 'RSA socle +25ans DOM',
				'RCDD1' => 'RSA activité -25ans DOM avec enfants à charge ou grossesse',
				'RCDD2' => 'RSA activ.+25ans DOM',
				'RSUD1' => 'RSA socle Contrat Aidé majoré DOM',
				'RSUD2' => 'RSA socle Contrat Aidé  -25ans DOM',
				'RSUD3' => 'RSA socle Contrat Aidé +25ans DOM',
				'RSUD4' => 'RSA socle Contrat Aidé jeune DOM',
				'RCUD1' => 'RSA activité Contrat Aidé majoré DOM',
				'RCUD2' => 'RSA activité Contrat Aidé -25ans DOM',
				'RCUD3' => 'RSA activité Contrat Aidé +25ans DOM',
				'RCUD4' => 'RSA activité Contrat Aidé jeune DOM',
				'RSBD1' => 'RSA socle local majoré DOM',
				'RSBD2' => 'RSA socle local -25ans DOM',
				'RSBD3' => 'RSA socle local +25ans DOM',
				'RCBD1' => 'RSA activité local majoré DOM',
				'RCBD2' => 'RSA activtié local -25ans DOM',
				'RCBD3' => 'RSA activité local +25ans DOM',
				'RSJD1' => 'RSA socle jeune DOM',
				'RCJD2' => 'RSA activité jeune DOM',
			);
		}

		/**
		 * Enums pour les champs
		 *	- dsps.soutdemarsoc
		 *	- dsps_revs.soutdemarsoc
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function soutdemarsoc() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dsp')->enum('soutdemarsoc')
			return array(
				'O' => 'Oui',
				'N' => 'Non',
				'P' => 'Pas de réponse'
			);
		}

		/**
		 * Enums pour les champs
		 *	- dossiers.statudemrsa
		 *	- situationsallocataires.statudemrsa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function statudemrsa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dossier')->enum('statudemrsa')
			return array(
				'N' => 'Non allocataire',
				'C' => 'Allocataire de la CAF',
				'A' => 'Allocataire d\'une autre CAF',
				'M' => 'Allocataire de la MSA',
				'S' => 'Allocataire d\'une autre MSA'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function statut_contrat_insertion() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'1' => 'Validé',
				'2' => 'En attente',
				'3' => 'A valider',
				'4' => 'Rejeté',
				'5' => 'Afficher tout'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function statutrdv() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'P' => 'Prévu',
				'T' => 'Honoré',
				'A' => 'Annulé',
				'R' => 'Reporté'
			);
		}

		/**
		 * Enums pour le champ informationseti.topaccre
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topaccre() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('topaccre')
			return array(
				'1' => 'Bénéficiaire de l`ACCRE',
				'0' => 'Non bénéficiaire de l`ACCRE'
			);
		}

		/**
		 * Enums pour le champ informationseti.topbeneti
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topbeneti() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('topbeneti')
			return array(
				'1' => 'Présence d\'un bénéfice',
				'0' => 'Pas de bénéfices'
			);
		}

		/**
		 * Enums pour le champ creancesalimentaires.topdemdisproccrealim
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topdemdisproccrealim() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Creancealimentaire')->enum('topdemdisproccrealim')
			return array(
				'1' => 'Demande de dispense',
				'0' => 'Pas de demande de dispense'
			);
		}

		/**
		 * Enums pour le champ informationseti.topcreaentre
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topcreaentre() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('topcreaentre')
			return array(
				'1' => 'Créateur d\'entreprise',
				'0' => 'Non créateur d\'entreprise'
			);
		}

		/**
		 * Enums pour le champ informationseti.topempl1ax
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topempl1ax() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('topempl1ax')
			return array(
				'1' => 'Emploie 1 ou plusieurs salariés',
				'0' => 'N\'emploie pas 1 ou plusieurs salariés'
			);
		}

		/**
		 * Enums pour le champ informationseti.topevoreveti
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topevoreveti() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('topevoreveti')
			return array(
				'1' => 'Evolution des revenus',
				'0' => 'Pas d\'évolution des revenus'
			);
		}

		/**
		 * Enums pour le champ detailsdroitsrsa.topfoydrodevorsa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topfoydrodevorsa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Detaildroitrsa')->enum('topfoydrodevorsa')
			return array(
				'1' => 'le foyer est soumis à Droits et devoirs (le montant des ressources d\'acitivtés (MTRESSMENRSA) pris en compte pour le rSa est inférieur  au montant du revenu minimum garanti  rSa (MTREVMINGARASA)',
				'0' =>  'le foyer n\'est pas soumis à Droits et devoirs (le montant des ressources d\'acitivtés (MTRESSMENRSA) pris en compte pour le rSa est supérieur ou égale au montant du revenu minimum garanti  rSa (MTREVMINGARASA)'
			);
		}

		/**
		 * Enums pour le champ creancesalimentaires.topjugpa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topjugpa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Creancealimentaire')->enum('topjugpa')
			return array(
				'1' => 'Jugement fixant une pension alimentaire',
				'0' => 'Pas de jugement fixant une pension alimentaire'
			);
		}

		/**
		 * Enums pour les champs
		 *	- calculsdroitsrsa.toppersdrodevorsa
		 *	- historiquesdroits.toppersdrodevorsa
		 *	- situationsallocataires.toppersdrodevorsa
		 *
		 * @return array
		 */
		public function toppersdrodevorsa( $nullEnLettre = false ) {
			return array(
				( $nullEnLettre ? 'NULL' : '' ) => 'Non défini',
				'1' => 'Oui',
				'0' => 'Non'
			);
		}

		/**
		 * Enums pour le champ informationseti.topressevaeti
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topressevaeti() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('topressevaeti')
			return array(
				'1' => 'Ressources à évaluer',
				'0' => 'Pas de ressources à évaluer'
			);
		}

		/**
		 * Enums pour le champ detailsdroitsrsa.topsansdomfixe
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topsansdomfixe() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Detaildroitrsa')->enum('topsansdomfixe')
			return array(
				'0' => 'Domicile fixe',
				'1' => 'Sans domicile fixe'
			);
		}

		/**
		 * Enums pour le champ informationseti.topsansempl
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topsansempl() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('topsansempl')
			return array(
				'1' => 'Sans employés',
				'0' => 'Avec employés'
			);
		}

		/**
		 * Enums pour le champ informationseti.topstag1ax
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function topstag1ax() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Informationeti')->enum('topstag1ax')
			return array(
				'1' => 'Emploie 1 ou plusieurs stagiaires',
				'0' => 'N\'emploie pas 1 ou plusieurs stagiaires'
			);
		}

		/**
		 * Enums pour le champ infosfinancieres.type_allocation
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function type_allocation() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Infofinanciere')->enum('type_allocation')
			return array(
				'AllocationsComptabilisees' => 'Allocations comptabilisées',
				'IndusConstates' => 'Indu constaté',
				'IndusTransferesCG' => 'Indu transféré au CG',
				'RemisesIndus' => 'Remise d\'indu',
				'AnnulationsFaibleMontant' => 'Annulation pour faible montant',
				'AutresAnnulations' => 'Autre annulation'
			);
		}

		/**
		 * Enums pour le champ derogations.typedero
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typedero() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Derogation')->enum('typedero')
			return array(
				'AGE' => 'Dérogation sur les conditions d\'age',
				'ACT' => 'Dérogation sur les conditions d\'activité',
				'RES' => 'Dérogation sur les conditions de résidence',
				'NAT' => 'Dérogation sur les conditions de nationnalité'
			);
		}

		/**
		 * Enums pour le champ adressesfoyers.typeadr
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typeadr() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Adressefoyer')->enum('typeadr')
			return array(
				'D' => 'Définitive',
				'P' => 'Provisoire',
				'R' => 'Retour foyer principal'
			);
		}

		/**
		 * Enums pour le champ personnes.typedtnai
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typedtnai() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Personne')->enum('typedtnai')
			return array(
				'J' => 'Jour inconnu',
				'N' => 'Jour et mois connus',
				'O' => 'Jour et mois inconnus'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typenotifpdo() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'RE' => 'Ressortissant européen',
				'AN' => 'Activité non salariée',
				'AA' => 'Activité non salariée agricole',
				'CN' => 'Création activité non salariée',
				'CA' => 'Création activité non salariée agricole',
				'SN' => 'Stagiaire non rémunéré',
				'AS' => 'Accord stagiaire',
				'RS' => 'Renseignements étudiants',
				'AE' => 'Accord étudiant, élève',
				'DR' => 'Décision de réduction',
				'RN' => 'Radiation pour éléments non déclarés',
				'RD' => 'Radiation pour défaut d\'insertion'
			);
		}

		/**
		 * Enums pour les champs
		 *	- contratsinsertion.typeocclog
		 *	- foyers.typeocclog
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typeocclog() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Foyer')->enum('typeocclog')
			return array(
				'ACC' => 'Proprietaire avec charges de remboursement',
				'BAL' => 'Forfait logement a appliquer',
				'HCG' => 'Hébergement collectif a titre gratuit',
				'HCO' => 'Hébergement collectif a titre onereux',
				'HGP' => 'Hébergement à titre gratuit par des particuliers',
				'HOP' => 'Hébergement onereux par des particuliers',
				'HOT' => 'Hotel',
				'LOC' => 'Locataire ou sous locataire',
				'OLI' => 'Occupation logement inconnue',
				'PRO' => 'Proprietaire sans charges de remboursement',
				'SRG' => 'Sans resid. stable avec forfait logement',
				'SRO' => 'Sans resid. stable sans forfait logement'
			);
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typepdo() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'N' => 'Non défini',
				'C' => 'PDO de contrôle',
				'M' => 'PDO de maintien',
				'O' => 'PDO d\'ouverture'
			);
		}

		/**
		 * Enums pour le champ infosfinancieres.typeopecompta
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typeopecompta() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Infofinanciere')->enum('typeopecompta')
			return array(
				/*AllocCompta*/
				'PME' => 'Pour le paiement mensuel',
				'PRA' => 'Pour le paiement de rappel sur mois antérieur',
				'RAI' => 'Pour réajustement  suite à annulation d\'indus',
				'RMU' => 'Pour réajustement suite à mutation du dossier',
				'RTR' => 'Pour réajustement suite à transformation d\'avances ou d\'acomptes en indus',
				/*AllocCompta*/
				/*Indus constatés*/
				'CIC' => 'Implantation de créance',
				'CAI' => 'Implantation de créance suite à une opération comptable de réajustement. Une opération de type RAI a été effectuée sur un autre dossier allocataire.',
				'CDC' => 'Implantation d\'un  débit complémentaire (augmentation de la créance)',
				/*Indus constatés*/
				/*Indus transférés*/
				'CCP' => 'Transfert  de la créance au Conseil général',
				/*Indus transférés*/
				/*Remises indus*/
				'CRC' => 'Remise de la créance par le Conseil général',
				'CRG' => 'Remise de la créance par la Caf',
				/*Remises indus*/
				/*Annulation faible*/
				'CAF' => 'Annulation de faible montant  inférieur au seuil réglementaire',
				'CFC' => 'Annulation de faible montant selon seuil fixé par le Conseil général (supérieur au seuil réglementaire)',
				/*Annulation faible*/
				/*Autre annulations*/
				'CEX' => 'Annulation exceptionelle',
				'CES' => 'Annulation suite à surendettement',
				'CRN' => 'Annulation suite à renouvellement ou revalorisation (publication tardive des baremes, seuils, …)'
				/*Autre annulations*/
			);
		}

		/**
		 * Enums pour le champ rattachements.typepar
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typepar() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Rattachement')->enum('typepar')
			return array(
				'ADP' => 'Adoption simple',
				'ASC' => 'Ascendant',
				'AUT' => 'Autre',
				'BFI' => 'Gendre ou bru',
				'COL' => 'Coll degré 4',
				'DES' => 'Descendant',
				'FRE' => 'Frère ou soeur',
				'LEG' => 'Légitime',
				'NAT' => 'Naturel',
				'NEV' => 'Neveu ou nièce',
				'ONC' => 'Oncle ou tante',
				'REA' => 'Recueilli en vue adoption',
				'REC' => 'Recueilli'
			);
		}

		/**
		 * Enums pour le champ dossiers.typeparte
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typeparte() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dossier')->enum('typeparte')
			return array(
				'CG' => 'Conseil Général', // Code identification partenaire = n° de département sur 3 positions
				'CT' => 'Collectivité Territoriale',
				'CCAS' => 'Centre Communal d\'Action Sociale', // Code identification partenaire = N° de commune Insee sur 5 positions
				'CIAS' => 'Centre Intercommunal d\'Action Sociale', // Code identification partenaire = N° de commune Insee du siège de l'intercommunalité sur 5 positions
				'PE' => 'Pole Emploi', // Code identification partenaire = a préciser avec PE
				'MDPH' => 'Maison Départementale Pour le Handicap' //  Code identification partenaire = n° de département sur 3 positions
			);
		}

		/**
		 * Enums pour le champ avispcgdroitsrsa.typeperstie
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typeperstie() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Avispcgdroitrsa')->enum('typeperstie')
			return array(
				'P' => 'S\'il s\'agit d\'un tiers personne physique',
				'M' => 'S\'il s\'agit d\'un tiers personne morale'
			);
		}

		/**
		 * Enums pour le champ adresses.typeres
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typeres() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Adresse')->enum('typeres')
			return array(
				'E' => 'Election de domicile',
				'O' => 'Election de domicile organisme non référencé',
				'S' => 'Stable'
			);
		}

		/**
		 * Enums pour les champs
		 *	- servicesinstructeurs.typeserins
		 *	- situationsallocataires.typeserins
		 *	- suivisinstruction.typeserins
		 *
		 * @return array
		 */
		public function typeserins() {
			return array(
				'' => 'Non renseigné',
				'A' => 'Organisme agréé',
				'C' => 'Centre Communal d\'Action Sociale',
				'F' => 'Caisse d\'Allocation Familiale',
				'G' => 'Mutualité Sociale Agricole',
				'I' => 'Internaute',
				'P' => 'Pôle emploi',
				'S' => 'Service Social Départemental',
				'T' => 'Centre Intercommunal d\'Action Sociale'
			);
		}

		/**
		 * Enums pour le champ totalisationsacomptes.type_totalisation
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function type_totalisation() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Totalisationacompte')->enum('type_totalisation')
			return array(
				'TotalAllocationsComptabilisees' => 'Total des allocations comptabilisees',
				'TotalIndusConstates' => 'Total des indus constates',
				'TotalIndusTransferesCG' => 'Total des indus transferés au CG',
				'TotalRemisesIndus' => 'Total des remises des indus',
				'TotalAnnulationsIndus' => 'Total des annulations des indus',
				'MontantTotalAcompte' => 'Montant total de l\'acompte'
			);
		}

		/**
		 * Enums pour les champs
		 *	- membreseps.typevoie
		 *	- partenaires.typevoie
		 *	- permanences.typevoie
		 *	- tiersprestatairesapres.typevoie
		 *	- users.typevoie
		 *
		 * @deprecated since 3.2
		 *
		 * @return array
		 */
		public function typevoie() {
			return array(
				'ABE' => 'Abbaye',
				'ACH' => 'Ancien chemin',
				'AGL' => 'Agglomération',
				'AIRE' => 'Aire',
				'ALL' => 'Allée',
				'ANSE' => 'Anse',
				'ARC' => 'Arcade',
				'ART' => 'Ancienne route',
				'AUT' => 'Autoroute',
				'AV' => 'Avenue',
				'BAST' => 'Bastion',
				'BCH' => 'Bas chemin',
				'BCLE' => 'Boucle',
				'BD' => 'Boulevard',
				'BEGI' => 'Béguinage',
				'BER' => 'Berge',
				'BOIS' => 'Bois',
				'BRE' => 'Barriere',
				'BRG' => 'Bourg',
				'BSTD' => 'Bastide',
				'BUT' => 'Butte',
				'CALE' => 'Cale',
				'CAMP' => 'Camp',
				'CAR' => 'Carrefour',
				'CARE' => 'Carriere',
				'CARR' => 'Carre',
				'CAU' => 'Carreau',
				'CAV' => 'Cavée',
				'CGNE' => 'Campagne',
				'CHE' => 'Chemin',
				'CHEM' => 'Cheminement',
				'CHEZ' => 'Chez',
				'CHI' => 'Charmille',
				'CHL' => 'Chalet',
				'CHP' => 'Chapelle',
				'CHS' => 'Chaussée',
				'CHT' => 'Château',
				'CHV' => 'Chemin vicinal',
				'CITE' => 'Cité',
				'CLOI' => 'Cloître',
				'CLOS' => 'Clos',
				'COL' => 'Col',
				'COLI' => 'Colline',
				'COR' => 'Corniche',
				'COTE' => 'Côte(au)',
				'COTT' => 'Cottage',
				'COUR' => 'Cour',
				'CPG' => 'Camping',
				'CRS' => 'Cours',
				'CST' => 'Castel',
				'CTR' => 'Contour',
				'CTRE' => 'Centre',
				'DARS' => 'Darse',
				'DEG' => 'Degré',
				'DIG' => 'Digue',
				'DOM' => 'Domaine',
				'DSC' => 'Descente',
				'ECL' => 'Ecluse',
				'EGL' => 'Eglise',
				'EN' => 'Enceinte',
				'ENC' => 'Enclos',
				'ENV' => 'Enclave',
				'ESC' => 'Escalier',
				'ESP' => 'Esplanade',
				'ESPA' => 'Espace',
				'ETNG' => 'Etang',
				'FG' => 'Faubourg',
				'FON' => 'Fontaine',
				'FORM' => 'Forum',
				'FORT' => 'Fort',
				'FOS' => 'Fosse',
				'FOYR' => 'Foyer',
				'FRM' => 'Ferme',
				'GAL' => 'Galerie',
				'GARE' => 'Gare',
				'GARN' => 'Garenne',
				'GBD' => 'Grand boulevard',
				'GDEN' => 'Grand ensemble',
				'GPE' => 'Groupe',
				'GPT' => 'Groupement',
				'GR' => 'Grand(e) rue',
				'GRI' => 'Grille',
				'GRIM' => 'Grimpette',
				'HAM' => 'Hameau',
				'HCH' => 'Haut chemin',
				'HIP' => 'Hippodrome',
				'HLE' => 'Halle',
				'HLM' => 'HLM',
				'ILE' => 'Ile',
				'IMM' => 'Immeuble',
				'IMP' => 'Impasse',
				'JARD' => 'Jardin',
				'JTE' => 'Jetée',
				'LD' => 'Lieu dit',
				'LEVE' => 'Levée',
				'LOT' => 'Lotissement',
				'MAIL' => 'Mail',
				'MAN' => 'Manoir',
				'MAR' => 'Marche',
				'MAS' => 'Mas',
				'MET' => 'Métro',
				'MF' => 'Maison forestiere',
				'MLN' => 'Moulin',
				'MTE' => 'Montée',
				'MUS' => 'Musée',
				'NTE' => 'Nouvelle route',
				'PAE' => 'Petite avenue',
				'PAL' => 'Palais',
				'PARC' => 'Parc',
				'PAS' => 'Passage',
				'PASS' => 'Passe',
				'PAT' => 'Patio',
				'PAV' => 'Pavillon',
				'PCH' => 'Porche - petit chemin',
				'PERI' => 'Périphérique',
				'PIM' => 'Petite impasse',
				'PKG' => 'Parking',
				'PL' => 'Place',
				'PLAG' => 'Plage',
				'PLAN' => 'Plan',
				'PLCI' => 'Placis',
				'PLE' => 'Passerelle',
				'PLN' => 'Plaine',
				'PLT' => 'Plateau(x)',
				'PN' => 'Passage à niveau',
				'PNT' => 'Pointe',
				'PONT' => 'Pont(s)',
				'PORQ' => 'Portique',
				'PORT' => 'Port',
				'POT' => 'Poterne',
				'POUR' => 'Pourtour',
				'PRE' => 'Pré',
				'PROM' => 'Promenade',
				'PRQ' => 'Presqu\'île',
				'PRT' => 'Petite route',
				'PRV' => 'Parvis',
				'PSTY' => 'Peristyle',
				'PTA' => 'Petite allée',
				'PTE' => 'Porte',
				'PTR' => 'Petite rue',
				'QU' => 'Quai',
				'QUA' => 'Quartier',
				'R' => 'Rue',
				'RAC' => 'Raccourci',
				'RAID' => 'Raidillon',
				'REM' => 'Rempart',
				'RES' => 'Résidence',
				'RLE' => 'Ruelle',
				'ROC' => 'Rocade',
				'ROQT' => 'Roquet',
				'RPE' => 'Rampe',
				'RPT' => 'Rond point',
				'RTD' => 'Rotonde',
				'RTE' => 'Route',
				'SEN' => 'Sentier',
				'SQ' => 'Square',
				'STA' => 'Station',
				'STDE' => 'Stade',
				'TOUR' => 'Tour',
				'TPL' => 'Terre plein',
				'TRA' => 'Traverse',
				'TRN' => 'Terrain',
				'TRT' => 'Tertre(s)',
				'TSSE' => 'Terrasse(s)',
				'VAL' => 'Val(lée)(lon)',
				'VCHE' => 'Vieux chemin',
				'VEN' => 'Venelle',
				'VGE' => 'Village',
				'VIA' => 'Via',
				'VLA' => 'Villa',
				'VOI' => 'Voie',
				'VTE' => 'Vieille route',
				'ZA' => 'Zone artisanale',
				'ZAC' => 'Zone d\'aménagement concerte',
				'ZAD' => 'Zone d\'aménagement différé',
				'ZI' => 'Zone industrielle',
				'ZONE' => 'Zone',
				'ZUP' => 'Zone à urbaniser en priorité'
			);
		}

		/**
		 * Retourne une liste clé / valeurs de libellées de types de voies pour
		 * les listes d'options des adresses.
		 *
		 * @return array
		 */
		public function libtypevoie() {
			return $this->_libtypevoie;
		}

		/**
		 * Enums pour le champ aidesdirectes.typo_aide
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function typo_aide() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Aidedirecte')->enum('typo_aide')
			return array(
				'1' => 'Insertion sociale',
				'2' => 'Insertion professionnelle',
				'3' => 'Reprise d\'activités'
			);
		}

		/**
		 * Enums pour le champ creancesalimentaires.verspa
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function verspa() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Creancealimentaire')->enum('verspa')
			return array(
				'N' => 'Pas de versement d\'une PA',
				'O' => 'Versement d\'une PA',
				'P' => 'Versement partiel d\'une PA'
			);
		}

		/**
		 * Enums pour le champ dossiers.fonorgcedmut
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function fonorgcedmut() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dossier')->enum('fonorgcedmut')
			return array(
				'CAF' => 'Demande gérée par la CAF',
				'MSA' => 'Demande gérée par la MSA',
				'OPF' => 'Autres organismes débiteur de prestations familiales'
			);
		}

		/**
		 * Enums pour le champ dossiers.fonorgprenmut
		 *
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function fonorgprenmut() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// ClassRegistry::init('Dossier')->enum('fonorgprenmut')
			return $this->fonorgcedmut();
		}

		/**
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function motifrecours() {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			// Origine inconnue
			return array(
				'N' => 'Non admissible',
				'A' => 'Admissible',
				'P' => 'Pièces manquantes'
			);
		}
	}
?>
