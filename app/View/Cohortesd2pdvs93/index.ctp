<div id="Cohortesd2pdv93IndexAjaxContainer">
	<?php
		echo $this->element( 'required_javascript' );

		echo $this->Default3->titleForLayout();

		if( Configure::read( 'debug' ) > 0 ) {
			echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
			echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
		}

		$searchFormOptions = array( 'domain' => 'search_plugin' );

		echo $this->Default3->actions(
			array(
				'/Cohortesd2pdvs93/index/#toggleform' => array(
					'onclick' => '$(\'Cohortesd2pdvs93IndexForm\').toggle(); return false;'
				),
			)
		);

		if( ( isset( $this->request->data['Search'] ) && !empty( $this->request->params['named'] ) ) ) {
			$out = "document.observe( 'dom:loaded', function() { \$('Cohortesd2pdvs93IndexForm').hide(); } );";
			echo $this->Html->scriptBlock( $out );
		}

		echo $this->Xform->create( null, array( 'id' => 'Cohortesd2pdvs93IndexForm' ) );

		// Filtres concernant le dossier
		echo $this->Search->blocDossier( $options['etatdosrsa'], 'Search' );

		echo $this->Allocataires->blocAdresse( array( 'options' => $options ) );

		// Filtres concernant l'allocataire
		echo '<fieldset>';
		echo sprintf( '<legend>%s</legend>', __d( 'cohortesd2pdvs93', 'Search.Personne' ) );
		//echo $this->Xform->input( 'Search.Dossier.dernier', array( 'type' => 'checkbox', 'domain' => 'cohortesd2pdvs93' ) );
		echo $this->Search->blocAllocataire( array(), array(), 'Search' );
		echo $this->Search->toppersdrodevorsa( $options['Calculdroitrsa']['toppersdrodevorsa'], 'Search.Calculdroitrsa.toppersdrodevorsa' );
	//	echo $this->SearchForm->dependantDateRange( 'Search.Personne.dtnai', $searchFormOptions );
		echo '</fieldset>';

		// Filtres concernant l'accompagnement
		echo '<fieldset>';
		echo sprintf( '<legend>%s</legend>', __d( 'cohortesd2pdvs93', 'Search.Questionnaired2pdv93' ) );
		echo $this->Xform->input( 'Search.Questionnaired1pdv93.annee', array( 'options' => $options['Questionnaired1']['annee'], 'domain' => 'cohortesd2pdvs93' ) );
		echo $this->Allocataires->communautesr( 'Rendezvous', array( 'options' => array( 'Search' => $options ), 'hide' => false ) );
		echo $this->Xform->input( 'Search.Rendezvous.structurereferente_id', array( 'options' => $options['Rendezvous']['structurereferente_id'], 'empty' => true, 'domain' => 'cohortesd2pdvs93' ) );
		echo $this->Xform->input( 'Search.Questionnaired2pdv93.exists', array( 'type' => 'checkbox', 'domain' => 'cohortesd2pdvs93' ) );

		echo $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', __d( 'cohortesd2pdvs93', 'Search.ReponsesQuestionnaired2pdv93' ) )
			.$this->Xform->input( 'Search.Questionnaired2pdv93.situationaccompagnement', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Questionnaired2pdv93.situationaccompagnement' ), 'domain' => 'cohortesd2pdvs93', 'empty' => true ) )
			.$this->Xform->input( 'Search.Questionnaired2pdv93.sortieaccompagnementd2pdv93_id', array( 'type' => 'hidden', 'id' => false, 'value' => '' ) )
			.$this->Xform->input( 'Search.Questionnaired2pdv93.sortieaccompagnementd2pdv93_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Questionnaired2pdv93.sortieaccompagnementd2pdv93_id' ), 'domain' => 'cohortesd2pdvs93', 'empty' => true ) )
			.$this->Xform->input( 'Search.Questionnaired2pdv93.chgmentsituationadmin', array( 'type' => 'hidden', 'id' => false, 'value' => '' ) )
			.$this->Xform->input( 'Search.Questionnaired2pdv93.chgmentsituationadmin', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Questionnaired2pdv93.chgmentsituationadmin' ), 'domain' => 'cohortesd2pdvs93', 'empty' => true ) )
		);
		echo '</fieldset>';

		echo $this->Allocataires->blocReferentparcours( array( 'prefix' => 'Search', 'options' => $options ) );
		echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
		echo $this->Search->observeDisableFormOnSubmit( 'Cohortesd2pdvs93IndexForm' );

		echo $this->Xform->end( 'Search' );


		if( isset( $results ) ) {
			echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

			$this->Default3->DefaultPaginator->options(
				array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
			);

			App::uses( 'SearchProgressivePagination', 'Search.Utility' );

			echo $this->Default3->index(
				$results,
				array(
					'Personne.nir',
					'Personne.nom',
					'Personne.prenom',
					'Personne.numfixe',
					'Personne.numport',
					'Rendezvous.daterdv',
					'Structurereferente.lib_struc',
					'Questionnaired2pdv93.created' => array( 'type' => 'date' ),
					// Lien ajax actif
					'/Cohortesd2pdvs93/ajaxadd/#Personne.id#/enabled' => array(
						'onclick' => 'try { ajaxAddEdit( "#Personne.id#", "#Questionnaired2pdv93.id#"); } catch(err) { console.log(err); } return false;',
						'class' => 'ajax',
						'condition' => 'true == "#/Questionnaired2pdv93/ajaxadd#"',
						'title' => __m( '/Cohortesd2pdvs93/ajaxadd' )
					),
					// Lien ajax inactif
					'/Cohortesd2pdvs93/ajaxadd/#Personne.id#/disabled' => array(
						'disabled' => true,
						'class' => 'ajax',
						'condition' => 'false == "#/Questionnaired2pdv93/ajaxadd#"',
						'title' => __m( '/Cohortesd2pdvs93/ajaxadd' )
					),
					'/Questionnairesd2pdvs93/index/#Personne.id#' => array( 'class' => 'external' ),
				),
				array(
					'options' => $options,
					'format' => $this->element( 'pagination_format' )
				)
			);
		}
	?>
	<?php if( isset( $results ) ): ?>
		<?php
			echo $this->element(
				'modalbox',
				array(
					'modalid' => 'Questionnaired2pdv93ModalForm',
					'modalcontent' => null,
					'modalmessage' => null,
					'modalclose' => false
				)
			);
		?>
		<script type="text/javascript">
		//<![CDATA[
			var cohorteUrl = '<?php echo $this->request->here;?>',
				ajaxAddEdit = function(personneId, questionnaired2pdv93Id) {
					var add = "<?php echo $this->Ajax->remoteFunction(
							array(
								'url' => array( 'controller' => 'questionnairesd2pdvs93', 'action' => 'add', '#personneId#' ),
								'update' => 'popup-content1',
								'evalScripts' => true
							)
						);?>",
						edit = "<?php echo $this->Ajax->remoteFunction(
							array(
								'url' => array( 'controller' => 'questionnairesd2pdvs93', 'action' => 'edit', '#questionnaired2pdv93Id#' ),
								'update' => 'popup-content1',
								'evalScripts' => true
							)
						);?>";

					if(questionnaired2pdv93Id === '') {
						eval(add.replace('%23personneId%23', personneId));
					} else {
						eval(edit.replace('%23questionnaired2pdv93Id%23', questionnaired2pdv93Id));
					}

					$( 'Questionnaired2pdv93ModalForm' ).show();
				};
		//]]>
		</script>
	<?php endif; ?>
	<?php
		if( $isAjax ) {
			$out = "\$('Cohortesd2pdvs93IndexForm').hide();document.fire('dom:loaded');";
			echo $this->Html->scriptBlock( $out );
		}
		else {
			echo $this->Observer->disableFieldsOnValue(
				'Search.Questionnaired2pdv93.situationaccompagnement',
				'Search.Questionnaired2pdv93.sortieaccompagnementd2pdv93_id',
				array( 'sortie_obligation' ),
				false
			);
			echo $this->Observer->disableFieldsOnValue(
				'Search.Questionnaired2pdv93.situationaccompagnement',
				'Search.Questionnaired2pdv93.chgmentsituationadmin',
				array( 'changement_situation' ),
				false
			);
		}
	?>
</div>