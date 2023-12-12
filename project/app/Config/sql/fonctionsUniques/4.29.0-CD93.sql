SET client_encoding = 'UTF8';

-- *****************************************************************************
BEGIN;
-- *****************************************************************************

--Insertion de la liste des décisions EP
INSERT INTO listedecisionssuspensionseps93
(code, libelle, nom_courrier, premier_niveau, deuxieme_niveau, actif, created, modified) 
values
('{annule}', 'Annulé (Remobilisation)', 'decision_annule.odt', true, true, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('{1maintien, 2maintien}', 'Maintien', 'decision_maintien.odt', true, true, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('{maintienreorientation}', 'Maintien et Réorientation', 'decision_maintien_reorientation.odt', true, true, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('{1reduction}', 'Réduction de 100 euros', 'decision_reduction_ppae.odt ou decision_reduction_pdv.odt', true, false, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('{2suspensionpartielle}', 'Suspension partielle', 'decision_suspensionpartielle.odt', false, true, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('{2suspensiontotale}', 'Suspension totale', 'decision_suspensiontotale.odt', false, true, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('{reporte}', 'Reporté EP ultérieure', 'decision_reporte.odt.', true, true, true, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('{1pasavis, 2pasavis}', 'Pas d''avis', 'decision_reporte.odt', false, false, false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('{1delai}', 'Délai supplémentaire de 1 mois', 'decision_delai.odt', false, false, false, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
-- *****************************************************************************
COMMIT;
-- *****************************************************************************
