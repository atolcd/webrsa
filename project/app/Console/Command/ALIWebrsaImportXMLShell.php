<?php
	/**
	 * Fichier source de la classe ALIWebrsaImportXML.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     *
     * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake ALIWebrsaImportXML -app app [Folderpath]
	 *
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses('CakeEmail', 'Network/Email');

	/**
	 * La classe ALIWebrsaImportXML ...
	 *
	 * @package app.Console.Command
	 */
	class ALIWebrsaImportXMLShell extends XShell
	{
		public $uses = [
            'CorrespondanceReferentiel',
            'SujetReferentiel',
            'Structurereferente',
            'StructurereferenteZonegeographique',
			'Personne',
			'PersonneReferent',
			'RapportEchangeALI',
			'Rendezvous',
			'Dsp',
			'DspRev',
			'Cer93',
			'Contratinsertion',
			'WebrsaOrientstruct',
			'Orientstruct',
			'Dossierep',
			'Reorientationep93',
			'Questionnaired1pdv93',
			'Questionnaired2pdv93',
			'Questionnaireb7pdv93',
			'User',
			'Histochoixcer93',
			'Situationallocataire'
        ];


		/**
		 *
		 */
		public function main() {
            // Vérification du path passé en argument
            if (!isset($this->args[0])) {
                $this->out("<error>Il n'y a pas de chemin de dossier passé en argument</error>");
                exit();
            } else if (substr($this->args[0], -1) !== '/') {
                $path = $this->args[0] . '/';
            } else {
                $path = $this->args[0];
            }

            // Récupération des fichiers dans le dossier
			$files = array_diff(scandir($path), array('..', '.', 'XSD'));
			$alertes = [];
			$liste_ali = [];

			if(empty($files)){
				$alertes = 'dossier_vide';
			}

			foreach($files as $file){
				$now_datetime = new DateTimeImmutable();
				$now = $now_datetime->format('Y-m-d_H:i:s');

				//Rapports
				$rapport = [];

				// Validation du format
				$dom = new DOMDocument;
				$dom->load($path.$file);
				$schema_valide = $dom->schemaValidate(Configure::read('EchangeALI.CheminValidation').'/ALI-WebRSA.xsd');
				if(!$schema_valide){
					//alerte
					$alertes[$file]['code'] = 'validation_schema';
				} else {

					$xml=simplexml_load_file($path.$file);


					//On traite l'entête
					$id_ali = intval($xml->entete->id_structure_ali->__toString());
					$date_generation = $xml->entete->date_generation->__toString();
					//Si le fichier de la même date a déjà été intégré, on enregistre une erreur
					$deja_traite = $this->RapportEchangeALI->dejaImporte($id_ali, $date_generation);
					//fichier de stock/différentiel
					$stock = intval($xml->entete->fichier_stock->__toString());

					//On récupère l'id de l'utilisateur en fonction de l'ali
					$user = $this->User->getUserByALI($id_ali);

					$liste_ali[] = $id_ali;

					if(empty($user)){
						$alertes[$file]['code'] = 'utilisateur_inconnu';
					} else if ($deja_traite) {
						$alertes[$file]['code'] = 'deja_traite';
					} else {
						$user_id = $user['id'];

						//-------------------------------------------------------

						$dossiers_erreurs = 0;
						$nom_fichier_erreurs = Configure::read('EchangeALI.CheminRapports').'/erreurs_'. substr($file, 0, -3) . 'csv';
						$alias_fichier_erreurs = 'erreurs_'. substr($file, 0, -3) . 'csv';

						//On passe aux dossiers
						foreach($xml->dossiers->dossier as $dossier){
							$personne_id = intval($dossier->id_personne->__toString());

							$personne = $this->Personne->findById($personne_id);
							if(empty($personne)){
								$alertes[$file]['personne_inconnue'][] = $personne_id;
								$rapport = $this->AddErreur($rapport, 'global', 'personne_inconnue', null, $personne_id);

							} else {

								$bool_referentparcours = null;
								$bool_rdv = null;
								$bool_dsp = null;
								$bool_cers = null;
								$bool_orient = null;
								$bool_d1 = null;
								$bool_d2 = null;
								$bool_b7 = null;

								/*---------------------------------------------
								------------référent de parcours---------------
								----------------------------------------------*/
								if(isset($dossier->liste_referents_parcours)){
									foreach($dossier->liste_referents_parcours->referent_parcours as $referent_parcours){

										//On récupère l'id webrsa du référent
										$ref_webrsa = $this->CorrespondanceReferentiel->find(
											'first',
											[
												'fields' => ['id_dans_table', 'referents_date_cloture', 'referents_structurereferente_id'],
												'conditions' => [
													'SujetReferentiel.code' => 'referents',
													'CorrespondanceReferentiel.id' => intval($referent_parcours->id_webrsa->__toString())
												],
												'recursive' => 1
											]
										);

										if(!empty($ref_webrsa)){
											$ref_webrsa = $ref_webrsa['CorrespondanceReferentiel'];

											$cloture = false;
											if (!is_null($ref_webrsa['referents_date_cloture'])) {
												//On clôture immédiatement sur la date de clôture
												$cloture = $ref_webrsa['referents_date_cloture'];
											}

											if(
												!is_null($ref_webrsa['referents_date_cloture'])
													&& $ref_webrsa['referents_date_cloture'] < $referent_parcours->date_designation->__toString()
											){
												//On ajoute l'erreur et on sort du référent de parcours
												//référent clôturé
												$bool_referentparcours = false;
												$rapport = $this->AddErreur($rapport, 'referent', 'referent_cloture', $personne_id);

											} else {
												//On enregistre
												$bool_referentparcours = $this->PersonneReferent->changeReferent(
													$personne_id,
													$ref_webrsa['id_dans_table'],
													$referent_parcours->date_designation->__toString(),
													$ref_webrsa['referents_structurereferente_id'],
													$cloture
												);
											}

										} else {
											//erreur référent inconnu
											$bool_referentparcours = false;
											$rapport = $this->AddErreur($rapport, 'referent', 'referent_inconnu', $personne_id);
										}
									}

								}

								/*---------------------------------------------
								------------------rendez-vous------------------
								----------------------------------------------*/
								if(isset($dossier->liste_rendez_vous)){
									foreach($dossier->liste_rendez_vous->rendez_vous as $rdv){

										$infos_rdv = [
											'personne_id' => $personne_id,
											'daterdv' => $rdv->date_rdv->__toString(),
											'heurerdv' => $rdv->heure_rdv->__toString(),
											'id_base_ali' => $rdv->id_ali
										];

										$rdv_structurereferente_id = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel(
											'structuresreferentes',
												intval($rdv->id_structurereferente->__toString())
										);


										if(empty($rdv_structurereferente_id)){
											//erreur structure inconnue
											$bool_rdv = false;
											$rapport = $this->AddErreur($rapport, 'rdv', 'structure_inconnue', $personne_id);

										} else {

											$infos_rdv['structurereferente_id'] = $rdv_structurereferente_id;

											//Tous les référentiels doivent exister
											$rdv_objet_id = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel(
												'rdv_objet',
													intval(intval($rdv->id_objet_rdv->__toString()))
											);

											if(empty($rdv_objet_id)){
												//erreur référentiel inconnu
												$bool_rdv = false;
												$rapport = $this->AddErreur($rapport, 'rdv', 'rdv_objet_inconnu', $personne_id);

											} else {
												$infos_rdv['typerdv_id'] = $rdv_objet_id;

											}


											if(!isset($rdv->id_referent) && $rdv_objet_id == Configure::read( 'Rendezvous.Typerdv.individuel_id' )){
												//erreur référent manquant
												$bool_rdv = false;
												$rapport = $this->AddErreur($rapport, 'rdv', 'referent_obligatoire', $personne_id);

											} else if (isset($rdv->id_referent)){
												$rdv_referent = $this->CorrespondanceReferentiel->find(
													'first',
													[
														'fields' => ['id_dans_table', 'referents_structurereferente_id'],
														'conditions' => [
															'SujetReferentiel.code' => 'referents',
															'CorrespondanceReferentiel.id' => intval($rdv->id_referent->__toString())
														],
														'recursive' => 1
													]
												);

												if(empty($rdv_referent)){
													//erreur référent inconnu
													$bool_rdv = false;
													$rapport = $this->AddErreur($rapport, 'rdv', 'referent_inconnu', $personne_id);
												} else {
													//Le référent et la structure doivent être cohérents
													if($rdv_referent['CorrespondanceReferentiel']['referents_structurereferente_id'] != $rdv_structurereferente_id){
														//erreur référent incohérent par rapport à la structure
														$bool_rdv = false;
														$rapport = $this->AddErreur($rapport, 'rdv', 'referent_incoherent', $personne_id);
													} else {
														$infos_rdv['referent_id'] = $rdv_referent['CorrespondanceReferentiel']['id_dans_table'] ;
													}
												}
											}

											$rdv_statut_id = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel(
												'rdv_statut',
												intval($rdv->id_statut_rdv->__toString())
											);

											if(empty($rdv_statut_id)){
												//erreur référentiel inconnu
												$bool_rdv = false;
												$rapport = $this->AddErreur($rapport, 'rdv', 'rdv_statut_inconnu', $personne_id);
											} else {
												$infos_rdv['statutrdv_id'] = $rdv_statut_id;
											}

											$thematiques = [];
											foreach($rdv->thematiques->id_thematique_rdv as $thematique) {

												$rdv_thematique = $this->CorrespondanceReferentiel->find(
													'first',
													[
														'fields' => ['id_dans_table', 'rdv_thematique_unefoisparan', 'rdv_thematique_typerdv_id'],
														'conditions' => [
															'SujetReferentiel.code' => 'rdv_thematique',
															'CorrespondanceReferentiel.id' => intval($thematique->__toString())
														],
														'recursive' => 1
													]
												);

												if(empty($rdv_thematique)){
													//erreur référentiel inconnu
													$bool_rdv = false;
													$rapport = $this->AddErreur($rapport, 'rdv', 'rdv_thematique_inconnu', $personne_id);

												} else {
													//il faut vérifier que la thématique est compatible avec le type de rdv
													if($rdv_objet_id != $rdv_thematique['CorrespondanceReferentiel']['rdv_thematique_typerdv_id']) {
														//erreur type de rdv et thématiques incompatibles
														$bool_rdv = false;
													$rapport = $this->AddErreur($rapport, 'rdv', 'rdv_type_thematique_incoherents', $personne_id);


													} else {

														//Si thématique une fois/an on vérifie qu'elle n'a pas déjà été attribuée
														if(in_array($rdv_thematique['CorrespondanceReferentiel']['id_dans_table'],  Configure::read('Rendezvous.thematiqueAnnuelleParStructurereferente'))){

															$annee_rdv = substr($rdv->date_rdv->__toString(), 0, 4);
															$statuts = Configure::read('Rendezvous.checkThematiqueAnnuelleParStructurereferente.statutrdv_id');
															$statuts = implode(",", $statuts);
															$sql = "
																select *
																from rendezvous_thematiquesrdvs rt
																join rendezvous r on rt.rendezvous_id = r.id
																where r.personne_id = $personne_id
																and extract (year from r.daterdv) = $annee_rdv
																and r.structurereferente_id = $rdv_structurereferente_id
																and rt.thematiquerdv_id = {$rdv_thematique['CorrespondanceReferentiel']['id_dans_table']}
																and r.statutrdv_id in ($statuts)
																and r.daterdv <> '{$rdv->date_rdv->__toString()}'
																and r.heurerdv <> '{$rdv->heure_rdv->__toString()}'
															";

															$rdv_annuel = $this->Personne->query($sql);

															if(!empty($rdv_annuel)){
																//erreur référentiel inconnu
																$bool_rdv = false;
																$rapport = $this->AddErreur($rapport, 'rdv', 'rdv_thematique_annuelle', $personne_id);

															}
														}

														$thematiques[] = $rdv_thematique['CorrespondanceReferentiel']['id_dans_table'];

														$infos_rdv['Thematiquerdv'][] = [
															'thematiquerdv_id' => $rdv_thematique['CorrespondanceReferentiel']['id_dans_table']
														];
													}
												}
											}

											if(sizeof($thematiques) > 1 && $rdv_objet_id != Configure::read( 'Rendezvous.Typerdv.individuel_id' )){
												//erreur une seule thématique possible pour les drv collectifs
												$bool_rdv = false;
												$rapport = $this->AddErreur($rapport, 'rdv', 'rdv_thematique_depassement', $personne_id);

											}

											if($bool_rdv !== false) {
												//On ajoute les champs facultatifs
												if(isset($rdv->objectif_rdv)){
													$infos_rdv['objetrdv'] = $rdv->objectif_rdv->__toString();
												}
												if(isset($rdv->commentaire_rdv)){
													$infos_rdv['commentairerdv'] = $rdv->commentaire_rdv->__toString();
												}
												if(isset($rdv->a_revoir_le)){
													$infos_rdv['arevoirle'] = $rdv->a_revoir_le->__toString().'-01';
												}

												//On recherche si on a déjà intégré à partir de l'id ALI
												//ou si un rdv de même jour/heure existe déjà

												$conditions_rdv = [
													'Rendezvous.structurereferente_id' => $rdv_structurereferente_id,
													'Rendezvous.daterdv' => $rdv->date_rdv->__toString(),
													'Rendezvous.heurerdv' => $rdv->heure_rdv->__toString(),
													'Rendezvous.id_base_ali' => null
												];

												if(isset($rdv_referent)){
													$conditions_rdv['Rendezvous.referent_id'] =  $rdv_referent['CorrespondanceReferentiel']['id_dans_table'];
												}

												$rdv_existant = $this->Rendezvous->find(
													'first',
													[
														'conditions' => [
															'OR' => [
																'Rendezvous.id_base_ali' => intval($rdv->id_ali->__toString()),
																'AND' => $conditions_rdv
															]
														]
													]
												);


												if(!empty($rdv_existant)){
													//On ajoute l'id pour mettre à jour le rdv existant
													$infos_rdv['id'] = $rdv_existant['Rendezvous']['id'];
												}


												//On enregistre le rdv avec les infos disponibles
												$bool_rdv = $this->Rendezvous->saveAssociated($infos_rdv, ['validate' => false]);
												$this->Rendezvous->clear();


												//Ajouter référent de parcours si besoin
												if( $bool_rdv && !empty( $rdv_referent['CorrespondanceReferentiel']['id_dans_table'] ) ) {
													$personneReferentActuel = $this->PersonneReferent->referentParcoursActuel($personne_id);

													if( empty( $personneReferentActuel ) ) {
														$this->PersonneReferent->save(
															[
																'personne_id' => $personne_id,
																'referent_id' => $rdv_referent['CorrespondanceReferentiel']['id_dans_table'],
																'dddesignation' => $rdv->date_rdv->__toString(),
																'structurereferente_id' => $rdv_structurereferente_id
															]
														);
														$this->PersonneReferent->clear();
													}
												}
											}
										}
									}
								}

								/*---------------------------------------------
								----------------------dsp----------------------
								----------------------------------------------*/
								if(isset($dossier->liste_dsps)){
									foreach($dossier->liste_dsps->dsp as $dsp){
										//infos de base des dsp
										$infos_dsp = [
											'personne_id' => $personne_id,
											'id_base_ali' => $dsp->id_ali,
											'topqualipro' => intval($dsp->niveau_etude->qualifications_pro->__toString()),
											'topcompeextrapro' => intval($dsp->niveau_etude->competences_extrapro->__toString())
										];

										//On vérifie que les 2 référentiels sont ok
										$dsp_nivetu_code = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
											'dsp_nivetu',
											intval($dsp->niveau_etude->id_niveau_etudes->__toString())
										);

										if(empty($dsp_nivetu_code)){
											//erreur nivetu inconnu
											$bool_dsp = false;
											$rapport = $this->AddErreur($rapport, 'dsp', 'nivetu_inconnu', $personne_id);
										} else {
											$infos_dsp['nivetu'] = $dsp_nivetu_code;
										}

										$dsp_diplome_code = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
											'diplome_max',
											intval($dsp->niveau_etude->id_diplome_plus_eleve->__toString())
										);

										if(empty($dsp_diplome_code)){
											//erreur diplome inconnu
											$bool_dsp = false;
											$rapport = $this->AddErreur($rapport, 'dsp', 'diplome_inconnu', $personne_id);
										} else {
											$infos_dsp['nivdipmaxobt'] = $dsp_diplome_code;
										}

										if($bool_dsp !== false){

											//On recherche si on a déjà enregistré cette dsp (avec l'id ali)
											$dsp_existant = $this->Dsp->find(
												'first',
												[
													'conditions' => [
														'Dsp.id_base_ali' => intval($dsp->id_ali->__toString())
													]
												]
											);

											if(!empty($dsp_existant)){
												//on ajoute l'id au tableau
												$infos_dsp['id'] = $dsp_existant['Dsp']['id'];
												$alias_table = 'Dsp';

											} else {
												$dsp_rev_existant = $this->DspRev->find(
													'first',
													[
														'conditions' => [
															'DspRev.id_base_ali' => intval($dsp->id_ali->__toString())
														]
													]
												);

												if(!empty($dsp_rev_existant)){
													//on ajoute l'id au tableau
													$infos_dsp['id'] = $dsp_rev_existant['DspRev']['id'];
													$alias_table = 'DspRev';

												} else {
													//on cherche si la personne a déjà une version de dsp pour connaître la table où enregistrer
													$dsp_personne = $this->Dsp->find(
														'first',
														[
															'conditions' => [
																'personne_id' => $personne_id
															]
														]
													);

													if(empty($dsp_personne)){
														$alias_table = 'Dsp';
													} else {
														$alias_table = 'DspRev';
														$infos_dsp['dsp_id'] = $dsp_personne['Dsp']['id'];
													}

												}
											}
											//On ajoute les autres champs
											if(isset($dsp->niveau_etude->annee_obtention)){
												$infos_dsp['annobtnivdipmax'] = intval($dsp->niveau_etude->annee_obtention->__toString());
											}
											if(isset($dsp->niveau_etude->precisions_qualifications_pro)){
												$infos_dsp['libautrqualipro'] = $dsp->niveau_etude->precisions_qualifications_pro->__toString();
											}
											if(isset($dsp->niveau_etude->precisions_competences_extrapro)){
												$infos_dsp['libcompeextrapro'] = $dsp->niveau_etude->precisions_competences_extrapro->__toString();
											}

											//On enregistre les données dans la table
											$bool_dsp = $this->$alias_table->saveAssociated($infos_dsp);
											$this->$alias_table->clear();
										}
									}

								}

								/*---------------------------------------------
								----------------------cer----------------------
								----------------------------------------------*/
								if(isset($dossier->liste_cers)){
									foreach($dossier->liste_cers->cer as $cer){

										$bool_cer = true;
										$infos_cer = [];
										$infos_histo = [];

										//On vérifie et ajoute les champs modifiables
										// date début contrat
										$interval = DateInterval::createFromDateString('3 months');
										if($cer->date_debut->__toString() > '2009-06-01' && $cer->date_debut->__toString() < $now_datetime->add($interval)->format('Y-m-d_H:i:s') ){
											//on ajoute aux infos à enregistrer
											$infos_cer['Contratinsertion']['dd_ci'] = $cer->date_debut->__toString();
										} else {
											//erreur intervale début de contrat
											$bool_cer = false;
											$rapport = $this->AddErreur($rapport, 'cer', 'intervale_debut_contrat', $personne_id);
										}

										//durée cer
										$code_duree = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel('cer_duree', intval($cer->id_duree_cer->__toString()));
										if(!empty($code_duree)){
											//on ajoute aux infos à enregistrer
											$infos_cer['Cer93']['duree'] = $code_duree;
										} else {
											//erreur référentiel inconnu
											$bool_cer = false;
											$rapport = $this->AddErreur($rapport, 'cer', 'cer_duree_inconnue', $personne_id);
										}

										if($bool_cer !== false){
											//Date de fin de contrat
											$interval_duree = DateInterval::createFromDateString($code_duree.' months');
											$dd = date_create_from_format('Y-m-d', $cer->date_debut->__toString());
											$date_fin = $dd->add($interval_duree);
											$infos_cer['Contratinsertion']['df_ci'] = $date_fin->format('Y-m-d');
										}


										//On recherche si le CER a déjà été intégré, si oui seuls certains champs sont modifiables
										$cer_existant = $this->Cer93->find('first', ['conditions' => ['id_base_ali' => intval($cer->id_ali->__toString())]]);
										//Si le cer existe, on ajoute son id
										if(!empty($cer_existant) && $bool_cer !== false){
											$infos_cer['Cer93']['id'] = $cer_existant['Cer93']['id'];
											$infos_cer['Contratinsertion']['id'] = $cer_existant['Cer93']['contratinsertion_id'];
										} else if($bool_cer !== false){

											//On vérifie si la personne a un référent de parcours actuellement
											$referent_actuel = $this->PersonneReferent->referentParcoursActuel(
												$personne_id
											);
											if(empty($referent_actuel)){
												//On vérifie si la personne a un référent de parcours à la date d'enregistrement du CER
												$referent_actuel = $this->PersonneReferent->referentParcoursADate(
													$personne_id,
													$cer->date_saisie->__toString()
												);
											}
											if(empty($referent_actuel)){
												$bool_cer = false;
												$rapport = $this->AddErreur($rapport, 'cer', 'cer_referent_obligatoire', $personne_id);
											} else {
												//On ajoute l'identifiant coté ali
												//A commenter pour les tests (permet d'enregistrer plusieurs fois les mêmes données)
												$infos_cer['Cer93']['id_base_ali'] = intval($cer->id_ali->__toString());

												//On travaille bloc par bloc pour plus de clarté

												//BLOC Référent et structure
												$referent = $this->CorrespondanceReferentiel->find(
													'first',
													[
														'fields' => ['id_dans_table', 'referents_structurereferente_id'],
														'conditions' => [
															'SujetReferentiel.code' => 'referents',
															'CorrespondanceReferentiel.id' => intval($cer->id_referent->__toString())
														],
														'recursive' => 1
													]
												);

												if(empty($referent)){
													//erreur référentiel inconnu
													$bool_cer = false;
													$rapport = $this->AddErreur($rapport, 'cer', 'referent_inconnu', $personne_id);
												} else {
													//on enregistre le referent et la structure
													$infos_cer['Contratinsertion']['referent_id'] = $referent['CorrespondanceReferentiel']['id_dans_table'];
													$infos_cer['Contratinsertion']['structurereferente_id'] = $referent['CorrespondanceReferentiel']['referents_structurereferente_id'];

													//Champ incohérence à déclarer
													if(isset($cer->incoherence)){
														$infos_cer['Cer93']['incoherencesetatcivil'] = $cer->incoherence->__toString();
													}
												}

												//BLOC Vérification des droits
												if($bool_cer !== false){

													//Champ inscrit pe
													$infos_cer['Cer93']['inscritpe'] = intval($cer->inscrit_PE->__toString());

													//Champ CMU
													$code_cmu = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel('cmu', intval($cer->id_cmu->__toString()));
													if(empty($code_cmu)){
														$bool_cer = false;
														$rapport = $this->AddErreur($rapport, 'cer', 'cmu_inconnu', $personne_id);
													} else {
														$infos_cer['Cer93']['cmu'] = $code_cmu;
													}

													//Champ CMU-C
													$code_cmu_c = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel('cmu', intval($cer->id_cmu_c->__toString()));
													if(empty($code_cmu_c)){
														$bool_cer = false;
														$rapport = $this->AddErreur($rapport, 'cer', 'cmuc_inconnu', $personne_id);
													} else {
														$infos_cer['Cer93']['cmuc'] = $code_cmu_c;
													}
												}

												//BLOC FORMATION ET EXPERIENCE
												if($bool_cer !== false){
													$code_nivetu = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel('cer_nivetu', intval($cer->id_niveau_etude->__toString()));
													if(empty($code_nivetu)){
														$bool_cer = false;
														$rapport = $this->AddErreur($rapport, 'cer', 'nivetu_inconnu', $personne_id);
													} else {
														$infos_cer['Cer93']['nivetu'] = $code_nivetu;

													}

													//Diplomes
													if(isset($cer->liste_diplomes)){
														foreach($cer->liste_diplomes->diplome as $diplome){
															$infos_cer['Cer93']['Diplomecer93'][] = [
																'name' => $diplome->intitule->__toString(),
																'annee' => intval($diplome->date_obtention->__toString()),
																'isetranger' => intval($diplome->etranger->__toString())
															];
														}
													}

													//Expériences professionnelles significatives
													if(isset($cer->liste_experiences_pro)){
														foreach($cer->liste_experiences_pro->experience_pro as $exp){
															$id_nature = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel('nature_contrat', $exp->id_nature_contrat);
															if(empty($id_nature)){
																$bool_cer = false;
																$rapport = $this->AddErreur($rapport, 'cer', 'nature_contrat_inconnu', $personne_id);
															}

															$id_code_famille = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel('code_famille', $exp->id_code_famille);
															if(empty($id_code_famille)){
																$bool_cer = false;
																$rapport = $this->AddErreur($rapport, 'cer', 'code_famille_inconnu', $personne_id);
															}
															$id_code_domaine = $this->CorrespondanceReferentiel->find(
																'first',
																[
																	'fields' => ['id_dans_table', 'code_domaine_codefamille_id'],
																	'conditions' => [
																		'SujetReferentiel.code' => 'code_domaine',
																		'CorrespondanceReferentiel.id' => $exp->id_code_domaine
																	],
																	'recursive' => 1
																]
															);
															if(empty($id_code_domaine)){
																$bool_cer = false;
																$rapport = $this->AddErreur($rapport, 'cer', 'code_domaine_inconnu', $personne_id);
															}
															$id_code_metier = $this->CorrespondanceReferentiel->find(
																'first',
																[
																	'fields' => ['id_dans_table', 'code_metier_codedomaine_id'],
																	'conditions' => [
																		'SujetReferentiel.code' => 'code_metier',
																		'CorrespondanceReferentiel.id' => $exp->id_code_metier
																	],
																	'recursive' => 1
																]
															);
															if(empty($id_code_metier)){
																$bool_cer = false;
																$rapport = $this->AddErreur($rapport, 'cer', 'code_metier_inconnu', $personne_id);
															}
															$id_appellation_metier = $this->CorrespondanceReferentiel->find(
																'first',
																[
																	'fields' => ['id_dans_table', 'appell_metier_codemetier_id'],
																	'conditions' => [
																		'SujetReferentiel.code' => 'appellation_metier',
																		'CorrespondanceReferentiel.id' => $exp->id_appellation_metier
																	],
																	'recursive' => 1
																]
															);
															if(empty($id_appellation_metier)){
																$bool_cer = false;
																$rapport = $this->AddErreur($rapport, 'cer', 'code_appellation_metier_inconnu', $personne_id);
															}

															$code_type_duree = null;
															$duree = null;
															if(isset($exp->duree) && isset($exp->id_type_duree)){
																$code_type_duree = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel('type_duree_contrat', $exp->id_type_duree->__toString());
																if(empty($code_type_duree)){
																	$bool_cer = false;
																	$rapport = $this->AddErreur($rapport, 'cer', 'type_duree_inconnu', $personne_id);
																}
																$duree = intval($exp->duree->__toString());
															}

															if($bool_cer !== false){

																//On vérifie la cohérence entre les codes famille/domaine etc...
																if(
																	$id_code_domaine['CorrespondanceReferentiel']['code_domaine_codefamille_id'] != $id_code_famille
																	|| $id_code_metier['CorrespondanceReferentiel']['code_metier_codedomaine_id']  != $id_code_domaine['CorrespondanceReferentiel']['id_dans_table']
																	|| $id_appellation_metier['CorrespondanceReferentiel']['appell_metier_codemetier_id']  != $id_code_metier['CorrespondanceReferentiel']['id_dans_table']
																){
																	$bool_cer = false;
																	$rapport = $this->AddErreur($rapport, 'cer', 'famille_incoherent', $personne_id);
																}

															}

															if($bool_cer !== false){

																//duree et type duree non obligatoire
																$infos_cer['Cer93']['Expprocer93'][] = [
																	'naturecontrat_id' => $id_nature,
																	'anneedeb' => $exp->annee_debut,
																	'nbduree' => $duree,
																	'typeduree' => $code_type_duree,
																	'Entreeromev3' => [
																		'familleromev3_id' => $id_code_famille,
																		'domaineromev3_id' => $id_code_domaine['CorrespondanceReferentiel']['id_dans_table'],
																		'metierromev3_id' => $id_code_metier['CorrespondanceReferentiel']['id_dans_table'],
																		'appellationromev3_id' => $id_appellation_metier['CorrespondanceReferentiel']['id_dans_table']
																	]
																];

															}
														}
													}

													//Autres expériences
													if(isset($cer->autre_experience)){
														$infos_cer['Cer93']['autresexps'] = $cer->autre_experience->__toString();
													}
													//Emploi trouvé
													if(intval($cer->emploi_trouve->__toString())){
														$infos_cer['Cer93']['isemploitrouv'] = 'O';
														$id_code_famille = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel('code_famille', $cer->detail_emploi_trouve->id_code_famille);
														if(empty($id_code_famille)){
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'code_famille_inconnu', $personne_id);
														}
														$id_code_domaine = $this->CorrespondanceReferentiel->find(
															'first',
															[
																'fields' => ['id_dans_table', 'code_domaine_codefamille_id'],
																'conditions' => [
																	'SujetReferentiel.code' => 'code_domaine',
																	'CorrespondanceReferentiel.id' => $cer->detail_emploi_trouve->id_code_domaine
																],
																'recursive' => 1
															]
														);

														if(empty($id_code_domaine)){
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'code_domaine_inconnu', $personne_id);
														}
														$id_code_metier = $this->CorrespondanceReferentiel->find(
															'first',
															[
																'fields' => ['id_dans_table', 'code_metier_codedomaine_id'],
																'conditions' => [
																	'SujetReferentiel.code' => 'code_metier',
																	'CorrespondanceReferentiel.id' => $cer->detail_emploi_trouve->id_code_metier
																],
																'recursive' => 1
															]
														);
														if(empty($id_code_metier)){
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'code_metier_inconnu', $personne_id);
														}
														$id_appellation_metier = $this->CorrespondanceReferentiel->find(
															'first',
															[
																'fields' => ['id_dans_table', 'appell_metier_codemetier_id'],
																'conditions' => [
																	'SujetReferentiel.code' => 'appellation_metier',
																	'CorrespondanceReferentiel.id' => $cer->detail_emploi_trouve->id_appellation_metier
																],
																'recursive' => 1
															]
														);
														if(empty($id_appellation_metier)){
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'code_appellation_metier_inconnu', $personne_id);
														}
														$id_metier_exerce = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel('metier_exerce', $cer->detail_emploi_trouve->id_metier_exerce);
														if(empty($id_metier_exerce)){
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'metier_exerce_inconnu', $personne_id);
														}
														$id_secteur_activite = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel('secteur_activite', $cer->detail_emploi_trouve->id_secteur_activite);
														if(empty($id_secteur_activite)){
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'secteur_activite_inconnu', $personne_id);
														}

														if($bool_cer !== false){

															//On vérifie la cohérence entre les codes famille/domaine etc...
															if(
																$id_code_domaine['CorrespondanceReferentiel']['code_domaine_codefamille_id'] != $id_code_famille
																|| $id_code_metier['CorrespondanceReferentiel']['code_metier_codedomaine_id']  != $id_code_domaine['CorrespondanceReferentiel']['id_dans_table']
																|| $id_appellation_metier['CorrespondanceReferentiel']['appell_metier_codemetier_id']  != $id_code_metier['CorrespondanceReferentiel']['id_dans_table']
															){
																$bool_cer = false;
																$rapport = $this->AddErreur($rapport, 'cer', 'famille_incoherent', $personne_id);
															}

														}

														$id_nature = $this->CorrespondanceReferentiel->find(
															'first',
															[
																'fields' => ['id_dans_table', 'nature_contrat_definir_duree'],
																'conditions' => [
																	'SujetReferentiel.code' => 'nature_contrat',
																	'CorrespondanceReferentiel.id' => intval($cer->detail_emploi_trouve->id_nature_contrat->__toString())
																],
																'recursive' => 1
															]
														);
														if(empty($id_nature)){
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'nature_contrat_inconnu', $personne_id);
														} else if ($id_nature['CorrespondanceReferentiel']['nature_contrat_definir_duree'] && !isset($cer->detail_emploi_trouve->id_duree_cdd)) {
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'duree_cdd_obligatoire', $personne_id);
														}

														if(intval($cer->detail_emploi_trouve->duree_hedbo->__toString()) > 39){
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'duree_hebdo_incorrecte', $personne_id);
														} else {
															$infos_cer['Cer93']['dureehebdo'] = intval($cer->detail_emploi_trouve->duree_hedbo->__toString());
														}

														if(isset($cer->detail_emploi_trouve->id_duree_cdd)){
															$id_duree_cdd = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel('duree_cdd', $cer->detail_emploi_trouve->id_duree_cdd);
															if(empty($id_duree_cdd)){
																$bool_cer = false;
																$rapport = $this->AddErreur($rapport, 'cer', 'code_duree_cdd_inconnu', $personne_id);
															} else {
																$infos_cer['Cer93']['dureecdd'] = $id_duree_cdd;
															}
														}

														if($bool_cer !== false) {

															$infos_cer['Cer93']['metierexerce_id'] = $id_metier_exerce;
															$infos_cer['Cer93']['secteuracti_id'] = $id_secteur_activite;
															$infos_cer['Cer93']['naturecontrat_id'] = $id_nature['CorrespondanceReferentiel']['id_dans_table'];

															$infos_cer['Cer93']['Emptrouvromev3'] = [
																'familleromev3_id' => $id_code_famille,
																'domaineromev3_id' => $id_code_domaine['CorrespondanceReferentiel']['id_dans_table'],
																'metierromev3_id' => $id_code_metier['CorrespondanceReferentiel']['id_dans_table'],
																'appellationromev3_id' => $id_appellation_metier['CorrespondanceReferentiel']['id_dans_table']
															];

														}

													} else {
														$infos_cer['Cer93']['isemploitrouv'] = 'N';
													}



												}

												//BLOC BILAN DU CONTRAT PRECEDENT
												if($bool_cer !== false){
													//Récupérer le contrat précédent
													$cer_precedent = $this->Contratinsertion->find(
														'first',
														[
															'contain' => [
																'Cer93' => [
																	'Sujetcer93',
																],
															],
															'conditions' => [
																'Contratinsertion.personne_id' => $personne_id,
																'Contratinsertion.decision_ci' => 'V'
															],
															'order' => ['Contratinsertion.rg_ci DESC']
														]
													);

													$infos_cer['Cer93']['prevupcd'] = isset($cer_precedent['Cer93']) ? $cer_precedent['Cer93']['prevu'] : null;

													if( isset( $cer_precedent['Cer93']['Sujetcer93'] ) ) {
														$sousSujetsIds = Hash::filter( (array)Set::extract('/Cer93/Sujetcer93/Cer93Sujetcer93/soussujetcer93_id', $cer_precedent )) ;
														$valeursparSousSujetsIds = Hash::filter( (array)Set::extract('/Cer93/Sujetcer93/Cer93Sujetcer93/valeurparsoussujetcer93_id', $cer_precedent )) ;

														if( !empty( $sousSujetsIds ) ) {
															$sousSujets = $this->Cer93->Sujetcer93->Soussujetcer93->find( 'list', array( 'conditions' => array( 'Soussujetcer93.id' => $sousSujetsIds ) ) );
															foreach( $cer_precedent['Cer93']['Sujetcer93'] as $key => $values ) {
																if( isset( $values['Cer93Sujetcer93']['soussujetcer93_id'] ) && !empty( $values['Cer93Sujetcer93']['soussujetcer93_id'] ) ) {
																	$cer_precedent['Cer93']['Sujetcer93'][$key]['Cer93Sujetcer93']['Soussujetcer93'] = array( 'name' => $sousSujets[$values['Cer93Sujetcer93']['soussujetcer93_id']] );
																}
																else {
																	$cer_precedent['Cer93']['Sujetcer93'][$key]['Cer93Sujetcer93']['Soussujetcer93'] = array( 'name' => null );
																}

																if( !empty( $valeursparSousSujetsIds ) ) {
																	// Valeur par sous sujet
																	$valeursparSousSujets = $this->Cer93->Sujetcer93->Soussujetcer93->Valeurparsoussujetcer93->find( 'list', array( 'conditions' => array( 'Valeurparsoussujetcer93.id' => $valeursparSousSujetsIds ) ) );

																	//Valeur par sous sujet
																	if( isset( $values['Cer93Sujetcer93']['valeurparsoussujetcer93_id'] ) && !empty( $values['Cer93Sujetcer93']['valeurparsoussujetcer93_id'] ) ) {
																		$cer_precedent['Cer93']['Sujetcer93'][$key]['Cer93Sujetcer93']['Valeurparsoussujetcer93'] = array( 'name' => $valeursparSousSujets[$values['Cer93Sujetcer93']['valeurparsoussujetcer93_id']] );
																	}
																	else {
																		$cer_precedent['Cer93']['Sujetcer93'][$key]['Cer93Sujetcer93']['Valeurparsoussujetcer93'] = array( 'name' => null );
																	}
																}
															}
														}

														// Informations complémentaires
														$sujetromev3 = (array)Hash::get( $cer_precedent, 'Cer93.Sujetromev3' );

														$infos_cer['Cer93']['sujetpcd'] = serialize( array( 'Sujetcer93' => $cer_precedent['Cer93']['Sujetcer93'], 'Sujetromev3' => $sujetromev3 ) );
													}

													//Bilan du contrat précédent
													if(isset($cer->bilan_actions_prec)){
														$infos_cer['Cer93']['bilancerpcd'] = $cer->bilan_actions_prec->__toString();
													}

												}

												//BLOC PROJET NOUVEAU CONTRAT
												if($bool_cer !== false){
													//Mois à venir
													$infos_cer['Cer93']['prevu'] = $cer->mois_a_venir->__toString();

													//Sujets du CER
													$liste_sujets = [];
													foreach($cer->liste_sujets->sujets as $sujet){
														$sujet_a_enregistrer = [];
														//On vérifie si l'id du sujet existe
														$id_sujet = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel('cer_sujet', $sujet->id_sujet_cer);
														if(empty($id_sujet)){
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'sujet_inconnu', $personne_id);
														} else {
															//On vérifie que le sujet n'est pas déjà dans la liste
															if(in_array($id_sujet, $liste_sujets)){
																$bool_cer = false;
																$rapport = $this->AddErreur($rapport, 'cer', 'sujet_concurrent', $personne_id);
															} else {
																//On ajoute le sujet à la liste
																array_push($liste_sujets, $id_sujet);
																$sujet_a_enregistrer['sujetcer93_id'] = $id_sujet;

																//On récupère les sous-sujets et on vérifie l'existence et la cohérence
																if(isset($sujet->id_sous_sujet_cer)){
																	$sous_sujet = $this->CorrespondanceReferentiel->find(
																		'first',
																		[
																			'fields' => ['id_dans_table', 'cer_sous_sujet_champ_texte', 'cer_sous_sujet_sujet_id'],
																			'conditions' => [
																				'SujetReferentiel.code' => 'cer_sous_sujet',
																				'CorrespondanceReferentiel.id' => intval($sujet->id_sous_sujet_cer->__toString())
																			],
																			'recursive' => 1
																		]
																	);
																	if(empty($sous_sujet)){
																		$bool_cer = false;
																		$rapport = $this->AddErreur($rapport, 'cer', 'sous_sujet_inconnu', $personne_id);
																	} else if($sous_sujet['CorrespondanceReferentiel']['cer_sous_sujet_sujet_id'] !== $id_sujet) {
																		//erreur si le sous sujet ne corespond pas au sujet
																		$bool_cer = false;
																		$rapport = $this->AddErreur($rapport, 'cer', 'sous_sujet_incohérent', $personne_id);
																	} else {
																		$sujet_a_enregistrer['soussujetcer93_id'] = $sous_sujet['CorrespondanceReferentiel']['id_dans_table'];

																		//Si champ texte, on le récupère
																		if($sous_sujet['CorrespondanceReferentiel']['cer_sous_sujet_champ_texte'] && isset($sujet->champ_libre)){
																			$sujet_a_enregistrer['commentaireautre'] = $sujet->champ_libre->__toString();
																		}

																		//On récupère les valeurs par sous-sujet et on vérifie l'existence et la cohérence
																		if(isset($sujet->id_valeur_sous_sujet_cer)){
																			$valeur_sous_sujet = $this->CorrespondanceReferentiel->find(
																				'first',
																				[
																					'fields' => ['id_dans_table', 'cer_valeurs_sous_sujet_sujet_id', 'cer_valeurs_sous_sujet_champ_texte'],
																					'conditions' => [
																						'SujetReferentiel.code' => 'cer_valeurs_sous_sujet',
																						'CorrespondanceReferentiel.id' => intval($sujet->id_valeur_sous_sujet_cer->__toString())
																					],
																					'recursive' => 1
																				]
																			);

																			if(empty($valeur_sous_sujet)){
																				$bool_cer = false;
																				$rapport = $this->AddErreur($rapport, 'cer', 'val_sous_sujet_inconnu', $personne_id);
																			} else if($valeur_sous_sujet['CorrespondanceReferentiel']['cer_valeurs_sous_sujet_sujet_id'] !== $sous_sujet['CorrespondanceReferentiel']['id_dans_table']) {
																				//erreur si la valeur par sous sujet ne corespond pas au sous sujet
																				$bool_cer = false;
																				$rapport = $this->AddErreur($rapport, 'cer', 'val_sous_sujet_incohérent', $personne_id);
																			} else {
																				$sujet_a_enregistrer['valeurparsoussujetcer93_id'] = $valeur_sous_sujet['CorrespondanceReferentiel']['id_dans_table'];

																				//Si champ texte, on le récupère
																				if($valeur_sous_sujet['CorrespondanceReferentiel']['cer_valeurs_sous_sujet_champ_texte'] && isset($sujet->champ_libre)){
																					$sujet_a_enregistrer['commentaireautre'] = $sujet->champ_libre->__toString();
																				}
																			}
																		}
																	}

																	//On ajoute à la liste des sujets à enregistrer pour le CER
																	$infos_cer['Cer93']['Sujetcer93'][] = $sujet_a_enregistrer;
																}
															}
														}

													}

												}

												//BLOC FINAL
												if($bool_cer !== false){
													//Poit parcours
													$point_parcours = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
														'cer_pointparcours',
														intval($cer->id_point_parcours->__toString())
													);

													if(empty($point_parcours)){
														$bool_cer = false;
														$rapport = $this->AddErreur($rapport, 'cer', 'point_parcours_inconnu', $personne_id);
													} else if($point_parcours == 'aladate' && !isset($cer->date_reprise_contact)) {
														//Si la date est obligatoire mais manquante
														$bool_cer = false;
														$rapport = $this->AddErreur($rapport, 'cer', 'date_point_parcours_obligatoire', $personne_id);
													} else if($point_parcours == 'aladate' && isset($cer->date_reprise_contact)) {
														//Si la date est renseignée on l'enregistre
														$infos_cer['Cer93']['datepointparcours'] = $cer->date_reprise_contact->__toString();
														$infos_cer['Cer93']['pointparcours'] = $point_parcours;
													} else {
														//On enregistre simplement le point de parcours
														$infos_cer['Cer93']['pointparcours'] = $point_parcours;
													}

													//Observations
													if(isset($cer->observations)){
														$infos_cer['Cer93']['observpro'] = $cer->observations;
													}

													//Date de saisie
													$interval_20_years = DateInterval::createFromDateString('20 years');
													$minus_20_years = $now_datetime->sub($interval_20_years)->format('Y-m-d_H:i:s');
													$plus_20_years = $now_datetime->add($interval_20_years)->format('Y-m-d_H:i:s');
													if($cer->date_saisie->__toString() < $minus_20_years || $cer->date_saisie->__toString() > $plus_20_years ){
														//La date de saisie doit être entre Y-20 et Y+20
														$bool_cer = false;
														$rapport = $this->AddErreur($rapport, 'cer', 'date_saisie_incorrecte', $personne_id);
													} else {
														$infos_cer['Contratinsertion']['date_saisi_ci'] = $cer->date_saisie->__toString();
													}

													//Forme du CER
													if(isset($cer->id_forme_cer)){
														$forme_cer = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
															'cer_forme',
															intval($cer->id_forme_cer->__toString())
														);

														if(empty($forme_cer)){
															$bool_cer = false;
															$rapport = $this->AddErreur($rapport, 'cer', 'forme_cer_inconnu', $personne_id);
														}
													} else {
														$forme_cer = 'S';
													}

													//Commentaire (forme du CER)
													if(isset($cer->liste_commentaires->commentaires)){
														$comm = [];
														foreach($cer->liste_commentaires->commentaires as $commentaire){
															$commentaire_autre = '';
															$id_commentaire = $this->CorrespondanceReferentiel->find(
																'first',
																[
																	'fields' => ['id_dans_table', 'cer_commentaire_champ_texte'],
																	'conditions' => [
																		'SujetReferentiel.code' => 'cer_commentaire',
																		'CorrespondanceReferentiel.id' => intval($commentaire->id_commentaire->__toString())
																	],
																	'recursive' => 1
																]
															);

															if(empty($id_commentaire)){
																$bool_cer = false;
																$rapport = $this->AddErreur($rapport, 'cer', 'commentaire_inconnu', $personne_id);
															} else {
																if($id_commentaire['CorrespondanceReferentiel']['cer_commentaire_champ_texte']){
																	if(isset($commentaire->commentaire_libre)){
																		$commentaire_autre = $commentaire->commentaire_libre->__toString();
																	}
																}
															}

															if($bool_cer !== false){
																$comm[] = [
																	'commentairenormecer93_id' => $id_commentaire['CorrespondanceReferentiel']['id_dans_table'],
																	'commentaireautre' => $commentaire_autre
																];
															}
														}
													} else {
														$comm = null;
													}


													$infos_histo['Histochoixcer93'] = [
														'etape' => '03attdecisioncg',
														'datechoix' => $cer->date_saisie->__toString(),
														'formeci' => $forme_cer,
														'user_id' => $user_id,
														'Commentairenormecer93' => [
															'Commentairenormecer93' => $comm
														]
													];

													//Date de signature
													if($cer->date_signature < $minus_20_years || $cer->date_signature > $now ){
														//La date de saisie doit être entre Y-20 et aujourd'hui
														$bool_cer = false;
														$rapport = $this->AddErreur($rapport, 'cer', 'date_signature_incorrecte', $personne_id);
													} else {
														$infos_cer['Cer93']['datesignature'] = $cer->date_signature->__toString();
													}
												}

												//L'enregistrement se fait dans 2 tables différentes (contratinsertion et cers93)
												if($bool_cer !== false){

													// On ajoute les informations manquantes
													// On calcule le rang
													$rang_cer_max = $this->Contratinsertion->query(
														"
														Select max(rg_ci) from contratsinsertion where personne_id = $personne_id;
														"
													);
													if(!empty($rang_cer_max)){
														$rang_cer = $rang_cer_max[0][0]['max']+1;
													} else {
														$rang_cer = 1;
													}
													$infos_cer['Contratinsertion']['rg_ci'] = $rang_cer;

													$infos_cer['Cer93']['user_id'] = $user_id;
													$infos_cer['Cer93']['positioncer'] = '03attdecisioncg';

													$infos_cer['Cer93']['matricule'] = $personne['Dossier'][0]['matricule'];
													$infos_cer['Cer93']['dtdemrsa'] = $personne['Dossier'][0]['dtdemrsa'];
													$infos_cer['Cer93']['qual'] = $personne['Personne']['qual'];
													$infos_cer['Cer93']['nom'] = $personne['Personne']['nom'];
													$infos_cer['Cer93']['nomnai'] = $personne['Personne']['nomnai'];
													$infos_cer['Cer93']['prenom'] = $personne['Personne']['prenom'];
													$infos_cer['Cer93']['dtnai'] = $personne['Personne']['dtnai'];
													$infos_cer['Cer93']['sitfam'] = $personne['Foyer']['sitfam'];
													$infos_cer['Cer93']['numdemrsa'] = $personne['Dossier'][0]['numdemrsa'];
													if(isset($personne['Situationallocataire'][0])){
														$infos_cer['Cer93']['rolepers'] = $personne['Situationallocataire'][0]['rolepers'];
														$infos_cer['Cer93']['identifiantpe'] = $personne['Situationallocataire'][0]['identifiantpe'];
														$infos_cer['Cer93']['adresse'] = $personne['Situationallocataire'][0]['numvoie']." ".$personne['Situationallocataire'][0]['nomvoie']." ".$personne['Situationallocataire'][0]['compladr'];
														$infos_cer['Cer93']['codepos'] = $personne['Situationallocataire'][0]['codepos'];
														$infos_cer['Cer93']['nomcom'] = $personne['Situationallocataire'][0]['nomcom'];
													}

													$infos_cer['Cer93']['natlog'] = null;
													if(isset($personne['DspRev']) && !empty($personne['DspRev'])){
														$max_dsprev = count($personne['DspRev']);
														$infos_cer['Cer93']['natlog'] = $personne['DspRev'][$max_dsprev - 1]['natlog'];
													} else if (isset($personne['Dsp'])){
														$infos_cer['Cer93']['natlog'] = $personne['Dsp']['natlog'];
													}
												}
											}
										}

										if($bool_cer !== false){
											$infos_cer['Contratinsertion']['personne_id'] = $personne_id;

											$infos_contrat = $infos_cer['Contratinsertion'];
											$infos_cer2['Cer93'] = $infos_cer['Cer93'];

											$contrat_ok = $this->Contratinsertion->saveAssociated($infos_contrat, ['validation' => true, 'deep' => true, 'atomic' => false]);

											//On ajout l'id du contrat d'insertion qui vient d'être créé
											$infos_cer2['Cer93']['contratinsertion_id'] = $this->Contratinsertion->id;
											$cer_ok = $this->Cer93->saveAssociated($infos_cer2, ['validation' => false, 'deep' => true, 'atomic' => false]);

											if(!isset($infos_cer['Cer93']['id'])){
												$infos_histo['Histochoixcer93']['cer93_id'] = $this->Cer93->id;
												$histo_ok = $this->Histochoixcer93->saveAssociated($infos_histo, ['validation' => true, 'deep' => true, 'atomic' => false]);
												$bool_cers = $bool_cer && $histo_ok['Histochoixcer93'];

											}

											$bool_cers = $bool_cer && $contrat_ok['Contratinsertion'] && $cer_ok['Cer93'];
											$this->Histochoixcer93->clear();
											$this->Cer93->clear();
											$this->Contratinsertion->clear();
										} else {
											$bool_cers = false;
										}

									}
								}
								/*---------------------------------------------
								-----------------orientation----------------------
								----------------------------------------------*/
								if(isset($dossier->liste_changements_orientation)){
									foreach($dossier->liste_changements_orientation->changement_orientation as $orientation){
										$infos_orient = [];

										$derniere_orientation = $this->Orientstruct->query($this->WebrsaOrientstruct->sqDerniere($personne_id));
										$derniere_orientation_id = isset($derniere_orientation[0]['orientsstructs']['id']) ? $derniere_orientation[0]['orientsstructs']['id'] : null;

										$orient_id_webrsa = isset($orientation->id_webrsa) ? intval($orientation->id_webrsa->__toString()) : null;
										$orient_id_ali = intval($orientation->id_ali->__toString());

										//référent
										$referent_orient = $this->CorrespondanceReferentiel->find(
											'first',
											[
												'fields' => ['id_dans_table', 'referents_structurereferente_id'],
												'conditions' => [
													'SujetReferentiel.code' => 'referents',
													'CorrespondanceReferentiel.id' => intval($orientation->id_referent->__toString())
												],
												'recursive' => 1
											]
										);

										if(empty($referent_orient)){
											$bool_orient = false;
											$rapport = $this->AddErreur($rapport, 'orient', 'referent_inconnu', $personne_id);
										} else if($referent_orient['CorrespondanceReferentiel']['referents_structurereferente_id'] !== $id_ali){
											$bool_orient = false;
											$rapport = $this->AddErreur($rapport, 'orient', 'referent_incoherent', $personne_id);
										}

										//origine (utilisable par les ALI)
										$code_origine_orient = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
											'orient_origine_utilisable_ALI',
											intval($orientation->id_origine->__toString())
										);

										if(empty($code_origine_orient)){
											$bool_orient = false;
											$rapport = $this->AddErreur($rapport, 'orient', 'origine_inconnue', $personne_id);
										}

										//type d'orientation
										$type_orient = $this->CorrespondanceReferentiel->find(
											'first',
											[
												'fields' => ['id_dans_table', 'typesorients_parent_id'],
												'conditions' => [
													'SujetReferentiel.code' => 'typesorients',
													'CorrespondanceReferentiel.id' => intval($orientation->id_type_orientation->__toString())
												],
												'recursive' => 1
											]
										);

										if(empty($type_orient)){
											$bool_orient = false;
											$rapport = $this->AddErreur($rapport, 'orient', 'type_orient_inconnu', $personne_id);
										} else if(is_null($type_orient['CorrespondanceReferentiel']['typesorients_parent_id'])){
											$bool_orient = false;
											$rapport = $this->AddErreur($rapport, 'orient', 'type_orient_parent', $personne_id);
										}

										//structure référente accueil
										$id_structure_accueil = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel(
											'structuresreferentes',
											intval($orientation->id_structure_referente_accueil->__toString())
										);

										if(empty($id_structure_accueil)){
											$bool_orient = false;
											$rapport = $this->AddErreur($rapport, 'orient', 'struct_accueil_inconnue', $personne_id);
										}

										if($bool_orient !== false){

											//référent accueil
											if(isset($orientation->id_referent_accueil)){
												$referent_accueil = $this->CorrespondanceReferentiel->find(
													'first',
													[
														'fields' => ['id_dans_table', 'referents_structurereferente_id'],
														'conditions' => [
															'SujetReferentiel.code' => 'referents',
															'CorrespondanceReferentiel.id' => intval($orientation->id_referent_accueil->__toString())
														],
														'recursive' => 1
													]
												);

												if(empty($referent_accueil)){
													$bool_orient = false;
													$rapport = $this->AddErreur($rapport, 'orient', 'ref_accueil_inconnu', $personne_id);
												} else if($referent_accueil['CorrespondanceReferentiel']['referents_structurereferente_id'] !== $id_structure_accueil) {
													$bool_orient = false;
													$rapport = $this->AddErreur($rapport, 'orient', 'ref_accueil_incoherent', $personne_id);
												}
											}
										}

										if($bool_orient !== false){
											//statut
											$code_statut_orient = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
												'orient_statut',
												intval($orientation->id_statut->__toString())
											);


											if(empty($code_statut_orient)){
												$bool_orient = false;
												$rapport = $this->AddErreur($rapport, 'orient', 'statut_inconnu', $personne_id);
											}

										}

										if($bool_orient !== false && $code_origine_orient == 'reorientation'){
											//Origine réorientation
											//Il faut qu'il existe déjà au moins une orientation
											if(empty($derniere_orientation_id)){
												$bool_orient = false;
												$rapport = $this->AddErreur($rapport, 'orient', 'reorient_orient_obligatoire', $personne_id);
											} else {

												if(!isset($orientation->reorientation)){
													$bool_orient = false;
													$rapport = $this->AddErreur($rapport, 'orient', 'motif_reorient_obligatoire', $personne_id);
												} else {
													$id_motif_reorient = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel(
														'reorientation_motifs',
														intval($orientation->reorientation->id_motif_reorientation->__toString())
													);

													if(empty($id_motif_reorient)){
														$bool_orient = false;
														$rapport = $this->AddErreur($rapport, 'orient', 'motif_reorient_inconnu', $personne_id);
													}
												}
											}
										}

										if($bool_orient !== false){
											//On regarde si l'orientation existe déjà
											$orient_existe = $this->Orientstruct->find(
												'first',
												[
													'conditions' => [
														'OR' => [
															'Orientstruct.id' => $orient_id_webrsa,
															'Orientstruct.id_base_ali' => $orient_id_ali,
															'AND' => [
																'Orientstruct.structurereferente_id' => $id_structure_accueil,
																'Orientstruct.typeorient_id' => $type_orient['CorrespondanceReferentiel']['id_dans_table'],
																'Orientstruct.date_propo' => $orientation->date_demande->__toString()
															]
														]
													]
												]
											);

											//On vérifie si la personne a déjà un dossier ep en cours
											$reorientationseps = $this->Orientstruct->Personne->Dossierep->getReorientationsEnCours( $personne_id );

											if(empty($orient_existe) && !empty($reorientationseps)){
												$bool_orient = false;
												$rapport = $this->AddErreur($rapport, 'orient', 'dossier_ep_en_cours', $personne_id);
											}

											$modif_ok = true;
											//Seule la plus récente est modifiable
											if((!empty($orient_existe) && $derniere_orientation_id != null && $orient_existe['Orientstruct']['id'] != $derniere_orientation_id)){
												//On ne peut pas modifier cette orientation
												$modif_ok = false;
											} else if (!empty($orient_existe)){
												//On peut modifier, on ajoute l'id de l'orientation
												$infos_orient['Orientstruct']['id'] = $orient_existe['Orientstruct']['id'];
											}
										}

										if($bool_orient !== false && $modif_ok && $code_origine_orient != 'reorientation'){
											//On remplit les champs pour l'orientation
											$infos_orient['Orientstruct']['personne_id'] = $personne_id;
											$infos_orient['Orientstruct']['typeorient_id'] = $type_orient['CorrespondanceReferentiel']['id_dans_table'];
											$infos_orient['Orientstruct']['structurereferente_id'] = $id_structure_accueil;
											$infos_orient['Orientstruct']['referent_id'] = isset($referent_accueil) ? $referent_accueil['CorrespondanceReferentiel']['id_dans_table'] : null;
											$infos_orient['Orientstruct']['date_propo'] = $orientation->date_demande->__toString();
											$infos_orient['Orientstruct']['date_valid'] = $orientation->date_demande->__toString();
											$infos_orient['Orientstruct']['statut_orient'] = $code_statut_orient;
											$infos_orient['Orientstruct']['structureorientante_id'] = $id_ali;
											$infos_orient['Orientstruct']['referentorientant_id'] = $referent_orient['CorrespondanceReferentiel']['id_dans_table'];
											$infos_orient['Orientstruct']['user_id'] = $user_id;
											$infos_orient['Orientstruct']['origine'] = $code_origine_orient;
											$infos_orient['Orientstruct']['id_base_ali'] = $orient_id_ali;

											//On sauvegarde l'orientation
											$orient_save = $this->Orientstruct->save($infos_orient);
											$bool_orient = ($orient_save !== false);
											//On lance un recalcul du rang
											$this->Orientstruct->forceRecalculeRang ($orient_save);

											//On ajoute le référent de parcours
											if(isset($referent_accueil)){
												//On transforme le référent pour l'enregistrement
												$infos_orient['Orientstruct']['referent_id'] = $id_structure_accueil."_".$referent_accueil['CorrespondanceReferentiel']['id_dans_table'];
												$bool_orient = $bool_orient && $this->Orientstruct->Referent->PersonneReferent->referentParModele(
													$infos_orient,
													$this->Orientstruct->alias,
													'date_valid'
												);

											}

											$this->Orientstruct->clear();


										} else if($bool_orient !== false && $code_origine_orient == 'reorientation'){

											//Si on est en réorientation
											//On créé un dossier ep pour la personne
											$infos_orient['Dossierep']['personne_id'] = $personne_id;
											$infos_orient['Dossierep']['themeep'] = 'reorientationseps93';
											$infos_orient['Dossierep']['actif'] = true;

											//On enregistre le dossier ep
											$dossier_save = $this->Dossierep->save($infos_orient['Dossierep']);
											$bool_orient = ($dossier_save !== false);

											//On créé une entrée dans la table réorientationep93 avec les infos précédentes
											$infos_orient['Reorientationep93']['dossierep_id'] = $dossier_save['Dossierep']['id'];
											$infos_orient['Reorientationep93']['orientstruct_id'] = $derniere_orientation_id;
											$infos_orient['Reorientationep93']['typeorient_id'] = $type_orient['CorrespondanceReferentiel']['id_dans_table'];
											$infos_orient['Reorientationep93']['user_id'] = $user_id;
											$infos_orient['Reorientationep93']['datedemande'] = $orientation->date_demande->__toString();
											$infos_orient['Reorientationep93']['motifreorientep93_id'] = $id_motif_reorient;
											$infos_orient['Reorientationep93']['structurereferente_id'] = $id_structure_accueil;
											$infos_orient['Reorientationep93']['referent_id'] = isset($referent_accueil) ? $referent_accueil['CorrespondanceReferentiel']['id_dans_table'] : null;
											$infos_orient['Reorientationep93']['accordaccueil'] = intval($orientation->reorientation->accord_referent->__toString());
											$infos_orient['Reorientationep93']['desaccordaccueil'] = isset($orientation->reorientation->motif_refus) ? $orientation->reorientation->motif_refus->__toString() : null;
											$infos_orient['Reorientationep93']['accordallocataire'] = intval($orientation->reorientation->accord_allocataire->__toString());
											$infos_orient['Reorientationep93']['urgent'] = intval($orientation->reorientation->urgence->__toString());


											//On enregistre la reorientation
											$reorient_save = $this->Reorientationep93->save($infos_orient['Reorientationep93']);
											$bool_orient = ($reorient_save !== false);

											$this->Dossierep->clear();
											$this->Reorientationep93->clear();

										}
									}
								}
								/*---------------------------------------------
								-----------------formulaire d1-----------------
								----------------------------------------------*/
								if(isset($dossier->formulaire_d1)){
									$d1 = $dossier->formulaire_d1;

									//On regarde si la personne a une situation
									if(empty($this->Situationallocataire->getSituation( $personne_id ))){
										$bool_d1 = false;
										$rapport = $this->AddErreur($rapport, 'd1', 'sans_situation', $personne_id);
									}

									//On récupère les ids et on regarde si le formulaire existe déjà en base
									$id_d1_webrsa = isset($d1->id_webrsa) ? intval($d1->id_webrsa->__toString()) : null;
									$id_d1_ali = intval($d1->id_ali->__toString());

									$infos_d1['Questionnaired1pdv93']['id_base_ali'] = $id_d1_ali;

									$d1_existe = $this->Questionnaired1pdv93->find(
										'first',
										[
											'conditions' => [
												'OR' => [
													'Questionnaired1pdv93.id' => $id_d1_webrsa,
													'Questionnaired1pdv93.id_base_ali' => $id_d1_ali
												]
											]
										]
									);

									if(empty($d1_existe) && $bool_d1 !== false){
										//On vérifie si un premier rdv de l'année existe
										$premier_rdv_id = $this->Questionnaired1pdv93->rendezvous($personne_id, true);

										if(empty($premier_rdv_id)){
											$bool_d1 = false;
											$rapport = $this->AddErreur($rapport, 'd1', 'premier_rdv_obligatoire', $personne_id);
										}

										//On vérifie que les DSP niveau d'étude sont renseignées
										$niveau_etude = $this->Questionnaired1pdv93->nivetu($personne_id);

										if(empty($niveau_etude)){
											$bool_d1 = false;
											$rapport = $this->AddErreur($rapport, 'd1', 'nivetu_obligatoire', $personne_id);
										}

										if($bool_d1 !== false){
											//nationalité
											$code_nationalite = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
												'nationalite',
												intval($d1->id_nationalite->__toString())
											);

											if(empty($code_nationalite)){
												$bool_d1 = false;
												$rapport = $this->AddErreur($rapport, 'd1', 'nationalite_inconnu', $personne_id);
											}

											//statut sur le marché du travail
											$code_statut_marche_travail = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
												'marche_travail',
												intval($d1->id_statut_marche_travail->__toString())
											);

											if(empty($code_statut_marche_travail)){
												$bool_d1 = false;
												$rapport = $this->AddErreur($rapport, 'd1', 'statut_marche_travail_inconnu', $personne_id);
											}

											//groupe vulnérable
											$code_groupe_vulnerable = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
												'groupe_vulnerable',
												intval($d1->id_groupes_vulnerables->__toString())
											);

											if(empty($code_groupe_vulnerable)){
												$bool_d1 = false;
												$rapport = $this->AddErreur($rapport, 'd1', 'groupe_vulnerable_inconnu', $personne_id);
											}

											//profession et catégories sociopro
											$code_cat_sociopro = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
												'cat_sociopro',
												intval($d1->id_cat_sociopro->__toString())
											);

											if(empty($code_cat_sociopro)){
												$bool_d1 = false;
												$rapport = $this->AddErreur($rapport, 'd1', 'cat_sociopro_inconnu', $personne_id);
											}

											//condition de logement
											$code_logement = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
												'conditions_logement',
												intval($d1->id_condition_logement->__toString())
											);

											if(empty($code_logement)){
												$bool_d1 = false;
												$rapport = $this->AddErreur($rapport, 'd1', 'cond_logement_inconnu', $personne_id);
											}
										}

										if($bool_d1 !== false && $code_logement == 'autre' && !isset($d1->condition_logement_autre)){
											//Si condition de logement autre, le commentaire est obligatoire
											$bool_d1 = false;
											$rapport = $this->AddErreur($rapport, 'd1', 'cond_log_precision_obligatoire', $personne_id);
										}

										if($bool_d1 !== false){
											//On récupère les champs du formulaire
											$form_data = $this->Questionnaired1pdv93->prepareFormData($personne_id, true);
											$infos_d1['Questionnaired1pdv93'] = array_merge($infos_d1['Questionnaired1pdv93'], $form_data['Questionnaired1pdv93']);
											$infos_d1['Situationallocataire'] = $form_data['Situationallocataire'];

											//On ajoute les autres champs
											//nationalite
											$infos_d1['Situationallocataire']['nati'] = $code_nationalite;
											//inscrit pe
											$infos_d1['Questionnaired1pdv93']['inscritpe'] = intval($d1->inscrit_pe->__toString());
											//statut marche travail
											$infos_d1['Questionnaired1pdv93']['marche_travail'] = $code_statut_marche_travail;
											//groupe vulnérable
											$infos_d1['Questionnaired1pdv93']['vulnerable'] = $code_groupe_vulnerable;
											//diplome etranger
											$infos_d1['Questionnaired1pdv93']['diplomes_etrangers'] = intval($d1->diplome_etranger_reconnu_france->__toString());
											//cat sociopro
											$infos_d1['Questionnaired1pdv93']['categorie_sociopro'] = $code_cat_sociopro;
											//condition logement
											$infos_d1['Questionnaired1pdv93']['conditions_logement'] = $code_logement;
											//autre
											$infos_d1['Questionnaired1pdv93']['conditions_logement_autre'] = isset($d1->condition_logement_autre) ? $d1->condition_logement_autre->__toString() : null;

											//On enregistre
											$bool_d1 = $this->Questionnaired1pdv93->saveAll($infos_d1);
											$this->Questionnaired1pdv93->clear();
										}
									}
								}
								/*---------------------------------------------
								-----------------formulaire d2-----------------
								----------------------------------------------*/
								if(isset($dossier->formulaire_d2)){
									$d2 = $dossier->formulaire_d2;

									//On récupère les ids et on regarde si le formulaire existe déjà en base
									$id_d2_webrsa = isset($d2->id_webrsa) ? intval($d2->id_webrsa->__toString()) : null;
									$id_d2_ali = intval($d2->id_ali->__toString());

									$infos_d2['Questionnaired2pdv93'] = [];

									$infos_d2['Questionnaired2pdv93']['id_base_ali'] = $id_d2_ali;

									$id_d2 = null;

									$d2_existe = $this->Questionnaired2pdv93->find(
										'first',
										[
											'conditions' => [
												'OR' => [
													'Questionnaired2pdv93.id' => $id_d2_webrsa,
													'Questionnaired2pdv93.id_base_ali' => $id_d2_ali
												]
											]
										]
									);

									if(empty($d2_existe)){
										//On vérifie s'il est possible d'ajouter un formulaire
										$status = $this->Questionnaired2pdv93->statusQuestionnaireD2( $personne_id );
										if($status['button'] == false){
											$message = $status['messageExist'] ? 'd2_existe_deja' : 'd2_d1_manquant';
											$bool_d2 = false;
											$rapport = $this->AddErreur($rapport, 'd2', $message, $personne_id);
										}
									} else {
										//On ajoute l'id pour la modification
										$id_d2 = $d2_existe['Questionnaired2pdv93']['id'];
										$infos_d2['Questionnaired2pdv93']['id'] = $id_d2;
									}

									if($bool_d2 !== false){
										//On vérifie les référentiels
										//Statut de l'accompagnement
										$code_statut_accompagnement = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
											'statut_accompagnement',
											intval($d2->id_statut_accompagnement->__toString())
										);

										if(empty($code_statut_accompagnement)){
											$bool_d2 = false;
											$rapport = $this->AddErreur($rapport, 'd2', 'statut_accompagnement_inconnu', $personne_id);
										} else {
											$infos_d2['Questionnaired2pdv93']['situationaccompagnement'] = $code_statut_accompagnement;
										}

									}

									//sortie de l'obligation d'accompagnement
									if($bool_d2 !== false && $code_statut_accompagnement == 'sortie_obligation'){
										if(isset($d2->sortie_accompagnement)){
											//sortie de l'obligation d'accompagnement
											$id_sortie_obligation = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel(
												'motif_sortie_obligation_accompagnement',
												intval($d2->sortie_accompagnement->id_motif_sortie_accompagnement->__toString())
											);

											if(empty($id_sortie_obligation)){
												$bool_d2 = false;
												$rapport = $this->AddErreur($rapport, 'd2', 'sortie_obligation_acc_inconnu', $personne_id);
											} else {
												$infos_d2['Questionnaired2pdv93']['sortieaccompagnementd2pdv93_id'] = $id_sortie_obligation;
											}

											//temps de travail
											if(isset($d2->sortie_accompagnement->id_temps_travail)){
												$id_temps_travail = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel(
													'temps_travail',
													intval($d2->sortie_accompagnement->id_temps_travail->__toString())
												);

												if(empty($id_temps_travail)){
													$bool_d2 = false;
													$rapport = $this->AddErreur($rapport, 'd2', 'temps_travail_inconnu', $personne_id);
												} else {
													$infos_d2['Questionnaired2pdv93']['dureeemploi_id'] = $id_temps_travail;
												}
											}
											//Emploi
											if(isset($d2->sortie_accompagnement->id_code_famille)){
												$id_d2_code_famille = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel('code_famille', $d2->sortie_accompagnement->id_code_famille);
												if(empty($id_d2_code_famille)){
													$bool_d2 = false;
													$rapport = $this->AddErreur($rapport, 'd2', 'code_famille_inconnu', $personne_id);
												} else {
													$infos_d2['Emploiromev3']['familleromev3_id'] = $id_d2_code_famille;
												}

												if(isset($d2->sortie_accompagnement->id_code_domaine)){
													$d2_code_domaine = $this->CorrespondanceReferentiel->find(
														'first',
														[
															'fields' => ['id_dans_table', 'code_domaine_codefamille_id'],
															'conditions' => [
																'SujetReferentiel.code' => 'code_domaine',
																'CorrespondanceReferentiel.id' => $d2->sortie_accompagnement->id_code_domaine
															],
															'recursive' => 1
														]
													);

													if(empty($d2_code_domaine)){
														$bool_d2 = false;
														$rapport = $this->AddErreur($rapport, 'd2', 'code_domaine_inconnu', $personne_id);
													} else if($d2_code_domaine['CorrespondanceReferentiel']['code_domaine_codefamille_id'] !== $id_d2_code_famille){
														$bool_d2 = false;
														$rapport = $this->AddErreur($rapport, 'd2', 'code_domaine_incohérent', $personne_id);
													} else {
														$infos_d2['Emploiromev3']['domaineromev3_id'] = $d2_code_domaine['CorrespondanceReferentiel']['id_dans_table'];
													}

													if($bool_d2 !== false && isset($d2->sortie_accompagnement->id_code_metier)){
														//on vérifie le code metier
														$d2_code_metier = $this->CorrespondanceReferentiel->find(
															'first',
															[
																'fields' => ['id_dans_table', 'code_metier_codedomaine_id'],
																'conditions' => [
																	'SujetReferentiel.code' => 'code_metier',
																	'CorrespondanceReferentiel.id' => $d2->sortie_accompagnement->id_code_metier
																],
																'recursive' => 1
															]
														);
														if(empty($d2_code_metier)){
															$bool_d2 = false;
															$rapport = $this->AddErreur($rapport, 'd2', 'code_metier_inconnu', $personne_id);
														} else if($d2_code_metier['CorrespondanceReferentiel']['code_metier_codedomaine_id'] !== $d2_code_domaine['CorrespondanceReferentiel']['id_dans_table']){
															$bool_d2 = false;
															$rapport = $this->AddErreur($rapport, 'd2', 'code_metier_incohérent', $personne_id);
														} else {
															$infos_d2['Emploiromev3']['metierromev3_id'] = $d2_code_metier['CorrespondanceReferentiel']['id_dans_table'];
														}

														if($bool_d2 !== false && isset($d2->sortie_accompagnement->id_appellation_metier)){
															//on vérifie l'appellation métier
															$d2_appellation_metier = $this->CorrespondanceReferentiel->find(
																'first',
																[
																	'fields' => ['id_dans_table', 'appell_metier_codemetier_id'],
																	'conditions' => [
																		'SujetReferentiel.code' => 'appellation_metier',
																		'CorrespondanceReferentiel.id' => $d2->sortie_accompagnement->id_appellation_metier
																	],
																	'recursive' => 1
																]
															);
															if(empty($d2_appellation_metier)){
																$bool_d2 = false;
																$rapport = $this->AddErreur($rapport, 'd2', 'code_appellation_metier_inconnu', $personne_id);
															} else if($d2_appellation_metier['CorrespondanceReferentiel']['appell_metier_codemetier_id'] !== $d2_code_metier['CorrespondanceReferentiel']['id_dans_table']){
																$bool_d2 = false;
																$rapport = $this->AddErreur($rapport, 'd2', 'appellation_metier_incohérent', $personne_id);
															} else {
																$infos_d2['Emploiromev3']['appellationromev3_id'] = $d2_appellation_metier['CorrespondanceReferentiel']['id_dans_table'];
															}
														}
													}
												}
											}

										} else {
											$bool_d2 = false;
											$rapport = $this->AddErreur($rapport, 'd2', 'sortie_obligation_acc_obligatoire', $personne_id);
										}

									}

									//changement de situation
									if($bool_d2 !== false && $code_statut_accompagnement == 'changement_situation'){
										//sortie changement administratif
										if(isset($d2->sortie_changement_admin)){
											$code_sortie_changement_admin = $this->CorrespondanceReferentiel->getCodeFromIdReferentiel(
												'motif_changement_admin',
												intval($d2->sortie_changement_admin->__toString())
											);

											if(empty($code_sortie_changement_admin)){
												$bool_d2 = false;
												$rapport = $this->AddErreur($rapport, 'd2', 'sortie_changement_admin_inconnu', $personne_id);
											} else {
												$infos_d2['Questionnaired2pdv93']['chgmentsituationadmin'] = $code_sortie_changement_admin;
											}

										} else {
											$bool_d2 = false;
											$rapport = $this->AddErreur($rapport, 'd2', 'sortie_changement_admin_obligatoire', $personne_id);
										}

									}

									if($bool_d2 !== false){
										//On récupère les données de base par défaut dans le formulaire et on merge avec les données récupérées
										$form_data_d2 = $this->Questionnaired2pdv93->prepareFormData($personne_id, $id_d2);
										$infos_d2['Questionnaired2pdv93'] = array_merge($form_data_d2['Questionnaired2pdv93'], $infos_d2['Questionnaired2pdv93']);

										//On ajoute les informations de l'emploiromev3
										$infos_d2['Questionnaired2pdv93']['emploiromev3_id'] = $this->Questionnaired2pdv93->getEmploiromev3Id( $infos_d2 );
										if(!empty($infos_d2['Questionnaired2pdv93']['emploiromev3_id'])){
											unset($infos_d2['Emploiromev3']);
										}
										//on enregistre
										$bool_d2 = $this->Questionnaired2pdv93->saveAll($infos_d2);
										$this->Questionnaired2pdv93->clear();
									}

								}
								/*---------------------------------------------
								-----------------formulaire b7-----------------
								----------------------------------------------*/
								if(isset($dossier->formulaire_b7)){
									$b7 = $dossier->formulaire_b7;

									//On récupère les ids et on regarde si le formulaire existe déjà en base
									$id_b7_webrsa = isset($b7->id_webrsa) ? intval($b7->id_webrsa->__toString()) : null;
									$id_b7_ali = intval($b7->id_ali->__toString());

									$infos_b7['Questionnaireb7pdv93']['id_base_ali'] = $id_b7_ali;

									$id_b7 = null;

									$b7_existe = $this->Questionnaireb7pdv93->find(
										'first',
										[
											'conditions' => [
												'OR' => [
													'Questionnaireb7pdv93.id' => $id_b7_webrsa,
													'Questionnaireb7pdv93.id_base_ali' => $id_b7_ali
												]
											]
										]
									);

									if(!empty($b7_existe)){
										//On ajoute l'id pour la modification
										$id_b7 = $b7_existe['Questionnaireb7pdv93']['id'];
										$infos_b7['Questionnaireb7pdv93']['id'] = $id_b7;
									}

									//On vérifie les référentiels

									//Type d'emploi
									$id_b7_type_emploi = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel(
										'type_emploi',
										intval($b7->id_type_emploi->__toString())
									);

									if(empty($id_b7_type_emploi)){
										$bool_b7 = false;
										$rapport = $this->AddErreur($rapport, 'b7', 'type_emploi_inconnu', $personne_id);
									} else {
										$infos_b7['Questionnaireb7pdv93']['typeemploi_id'] = $id_b7_type_emploi;
									}

									//temps de travail
									$id_b7_temps_travail = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel(
										'temps_travail',
										intval($b7->id_temps_travail->__toString())
									);

									if(empty($id_b7_temps_travail)){
										$bool_b7 = false;
										$rapport = $this->AddErreur($rapport, 'b7', 'temps_travail_inconnu', $personne_id);
									} else {
										$infos_b7['Questionnaireb7pdv93']['dureeemploi_id'] = $id_b7_temps_travail;
									}

									//date d'embauche -> bornes
									$interval_39_years = DateInterval::createFromDateString('39 years');
									$interval_1_year = DateInterval::createFromDateString('1 years');
									$minus_39_years = $now_datetime->sub($interval_39_years)->format("Y-m-d");
									$plus_1_year = $now_datetime->add($interval_1_year)->format("Y-m-d");

									if($b7->date_embauche->__toString() < $minus_39_years || $b7->date_embauche->__toString() > $plus_1_year){
										$bool_b7 = false;
										$rapport = $this->AddErreur($rapport, 'b7', 'date_embauche_incorrecte', $personne_id);
									} else {
										$infos_b7['Questionnaireb7pdv93']['dateemploi'] = $b7->date_embauche->__toString();
									}

									//On vérifie les codes emplois
									$id_b7_code_famille = $this->CorrespondanceReferentiel->getIdTableFromIdReferentiel('code_famille', $b7->id_code_famille);
									if(empty($id_b7_code_famille)){
										$bool_b7 = false;
										$rapport = $this->AddErreur($rapport, 'b7', 'code_famille_inconnu', $personne_id);
									} else {
										$infos_b7['Emploiromev3']['familleromev3_id'] = $id_b7_code_famille;
									}

									if(isset($b7->id_code_domaine)){
										$b7_code_domaine = $this->CorrespondanceReferentiel->find(
											'first',
											[
												'fields' => ['id_dans_table', 'code_domaine_codefamille_id'],
												'conditions' => [
													'SujetReferentiel.code' => 'code_domaine',
													'CorrespondanceReferentiel.id' => $b7->id_code_domaine
												],
												'recursive' => 1
											]
										);

										if(empty($b7_code_domaine)){
											$bool_b7 = false;
											$rapport = $this->AddErreur($rapport, 'b7', 'code_domaine_inconnu', $personne_id);
										} else if($b7_code_domaine['CorrespondanceReferentiel']['code_domaine_codefamille_id'] !== $id_b7_code_famille){
											$bool_b7 = false;
											$rapport = $this->AddErreur($rapport, 'b7', 'code_domaine_incohérent', $personne_id);
										} else {
											$infos_b7['Emploiromev3']['domaineromev3_id'] = $b7_code_domaine['CorrespondanceReferentiel']['id_dans_table'];
										}

										if($bool_b7 !== false && isset($b7->id_code_metier)){
											//on vérifie le code metier
											$b7_code_metier = $this->CorrespondanceReferentiel->find(
												'first',
												[
													'fields' => ['id_dans_table', 'code_metier_codedomaine_id'],
													'conditions' => [
														'SujetReferentiel.code' => 'code_metier',
														'CorrespondanceReferentiel.id' => $b7->id_code_metier
													],
													'recursive' => 1
												]
											);
											if(empty($b7_code_metier)){
												$bool_b7 = false;
												$rapport = $this->AddErreur($rapport, 'b7', 'code_metier_inconnu', $personne_id);
											} else if($b7_code_metier['CorrespondanceReferentiel']['code_metier_codedomaine_id'] !== $b7_code_domaine['CorrespondanceReferentiel']['id_dans_table']){
												$bool_b7 = false;
												$rapport = $this->AddErreur($rapport, 'b7', 'code_metier_incohérent', $personne_id);
											} else {
												$infos_b7['Emploiromev3']['metierromev3_id'] = $b7_code_metier['CorrespondanceReferentiel']['id_dans_table'];
											}

											if($bool_b7 !== false && isset($b7->id_appellation_metier)){
												//on vérifie l'appellation métier
												$b7_appellation_metier = $this->CorrespondanceReferentiel->find(
													'first',
													[
														'fields' => ['id_dans_table', 'appell_metier_codemetier_id'],
														'conditions' => [
															'SujetReferentiel.code' => 'appellation_metier',
															'CorrespondanceReferentiel.id' => $b7->id_appellation_metier
														],
														'recursive' => 1
													]
												);
												if(empty($b7_appellation_metier)){
													$bool_b7 = false;
													$rapport = $this->AddErreur($rapport, 'b7', 'code_appellation_metier_inconnu', $personne_id);
												} else if($b7_appellation_metier['CorrespondanceReferentiel']['appell_metier_codemetier_id'] !== $b7_code_metier['CorrespondanceReferentiel']['id_dans_table']){
													$bool_b7 = false;
													$rapport = $this->AddErreur($rapport, 'b7', 'appellation_metier_incohérent', $personne_id);
												} else {
													$infos_b7['Emploiromev3']['appellationromev3_id'] = $b7_appellation_metier['CorrespondanceReferentiel']['id_dans_table'];
												}
											}
										}
									}

									//On récupère l'id de l'entrée romev3
									$infos_b7['Questionnaireb7pdv93']['expproromev3_id'] = $this->Questionnaired2pdv93->getEmploiromev3Id($infos_b7);
									$infos_b7['Questionnaireb7pdv93']['personne_id'] = $personne_id;

									//On enregistre
									if($bool_b7 !== false){
										$bool_b7 = $this->Questionnaireb7pdv93->saveAll($infos_b7);
										$this->Questionnaireb7pdv93->clear();
									}


								}

								//On ajoute la personne
								$rapport['PersonneEchangeALI'][] = [
									'personne_id' => $personne_id,
									'referentparcours' => $bool_referentparcours,
									'rendezvous' => $bool_rdv,
									'dsp' => $bool_dsp,
									'cer' => $bool_cers,
									'orient' => $bool_orient,
									'd1' => $bool_d1,
									'd2' => $bool_d2,
									'b7' => $bool_b7
								];

								if(
									$bool_referentparcours === false
									|| $bool_rdv === false
									|| $bool_dsp === false
									|| $bool_cers === false
									|| $bool_orient === false
									|| $bool_d1 === false
									|| $bool_d2 === false
									|| $bool_b7 === false
								){
									//On ajoute le dossier au nombre d'erreurs
									$dossiers_erreurs++;
								}

							}//Fin du traitement d'un dossier avec personne connue

						} //Fin du foreach des dossiers

						$alertes[$file]['dossiers_erreurs'] = 0;

						if($dossiers_erreurs != 0 || isset($alertes[$file]['personne_inconnue'])){
							//On ajoute le nombre total de personnes
							$alertes[$file]['nb_dossiers'] = count($rapport['PersonneEchangeALI']) + (isset($alertes[$file]['personne_inconnue']) ? count($alertes[$file]['personne_inconnue']) : 0);

							//On ajoute le nombre d'erreurs
							$alertes[$file]['dossiers_erreurs'] = $dossiers_erreurs;

							//On ajoute le fichier d'erreurs
							$alertes[$file]['fichier_erreur'] = $nom_fichier_erreurs;
							$alertes[$file]['alias_fichier_erreur'] = $alias_fichier_erreurs;

							//On récupère les erreurs
							$alertes[$file]['rapport'] = isset($rapport['ErreurEchangeALI']) ? $rapport['ErreurEchangeALI'] : null;

						}

						// Ecriture dans la table de rapports
						$rapport['RapportEchangeALI'] = [
							'nom_fichier' => $file,
							'type' => 'import',
							'debut' => $now,
							'ali_id' => $id_ali,
							'stock' => $stock,
							'date_fichier' => $date_generation
						];

						$this->RapportEchangeALI->saveAssociated($rapport);
						$this->RapportEchangeALI->clear();

					}

					//La date
					$alertes[$file]['date'] = $now;
					//Le destinataire
					$alertes[$file]['to'] = !empty($user) ? $user['email'] : '';
					//L'ALI
					$alertes[$file]['ali'] = $this->Structurereferente->findById($id_ali)['Structurereferente']['lib_struc'];
					$alertes[$file]['id_ali'] = $id_ali;

				}



			} //fin du foreach sur les fichiers



			//On récupère toutes les ALI et on vérifie qu'il y a au moins un fichier pour chaque
			$alis_manquantes = array_diff($this->Structurereferente->getALIexport(true), $liste_ali);



			//Un fichier et un mail par fichier
			if(!empty($alertes) || !empty($alis_manquantes)) {
				//Création des fichiers d'erreur
				$liste_fichiers = $this->fichierErreur($alertes);
				$this->preparationMail($alertes, $liste_fichiers, $alis_manquantes);
			}
		}

		public function preparationMail($alertes, $liste_fichiers, $alis_manquantes){
			if($alertes != 'dossier_vide'){
				foreach ($alertes as $fichier => $alerte){
					$attachments = null;
					$mailBody = 'Fichier : '.$fichier.'<br>';

					if(isset($alerte['code']) && $alerte['code'] == 'validation_schema'){
						//message problème schéma
						$mailBody .= __d('rapportsechangesali', 'validation_schema');
					} else {
						$mailBody .= 'Date d\'intégration : '.$alerte['date'].'<br>';
						$mailBody .= 'Structure : '.$alerte['ali'].'<br>';

						 if (isset($alerte['code']) && $alerte['code'] == 'deja_traite') {
							//message fichier déjà traité
							$mailBody .= __d('rapportsechangesali', 'deja_traite');
						} else if (isset($alerte['code']) && $alerte['code'] == 'utilisateur_inconnu') {
							//message fichier déjà traité
							$mailBody .= __d('rapportsechangesali', 'utilisateur_inconnu');
						} else if ($alerte['dossiers_erreurs'] != 0 || isset($alerte ['personne_inconnue'])) {
							//on récupère les erreurs (nombre de dossiers + nombre de personnes inconnues)
							//On joint le fichier de détails
							$nb_personnes_inconnues = isset($alerte ['personne_inconnue']) ? count($alerte ['personne_inconnue']) : 0;
							$mailBody .= $alerte['dossiers_erreurs'].' dossier(s) en erreur <br>'.$nb_personnes_inconnues.' personne(s) inconnue(s) <br><br>';
							if(isset($alerte['fichier_erreur']) && in_array($alerte['fichier_erreur'], $liste_fichiers)){
								$attachments = [
									$alerte['alias_fichier_erreur'] => [
										'file' => $alerte['fichier_erreur'],
										'mimetype' => 'text/comma-separated-values'
									]
								];
							}
						}
					}

					if((isset($alerte['dossiers_erreurs']) && $alerte['dossiers_erreurs'] != 0) || isset($alerte ['personne_inconnue']) || isset($alerte['code'])){

						//On récupère le mail de l'ali dans le user;
						$to = isset($alerte['to']) ? $alerte['to'] : null;
						$this->envoiMail($mailBody, $to, $alerte['id_ali'], $attachments);
					}
				}

			}

			//Si il y a des alis manquantes, on récupère le mail et on envoie une alerte
			if(!empty($alis_manquantes)){
					$now_datetime = new DateTimeImmutable();
					$now = $now_datetime->format('Y-m-d_H:i:s');
				foreach($alis_manquantes as $ali_manquante){
					$to = $this->User->getUserByALI($ali_manquante)['email'];

					$mailBody = 'Date : '.$now.'<br>';
					$mailBody .= 'Structure : '.$this->Structurereferente->findById($ali_manquante)['Structurereferente']['lib_struc'].'<br>';
					$mailBody .= __d('rapportsechangesali', 'fichier_manquant');
					$this->envoiMail($mailBody, $to, $ali_manquante);
				}
			}



		}

		public function envoiMail($mailBody, $to, $id_ali, $attachments = null){
			$success = false;

			try {
				$Email = new CakeEmail('echange_ali');
				$Email->emailFormat('html');
				$Email->config([
					'subject' => sprintf(__d('rapportsechangesali','mail.objet'), $id_ali)
				]);
				if(!empty($to)){
					$Email->config([
						'to' => $to,
					]);
				}
				$Email->attachments($attachments);


				$result = $Email->send( $mailBody );
				$success = !empty( $result );
			} catch( Exception $e ) {
				$this->log( $e->getMessage(), LOG_ERROR );
				$success = false;
			}

			if( $success ) {
				$this->out( 'Mail envoyé' );
			}
			else {
				$this->out( 'Mail non envoyé' );
			}
		}

		public function AddErreur($rapport, $bloc, $message, $personne_id, $commentaire = null){

			if(is_null($personne_id)){
				$rapport['ErreurEchangeALI'][] = ['bloc' => $bloc, 'code' => $message, 'commentaire' => $commentaire];
			} else {
				$rapport['ErreurEchangeALI'][] = ['bloc' => $bloc, 'code' => $message, 'personne_id' => $personne_id, 'commentaire' => $commentaire];
			}

			return $rapport;
		}

		public function fichierErreur($alertes){
			$liste_fichiers = [];
			//Création des fichiers csv contenant toutes les erreurs à corriger
			if(!empty($alertes) && is_array($alertes)){
				foreach($alertes as $file => $alerte){
					$lignes = [];

					if(isset($alerte['rapport'])){
						$lignes[0] = [
							'Bloc',
							'Personne_id',
							'Erreur',
						];

						foreach($alerte['rapport'] as $erreur){
							$lignes[] = [
								__d('rapportsechangesali', 'Erreur.'.$erreur['bloc']),
								$erreur['code'] == 'personne_inconnue' ? $erreur['commentaire'] : $erreur['personne_id'],
								__d('rapportsechangesali', $erreur['code'])
							];
						}

						$fichier_erreur = fopen($alerte['fichier_erreur'], "w");


						foreach($lignes as $ligne){
							fputcsv($fichier_erreur, $ligne, ";");
						}
						fclose($fichier_erreur);

						$liste_fichiers[] = $alerte['fichier_erreur'];
					}
				}
			}

			return $liste_fichiers;
		}

	}