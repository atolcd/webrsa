SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;
SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

UPDATE contratsinsertion SET decision_ci = 'R' WHERE decision_ci = 'N';

INSERT INTO secteursactis ( name ) VALUES
	( 'Agriculture, sylviculture et pêche' ),
	( 'Industries extractives' ),
	( 'Industrie manufacturière' ),
	( 'Production et distribution d''électricité, de gaz, de vapeur et d''air conditionné' ),
	( 'Production et distribution d''eau ; assainissement, gestion des déchets et dépollution' ),
	( 'Construction' ),
	( 'Commerce ; réparation d''automobiles et de motocycles' ),
	( 'Transports et entreposage' ),
	( 'Hébergement et restauration' ),
	( 'Information et communication' ),
	( 'Activités financières et d''assurance' ),
	( 'Activités immobilières' ),
	( 'Activités spécialisées, scientifiques et techniques' ),
	( 'Activités de services administratifs et de soutien' ),
	( 'Administration publique' ),
	( 'Enseignement' ),
	( 'Santé humaine et action sociale' ),
	( 'Arts, spectacles et activités récréatives' ),
	( 'Autres activités de services' ),
	( 'Activités des ménages en tant qu''employeurs; activités indifférenciées des ménages en tant que producteurs de biens et services pour usage propre' ),
	( 'Activités extra-territoriales' );

INSERT INTO metiersexerces ( name ) VALUES
	( 'Agriculteurs (salariés de leur exploitation)' ),
	( 'Artisans (salariés de leur entreprise)' ),
	( 'Commerçants et assimilés (salariés de leur entreprise)' ),
	( 'Chefs d''entreprise de 10 salariés ou plus (salariés de leur entreprise)' ),
	( 'Professions libérales (exercées sous statut de salarié)' ),
	( 'Cadres de la fonction publique' ),
	( 'Professeurs, professions scientifiques' ),
	( 'Professions de l''information, des arts et des spectacles' ),
	( 'Cadres administratifs et commerciaux d''entreprises' ),
	( 'Ingénieurs et cadres techniques d''entreprises' ),
	( 'Professeurs des écoles, instituteurs et professions assimilées' ),
	( 'Professions intermédiaires de la santé et du travail social' ),
	( 'Clergé, religieux' ),
	( 'Professions intermédiaires administratives de la fonction publique' ),
	( 'Professions intermédiaires administratives et commerciales des entreprises' ),
	( 'Techniciens (sauf techniciens tertiaires)' ),
	( 'Contremaîtres, agents de maîtrise (maîtrise administrative exclue)' ),
	( 'Employés civils et agents de service de la fonction publique' ),
	( 'Agents de surveillance' ),
	( 'Employés administratifs d''entreprise' ),
	( 'Employés de commerce' ),
	( 'Personnels des services directs aux particuliers' ),
	( 'Ouvriers qualifiés de type industriel' ),
	( 'Ouvriers qualifiés de type artisanal' ),
	( 'Chauffeurs' ),
	( 'Ouvriers qualifiés de la manutention, du magasinage et du transport' ),
	( 'Ouvriers non qualifiés de type industriel' ),
	( 'Ouvriers non qualifiés de type artisanal' ),
	( 'Ouvriers agricoles et assimilés' );

INSERT INTO naturescontrats ( name, isduree ) VALUES
	( 'Travailleur indépendant', '0' ),
	( 'CDI', '0' ),
	( 'CDD', '1' ),
	( 'Contrat de travail temporaire (Intérim)', '0' ),
	( 'Contrat de professionnalisation', '0' ),
	( 'Contrat d''apprentissage', '0' ),
	( 'Contrat Initiative Emploi (CIE)', '0' ),
	( 'Contrat d''Accompagnement dans l''Emploi (CAE)', '0' ),
	( 'Chèque Emploi Service Universel (CESU)', '0' );

