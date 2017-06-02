BEGIN;

INSERT INTO eps ( name ) VALUES
	( 'CLI 1 Equipe 1.2' );

INSERT INTO fonctionspartseps ( name ) VALUES
	( 'Chef de projet' );

INSERT INTO partseps ( qual, nom, prenom, tel, email, ep_id, fonctionpartep_id, rolepartep ) VALUES
	( 'MR', 'Hamzaoui', 'Michel', NULL, NULL, '1', '1', 'titulaire' );

INSERT INTO eps_zonesgeographiques ( ep_id, zonegeographique_id ) VALUES
	( 1, 35 ),
	( 1, 43 ),
	( 1, 17 );

INSERT INTO sceanceseps ( ep_id, structurereferente_id, datesceance, finaliseeep, finaliseecg, reorientation ) VALUES
	( 1, 24, '2010-04-06 10:00:00', '0', '0', 'decisionep' );

INSERT INTO partseps_sceanceseps ( partep_id, sceanceep_id, reponseinvitation, presence, partep_remplacant_id ) VALUES
	( 1, 1, 'confirme', NULL, NULL );

INSERT INTO motifsdemsreorients ( name ) VALUES
	( 'Déménagement' );

-- INSERT INTO demandesreorient ( personne_id, orientstruct_id, motifdemreorient_id, urgent, passageep, vx_typeorient_id, vx_structurereferente_id, vx_referent_id, nv_typeorient_id, nv_structurereferente_id, nv_referent_id, accordconcertation, dateconcertation, dateecheance, motivation, sceanceep_id, nv_orientstruct_id, vx_demandereorient_id, created, modified) VALUES
-- 	( 174448, 78890, 1, '0', '0', '3', '79', NULL, '2', '103', NULL, 'attente', NULL, NULL, NULL, NULL, NULL, NULL, '2010-06-03 14:50:00', NULL);
--
-- UPDATE demandesreorient
-- 	SET accordconcertation = 'accord',
-- 		dateconcertation = '2010-06-03 16:20:00',
-- 		dateecheance = '2010-07-03 16:20:00'
-- 	WHERE id = 1;

COMMIT;
