SET client_encoding = 'UTF8';
-- *****************************************************************************
BEGIN;
-- ******************************************************************************

copy (
SELECT current_date AS "e_date_extraction",
fichesprescriptions93.id AS "identifiant_webrsa",
fichesprescriptions93.frsa_id AS "identifiant_frsa",
fichesprescriptions93.posorigine AS "pos_origine",
fichesprescriptions93.referent_id AS "ref_identifiant",
referents.structurereferente_id AS "structref_id",
fichesprescriptions93.objet AS "motif_pos",
fichesprescriptions93.personne_id AS "beneficiaire_identifiant",
instantanesdonneesfps93.benef_matricule AS "beneficiaire_numero_caf",
CASE
	when instantanesdonneesfps93.benef_qual='MR'  then 'Homme'
	when instantanesdonneesfps93.benef_qual='MME'  then 'Femme'
	ELSE null
END AS "beneficiaire_civilite",
instantanesdonneesfps93.benef_nom AS "beneficiaire_nom",
instantanesdonneesfps93.benef_prenom AS "beneficiaire_prenom",
instantanesdonneesfps93.benef_dtnai AS "beneficiaire_naissance",
( COALESCE ( instantanesdonneesfps93.benef_numvoie, '') || ' '
|| COALESCE (instantanesdonneesfps93.benef_libtypevoie,'') || ' '
|| COALESCE (instantanesdonneesfps93.benef_nomvoie,'') || ' '
|| COALESCE (instantanesdonneesfps93.benef_complideadr,'') || ' '
|| COALESCE (instantanesdonneesfps93.benef_compladr,'' ) )
 AS "beneficiaire_adresse",
instantanesdonneesfps93.benef_codepos AS "beneficiaire_code_postal",
instantanesdonneesfps93.benef_nomcom AS "beneficiaile_ville",
trim(regexp_replace(instantanesdonneesfps93.benef_tel_fixe,'[^(0-9)]','','g'))
	AS "beneficiaire_telephone_fixe",
trim(regexp_replace(instantanesdonneesfps93.benef_tel_port,'[^(0-9)]','','g'))
	AS "beneficiaire_telephone_mobile",
instantanesdonneesfps93.benef_email AS "beneficiaire_email",
CASE
	WHEN
		instantanesdonneesfps93.benef_natpf_socle ='1' AND
		instantanesdonneesfps93.benef_natpf_majore ='1' AND
		instantanesdonneesfps93.benef_natpf_activite ='1'
	THEN 'socle_majore_activite'
	WHEN
		instantanesdonneesfps93.benef_natpf_socle ='1' AND
		instantanesdonneesfps93.benef_natpf_majore ='1' AND
		instantanesdonneesfps93.benef_natpf_activite ='0'
	THEN 'socle_activite'
	WHEN
		instantanesdonneesfps93.benef_natpf_socle ='1' AND
		instantanesdonneesfps93.benef_natpf_majore ='0' AND
		instantanesdonneesfps93.benef_natpf_activite ='1'
	THEN 'socle_majore'
	WHEN
		instantanesdonneesfps93.benef_natpf_socle ='1' AND
		instantanesdonneesfps93.benef_natpf_majore ='0' AND
		instantanesdonneesfps93.benef_natpf_activite ='0'
	THEN 'socle'
	WHEN 
		instantanesdonneesfps93.benef_natpf_socle ='0' AND
		instantanesdonneesfps93.benef_natpf_majore ='1' AND
		instantanesdonneesfps93.benef_natpf_activite ='1'
	THEN 'activite_majore'
	WHEN
		instantanesdonneesfps93.benef_natpf_socle ='0' AND
		instantanesdonneesfps93.benef_natpf_majore ='1' AND
		instantanesdonneesfps93.benef_natpf_activite ='0'
	THEN 'activite'
	ELSE 'NC'
END AS "beneficiaire_nature_prestation",
instantanesdonneesfps93.benef_natpf_3mois AS "beneficiaire_rsa_statut_3_mois_avant",
case instantanesdonneesfps93.benef_inscritpe
	WHEN '0' THEN 'false'
	WHEN '1' THEN 'true'
	ELSE null
END AS "beneficiaire_pole_emploi_inscrit",
instantanesdonneesfps93.benef_identifiantpe AS "beneficiaire_pole_emploi_identifiant",
instantanesdonneesfps93.benef_nivetu AS "beneficiaire_nivetu",
instantanesdonneesfps93.benef_dernier_dip AS "beneficiaire_dernier_diplome",
CASE instantanesdonneesfps93.benef_dip_ce
	WHEN '0' THEN 'false'
	WHEN '1' THEN 'true'
	ELSE null
END AS "beneficiaire_dernier_diplome_europe",
instantanesdonneesfps93.benef_positioncer AS  "cer_valide",
instantanesdonneesfps93.benef_dd_ci AS "cer_date_debut",
instantanesdonneesfps93.benef_df_ci AS "cer_date_fin",
actionsfps93.numconvention AS "action_numero_convention",
fichesprescriptions93.date_transmission AS "date_transmission",
(SELECT jsonb_agg(modstransmsfps93.name)
	FROM modstransmsfps93
	INNER JOIN fichesprescriptions93_modstransmsfps93
	ON ( modstransmsfps93.id = fichesprescriptions93_modstransmsfps93.modtransmfp93_id )
	WHERE fichesprescriptions93_modstransmsfps93.ficheprescription93_id = fichesprescriptions93.id
) AS "modalite_transmission",
/*
fichesprescriptions93. AS "reception_candidature",
fichesprescriptions93. AS "reception_candidature_refusee_raison",
*/
null AS "reception_candidature",
null AS "reception_candidature_refusee_raison",
to_char( fichesprescriptions93.rdvprestataire_date, 'YYYY-MM-DD') AS "date_entretien",
fichesprescriptions93.motifcontactfp93_id AS "motif_entretien",
fichesprescriptions93.benef_retour_presente AS "beneficiaire_present",
CASE fichesprescriptions93.personne_retenue
	WHEN '0' THEN 'false'
	WHEN '1' THEN 'true'
	ELSE null
END AS "beneficiaire_retenu",
fichesprescriptions93.motifnonretenuefp93_id AS "beneficiaire_rejete_motif",
fichesprescriptions93.personne_nonretenue_autre AS "beneficiaire_rejete_motif_autre",
CASE fichesprescriptions93.personne_a_integre
	WHEN '0' THEN 'false'
	WHEN '1' THEN 'true'
	ELSE null
END  AS "beneficiaire_integre_action",
fichesprescriptions93.motifnonintegrationfp93_id AS "beneficiaire_pas_integre_action_raison",
fichesprescriptions93.personne_nonintegre_autre AS "beneficiaire_pas_integre_action_raison_autre",
CASE fichesprescriptions93.personne_acheve
	WHEN '0' THEN 'false'
	WHEN '1' THEN 'true'
	ELSE null
END  AS "beneficiaire_termine_action",
fichesprescriptions93.motifnonactionachevefp93_id AS "beneficaire_abandon_action_raison",
fichesprescriptions93.personne_acheve_autre AS "beneficaire_abandon_action_raison_autre",
fichesprescriptions93.motifactionachevefp93_id AS "resultat_action",
fichesprescriptions93.personne_acheve_autre AS "resultat_action_autre",
fichesprescriptions93.date_bilan_mi_parcours AS "date_bilan_mi_parcours",
fichesprescriptions93.date_bilan_final AS "date_bilan_final"
FROM fichesprescriptions93
INNER JOIN instantanesdonneesfps93
	ON ( instantanesdonneesfps93.ficheprescription93_id = fichesprescriptions93.id )
INNER JOIN referents
	ON ( referents.id = fichesprescriptions93.referent_id )
INNER JOIN actionsfps93 ON actionsfps93.id = fichesprescriptions93.actionFP93_id
WHERE fichesprescriptions93.modified >= (now() - interval '1' DAY)
) to '/etl/rsa/out/FRSA/positionnement/POS_W_yyyy_MM_dd__hh_mm.csv' WITH DELIMITER AS ';' CSV HEADER;

-- *****************************************************************************
COMMIT;
-- *****************************************************************************