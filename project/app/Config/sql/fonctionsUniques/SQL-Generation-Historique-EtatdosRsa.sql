SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

INSERT INTO historiquesdroits (personne_id,toppersdrodevorsa, etatdosrsa, created, modified)
	SELECT
		personne_id, calculsdroitsrsa.toppersdrodevorsa, situationsdossiersrsa.etatdosrsa,
		CASE WHEN evenements.dtliq IS NULL THEN dossiers.dtdemrsa else evenements.dtliq END AS created,
		NOW()
	FROM personnes
	INNER JOIN calculsdroitsrsa ON calculsdroitsrsa.personne_id = personnes.id
	INNER JOIN foyers ON foyers.id = personnes.foyer_id
	INNER JOIN dossiers ON dossiers.id = foyers.dossier_id
	INNER JOIN situationsdossiersrsa ON situationsdossiersrsa.dossier_id = dossiers.id
	LEFT JOIN evenements ON evenements.foyer_id = foyers.id AND evenements.id IN ( SELECT id FROM evenements WHERE personnes.foyer_id=evenements.foyer_id ORDER BY evenements.dtliq DESC LIMIT 1 )
	WHERE NOT EXISTS(SELECT id FROM historiquesdroits WHERE historiquesdroits.personne_id = personnes.id)
;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