INSERT INTO cers93( contratinsertion_id, user_id, matricule, dtdemrsa, qual, nom, nomnai, prenom, dtnai, adresse, codepos, locaadr, sitfam, numdemrsa, rolepers, positioncer, formeci, datesignature, isemploitrouv, secteuracti_id, metierexerce_id, dureehebdo, naturecontrat_id, dureecdd, prevu, observpro, structureutilisateur, nomutilisateur )
SELECT
		contratsinsertion.id AS contratinsertion_id,
		( SELECT id FROM users WHERE username = 'hpont' ) AS user_id,
		( dossiers.matricule ) AS matricule,
		( dossiers.dtdemrsa ) AS dtdemrsa,
		( personnes.qual ) AS qual,
		( personnes.nom ) AS nom,
		( personnes.nomnai ) AS nomnai,
		( personnes.prenom ) AS prenom,
		( personnes.dtnai ) AS dtnai,
		(
			TRIM(
				BOTH ' ' FROM
				(
					COALESCE( adresses.numvoie, '' ) || ' ' ||
					(
						CASE
							WHEN adresses.typevoie = 'ABE' THEN 'Abbaye'
							WHEN adresses.typevoie = 'ACH' THEN 'Ancien chemin'
							WHEN adresses.typevoie = 'AGL' THEN 'Agglomération'
							WHEN adresses.typevoie = 'AIRE' THEN 'Aire'
							WHEN adresses.typevoie = 'ALL' THEN 'Allée'
							WHEN adresses.typevoie = 'ANSE' THEN 'Anse'
							WHEN adresses.typevoie = 'ARC' THEN 'Arcade'
							WHEN adresses.typevoie = 'ART' THEN 'Ancienne route'
							WHEN adresses.typevoie = 'AUT' THEN 'Autoroute'
							WHEN adresses.typevoie = 'AV' THEN 'Avenue'
							WHEN adresses.typevoie = 'BAST' THEN 'Bastion'
							WHEN adresses.typevoie = 'BCH' THEN 'Bas chemin'
							WHEN adresses.typevoie = 'BCLE' THEN 'Boucle'
							WHEN adresses.typevoie = 'BD' THEN 'Boulevard'
							WHEN adresses.typevoie = 'BEGI' THEN 'Béguinage'
							WHEN adresses.typevoie = 'BER' THEN 'Berge'
							WHEN adresses.typevoie = 'BOIS' THEN 'Bois'
							WHEN adresses.typevoie = 'BRE' THEN 'Barriere'
							WHEN adresses.typevoie = 'BRG' THEN 'Bourg'
							WHEN adresses.typevoie = 'BSTD' THEN 'Bastide'
							WHEN adresses.typevoie = 'BUT' THEN 'Butte'
							WHEN adresses.typevoie = 'CALE' THEN 'Cale'
							WHEN adresses.typevoie = 'CAMP' THEN 'Camp'
							WHEN adresses.typevoie = 'CAR' THEN 'Carrefour'
							WHEN adresses.typevoie = 'CARE' THEN 'Carriere'
							WHEN adresses.typevoie = 'CARR' THEN 'Carre'
							WHEN adresses.typevoie = 'CAU' THEN 'Carreau'
							WHEN adresses.typevoie = 'CAV' THEN 'Cavée'
							WHEN adresses.typevoie = 'CGNE' THEN 'Campagne'
							WHEN adresses.typevoie = 'CHE' THEN 'Chemin'
							WHEN adresses.typevoie = 'CHEM' THEN 'Cheminement'
							WHEN adresses.typevoie = 'CHEZ' THEN 'Chez'
							WHEN adresses.typevoie = 'CHI' THEN 'Charmille'
							WHEN adresses.typevoie = 'CHL' THEN 'Chalet'
							WHEN adresses.typevoie = 'CHP' THEN 'Chapelle'
							WHEN adresses.typevoie = 'CHS' THEN 'Chaussée'
							WHEN adresses.typevoie = 'CHT' THEN 'Château'
							WHEN adresses.typevoie = 'CHV' THEN 'Chemin vicinal'
							WHEN adresses.typevoie = 'CITE' THEN 'Cité'
							WHEN adresses.typevoie = 'CLOI' THEN 'Cloître'
							WHEN adresses.typevoie = 'CLOS' THEN 'Clos'
							WHEN adresses.typevoie = 'COL' THEN 'Col'
							WHEN adresses.typevoie = 'COLI' THEN 'Colline'
							WHEN adresses.typevoie = 'COR' THEN 'Corniche'
							WHEN adresses.typevoie = 'COTE' THEN 'Côte(au)'
							WHEN adresses.typevoie = 'COTT' THEN 'Cottage'
							WHEN adresses.typevoie = 'COUR' THEN 'Cour'
							WHEN adresses.typevoie = 'CPG' THEN 'Camping'
							WHEN adresses.typevoie = 'CRS' THEN 'Cours'
							WHEN adresses.typevoie = 'CST' THEN 'Castel'
							WHEN adresses.typevoie = 'CTR' THEN 'Contour'
							WHEN adresses.typevoie = 'CTRE' THEN 'Centre'
							WHEN adresses.typevoie = 'DARS' THEN 'Darse'
							WHEN adresses.typevoie = 'DEG' THEN 'Degré'
							WHEN adresses.typevoie = 'DIG' THEN 'Digue'
							WHEN adresses.typevoie = 'DOM' THEN 'Domaine'
							WHEN adresses.typevoie = 'DSC' THEN 'Descente'
							WHEN adresses.typevoie = 'ECL' THEN 'Ecluse'
							WHEN adresses.typevoie = 'EGL' THEN 'Eglise'
							WHEN adresses.typevoie = 'EN' THEN 'Enceinte'
							WHEN adresses.typevoie = 'ENC' THEN 'Enclos'
							WHEN adresses.typevoie = 'ENV' THEN 'Enclave'
							WHEN adresses.typevoie = 'ESC' THEN 'Escalier'
							WHEN adresses.typevoie = 'ESP' THEN 'Esplanade'
							WHEN adresses.typevoie = 'ESPA' THEN 'Espace'
							WHEN adresses.typevoie = 'ETNG' THEN 'Etang'
							WHEN adresses.typevoie = 'FG' THEN 'Faubourg'
							WHEN adresses.typevoie = 'FON' THEN 'Fontaine'
							WHEN adresses.typevoie = 'FORM' THEN 'Forum'
							WHEN adresses.typevoie = 'FORT' THEN 'Fort'
							WHEN adresses.typevoie = 'FOS' THEN 'Fosse'
							WHEN adresses.typevoie = 'FOYR' THEN 'Foyer'
							WHEN adresses.typevoie = 'FRM' THEN 'Ferme'
							WHEN adresses.typevoie = 'GAL' THEN 'Galerie'
							WHEN adresses.typevoie = 'GARE' THEN 'Gare'
							WHEN adresses.typevoie = 'GARN' THEN 'Garenne'
							WHEN adresses.typevoie = 'GBD' THEN 'Grand boulevard'
							WHEN adresses.typevoie = 'GDEN' THEN 'Grand ensemble'
							WHEN adresses.typevoie = 'GPE' THEN 'Groupe'
							WHEN adresses.typevoie = 'GPT' THEN 'Groupement'
							WHEN adresses.typevoie = 'GR' THEN 'Grand(e) rue'
							WHEN adresses.typevoie = 'GRI' THEN 'Grille'
							WHEN adresses.typevoie = 'GRIM' THEN 'Grimpette'
							WHEN adresses.typevoie = 'HAM' THEN 'Hameau'
							WHEN adresses.typevoie = 'HCH' THEN 'Haut chemin'
							WHEN adresses.typevoie = 'HIP' THEN 'Hippodrome'
							WHEN adresses.typevoie = 'HLE' THEN 'Halle'
							WHEN adresses.typevoie = 'HLM' THEN 'HLM'
							WHEN adresses.typevoie = 'ILE' THEN 'Ile'
							WHEN adresses.typevoie = 'IMM' THEN 'Immeuble'
							WHEN adresses.typevoie = 'IMP' THEN 'Impasse'
							WHEN adresses.typevoie = 'JARD' THEN 'Jardin'
							WHEN adresses.typevoie = 'JTE' THEN 'Jetée'
							WHEN adresses.typevoie = 'LD' THEN 'Lieu dit'
							WHEN adresses.typevoie = 'LEVE' THEN 'Levée'
							WHEN adresses.typevoie = 'LOT' THEN 'Lotissement'
							WHEN adresses.typevoie = 'MAIL' THEN 'Mail'
							WHEN adresses.typevoie = 'MAN' THEN 'Manoir'
							WHEN adresses.typevoie = 'MAR' THEN 'Marche'
							WHEN adresses.typevoie = 'MAS' THEN 'Mas'
							WHEN adresses.typevoie = 'MET' THEN 'Métro'
							WHEN adresses.typevoie = 'MF' THEN 'Maison forestiere'
							WHEN adresses.typevoie = 'MLN' THEN 'Moulin'
							WHEN adresses.typevoie = 'MTE' THEN 'Montée'
							WHEN adresses.typevoie = 'MUS' THEN 'Musée'
							WHEN adresses.typevoie = 'NTE' THEN 'Nouvelle route'
							WHEN adresses.typevoie = 'PAE' THEN 'Petite avenue'
							WHEN adresses.typevoie = 'PAL' THEN 'Palais'
							WHEN adresses.typevoie = 'PARC' THEN 'Parc'
							WHEN adresses.typevoie = 'PAS' THEN 'Passage'
							WHEN adresses.typevoie = 'PASS' THEN 'Passe'
							WHEN adresses.typevoie = 'PAT' THEN 'Patio'
							WHEN adresses.typevoie = 'PAV' THEN 'Pavillon'
							WHEN adresses.typevoie = 'PCH' THEN 'Porche - petit chemin'
							WHEN adresses.typevoie = 'PERI' THEN 'Périphérique'
							WHEN adresses.typevoie = 'PIM' THEN 'Petite impasse'
							WHEN adresses.typevoie = 'PKG' THEN 'Parking'
							WHEN adresses.typevoie = 'PL' THEN 'Place'
							WHEN adresses.typevoie = 'PLAG' THEN 'Plage'
							WHEN adresses.typevoie = 'PLAN' THEN 'Plan'
							WHEN adresses.typevoie = 'PLCI' THEN 'Placis'
							WHEN adresses.typevoie = 'PLE' THEN 'Passerelle'
							WHEN adresses.typevoie = 'PLN' THEN 'Plaine'
							WHEN adresses.typevoie = 'PLT' THEN 'Plateau(x)'
							WHEN adresses.typevoie = 'PN' THEN 'Passage à niveau'
							WHEN adresses.typevoie = 'PNT' THEN 'Pointe'
							WHEN adresses.typevoie = 'PONT' THEN 'Pont(s)'
							WHEN adresses.typevoie = 'PORQ' THEN 'Portique'
							WHEN adresses.typevoie = 'PORT' THEN 'Port'
							WHEN adresses.typevoie = 'POT' THEN 'Poterne'
							WHEN adresses.typevoie = 'POUR' THEN 'Pourtour'
							WHEN adresses.typevoie = 'PRE' THEN 'Pré'
							WHEN adresses.typevoie = 'PROM' THEN 'Promenade'
							WHEN adresses.typevoie = 'PRQ' THEN 'Presqu''île'
							WHEN adresses.typevoie = 'PRT' THEN 'Petite route'
							WHEN adresses.typevoie = 'PRV' THEN 'Parvis'
							WHEN adresses.typevoie = 'PSTY' THEN 'Peristyle'
							WHEN adresses.typevoie = 'PTA' THEN 'Petite allée'
							WHEN adresses.typevoie = 'PTE' THEN 'Porte'
							WHEN adresses.typevoie = 'PTR' THEN 'Petite rue'
							WHEN adresses.typevoie = 'QU' THEN 'Quai'
							WHEN adresses.typevoie = 'QUA' THEN 'Quartier'
							WHEN adresses.typevoie = 'R' THEN 'Rue'
							WHEN adresses.typevoie = 'RAC' THEN 'Raccourci'
							WHEN adresses.typevoie = 'RAID' THEN 'Raidillon'
							WHEN adresses.typevoie = 'REM' THEN 'Rempart'
							WHEN adresses.typevoie = 'RES' THEN 'Résidence'
							WHEN adresses.typevoie = 'RLE' THEN 'Ruelle'
							WHEN adresses.typevoie = 'ROC' THEN 'Rocade'
							WHEN adresses.typevoie = 'ROQT' THEN 'Roquet'
							WHEN adresses.typevoie = 'RPE' THEN 'Rampe'
							WHEN adresses.typevoie = 'RPT' THEN 'Rond point'
							WHEN adresses.typevoie = 'RTD' THEN 'Rotonde'
							WHEN adresses.typevoie = 'RTE' THEN 'Route'
							WHEN adresses.typevoie = 'SEN' THEN 'Sentier'
							WHEN adresses.typevoie = 'SQ' THEN 'Square'
							WHEN adresses.typevoie = 'STA' THEN 'Station'
							WHEN adresses.typevoie = 'STDE' THEN 'Stade'
							WHEN adresses.typevoie = 'TOUR' THEN 'Tour'
							WHEN adresses.typevoie = 'TPL' THEN 'Terre plein'
							WHEN adresses.typevoie = 'TRA' THEN 'Traverse'
							WHEN adresses.typevoie = 'TRN' THEN 'Terrain'
							WHEN adresses.typevoie = 'TRT' THEN 'Tertre(s)'
							WHEN adresses.typevoie = 'TSSE' THEN 'Terrasse(s)'
							WHEN adresses.typevoie = 'VAL' THEN 'Val(lée)(lon)'
							WHEN adresses.typevoie = 'VCHE' THEN 'Vieux chemin'
							WHEN adresses.typevoie = 'VEN' THEN 'Venelle'
							WHEN adresses.typevoie = 'VGE' THEN 'Village'
							WHEN adresses.typevoie = 'VIA' THEN 'Via'
							WHEN adresses.typevoie = 'VLA' THEN 'Villa'
							WHEN adresses.typevoie = 'VOI' THEN 'Voie'
							WHEN adresses.typevoie = 'VTE' THEN 'Vieille route'
							WHEN adresses.typevoie = 'ZA' THEN 'Zone artisanale'
							WHEN adresses.typevoie = 'ZAC' THEN 'Zone d''aménagement concerte'
							WHEN adresses.typevoie = 'ZAD' THEN 'Zone d''aménagement différé'
							WHEN adresses.typevoie = 'ZI' THEN 'Zone industrielle'
							WHEN adresses.typevoie = 'ZONE' THEN 'Zone'
							WHEN adresses.typevoie = 'ZUP' THEN 'Zone à urbaniser en priorité'
							ELSE COALESCE( adresses.typevoie, '' )
						END
					) || ' ' ||
					COALESCE( adresses.nomvoie, '' ) || E'\n' ||
					COALESCE( adresses.compladr, '' ) || ' ' ||
					COALESCE( adresses.complideadr, '' )
				)
			)
		) AS adresse,
		( adresses.codepos ) AS codepos,
		( adresses.locaadr ) AS locaadr,
		( foyers.sitfam ) AS sitfam,
		( dossiers.numdemrsa ) AS numdemrsa,
		( prestations.rolepers ) AS rolepers,
		(
			CASE
				WHEN decision_ci = 'A' THEN '99rejete'
				WHEN decision_ci = 'E' THEN (
					CASE
						WHEN EXISTS(
							SELECT *
								FROM dossierseps
									INNER JOIN contratscomplexeseps93 ON ( contratscomplexeseps93.dossierep_id = dossierseps.id )
								WHERE
									contratscomplexeseps93.contratinsertion_id = contratsinsertion.id
						)
						THEN '07attavisep'
						ELSE '01signe'
					END
				)
				WHEN decision_ci = 'N' THEN '99rejete'
				WHEN decision_ci = 'R' THEN '99rejete'
				WHEN decision_ci = 'V' THEN '99valide'
				ELSE NULL
			END
		) AS positioncer,
		( contratsinsertion.forme_ci ) AS formeci,
		( contratsinsertion.date_saisi_ci ) AS datesignature,
		( CASE WHEN emp_trouv = true THEN 'O' WHEN emp_trouv = false THEN 'N' ELSE NULL END ) AS isemploitrouv,
		(
			CASE
				WHEN sect_acti_emp = 'A' THEN ( SELECT id FROM secteursactis WHERE name = 'Agriculture, sylviculture et pêche' )
				WHEN sect_acti_emp = 'B' THEN ( SELECT id FROM secteursactis WHERE name = 'Industries extractives' )
				WHEN sect_acti_emp = 'C' THEN ( SELECT id FROM secteursactis WHERE name = 'Industrie manufacturière' )
				WHEN sect_acti_emp = 'D' THEN ( SELECT id FROM secteursactis WHERE name = 'Production et distribution d''électricité, de gaz, de vapeur et d''air conditionné' )
				WHEN sect_acti_emp = 'E' THEN ( SELECT id FROM secteursactis WHERE name = 'Production et distribution d''eau ; assainissement, gestion des déchets et dépollution' )
				WHEN sect_acti_emp = 'F' THEN ( SELECT id FROM secteursactis WHERE name = 'Construction' )
				WHEN sect_acti_emp = 'G' THEN ( SELECT id FROM secteursactis WHERE name = 'Commerce ; réparation d''automobiles et de motocycles' )
				WHEN sect_acti_emp = 'H' THEN ( SELECT id FROM secteursactis WHERE name = 'Transports et entreposage' )
				WHEN sect_acti_emp = 'I' THEN ( SELECT id FROM secteursactis WHERE name = 'Hébergement et restauration' )
				WHEN sect_acti_emp = 'J' THEN ( SELECT id FROM secteursactis WHERE name = 'Information et communication' )
				WHEN sect_acti_emp = 'K' THEN ( SELECT id FROM secteursactis WHERE name = 'Activités financières et d''assurance' )
				WHEN sect_acti_emp = 'L' THEN ( SELECT id FROM secteursactis WHERE name = 'Activités immobilières' )
				WHEN sect_acti_emp = 'M' THEN ( SELECT id FROM secteursactis WHERE name = 'Activités spécialisées, scientifiques et techniques' )
				WHEN sect_acti_emp = 'N' THEN ( SELECT id FROM secteursactis WHERE name = 'Activités de services administratifs et de soutien' )
				WHEN sect_acti_emp = 'O' THEN ( SELECT id FROM secteursactis WHERE name = 'Administration publique' )
				WHEN sect_acti_emp = 'P' THEN ( SELECT id FROM secteursactis WHERE name = 'Enseignement' )
				WHEN sect_acti_emp = 'Q' THEN ( SELECT id FROM secteursactis WHERE name = 'Santé humaine et action sociale' )
				WHEN sect_acti_emp = 'R' THEN ( SELECT id FROM secteursactis WHERE name = 'Arts, spectacles et activités récréatives' )
				WHEN sect_acti_emp = 'S' THEN ( SELECT id FROM secteursactis WHERE name = 'Autres activités de services' )
				WHEN sect_acti_emp = 'T' THEN ( SELECT id FROM secteursactis WHERE name = 'Activités des ménages en tant qu''employeurs; activités indifférenciées des ménages en tant que producteurs de biens et services pour usage propre' )
				WHEN sect_acti_emp = 'U' THEN ( SELECT id FROM secteursactis WHERE name = 'Activités extra-territoriales' )
				ELSE NULL
			END
		) AS secteuracti_id,
		(
			CASE
				WHEN emp_occupe = '10' THEN ( SELECT id FROM metiersexerces WHERE name = 'Agriculteurs (salariés de leur exploitation)' )
				WHEN emp_occupe = '21' THEN ( SELECT id FROM metiersexerces WHERE name = 'Artisans (salariés de leur entreprise)' )
				WHEN emp_occupe = '22' THEN ( SELECT id FROM metiersexerces WHERE name = 'Commerçants et assimilés (salariés de leur entreprise)' )
				WHEN emp_occupe = '23' THEN ( SELECT id FROM metiersexerces WHERE name = 'Chefs d''entreprise de 10 salariés ou plus (salariés de leur entreprise)' )
				WHEN emp_occupe = '31' THEN ( SELECT id FROM metiersexerces WHERE name = 'Professions libérales (exercées sous statut de salarié)' )
				WHEN emp_occupe = '33' THEN ( SELECT id FROM metiersexerces WHERE name = 'Cadres de la fonction publique' )
				WHEN emp_occupe = '34' THEN ( SELECT id FROM metiersexerces WHERE name = 'Professeurs, professions scientifiques' )
				WHEN emp_occupe = '35' THEN ( SELECT id FROM metiersexerces WHERE name = 'Professions de l''information, des arts et des spectacles' )
				WHEN emp_occupe = '37' THEN ( SELECT id FROM metiersexerces WHERE name = 'Cadres administratifs et commerciaux d''entreprises' )
				WHEN emp_occupe = '38' THEN ( SELECT id FROM metiersexerces WHERE name = 'Ingénieurs et cadres techniques d''entreprises' )
				WHEN emp_occupe = '42' THEN ( SELECT id FROM metiersexerces WHERE name = 'Professeurs des écoles, instituteurs et professions assimilées' )
				WHEN emp_occupe = '43' THEN ( SELECT id FROM metiersexerces WHERE name = 'Professions intermédiaires de la santé et du travail social' )
				WHEN emp_occupe = '44' THEN ( SELECT id FROM metiersexerces WHERE name = 'Clergé, religieux' )
				WHEN emp_occupe = '45' THEN ( SELECT id FROM metiersexerces WHERE name = 'Professions intermédiaires administratives de la fonction publique' )
				WHEN emp_occupe = '46' THEN ( SELECT id FROM metiersexerces WHERE name = 'Professions intermédiaires administratives et commerciales des entreprises' )
				WHEN emp_occupe = '47' THEN ( SELECT id FROM metiersexerces WHERE name = 'Techniciens (sauf techniciens tertiaires)' )
				WHEN emp_occupe = '48' THEN ( SELECT id FROM metiersexerces WHERE name = 'Contremaîtres, agents de maîtrise (maîtrise administrative exclue)' )
				WHEN emp_occupe = '52' THEN ( SELECT id FROM metiersexerces WHERE name = 'Employés civils et agents de service de la fonction publique' )
				WHEN emp_occupe = '53' THEN ( SELECT id FROM metiersexerces WHERE name = 'Agents de surveillance' )
				WHEN emp_occupe = '54' THEN ( SELECT id FROM metiersexerces WHERE name = 'Employés administratifs d''entreprise' )
				WHEN emp_occupe = '55' THEN ( SELECT id FROM metiersexerces WHERE name = 'Employés de commerce' )
				WHEN emp_occupe = '56' THEN ( SELECT id FROM metiersexerces WHERE name = 'Personnels des services directs aux particuliers' )
				WHEN emp_occupe = '62' THEN ( SELECT id FROM metiersexerces WHERE name = 'Ouvriers qualifiés de type industriel' )
				WHEN emp_occupe = '63' THEN ( SELECT id FROM metiersexerces WHERE name = 'Ouvriers qualifiés de type artisanal' )
				WHEN emp_occupe = '64' THEN ( SELECT id FROM metiersexerces WHERE name = 'Chauffeurs' )
				WHEN emp_occupe = '65' THEN ( SELECT id FROM metiersexerces WHERE name = 'Ouvriers qualifiés de la manutention, du magasinage et du transport' )
				WHEN emp_occupe = '67' THEN ( SELECT id FROM metiersexerces WHERE name = 'Ouvriers non qualifiés de type industriel' )
				WHEN emp_occupe = '68' THEN ( SELECT id FROM metiersexerces WHERE name = 'Ouvriers non qualifiés de type artisanal' )
				WHEN emp_occupe = '69' THEN ( SELECT id FROM metiersexerces WHERE name = 'Ouvriers agricoles et assimilés' )
				ELSE NULL
			END
		) AS metierexerce_id,
		(
			CASE
				WHEN duree_hebdo_emp = 'DHT1' THEN 34
				WHEN duree_hebdo_emp = 'DHT2' THEN 35
				WHEN duree_hebdo_emp = 'DHT3' THEN 39
				ELSE NULL
			END
		) AS dureehebdo,
		(
			CASE
				WHEN nat_cont_trav = 'TCT1' THEN ( SELECT id FROM naturescontrats WHERE name = 'Travailleur indépendant' )
				WHEN nat_cont_trav = 'TCT2' THEN ( SELECT id FROM naturescontrats WHERE name = 'CDI' )
				WHEN nat_cont_trav = 'TCT3' THEN ( SELECT id FROM naturescontrats WHERE name = 'CDD' )
				WHEN nat_cont_trav = 'TCT4' THEN ( SELECT id FROM naturescontrats WHERE name = 'Contrat de travail temporaire (Intérim)' )
				WHEN nat_cont_trav = 'TCT5' THEN ( SELECT id FROM naturescontrats WHERE name = 'Contrat de professionnalisation' )
				WHEN nat_cont_trav = 'TCT6' THEN ( SELECT id FROM naturescontrats WHERE name = 'Contrat d''apprentissage' )
				WHEN nat_cont_trav = 'TCT7' THEN ( SELECT id FROM naturescontrats WHERE name = 'Contrat Initiative Emploi (CIE)' )
				WHEN nat_cont_trav = 'TCT8' THEN ( SELECT id FROM naturescontrats WHERE name = 'Contrat d''Accompagnement dans l''Emploi (CAE)' )
				WHEN nat_cont_trav = 'TCT9' THEN ( SELECT id FROM naturescontrats WHERE name = 'Chèque Emploi Service Universel (CESU)' )
				ELSE NULL
			END
		) AS naturecontrat_id,
		duree_cdd AS dureecdd,
		TRIM( BOTH ' ' FROM nature_projet ) AS prevu,
		( contratsinsertion.observ_ci ) AS observpro,
		( SELECT structuresreferentes.lib_struc FROM structuresreferentes WHERE structuresreferentes.id = contratsinsertion.structurereferente_id ) AS structureutilisateur,
		( SELECT ( users.nom || ' ' || users.prenom ) FROM users WHERE username = 'hpont' ) AS nomutilisateur
	FROM contratsinsertion
		INNER JOIN personnes ON ( contratsinsertion.personne_id = personnes.id )
		LEFT OUTER JOIN prestations ON ( prestations.personne_id = personnes.id AND prestations.natprest = 'RSA' )
		INNER JOIN foyers ON ( personnes.foyer_id = foyers.id )
		INNER JOIN dossiers ON ( foyers.dossier_id = dossiers.id )
		LEFT OUTER JOIN adressesfoyers ON ( adressesfoyers.foyer_id = foyers.id AND adressesfoyers.rgadr = '01' )
		LEFT OUTER JOIN adresses ON ( adressesfoyers.adresse_id = adresses.id )
	WHERE
		contratsinsertion.id NOT IN (
			SELECT cers93.contratinsertion_id
				FROM cers93
				WHERE cers93.contratinsertion_id = contratsinsertion.id
		)
		AND (
			prestations.id IS NULL
			OR (
				prestations.id = (
					SELECT p.id
						FROM prestations AS p
						WHERE
							p.personne_id = personnes.id
							AND p.natprest = 'RSA'
						ORDER BY p.id DESC
						LIMIT 1
				)
			)
		)
		AND (
			adressesfoyers.id IS NULL
			OR (
				adressesfoyers.id = (
					SELECT a.id
						FROM adressesfoyers AS a
						WHERE
							a.foyer_id = foyers.id
							AND a.rgadr = '01'
						ORDER BY a.id DESC
						LIMIT 1
				)
			)
		);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************