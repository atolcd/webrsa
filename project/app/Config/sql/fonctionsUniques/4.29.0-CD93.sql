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

-- Mise à niveau des rapports talends des modes de contact
do $$
declare rapport record;
begin FOR rapport in
	(select created, fichier from administration.rapportstalendmodescontacts group by created, fichier having count(*) filter (where motif ilike 'COUNT_OK') = 0)
	loop
		insert into administration.rapportstalendmodescontacts
		(created, fichier, motif) values (rapport.created, rapport.fichier, 'COUNT_OK');
		insert into administration.rapportstalendmodescontacts
		(created, fichier, motif) values (rapport.created, rapport.fichier, 'COUNT_TOTAL');
	end loop;
end;
$$