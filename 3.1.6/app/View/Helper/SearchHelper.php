<?php
	/**
	 * Fichier source de la classe SearchHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe SearchHelper comprend des combinaisons de champs de formulaire
	 * de recherche pour WebRSA.
	 *
	 * @package app.View.Helper
	 */
	class SearchHelper extends AppHelper
	{
		/**
		 * Les helpers utilisés par ce helper.
		 *
		 * @var array
		 */
		public $helpers = array( 'Xhtml', 'Html', 'Xform', 'Form' );

		/**
		 * Retourne le code javascript permettant d'activer ou de désactiver le fieldset contentant les cases à
		 * cocher (états du dossier, natures de la prestation, ...) suivant la valeur de la case à cocher
		 * "parente" ("choice").
		 *
		 * @param string $observeId
		 * @param string $updateId
		 * @param boolean $up
		 * @return string
		 */
		protected function _constuctObserve( $observeId, $updateId, $up = true ) {
			$stringUp = '';
			if( $up ) {
				$stringUp = ".up( 'fieldset' )";
			}
			$out = "document.observe('dom:loaded', function() {
				observeDisableFieldsetOnCheckbox( '{$observeId}', $( '{$updateId}' ){$stringUp}, false );
			});";
			return "<script type='text/javascript'>{$out}</script>";
		}

		/**
		 * Filtre sur les états du dossier RSA
		 *
		 * @param string $etatdosrsa
		 * @param string $path
		 * @return string
		 */
		public function etatdosrsa( $etatdosrsa, $path = 'Situationdossierrsa.etatdosrsa' ) {
			$fieldsetId = $this->domId( $path );

			$script = $this->_constuctObserve( $this->domId( $path.'_choice' ), $fieldsetId, false );

			$input = $this->Xform->input( $path.'_choice', array( 'label' => 'Filtrer par état du dossier', 'type' => 'checkbox' ) );

			$etatsCoches = Set::extract( $this->request->data, $path );
			if( empty( $etatsCoches ) ) {
				$etatsCoches = array_keys( $etatdosrsa );
			}
			// TODO: voir hiddenField pour éviter que lorsqu'on ne coche que "Non défini", on se retrouve avec "Nlle demande cochée"
			$input.= $this->Xhtml->tag(
				'fieldset',
				$this->Xhtml->tag( 'legend', 'État du dossier RSA' ).
				$this->Xform->input( $path, array( 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'options' => $etatdosrsa, 'value' => $etatsCoches, 'fieldset' => false ) ),
				array( 'id' => $fieldsetId )
			);

			return $script.$input;
		}

		/**
		 * Filtre sur les natures de prestation
		 *
		 * @param string $natpf
		 * @param string $path
		 * @return string
		 */
		public function natpf( $natpf, $path = 'Detailcalculdroitrsa.natpf' ) {
			$fieldsetId = $this->domId( $path );

			$script = $this->_constuctObserve( $this->domId( $path.'_choice' ), $fieldsetId, false );

			$input = $this->Xform->input( $path.'_choice', array( 'label' => 'Filtrer par nature de prestation', 'type' => 'checkbox' ) );

			$natpfsCoches = Set::extract( $this->request->data, $path );
			if( empty( $natpfsCoches ) ) {
				$natpfsCoches = array_keys( $natpf );
			}

			$input.= $this->Xhtml->tag(
				'fieldset',
				$this->Xhtml->tag( 'legend', 'Nature de la prestation' ).
				$this->Xform->input( $path, array( 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'options' => $natpf, 'value' => $natpfsCoches, 'fieldset' => false ) ),
				array( 'id' => $fieldsetId )
			);

			return $script.$input;
		}

		/**
		 * Filtre sur les états du dossierpcg66
		 *
		 * @param string $etatdossierpcg
		 * @param string $path
		 * @return string
		 */
		public function etatDossierPCG66( $etatdossierpcg, $path = 'Dossierpcg66.etatdossierpcg' ) {
			$fieldsetId = $this->domId( $path );

			$script = $this->_constuctObserve( $this->domId( $path.'_choice' ), $fieldsetId, false );

			$input = $this->Xform->input( $path.'_choice', array( 'label' => 'Filtrer par état du dossier PCG', 'type' => 'checkbox' ) );

			$etatsDossiersPCGCoches = Set::extract( $this->request->data, $path );
			if( empty( $etatsDossiersPCGCoches ) ) {
				$etatsDossiersPCGCoches = array_keys( $etatdossierpcg );
			}

			$input.= $this->Xhtml->tag(
				'fieldset',
				$this->Xhtml->tag( 'legend', 'État du dossier PCG' ).
				$this->Xform->input( $path, array( 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'options' => $etatdossierpcg, 'fieldset' => false ) ),
				array( 'id' => $fieldsetId )
			);

			return $script.$input;
		}

		/**
		 * Retourne un fieldset de recherche par adresse contenant les champs suivants:
		 * Adresse.nomcom, Adresse.numcom et Canton.canton (si on utilise les cantons).
		 *
		 * @param array $mesCodesInsee La liste des codes INSEE pour remplir le menu déroulant
		 * @param array $cantons La liste des cantons pour remplir le menu déroulant
		 * @param string $prefix Le préfixe éventuel (ex.: Search)
		 * @param boolean $fieldset Doit-on entourer ces champs par un fieldset ?
		 * @return string
		 */
		public function blocAdresse( $mesCodesInsee, $cantons, $prefix = null, $fieldset = true ) {
			$prefix = ( !empty( $prefix ) ? "{$prefix}." : null );
            $content = $this->Xform->input( "{$prefix}Adresse.nomvoie", array( 'label' => 'Nom de voie de l\'allocataire ', 'type' => 'text' ) );
			$content .= $this->Xform->input( "{$prefix}Adresse.nomcom", array( 'label' => 'Commune de l\'allocataire ', 'type' => 'text' ) );
			$content .= $this->Xform->input( "{$prefix}Adresse.numcom", array( 'label' => 'Numéro de commune au sens INSEE', 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true ) );
			if( Configure::read( 'CG.cantons' ) ) {
				$content .= $this->Xform->input( "{$prefix}Canton.canton", array( 'label' => 'Canton', 'type' => 'select', 'options' => $cantons, 'empty' => true ) );
			}

			if( !$fieldset ) {
				return $content;
			}
			else {
				return $this->Xhtml->tag(
					'fieldset',
					$this->Xhtml->tag( 'legend', 'Recherche par Adresse' )
					.$content
				);
			}
		}

		/**
		 * Retourne une fieldset de recherche par allocataire contenant les champs
		 * suivants: Personne.dtnai, Personne.nom, Personne.nomnai, Personne.prenom,
		 * Personne.nir, Personne.sexe, Personne.trancheage.
		 *
		 * @param array $trancheage La liste des tranches d'âge à utiliser dans le menu déroulant
		 * @param array $sexe La liste des sexes à utiliser dans le menu déroulant
		 * @param string $prefix Le préfixe éventuel (ex.: Search)
		 * @return string
		 */
		public function blocAllocataire( $trancheage = array(), array $sexe = array(), $prefix = null ) {
			$prefix = ( !empty( $prefix ) ? "{$prefix}." : null );

			$content = $this->Xform->input( "{$prefix}Personne.dtnai", array( 'label' => 'Date de naissance', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'empty' => true ) );
			$content .= $this->Xform->input( "{$prefix}Personne.nom", array( 'label' => 'Nom' ) );
			$content .= $this->Xform->input( "{$prefix}Personne.nomnai", array( 'label' => 'Nom de naissance' ) );
			$content .= $this->Xform->input( "{$prefix}Personne.prenom", array( 'label' => 'Prénom' ) );
			$content .= $this->Xform->input( "{$prefix}Personne.nir", array( 'label' => 'NIR', 'maxlength' => 15 ) );

			if( !empty( $sexe ) ) {
				$content .= $this->Xform->input( "{$prefix}Personne.sexe", array( 'label' => 'Sexe', 'options' => $sexe, 'empty' => true ) );
			}

			if( !empty( $trancheage ) ) {
				$content .= $this->Xform->input( "{$prefix}Personne.trancheage", array( 'label' => 'Tranche d\'âge', 'options' => $trancheage, 'empty' => true ) );
			}

			return $this->Xhtml->tag(
				'fieldset',
				$this->Xhtml->tag( 'legend', 'Recherche par allocataire' )
				.$content
			);
		}

		/**
		 * Filtre par plage de dates.
		 *
		 * @see app/views/criteres/index.ctp
		 * @see app/views/cohortes/filtre.ctp
		 * @todo Dossier.dtdemrsa, ...
		 * @todo $options from.minYear, ....
		 *
		 * @param string $path
		 * @return string
		 */
		public function date( $path, $fieldLabel = null, array $params = array() ) {
			$params += array(
				'minYear_from' => date( 'Y' ) - 120,
				'maxYear_from' => date( 'Y' ),
				'minYear_to' => date( 'Y' ) - 120,
				'maxYear_to' => date( 'Y' ) + 5,
			);
			$fieldsetId = $this->domId( $path ).'_from_to';

			$script = $this->_constuctObserve( $this->domId( $path ), $fieldsetId, false );

			list( $model, $field ) = model_field( $path);
			$domain = Inflector::underscore( $model );
			if( empty( $fieldLabel ) ) {
				$fieldLabel = __d( $domain, "{$model}.{$field}" );
			}

			$input = $this->Xform->input( $path, array( 'label' => 'Filtrer par '.lcfirst( $fieldLabel ), 'type' => 'checkbox' ) );

			$input .= $this->Xhtml->tag(
				'fieldset',
				$this->Xhtml->tag( 'legend', $fieldLabel )
				.$this->Xform->input( $path.'_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => $params['maxYear_from'], 'minYear' => $params['minYear_from'], 'default' => strtotime( '-1 week' ) ) )
				.$this->Xform->input( $path.'_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => $params['maxYear_to'], 'minYear' => $params['minYear_to'] ) ),
				array( 'id' => $fieldsetId )
			);

			return $script.$input;
		}

		/**
		 * Fieldset contenant un checkbox permettant de préciser si l'on veut obtenir le nombre total de
		 * résultats.
		 *
		 * @param string $path
		 * @return string
		 */
		public function paginationNombretotal( $path = 'Pagination.nombre_total' ) {
			return $this->Xhtml->tag(
				'fieldset',
				$this->Xhtml->tag( 'legend', 'Comptage des résultats' )
				.$this->Xform->input( $path, array( 'label' =>  __d( 'search_plugin', 'Pagination.nombre_total' ), 'type' => 'checkbox' ) )
			);
		}

		/**
		 * Retourne une liste déroulante permettant de choisir si l'allocataire est soumis à droits et devoirs.
		 *
		 * @param array $toppersdrodevorsa
		 * @param string $path
		 * @return string
		 */
		public function toppersdrodevorsa( $toppersdrodevorsa, $path = 'Calculdroitrsa.toppersdrodevorsa' ) {
			list( $model, $field ) = model_field( $path );
			$domain = Inflector::underscore( $model );
			$fieldLabel = __d( $domain, "{$model}.{$field}" );

			// INFO: ne pas passer par XformHelper, sinon 'Non défini' devient 'Non'
			return $this->Form->input(
				$path,
				array(
					'label' => $fieldLabel,
					'type' => 'select',
					'options' => $toppersdrodevorsa,
					'empty' => true,
				)
			);
		}

		/**
		 * Filtre sur les statuts du CER CG93
		 *
		 * @param string $positioncer
		 * @param string $path
		 * @return string
		 */
		public function statutCER93( $positioncer, $path = 'Cer93.positioncer' ) {
			$fieldsetId = $this->domId( $path );

			$script = $this->_constuctObserve( $this->domId( $path.'_choice' ), $fieldsetId, false );

			$input = $this->Xform->input( $path.'_choice', array( 'label' => 'Filtrer par statut du CER', 'type' => 'checkbox' ) );

			$positionscersCoches = Set::extract( $this->request->data, $path );
			if( empty( $positionscersCoches ) ) {
				$positionscersCoches = array_keys( $positioncer );
			}

			$input.= $this->Xhtml->tag(
				'fieldset',
				$this->Xhtml->tag( 'legend', 'Statut du CER' ).
				$this->Xform->input( $path, array( 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'options' => $positioncer, 'fieldset' => false ) ),
				array( 'id' => $fieldsetId )
			);

			return $script.$input;
		}

		/**
		 * Retourne une fieldset de recherche par dossier contenant les champs suivants:
		 *
		 * @param string $prefix Le préfixe éventuel (ex.: Search)
		 * @return string
		 */
		public function blocDossier( $etatsdosrsa = array(), $prefix = null ) {
			$prefix = ( !empty( $prefix ) ? "{$prefix}." : null );

			$content = $this->Xform->input( "{$prefix}Dossier.numdemrsa", array( 'label' => 'Numéro de dossier RSA' ) );
			$content .= $this->Xform->input( "{$prefix}Dossier.matricule", array( 'label' => __d( 'dossier', 'Dossier.matricule.large' ) ) );
			$content .= $this->date( "{$prefix}Dossier.dtdemrsa" );
			
			if (!empty($etatsdosrsa)) {
				$content .= $this->etatdosrsa( $etatsdosrsa, "{$prefix}Situationdossierrsa.etatdosrsa" );
			}
			
			$content .= $this->Xform->input( "{$prefix}Dossier.dernier", array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox' ) );


			return $this->Xhtml->tag(
				'fieldset',
				$this->Xhtml->tag( 'legend', 'Recherche par dossier' )
				.$content
			);
		}

		/**
		 * Méthode générique permettant de retourner un ensemble de cases à cocher au sein d'un
		 * fieldset, activées ou désactivées par une autre case à cocher située au-dessus du fieldset.
		 *
		 * Les traductions - "{$path}_choice" pour la case à cocher d'activation/désactivation et
		 * $path pour le fieldset sont faites dans le fichier de traduction correspondant au nom
		 * du contrôleur.
		 *
		 * @param $options array Les différentes valeurs pour les cases à cocher.
		 * @param $path string Le chemin au sens formulaire CakePHP
		 * @return string
		 */
		public function multipleCheckboxChoice( $options, $path ) {
			$fieldsetId = $this->domId( $path );

			$script = $this->_constuctObserve( $this->domId( $path.'_choice' ), $fieldsetId, false );

			$domain = Inflector::underscore( $this->request->params['controller'] );

			$input = $this->Xform->input( $path.'_choice', array( 'label' => __d( $domain, $path.'_choice' ), 'type' => 'checkbox' ) );

			$cochees = Set::extract( $this->request->data, $path );
			if( empty( $cochees ) ) {
				$cochees = array_keys( $options );
			}

			$input.= $this->Xhtml->tag(
				'fieldset',
				$this->Xhtml->tag( 'legend', __d( $domain, $path ) ).
				$this->Xform->input( $path, array( 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'options' => $options, 'fieldset' => false ) ),
				array( 'id' => $fieldsetId )
			);

			return $script.$input;
		}

		/**
		 * Fournit le code javascript permettant de désactiver les boutons de
		 * soumission d'un formumlaire lors de son envoi afin de ne pas renvoyer
		 * celui-ci plusieurs fois avant que le reqête n'ait abouti.
		 *
		 * @param string $form L'id du formulaire au sens Prototype
		 * @param string $message Le message (optionnel) qui apparaîtra en haut du formulaire
		 * @return string
		 */
		public function observeDisableFormOnSubmit( $form, $message = null ) {
			if( empty( $message ) ) {
				$out = "document.observe( 'dom:loaded', function() {
					observeDisableFormOnSubmit( '{$form}' );
				} );";
			}
			else {
				$message = str_replace( "'", "\\'", $message );

				$out = "document.observe( 'dom:loaded', function() {
					observeDisableFormOnSubmit( '{$form}', '{$message}' );
				} );";
			}

			return "<script type='text/javascript'>{$out}</script>";
		}

		/**
		 * Retourne un fieldset contenant les filtres sur la structure référente
		 * du référent du parcours ainsi que le référent du parcours (listes
		 * déroulantes liées).
		 *
		 * @param array $structuresreferentesparcours
		 * @param array $referentsparcours
		 * @param string $prefix
		 * @return string
		 */
		public function referentParcours( array $structuresreferentesparcours, array $referentsparcours, $prefix = null ) {
			$prefix = ( !empty( $prefix ) ? "{$prefix}." : null );

			$script = "document.observe( 'dom:loaded', function() {
				dependantSelect( '".$this->domId( "{$prefix}PersonneReferent.referent_id" )."', '".$this->domId( "{$prefix}PersonneReferent.structurereferente_id" )."' );
			} );";

			$domain = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

			$labels = array(
				'structurereferente_id' => __d( $domain, 'Structurereferenteparcours.lib_struc' ),
				'referent_id' => __d( $domain, 'Referentparcours.nom_complet' )
			);

			return $this->Xhtml->tag(
				'fieldset',
				$this->Xhtml->tag( 'legend', 'Suivi du parcours' )
				.$this->Xform->input( "{$prefix}PersonneReferent.structurereferente_id", array( 'label' => $labels['structurereferente_id'], 'type' => 'select', 'options' => $structuresreferentesparcours, 'empty' => true ) )
				.$this->Xform->input( "{$prefix}PersonneReferent.referent_id", array( 'label' => $labels['referent_id'], 'type' => 'select', 'options' => $referentsparcours, 'empty' => true ) )
			).$this->Xhtml->scriptBlock( $script, array( 'inline' => true, 'safe' => false ) );
		}
	}
?>