/*
* Mise en place de vérifications pour les erreurs sur les tables adresses_foyers et adresses
*/

BEGIN;

-- Prévention: pour s'assurer que le rang des adresses soit bien une valeur parmi '01', '02' ou '03')
ALTER TABLE adresses_foyers ADD CONSTRAINT adresses_foyers_rgadr_correct CHECK ( rgadr IN ( '01', '02', '03' ) );

-- Prévention: pour s'assurer que pour un  foyer donné, celui-ci ne possède qu'un seul enregistrement pour un rang donné
ALTER TABLE adresses_foyers ADD CONSTRAINT adresses_foyers_unique_rgadr UNIQUE (foyer_id, rgadr);

-- Prévention: pour s'assurer qu'une adresse n'est référencée que par un adresses_foyers
ALTER TABLE adresses_foyers ADD CONSTRAINT adresses_foyers_unique_adresse_id UNIQUE (adresse_id);

COMMIT;