SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

-- Ajout des nouvelles lignes dans thematiquesrdvs
INSERT INTO public.thematiquesrdvs ("name", typerdv_id, actif, acomptabiliser)
    VALUES ('Action collective : emploi(TRE, découverte des métiers, recherche d''emploi par internet, forums...', 14, 1, 1),
    ('Action collective : formation(présentation d''actions organisées par des organismes, sensibilation aux outils informatiques, ...', 14, 1, 1),
    ('Action collective : resocialisation, redynamisation, revalorisation (image de soi, ...)', 14, 1, 1),
    ('Action collective : information dispositif RSA (dispositif RSA uniquement)', 14, 1, 1),
    ('Action collective : accès aux droits (retraite, CMU, transport, ...', 14, 1, 0),
    ('Action collective : vie sociale (soutien administratif, logement, famille, mobilité, ...', 14, 1, 0),
    ('Action collective : santé (prévention, ...)', 14, 1, 0),
    ('Action collective : loisirs, culture et vacances (relais cultures du coeur, séjours vacances, ...', 14, 1, 0);

-- *****************************************************************************
COMMIT;
-- *****************************************************************************