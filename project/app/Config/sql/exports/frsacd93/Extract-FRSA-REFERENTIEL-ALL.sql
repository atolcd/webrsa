SET client_encoding = 'UTF8';
-- *****************************************************************************
BEGIN;
-- *****************************************************************************

copy (
	SELECT jsonb_object_agg ( cle, value)
	FROM  (
		SELECT 'motif_entretien' as cle,
			jsonb_agg(jsonb_build_object( 'id', CAST (id AS text ), 'label', name, 'autre',  CAST (autre AS boolean ))) as value
			FROM motifscontactsfps93
		UNION
		SELECT 'beneficiaire_rejete_motif' as cle,
			jsonb_agg(jsonb_build_object( 'id', CAST (id AS text ), 'label', name, 'autre',  CAST (autre AS boolean ))) as value
			FROM motifsnonretenuesfps93
		UNION
		SELECT 'beneficiaire_pas_integre_action_raison' as cle,
			jsonb_agg(jsonb_build_object( 'id', CAST (id AS text ), 'label', name, 'autre',  CAST (autre AS boolean ) )) as value
			FROM motifsnonintegrationsfps93
		UNION
		SELECT 'beneficiaire_abandon_action_raison' as cle,
			jsonb_agg(jsonb_build_object('id', CAST (id AS text ), 'label', name, 'autre',  CAST (autre AS boolean ))) as value
			FROM motifsnonactionachevesfps93
		UNION
		SELECT 'resultat_action' as cle,
			jsonb_agg(jsonb_build_object('id', CAST (id AS text ), 'label', name, 'autre',  CAST (autre AS boolean ))) as value
			FROM motifsactionachevesfps93
	)
	AS referentiel

) to '/etl/rsa/out/FRSA/referentiel/REFERENTIEL_W_yyyy_MM_dd__hh_mm.json';

-- *****************************************************************************
COMMIT;
-- *****************************************************************************
