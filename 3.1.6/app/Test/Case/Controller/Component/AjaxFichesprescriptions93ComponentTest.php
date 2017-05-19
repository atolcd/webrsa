<?php
	/**
	 * Code source de la classe AjaxFichesprescriptions93ComponentTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Controller', 'Controller' );
	App::uses( 'AppController', 'Controller' );
	App::uses( 'Component', 'Controller' );
	App::uses( 'AjaxFichesprescriptions93Component', 'Controller/Component' );

	/**
	 * AjaxFichesprescriptions93TestsController class
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class AjaxFichesprescriptions93TestsController extends AppController
	{
		/**
		 * name property
		 *
		 * @var string
		 */
		public $name = 'AjaxFichesprescriptions93TestsController';

		/**
		 * uses property
		 *
		 * @var mixed null
		 */
		public $uses = array( 'Ficheprescription93' );

		/**
		 * components property
		 *
		 * @var array
		 */
		public $components = array(
			'AjaxFichesprescriptions93'
		);
	}

	/**
	 * La classe AjaxFichesprescriptions93ComponentTest ...
	 *
	 * @package app.Test.Case.Controller.Component
	 */
	class AjaxFichesprescriptions93ComponentTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'app.Actionfp93',
			'app.Adresseprestatairefp93',
			'app.Categoriefp93',
			'app.Filierefp93',
			'app.Ficheprescription93',
			'app.Prestatairefp93',
			'app.Thematiquefp93',
		);

		/**
		 * Controller property
		 *
		 * @var AjaxFichesprescriptions93Component
		 */
		public $Controller;

		public $emptyJson = array(
			'onChange' => array(
				'success' => true,
				'fields' => array(
					'Ficheprescription93.thematiquefp93_id' => array(
						'id' => 'Ficheprescription93Thematiquefp93Id',
						'value' => NULL,
						'type' => 'select',
						'options' => array(),
					),
					'Ficheprescription93.categoriefp93_id' => array(
						'id' => 'Ficheprescription93Categoriefp93Id',
						'value' => NULL,
						'type' => 'select',
						'options' => array(),
					),
					'Ficheprescription93.filierefp93_id' => array(
						'id' => 'Ficheprescription93Filierefp93Id',
						'value' => NULL,
						'type' => 'select',
						'options' => array(),
					),
					'Ficheprescription93.prestatairefp93_id' => array(
						'id' => 'Ficheprescription93Prestatairefp93Id',
						'value' => NULL,
						'type' => 'select',
						'options' => array(),
					),
					'Ficheprescription93.actionfp93_id' => array(
						'id' => 'Ficheprescription93Actionfp93Id',
						'value' => NULL,
						'type' => 'select',
						'options' => array(),
					),
					'Ficheprescription93.numconvention' => array(
						'id' => 'Ficheprescription93Numconvention',
						'value' => NULL,
						'type' => 'text',
						'options' => array(),
					),
					'Ficheprescription93.adresseprestatairefp93_id' => array(
						'id' => 'Ficheprescription93Adresseprestatairefp93Id',
						'value' => NULL,
						'type' => 'select',
						'options' => array(),
					),
					'Ficheprescription93.actionfp93' => array(
						'id' => 'Ficheprescription93Actionfp93',
						'value' => NULL,
						'type' => 'text',
					),
					'Ficheprescription93.selection_adresse_prestataire' => array(
						'id' => 'Ficheprescription93SelectionAdressePrestataire',
						'value' => NULL,
						'type' => 'select',
						'options' => array(),
					),
					'Prestatairehorspdifp93.name' => array(
						'id' => 'Prestatairehorspdifp93Name',
						'value' => NULL,
						'type' => 'text',
					),
					'Prestatairehorspdifp93.adresse' => array(
						'id' => 'Prestatairehorspdifp93Adresse',
						'value' => NULL,
						'type' => 'text',
					),
					'Prestatairehorspdifp93.codepos' => array(
						'id' => 'Prestatairehorspdifp93Codepos',
						'value' => NULL,
						'type' => 'text',
					),
					'Prestatairehorspdifp93.localite' => array(
						'id' => 'Prestatairehorspdifp93Localite',
						'value' => NULL,
						'type' => 'text',
					),
					'Prestatairehorspdifp93.tel' => array(
						'id' => 'Prestatairehorspdifp93Tel',
						'value' => NULL,
						'type' => 'text',
					),
					'Prestatairehorspdifp93.fax' => array(
						'id' => 'Prestatairehorspdifp93Fax',
						'value' => NULL,
						'type' => 'text',
					),
					'Prestatairehorspdifp93.email' => array(
						'id' => 'Prestatairehorspdifp93Email',
						'value' => NULL,
						'type' => 'text',
					),
					'Ficheprescription93.rdvprestataire_adresse_check' => array(
						'id' => 'Ficheprescription93RdvprestataireAdresseCheck',
						'value' => NULL,
						'type' => 'checkbox',
					),
					'Ficheprescription93.rdvprestataire_adresse' => array(
						'id' => 'Ficheprescription93RdvprestataireAdresse',
						'value' => NULL,
						'type' => 'text',
					),
				)
			)
		);

		/**
		 * Préparation du test.
		 */
		public function setUp() {
			parent::setUp();

			$Request = new CakeRequest( 'fichesprescriptions93/search', false );
			$Request->addParams(array( 'controller' => 'fichesprescriptions93', 'action' => 'search' ) );

			$this->Controller = new AjaxFichesprescriptions93TestsController( $Request );
			$this->Controller->Components->init( $this->Controller );
			$this->Controller->AjaxFichesprescriptions93->initialize( $this->Controller );
		}

		/**
		 * Test de la méthode AjaxFichesprescriptions93Component::ajaxOnChange()
		 *
		 * @medium
		 */
		public function testAjaxOnChange() {
			// 1. En changeant le type de thématique
			$data = array(
				'prefix' => null,
				'Ficheprescription93' => array(
					'typethematiquefp93_id' => 'pdi'
				),
				'Target' => array(
					'name' => 'data[Ficheprescription93][typethematiquefp93_id]'
				)
			);
			$result = $this->Controller->AjaxFichesprescriptions93->ajaxOnChange( $data );

			$expected = Hash::merge(
				$this->emptyJson['onChange'],
				array(
					'fields' => array(
						'Ficheprescription93.thematiquefp93_id' => array(
							'id' => 'Ficheprescription93Thematiquefp93Id',
							'value' => NULL,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 1,
									'name' => 'Thématique de test',
								)
							)
						)
					),
					'events' => array(
						'changed:Ficheprescription93.actionfp93_id',
						'changed:Ficheprescription93.adresseprestatairefp93_id',
						'reset:Ficheprescription93.actionprestataire'
					)
				)
			);

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. En changeant la thématique
			$data = array(
				'prefix' => null,
				'Ficheprescription93' => array(
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => '1',
				),
				'Target' => array(
					'name' => 'data[Ficheprescription93][thematiquefp93_id]'
				)
			);
			$result = $this->Controller->AjaxFichesprescriptions93->ajaxOnChange( $data );

			$expected = Hash::merge(
				$this->emptyJson['onChange'],
				array(
					'fields' => array(
						'Ficheprescription93.categoriefp93_id' => array(
							'id' => 'Ficheprescription93Categoriefp93Id',
							'value' => NULL,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 1,
									'name' => 'Catégorie de test',
								)
							)
						)
					),
					'events' => array(
						'changed:Ficheprescription93.actionfp93_id',
						'changed:Ficheprescription93.adresseprestatairefp93_id',
						'reset:Ficheprescription93.actionprestataire'
					)
				)
			);

			$paths = array(
				'Ficheprescription93.thematiquefp93_id',
				'Ficheprescription93.actionfp93',
				'Ficheprescription93.selection_adresse_prestataire',
				'Prestatairehorspdifp93.name',
				'Prestatairehorspdifp93.adresse',
				'Prestatairehorspdifp93.codepos',
				'Prestatairehorspdifp93.localite',
				'Prestatairehorspdifp93.tel',
				'Prestatairehorspdifp93.fax',
				'Prestatairehorspdifp93.email',
				'Ficheprescription93.rdvprestataire_adresse_check',
				'Ficheprescription93.rdvprestataire_adresse',
			);
			foreach( $paths as $path ) {
				unset( $expected['fields'][$path] );
			}

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 2. En changeant la catégorie
			$data = array(
				'prefix' => null,
				'Ficheprescription93' => array(
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => '1',
					'categoriefp93_id' => '1',
				),
				'Target' => array(
					'name' => 'data[Ficheprescription93][categoriefp93_id]'
				)
			);
			$result = $this->Controller->AjaxFichesprescriptions93->ajaxOnChange( $data );

			$expected = Hash::merge(
				$this->emptyJson['onChange'],
				array(
					'fields' => array(
						'Ficheprescription93.filierefp93_id' => array(
							'id' => 'Ficheprescription93Filierefp93Id',
							'value' => NULL,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 1,
									'name' => 'Filière de test',
								)
							)
						)
					),
					'events' => array(
						'changed:Ficheprescription93.actionfp93_id',
						'changed:Ficheprescription93.adresseprestatairefp93_id',
						'reset:Ficheprescription93.actionprestataire'
					)
				)
			);

			$paths = array(
				'Ficheprescription93.thematiquefp93_id',
				'Ficheprescription93.categoriefp93_id',
				'Ficheprescription93.actionfp93',
				'Ficheprescription93.selection_adresse_prestataire',
				'Prestatairehorspdifp93.name',
				'Prestatairehorspdifp93.adresse',
				'Prestatairehorspdifp93.codepos',
				'Prestatairehorspdifp93.localite',
				'Prestatairehorspdifp93.tel',
				'Prestatairehorspdifp93.fax',
				'Prestatairehorspdifp93.email',
				'Ficheprescription93.rdvprestataire_adresse_check',
				'Ficheprescription93.rdvprestataire_adresse',
			);
			foreach( $paths as $path ) {
				unset( $expected['fields'][$path] );
			}

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 3. En changeant la filière
			$data = array(
				'prefix' => null,
				'Ficheprescription93' => array(
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => '1',
					'categoriefp93_id' => '1',
					'filierefp93_id' => '1',
				),
				'Target' => array(
					'name' => 'data[Ficheprescription93][filierefp93_id]'
				)
			);
			$result = $this->Controller->AjaxFichesprescriptions93->ajaxOnChange( $data );

			$expected = Hash::merge(
				$this->emptyJson['onChange'],
				array(
					'fields' => array(
						'Ficheprescription93.prestatairefp93_id' => array(
							'id' => 'Ficheprescription93Prestatairefp93Id',
							'value' => NULL,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 1,
									'name' => 'Association LE PRISME',
								),
							),
						),
						'Ficheprescription93.actionfp93_id' => array(
							'id' => 'Ficheprescription93Actionfp93Id',
							'value' => NULL,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 1,
									'name' => 'Action de test',
								),
								array(
									'id' => 2,
									'name' => 'Action de test',
								),
							),
						),
					),
					'events' => array(
						'changed:Ficheprescription93.actionfp93_id',
						'changed:Ficheprescription93.adresseprestatairefp93_id',
						'reset:Ficheprescription93.actionprestataire'
					)
				)
			);

			$paths = array(
				'Ficheprescription93.thematiquefp93_id',
				'Ficheprescription93.categoriefp93_id',
				'Ficheprescription93.filierefp93_id',
				'Ficheprescription93.actionfp93',
				'Ficheprescription93.selection_adresse_prestataire',
				'Prestatairehorspdifp93.name',
				'Prestatairehorspdifp93.adresse',
				'Prestatairehorspdifp93.codepos',
				'Prestatairehorspdifp93.localite',
				'Prestatairehorspdifp93.tel',
				'Prestatairehorspdifp93.fax',
				'Prestatairehorspdifp93.email',
				'Ficheprescription93.rdvprestataire_adresse_check',
				'Ficheprescription93.rdvprestataire_adresse',
			);
			foreach( $paths as $path ) {
				unset( $expected['fields'][$path] );
			}

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 4. En changeant la filière lors d'un add
			$data = array(
				'prefix' => null,
				'Ficheprescription93' => array(
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => '1',
					'categoriefp93_id' => '1',
					'filierefp93_id' => '1',
					'action' => 'add'
				),
				'Target' => array(
					'name' => 'data[Ficheprescription93][filierefp93_id]'
				)
			);
			$result = $this->Controller->AjaxFichesprescriptions93->ajaxOnChange( $data );

			$expected = Hash::merge(
				$this->emptyJson['onChange'],
				array(
					'fields' => array(
						'Ficheprescription93.prestatairefp93_id' => array(
							'id' => 'Ficheprescription93Prestatairefp93Id',
							'value' => NULL,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 1,
									'name' => 'Association LE PRISME',
								),
							),
						),
						'Ficheprescription93.actionfp93_id' => array(
							'id' => 'Ficheprescription93Actionfp93Id',
							'value' => NULL,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 2,
									'name' => 'Action de test',
								),
							),
						),
					),
					'events' => array(
						'changed:Ficheprescription93.actionfp93_id',
						'changed:Ficheprescription93.adresseprestatairefp93_id',
						'reset:Ficheprescription93.actionprestataire'
					)
				)
			);

			$paths = array(
				'Ficheprescription93.thematiquefp93_id',
				'Ficheprescription93.categoriefp93_id',
				'Ficheprescription93.filierefp93_id',
				'Ficheprescription93.actionfp93',
				'Ficheprescription93.selection_adresse_prestataire',
				'Prestatairehorspdifp93.name',
				'Prestatairehorspdifp93.adresse',
				'Prestatairehorspdifp93.codepos',
				'Prestatairehorspdifp93.localite',
				'Prestatairehorspdifp93.tel',
				'Prestatairehorspdifp93.fax',
				'Prestatairehorspdifp93.email',
				'Ficheprescription93.rdvprestataire_adresse_check',
				'Ficheprescription93.rdvprestataire_adresse',
			);
			foreach( $paths as $path ) {
				unset( $expected['fields'][$path] );
			}

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 5. En changeant le prestataire
			$data = array(
				'prefix' => null,
				'Ficheprescription93' => array(
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => '1',
					'categoriefp93_id' => '1',
					'filierefp93_id' => '1',
					'prestatairefp93_id' => '1',
				),
				'Target' => array(
					'name' => 'data[Ficheprescription93][prestatairefp93_id]'
				)
			);
			$result = $this->Controller->AjaxFichesprescriptions93->ajaxOnChange( $data );

			$expected = Hash::merge(
				$this->emptyJson['onChange'],
				array(
					'fields' => array(
						'Ficheprescription93.actionfp93_id' => array(
							'id' => 'Ficheprescription93Actionfp93Id',
							'value' => NULL,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 1,
									'name' => 'Action de test',
								),
								array(
									'id' => 2,
									'name' => 'Action de test',
								),
							),
						),
						'Ficheprescription93.adresseprestatairefp93_id' => array(
							'id' => 'Ficheprescription93Adresseprestatairefp93Id',
							'value' => 1,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 1,
									'name' => 'Av. de la république, 93000 Bobigny',
									'title' => '',
								),
							),
						)
					),
					'events' => array(
						'changed:Ficheprescription93.actionfp93_id',
						'changed:Ficheprescription93.adresseprestatairefp93_id',
						'changed:Ficheprescription93.adresseprestatairefp93_id',
						'reset:Ficheprescription93.actionprestataire'
					)
				)
			);

			$paths = array(
				'Ficheprescription93.thematiquefp93_id',
				'Ficheprescription93.categoriefp93_id',
				'Ficheprescription93.filierefp93_id',
				'Ficheprescription93.prestatairefp93_id',
				'Ficheprescription93.actionfp93',
				'Ficheprescription93.selection_adresse_prestataire',
				'Prestatairehorspdifp93.name',
				'Prestatairehorspdifp93.adresse',
				'Prestatairehorspdifp93.codepos',
				'Prestatairehorspdifp93.localite',
				'Prestatairehorspdifp93.tel',
				'Prestatairehorspdifp93.fax',
				'Prestatairehorspdifp93.email',
				'Ficheprescription93.rdvprestataire_adresse_check',
				'Ficheprescription93.rdvprestataire_adresse',
			);
			foreach( $paths as $path ) {
				unset( $expected['fields'][$path] );
			}

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 6. En changeant le prestataire lors d'un add
			$data = array(
				'prefix' => null,
				'Ficheprescription93' => array(
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => '1',
					'categoriefp93_id' => '1',
					'filierefp93_id' => '1',
					'prestatairefp93_id' => '1',
					'action' => 'add',
				),
				'Target' => array(
					'name' => 'data[Ficheprescription93][prestatairefp93_id]'
				)
			);
			$result = $this->Controller->AjaxFichesprescriptions93->ajaxOnChange( $data );

			$expected = Hash::merge(
				$this->emptyJson['onChange'],
				array(
					'fields' => array(
						'Ficheprescription93.actionfp93_id' => array(
							'id' => 'Ficheprescription93Actionfp93Id',
							'value' => NULL,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 2,
									'name' => 'Action de test',
								),
							),
						),
						'Ficheprescription93.adresseprestatairefp93_id' => array(
							'id' => 'Ficheprescription93Adresseprestatairefp93Id',
							'value' => 1,
							'type' => 'select',
							'options' => array(
								array(
									'id' => 1,
									'name' => 'Av. de la république, 93000 Bobigny',
									'title' => '',
								),
							),
						)
					),
					'events' => array(
						'changed:Ficheprescription93.actionfp93_id',
						'changed:Ficheprescription93.adresseprestatairefp93_id',
						'changed:Ficheprescription93.adresseprestatairefp93_id',
						'reset:Ficheprescription93.actionprestataire'
					)
				)
			);

			$paths = array(
				'Ficheprescription93.thematiquefp93_id',
				'Ficheprescription93.categoriefp93_id',
				'Ficheprescription93.filierefp93_id',
				'Ficheprescription93.prestatairefp93_id',
				'Ficheprescription93.actionfp93',
				'Ficheprescription93.selection_adresse_prestataire',
				'Prestatairehorspdifp93.name',
				'Prestatairehorspdifp93.adresse',
				'Prestatairehorspdifp93.codepos',
				'Prestatairehorspdifp93.localite',
				'Prestatairehorspdifp93.tel',
				'Prestatairehorspdifp93.fax',
				'Prestatairehorspdifp93.email',
				'Ficheprescription93.rdvprestataire_adresse_check',
				'Ficheprescription93.rdvprestataire_adresse',
			);
			foreach( $paths as $path ) {
				unset( $expected['fields'][$path] );
			}

			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 7. En changeant l'action
			$data = array(
				'prefix' => null,
				'Ficheprescription93' => array(
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => '1',
					'categoriefp93_id' => '1',
					'filierefp93_id' => '1',
					'prestatairefp93_id' => '1',
					'actionfp93_id' => '1',
				),
				'Target' => array(
					'name' => 'data[Ficheprescription93][actionfp93_id]'
				)
			);
			$result = $this->Controller->AjaxFichesprescriptions93->ajaxOnChange( $data );

			$expected = array(
				'success' => true,
				'fields' => array(
					'Ficheprescription93.numconvention' => array(
						'id' => 'Ficheprescription93Numconvention',
						'value' => '93XXX1300001',
						'type' => 'text',
						'options' => array( ),
					),
					'Ficheprescription93.adresseprestatairefp93_id' => array(
						'id' => 'Ficheprescription93Adresseprestatairefp93Id',
						'value' => 1,
						'type' => 'select',
						'options' => array(
							array(
								'id' => 1,
								'name' => 'Av. de la république, 93000 Bobigny',
								'title' => '',
							),
						),
					),
					'Ficheprescription93.prestatairefp93_id' => array(
						'value' => 1,
						'id' => 'Ficheprescription93Prestatairefp93Id',
						'type' => 'select'
					),
				),
				'events' => array(
					'changed:Ficheprescription93.actionfp93_id',
					'changed:Ficheprescription93.adresseprestatairefp93_id',
					'reset:Ficheprescription93.actionprestataire'
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );

			// 8. En changeant l'adresse du prestataire
			$data = array(
				'prefix' => null,
				'Ficheprescription93' => array(
					'typethematiquefp93_id' => 'pdi',
					'thematiquefp93_id' => '1',
					'categoriefp93_id' => '1',
					'filierefp93_id' => '1',
					'prestatairefp93_id' => '1',
					'actionfp93_id' => '1',
					'adresseprestatairefp93_id' => '1',
				),
				'Target' => array(
					'name' => 'data[Ficheprescription93][adresseprestatairefp93_id]'
				)
			);
			$result = $this->Controller->AjaxFichesprescriptions93->ajaxOnChange( $data );

			$expected = array(
				'success' => true,
				'fields' => array(),
				'events' => array(
					'changed:Ficheprescription93.adresseprestatairefp93_id',
				)
			);
			$this->assertEqual( $result, $expected, var_export( $result, true ) );
		}
	}
?>