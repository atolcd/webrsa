<?php
	/**
	 *
	 */
	Configure::write(
		'ConfigurableQuery.Covs58.visualisationdecisions',
		array(
			'proposorientationscovs58' => array(
				'Personne.nir',
				'Personne.nom_complet',
				'Adresse.complete',
				'Personne.dtnai',
				'Dossiercov58.created' => array( 'type' => 'date' ),
				'Decisionpropoorientationcov58.decisioncov',
				'NvTypeorient.lib_type_orient',
				'NvStructurereferente.lib_struc',
				'NvReferent.nom_complet',
				'/Covs58/impressiondecision/#Passagecov58.id#' => array(
					'title' => false,
					'class' => 'print'
				),
				'/Orientsstructs/index/#Personne.id#' => array(
					'title' => false,
					'class' => 'view'
				),
				'/Historiquescovs58/view/#Passagecov58.id#' => array(
					'title' => false
				)
			),
			'proposcontratsinsertioncovs58' => array(
				'Personne.nir',
				'Personne.nom_complet',
				'Adresse.complete',
				'Personne.dtnai',
				'Dossiercov58.created' => array(
					'type' => 'date'
				),
				'VxReferent.nom_complet' => array(
					'label' => 'Nom du prescripteur'
				),
				'Decisionpropocontratinsertioncov58.decisioncov',
				'Decisionpropocontratinsertioncov58.dd_ci',
				'Decisionpropocontratinsertioncov58.duree_engag',
				'Decisionpropocontratinsertioncov58.df_ci',
				'/Covs58/impressiondecision/#Passagecov58.id#' => array(
					'title' => false,
					'class' => 'print'
					// FIXME: conditions
				),
				'/Contratsinsertion/index/#Personne.id#' => array(
					'title' => false,
					'class' => 'view'
				),
				'/Historiquescovs58/view/#Passagecov58.id#' => array(
					'title' => false
				)
			),
			'proposnonorientationsproscovs58' => array(
				'Personne.nir',
				'Personne.nom_complet',
				'Adresse.complete',
				'Personne.dtnai',
				'Dossiercov58.created' => array( 'type' => 'date' ),
				'Decisionpropononorientationprocov58.decisioncov',
				'NvTypeorient.lib_type_orient',
				'NvStructurereferente.lib_struc',
				'NvReferent.nom_complet',
				'/Covs58/impressiondecision/#Passagecov58.id#' => array(
					'title' => false,
					'class' => 'print'
					// FIXME: conditions
				),
				'/Orientsstructs/index/#Personne.id#' => array(
					'title' => false,
					'class' => 'view'
				),
				'/Historiquescovs58/view/#Passagecov58.id#' => array(
					'title' => false
				)
			),
			'proposorientssocialescovs58' => array(
				'Personne.nir',
				'Personne.nom_complet',
				'Adresse.complete',
				'Personne.dtnai',
				'Dossiercov58.created' => array( 'type' => 'date' ),
				'Decisionpropoorientsocialecov58.decisioncov',
				'NvTypeorient.lib_type_orient',
				'NvStructurereferente.lib_struc',
				'NvReferent.nom_complet',
				'/Covs58/impressiondecision/#Passagecov58.id#' => array(
					'title' => false,
					'class' => 'print'
					// FIXME: conditions
				),
				'/Orientsstructs/index/#Personne.id#' => array(
					'title' => false,
					'class' => 'view'
				),
				'/Historiquescovs58/view/#Passagecov58.id#' => array(
					'title' => false
				)
			),
			'nonorientationsproscovs58' => array(
				'Personne.nir',
				'Personne.nom_complet',
				'Adresse.complete',
				'Personne.dtnai',
				'Dossiercov58.created' => array( 'type' => 'date' ),
				'Decisionnonorientationprocov58.decisioncov',
				'NvTypeorient.lib_type_orient',
				'NvStructurereferente.lib_struc',
				'NvReferent.nom_complet',
				'/Covs58/impressiondecision/#Passagecov58.id#' => array(
					'title' => false,
					'class' => 'print'
					// FIXME: conditions
				),
				'/Orientsstructs/index/#Personne.id#' => array(
					'title' => false,
					'class' => 'view'
				),
				'/Historiquescovs58/view/#Passagecov58.id#' => array(
					'title' => false
				)
			),
			'regressionsorientationscovs58' => array(
				'Personne.nir',
				'Personne.nom_complet',
				'Adresse.complete',
				'Personne.dtnai',
				'Dossiercov58.created' => array( 'type' => 'date' ),
				'Decisionregressionorientationcov58.decisioncov',
				'NvTypeorient.lib_type_orient',
				'NvStructurereferente.lib_struc',
				'NvReferent.nom_complet',
				'/Covs58/impressiondecision/#Passagecov58.id#' => array(
					'title' => false,
					'class' => 'print'
					// FIXME: conditions
				),
				'/Orientsstructs/index/#Personne.id#' => array(
					'title' => false,
					'class' => 'view'
				),
				'/Historiquescovs58/view/#Passagecov58.id#' => array(
					'title' => false
				)
			),
		)
	);
?>