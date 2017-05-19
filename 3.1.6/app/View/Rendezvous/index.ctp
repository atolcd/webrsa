<?php
	$this->pageTitle = 'Rendez-vous de la personne';

	$messages = array();
	if (!empty($dossierep)) {
		$messages['Ce dossier est en cours de passage en EP : '.$dossierep['StatutrdvTyperdv']['motifpassageep']] = 'error';
	}
	if (!empty($dossiercov)) {
		$messages['Ce dossier est en cours de passage en COV: '.$dossiercov['StatutrdvTyperdv']['motifpassageep']] = 'error';
	}
	$paramsElement = array(
		'messages' => $messages,
	);
	echo $this->element('default_index', $paramsElement);

	echo $this->Default3->index(
		$rdvs,
		$this->Translator->normalize(
			array(
				'Personne.nom_complet',
				'Structurereferente.lib_struc',
				'Referent.nom_complet',
				'Permanence.libpermanence',
				'Typerdv.libelle',
				'Statutrdv.libelle',
				'Rendezvous.daterdv',
				'Rendezvous.heurerdv' => array('format' => '%Hh%M'),
			) + WebrsaAccess::links(
				array(
					'/Rendezvous/view/#Rendezvous.id#',
					'/Rendezvous/edit/#Rendezvous.id#',
					'/Rendezvous/impression/#Rendezvous.id#',
					'/Rendezvous/delete/#Rendezvous.id##d1' => array(
						'condition' => "'#Rendezvous.has_questionnaired1pdv93#' == true",
						'confirm' => true,
					),
					'/Rendezvous/delete/#Rendezvous.id#' => array(
						'condition' => "'#Rendezvous.has_questionnaired1pdv93#' == false",
						'confirm' => true,
					),
					'/Rendezvous/filelink/#Rendezvous.id#',
				)
			)
		),
		array(
			'paginate' => false,
			'empty_label' => __m('Rendezvous::index::emptyLabel'),
			'innerTable' => $this->Translator->normalize(
				array_merge(
					array(
						'Rendezvous.objetrdv' => array(
							'format' => 'truncate'
						),
						'Rendezvous.commentairerdv' => array(
							'format' => 'truncate'
						)
					),
					(
						Configure::read( 'Rendezvous.useThematique' )
						? array(
							'Rendezvous.thematiques' => array(
								'type' => 'list'
							)
						)
						: array()
					)
				)
			)
		)
	);