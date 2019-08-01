SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

UPDATE questionnairesd2pdvs93 SET
	sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à une activité d''indépendant, création d''entreprise'
	)
WHERE questionnairesd2pdvs93.sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Création d''activité'
	)
	AND questionnairesd2pdvs93.created >= '01-01-2019'
;

UPDATE questionnairesd2pdvs93 SET
	sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à une activité d''indépendant, création d''entreprise'
	)
WHERE questionnairesd2pdvs93.sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Maintien ou développement de l''emploi ou de l''activité'
	)
	AND questionnairesd2pdvs93.created >= '01-01-2019'
;

UPDATE questionnairesd2pdvs93 SET
	sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un emploi temporaire (CDD de - de 6 mois, intérim)'
	)
WHERE questionnairesd2pdvs93.sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un emploi temporaire ou saisonnier (< ou = à 6 mois)'
	)
	AND questionnairesd2pdvs93.created >= '01-01-2019'
;

UPDATE questionnairesd2pdvs93 SET
	sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un emploi aidé'
	)
WHERE questionnairesd2pdvs93.sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un contrat aidé'
	)
	AND questionnairesd2pdvs93.created >= '01-01-2019'
;

UPDATE questionnairesd2pdvs93 SET
	sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un emploi salarié SIAE'
	)
WHERE questionnairesd2pdvs93.sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un emploi salarié SIAE (hors contrat aidé)'
	)
	AND questionnairesd2pdvs93.created >= '01-01-2019'
;


-- D'une seul action Accès à un emploi durable (plus de 6 mois) vers deux actions :
--    *Accès à un emploi CDD de plus de 6 mois
--    *Accès à un emploi CDI

-- Il manque la méthode permetant de faire la difference entre les cibles sortieaccompagnementd2pdv93_id des questionnairesd2pdvs93
-- Actuellement dans la base de 2019 il n'y aucun questionnairesd2pdvs93 avec un sortieaccompagnementd2pdv93_id 'Accès à un emploi durable (plus de 6 mois)' affecté durant l'année 2019
-- L'existance d'une donnée peut etre testé grace a cette fonction :
/*
SELECT questionnairesd2pdvs93.*
FROM questionnairesd2pdvs93
WHERE questionnairesd2pdvs93.sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un emploi durable (plus de 6 mois)'
	)
	AND questionnairesd2pdvs93.created >= '01-01-2019'
;
*/
-- Dans le cas ou il y a une donnée crée en 2019 en prod il faut ajouter aux deux fonction suivantes une méthode de différentiation de la sortieaccompagnementd2pdv93 cible.
/*
UPDATE questionnairesd2pdvs93 SET
	sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un emploi CDD de + de 6 mois')
WHERE questionnairesd2pdvs93.sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un emploi durable (plus de 6 mois)'
	)
	AND questionnairesd2pdvs93.created >= '01-01-2019'
;

UPDATE questionnairesd2pdvs93 SET
	sortieaccompagnementd2pdv93_id = (
		SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
		WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un emploi CDI'
	)
WHERE questionnairesd2pdvs93.sortieaccompagnementd2pdv93_id = (
	SELECT sortiesaccompagnementsd2pdvs93.id FROM sortiesaccompagnementsd2pdvs93
	WHERE sortiesaccompagnementsd2pdvs93.name LIKE 'Accès à un emploi durable (plus de 6 mois)'
	)
	AND questionnairesd2pdvs93.created >= '01-01-2019'
;
*/

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
