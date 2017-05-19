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
-- 2. Les utilisateurs du CCAS de Nevers ne doivent avoir accès qu'aux dossiers :
--     - des couples sans enfants
--     - des bénéficiaires seuls sans enfants
--     - si le service instructeur est renseigné, on filtre sur le CCAS de Nevers, sinon rien de plus

UPDATE servicesinstructeurs
    SET sqrecherche = '(
    (
        -- Couples sans enfants
        (
            (
                SELECT COUNT(personnes.id)
                    FROM personnes
                        INNER JOIN prestations ON (
                            personnes.id = prestations.personne_id
                            AND prestations.natprest = ''RSA''
                            AND prestations.rolepers IN ( ''DEM'', ''CJT'' )
                        )
                    WHERE personnes.foyer_id = Foyer.id
            ) = 2
            AND
            (
                SELECT COUNT(personnes.id)
                    FROM personnes
                        INNER JOIN prestations ON (
                            personnes.id = prestations.personne_id
                            AND prestations.natprest = ''RSA''
                            AND prestations.rolepers NOT IN ( ''DEM'', ''CJT'' )
                        )
                    WHERE personnes.foyer_id = Foyer.id
            ) = 0
        )
        OR
        -- Bénéficiaires seuls sans enfants
        (
            (
                SELECT COUNT(personnes.id)
                    FROM personnes
                        INNER JOIN prestations ON (
                            personnes.id = prestations.personne_id
                            AND prestations.natprest = ''RSA''
                            AND prestations.rolepers IN ( ''DEM'' )
                        )
                    WHERE personnes.foyer_id = Foyer.id
            ) = 1
            AND
            (
                SELECT COUNT(personnes.id)
                    FROM personnes
                        INNER JOIN prestations ON (
                            personnes.id = prestations.personne_id
                            AND prestations.natprest = ''RSA''
                        )
                    WHERE personnes.foyer_id = Foyer.id
            ) = 1
        )
    )
    AND
    (
        -- Aucune entrée dans suivisinstruction
        (
            SELECT COUNT(dossier_id)
                FROM suivisinstruction
                WHERE suivisinstruction.dossier_id = Dossier.id
        ) = 0
        OR
        -- Cette entrée correspond au CCAS de Nevers
        (
            SELECT COUNT(dossier_id)
                FROM suivisinstruction
                WHERE suivisinstruction.dossier_id = Dossier.id
                    AND suivisinstruction.numdepins = ''058''
                    AND suivisinstruction.typeserins = ''C''
                    AND suivisinstruction.numcomins = ''194''
                    AND suivisinstruction.numagrins = ''2''
        ) = 1
    )
)'
    WHERE servicesinstructeurs.id = 13;
-- *****************************************************************************
COMMIT;
-- *****************************************************************************