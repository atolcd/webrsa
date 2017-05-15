<?php
	/**
	 * Code source de la classe DefaultTableHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package Default
	 * @subpackage Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'DefaultTableHelper', 'Default.View/Helper' );
	App::uses( 'DefaultAbstractTestCase', 'Default.Test/Case' );
	App::uses( 'CakeTestSession', 'CakeTest.Model/Datasource' );

	/**
	 * La classe DefaultTableHelperTest ...
	 *
	 * @package Default
	 * @subpackage Test.Case.View.Helper
	 */
	class DefaultTableHelperTest extends DefaultAbstractTestCase
	{
		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'core.Apple' // TODO: détacher tous les behaviors si possible, ce qui permettra d'éviter required="required"
		);

		public static $requestsParams = array(
			'page_1_of_1' => array(
				'paging' => array(
					'Apple' => array(
						'page' => 1,
						'current' => 9,
						'count' => 19,
						'prevPage' => false,
						'nextPage' => false,
						'pageCount' => 1,
						'order' => null,
						'limit' => 20,
						'options' => array(
							'page' => 1,
							'conditions' => array( )
						),
						'paramType' => 'named'
					)
				),
				'controller' => 'apples',
				'action' => 'index',
			),
			'page_1_of_2' => array(
				'paging' => array(
					'Apple' => array(
						'page' => 1,
						'current' => 9,
						'count' => 21,
						'prevPage' => false,
						'nextPage' => true,
						'pageCount' => 2,
						'order' => null,
						'limit' => 20,
						'options' => array(
							'page' => 1,
							'conditions' => array( )
						),
						'paramType' => 'named'
					)
				),
				'controller' => 'apples',
				'action' => 'index',
			),
			'page_2_of_2' => array(
				'paging' => array(
					'Apple' => array(
						'page' => 2,
						'current' => 21,
						'count' => 21,
						'prevPage' => true,
						'nextPage' => false,
						'pageCount' => 2,
						'order' => null,
						'limit' => 20,
						'options' => array(
							'page' => 2,
							'conditions' => array( )
						),
						'paramType' => 'named'
					)
				),
				'controller' => 'apples',
				'action' => 'index',
			),
			'page_2_of_7' => array(
				'paging' => array(
					'Apple' => array(
						'page' => 2,
						'current' => 9,
						'count' => 62,
						'prevPage' => false,
						'nextPage' => true,
						'pageCount' => 7,
						'order' => null,
						'limit' => 20,
						'options' => array(
							'page' => 1,
							'conditions' => array( )
						),
						'paramType' => 'named'
					)
				),
				'controller' => 'apples',
				'action' => 'index',
			),
		);

		/**
		 *
		 * @var array
		 */
		public $fields = array(
			'Apple.id',
			'data[Apple][color]',
			'/Apples/view/#Apple.id#'
		);

		/**
		 *
		 * @var array
		 */
		public $data = array(
			array(
				'Apple' => array(
					'id' => 6,
					'color' => 'red',
					'code' => "-0402\n\r-0404\n\r-0405",
					'created' => '2015-07-03 11:58:13',
					'modified' => '2015-07-03 14:07:37'
				)
			)
		);

		/**
		 * Defini une url fictive
		 *
		 * @param array $requestParams
		 */
		protected function _setRequest( array $requestParams = array() ) {
			$default = array(
				'plugin' => null,
				'controller' => 'apples',
				'action' => 'index',
			);

			$requestParams = Hash::merge( $default, $requestParams );

			Router::reload();
			$request = new CakeRequest();

			$request->addParams( $requestParams );

			Router::setRequestInfo( $request );

			$this->DefaultTable->request = $request;
			$this->DefaultTable->DefaultPaginator->request = $request;
		}

		/**
		 * Préparation du test.
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			CakeTestSession::start();

			$controller = null;
			$this->View = new View( $controller );
			$this->DefaultTable = new DefaultTableHelper( $this->View );

			$this->_setRequest( self::$requestsParams['page_2_of_7'] );
			Configure::write('ConfigurableQuery.common.two_ways_order.enabled', false);
		}

		/**
		 * Nettoyage postérieur au test.
		 *
		 * @return void
		 */
		public function tearDown() {
			CakeTestSession::destroy();
			parent::tearDown();
			unset(
				/*$this->DefaultTable->DefaultTableCell,
				$this->DefaultTable->DefaultHtml,
				$this->DefaultTable->DefaultPaginator,*/
				$this->View,
				$this->DefaultTable
			);
		}

		/**
		 * test case startup
		 *
		 * @return void
		 */
		public static function setupBeforeClass() {
			CakeTestSession::setupBeforeClass();
		}

		/**
		 * cleanup after test case.
		 *
		 * @return void
		 */
		public static function teardownAfterClass() {
			CakeTestSession::teardownAfterClass();
		}

		/**
		 * Test de la méthode DefaultTableHelper::thead()
		 *
		 * @return void
		 */
		public function testThead() {
			$params = array();

			// Sans donnée
			$result = $this->DefaultTable->thead( array(), $params );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// Avec le tri
			$result = $this->DefaultTable->thead( $this->fields, $params );
			$expected = '<thead>
							<tr>
								<th id="ColumnAppleId"><a href="'.Router::url('/').'apples/index/page:1/sort:Apple.id/direction:asc">Apple.id</a></th>
								<th id="ColumnInputDataAppleColor">data[Apple][color]</th>
								<th class="actions" id="ColumnActions">Actions</th>
							</tr>
						</thead>';

			$this->assertEqualsXhtml( $expected, $result );

			// Sans le tri
			$result = $this->DefaultTable->thead( $this->fields, $params + array( 'sort' => false ) );
			$expected = '<thead>
							<tr>
								<th id="ColumnAppleId">Apple.id</th>
								<th id="ColumnInputDataAppleColor">data[Apple][color]</th>
								<th class="actions" id="ColumnActions">Actions</th>
							</tr>
						</thead>';
			$this->assertEqualsXhtml( $expected, $result );

			// Sans le tri sur certaines colonnes
			$fields = Hash::normalize($this->fields);
			$fields['Apple.id']['sort'] = false;
			$result = $this->DefaultTable->thead( $fields, $params );
			$expected = '<thead>
							<tr>
								<th id="ColumnAppleId">Apple.id</th>
								<th id="ColumnInputDataAppleColor">data[Apple][color]</th>
								<th class="actions" id="ColumnActions">Actions</th>
							</tr>
						</thead>';
			$this->assertEqualsXhtml( $expected, $result );

			// Avec un nom de colonne qui surcharge la traduction
			$fields = Hash::normalize( $this->fields );
			$fields['Apple.id'] = array( 'label' => 'Test Apple.id' );

			$result = $this->DefaultTable->thead( $fields, $params + array( 'sort' => false ) );
			$expected = '<thead>
							<tr>
								<th id="ColumnAppleId">Test Apple.id</th>
								<th id="ColumnInputDataAppleColor">data[Apple][color]</th>
								<th class="actions" id="ColumnActions">Actions</th>
							</tr>
						</thead>';
			$this->assertEqualsXhtml( $expected, $result );
		}

		/**
		 * Test de la méthode DefaultTableHelper::thead() avec des conditions sur
		 * l'affichage des colonnes.
		 *
		 * @return void
		 */
		public function testTheadConditions() {
			// 1. Sans condition_group
			$fields = array(
				'Apple.id',
				'Apple.color',
				'/Apples/edit/#Apple.id#' => array(
					'disabled' => '!"#/Apples/edit#"'
				),
				'/Apples/turn_blue/#Apple.id#' => array(
					'condition' => '"#Apple.color#" === "red"'
				),
				'/Apples/turn_red/#Apple.id#' => array(
					'condition' => '"#Apple.color#" !== "red"'
				),
				'/Apples/print/#Apple.id#',
			);

			$result = $this->DefaultTable->thead(
				Hash::normalize( $fields ),
				array( 'sort' => false )
			);
			$expected = '<thead>
							<tr>
								<th id="ColumnAppleId">Apple.id</th>
								<th id="ColumnAppleColor">Apple.color</th>
								<th colspan="3" class="actions" id="ColumnActions">Actions</th>
							</tr>
						</thead>';

			$this->assertEqualsXhtml( $expected, $result );

			// 1. Avec et sans condition_group
			$fields = array(
				'Apple.id',
				'Apple.color',
				'/Apples/edit/#Apple.id#' => array(
					'disabled' => '!"#/Apples/edit#"'
				),
				'/Apples/turn_blue/#Apple.id#' => array(
					'condition' => '"#Apple.color#" === "red"'
				),
				'/Apples/turn_red/#Apple.id#' => array(
					'condition' => '"#Apple.color#" !== "red"'
				),
				'/Apples/eat/#Apple.id#' => array(
					'condition' => '"#Apple.color#" === "red"',
					'condition_group' => 'eatable'
				),
				'/Apples/throw/#Apple.id#' => array(
					'condition' => '"#Apple.color#" !== "red"',
					'condition_group' => 'eatable'
				),
				'/Apples/print/#Apple.id#',
			);

			$result = $this->DefaultTable->thead(
				Hash::normalize( $fields ),
				array( 'sort' => false )
			);
			$expected = '<thead>
							<tr>
								<th id="ColumnAppleId">Apple.id</th>
								<th id="ColumnAppleColor">Apple.color</th>
								<th colspan="4" class="actions" id="ColumnActions">Actions</th>
							</tr>
						</thead>';

			$this->assertEqualsXhtml( $expected, $result );
		}

		/**
		 * Test de la méthode DefaultTableHelper::tbody()
		 *
		 * @return void
		 */
		public function testBody() {
			$_SESSION['Auth']['Permissions']['Module:Apples'] = true;
			$params = array();

			$result = $this->DefaultTable->tbody( array(), $this->fields, $params );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$result = $this->DefaultTable->tbody( $this->data, $this->fields, $params );
			$expected = '<tbody>
							<tr class="odd">
								<td class="data integer positive">6</td>
								<td class="input string">
									<div class="input text">
										<label for="AppleColor">Color</label>
										<input name="data[Apple][color]" maxlength="40" type="text" id="AppleColor"/>
									</div>
								</td>
								<td class="action">
									<a href="'.Router::url('/').'apples/view/6" title="/Apples/view/6" class="apples view">/Apples/view</a>
								</td>
							</tr>
						</tbody>';
			$this->assertEqualsXhtml( $expected, $result );

			$fields = array(
				'data[Apple][][id]',
				'Apple.color',
			);
			$result = $this->DefaultTable->tbody( $this->data, $fields, $params + array( 'options' => array( 'Apple' => array( 'color' => array( 'red' => 'Red' ) ) ) ) );
			$expected = '<tbody>
							<tr class="odd">
								<td class="input integer">
									<input type="hidden" name="data[Apple][0][id]" id="Apple0Id"/>
								</td>
								<td class="data string ">Red</td>
							</tr>
						</tbody>';
			$this->assertEqualsXhtml( $expected, $result );
		}

		/**
		 * Test de la méthode DefaultTable::tableParams()
		 */
		public function testTableParams() {
			// 1. Valeurs par défaut
			$result = $this->DefaultTable->tableParams();
			$expected = array(
				'id' => 'TableApplesIndex',
				'class' => 'apples index',
				'domain' => 'apples',
				'sort' => true
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Surcharge des valeurs
			$params = array(
				'id' => 'TableMyApplesIndex',
				'class' => 'my_apples',
				'domain' => 'my_apples',
				'sort' => false
			);
			$result = $this->DefaultTable->tableParams( $params );
			$expected = array(
				'id' => 'TableMyApplesIndex',
				'class' => 'apples index my_apples',
				'domain' => 'my_apples',
				'sort' => false
			);
			$this->assertEquals( $expected, $result, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode DefaultTableHelper::index()
		 *
		 * @return void
		 */
		public function testIndex() {
			$_SESSION['Auth']['Permissions']['Module:Apples'] = true;
			$params = array();

			$result = $this->DefaultTable->index( array(), $this->fields, $params );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 1.
			$result = $this->DefaultTable->index( $this->data, $this->fields, $params );
			$expected = '<table id="TableApplesIndex" class="apples index">
							<thead>
								<tr>
									<th id="TableApplesIndexColumnAppleId"><a href="'.Router::url('/').'apples/index/page:1/sort:Apple.id/direction:asc">Apple.id</a></th>
									<th id="TableApplesIndexColumnInputDataAppleColor">data[Apple][color]</th>
									<th class="actions" id="TableApplesIndexColumnActions">Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr class="odd">
									<td class="data integer positive">6</td>
									<td class="input string">
										<div class="input text">
											<label for="AppleColor">Color</label>
											<input name="data[Apple][color]" maxlength="40" type="text" id="AppleColor"/>
										</div>
									</td>
									<td class="action">
										<a href="'.Router::url('/').'apples/view/6" title="/Apples/view/6" class="apples view">/Apples/view</a>
									</td>
								</tr>
							</tbody>
						</table>';
			$this->assertEqualsXhtml( $expected, $result );

			// 2. En ajoutant explicitement l'id de la table
			$result = $this->DefaultTable->index( $this->data, $this->fields, $params + array( 'id' => 'TableTestApplesIndex' ) );
			$expected = '<table id="TableTestApplesIndex" class="apples index">
							<thead>
								<tr>
									<th id="TableTestApplesIndexColumnAppleId"><a href="'.Router::url('/').'apples/index/page:1/sort:Apple.id/direction:asc">Apple.id</a></th>
									<th id="TableTestApplesIndexColumnInputDataAppleColor">data[Apple][color]</th>
									<th class="actions" id="TableTestApplesIndexColumnActions">Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr class="odd">
									<td class="data integer positive">6</td>
									<td class="input string">
										<div class="input text">
											<label for="AppleColor">Color</label>
											<input name="data[Apple][color]" maxlength="40" type="text" id="AppleColor"/>
										</div>
									</td>
									<td class="action">
										<a href="'.Router::url('/').'apples/view/6" title="/Apples/view/6" class="apples view">/Apples/view</a>
									</td>
								</tr>
							</tbody>
						</table>';

			$this->assertEqualsXhtml( $expected, $result );

			// 3. En ajoutant des classes aux colonnes
			$fields = Hash::normalize( $this->fields );
			$fields['Apple.id'] = array( 'class' => 'dossier_id' );
			$result = $this->DefaultTable->index( $this->data, $fields, $params + array( 'id' => 'TableTestApplesIndex' ) );
			$expected = '<table id="TableTestApplesIndex" class="apples index">
							<thead>
								<tr>
									<th class="dossier_id" id="TableTestApplesIndexColumnAppleId"><a href="'.Router::url('/').'apples/index/page:1/sort:Apple.id/direction:asc">Apple.id</a></th>
									<th id="TableTestApplesIndexColumnInputDataAppleColor">data[Apple][color]</th>
									<th class="actions" id="TableTestApplesIndexColumnActions">Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr class="odd">
									<td class="dossier_id data integer positive">6</td>
									<td class="input string">
										<div class="input text">
											<label for="AppleColor">Color</label>
											<input name="data[Apple][color]" maxlength="40" type="text" id="AppleColor"/>
										</div>
									</td>
									<td class="action">
										<a href="'.Router::url('/').'apples/view/6" title="/Apples/view/6" class="apples view">/Apples/view</a>
									</td>
								</tr>
							</tbody>
						</table>';

			$this->assertEqualsXhtml( $expected, $result );

			// 4. En ajoutant une ligne d'en-têtes
			$fields = Hash::normalize( $this->fields );
			$fields['Apple.id'] = array( 'class' => 'dossier_id' );
			$header = array(
				array( 'Apple' => array( 'colspan' => 2 ) ),
				array( ' ' => array( 'class' => 'action' ) )
			);
			$result = $this->DefaultTable->index( $this->data, $fields, $params + array( 'id' => 'TableTestApplesIndex', 'header' => $header ) );
			$expected = '<table id="TableTestApplesIndex" class="apples index">
							<thead>
								<tr>
									<th colspan="2">Apple</th>
									<th class="action"> </th>
								</tr>
								<tr>
									<th class="dossier_id" id="TableTestApplesIndexColumnAppleId"><a href="'.Router::url('/').'apples/index/page:1/sort:Apple.id/direction:asc">Apple.id</a></th>
									<th id="TableTestApplesIndexColumnInputDataAppleColor">data[Apple][color]</th>
									<th class="actions" id="TableTestApplesIndexColumnActions">Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr class="odd">
									<td class="dossier_id data integer positive">6</td>
									<td class="input string">
										<div class="input text">
											<label for="AppleColor">Color</label>
											<input name="data[Apple][color]" maxlength="40" type="text" id="AppleColor"/>
										</div>
									</td>
									<td class="action">
										<a href="'.Router::url('/').'apples/view/6" title="/Apples/view/6" class="apples view">/Apples/view</a>
									</td>
								</tr>
							</tbody>
						</table>';

			$this->assertEqualsXhtml( $expected, $result );

			// 5. En spécifiant ou non le format du datetime
			$fields = array(
				'Apple.id',
				'Apple.created',
				'Apple.modified' => array(
					'format' => '%A %e %B %Y %H:%M'
				)
			);
			$result = $this->DefaultTable->index( $this->data, $fields, $params );
			$expected = '<table id="TableApplesIndex" class="apples index">
				<thead>
					<tr>
						<th id="TableApplesIndexColumnAppleId"><a href="'.Router::url('/').'apples/index/page:1/sort:Apple.id/direction:asc">Apple.id</a></th>
						<th id="TableApplesIndexColumnAppleCreated"><a href="'.Router::url('/').'apples/index/page:1/sort:Apple.created/direction:asc">Apple.created</a></th>
						<th id="TableApplesIndexColumnAppleModified"><a href="'.Router::url('/').'apples/index/page:1/sort:Apple.modified/direction:asc">Apple.modified</a></th>
					</tr>
				</thead>
				<tbody>
					<tr class="odd">
						<td class="data integer positive">6</td><td class="data datetime ">03/07/2015 à 11:58:13</td>
						<td class="data datetime ">vendredi 3 juillet 2015 14:07</td>
					</tr>
				</tbody>
			</table>';
			$this->assertEqualsXhtml( $expected, $result );

			$result = $this->DefaultTable->index( $this->data, $this->fields, $params );
			$expected = '<table id="TableApplesIndex" class="apples index">
							<thead>
								<tr>
									<th id="TableApplesIndexColumnAppleId"><a href="'.Router::url('/').'apples/index/page:1/sort:Apple.id/direction:asc">Apple.id</a></th>
									<th id="TableApplesIndexColumnInputDataAppleColor">data[Apple][color]</th>
									<th class="actions" id="TableApplesIndexColumnActions">Actions</th>
								</tr>
							</thead>
							<tbody>
								<tr class="odd">
									<td class="data integer positive">6</td>
									<td class="input string">
										<div class="input text">
											<label for="AppleColor">Color</label>
											<input name="data[Apple][color]" maxlength="40" type="text" id="AppleColor"/>
										</div>
									</td>
									<td class="action">
										<a href="'.Router::url('/').'apples/view/6" title="/Apples/view/6" class="apples view">/Apples/view</a>
									</td>
								</tr>
							</tbody>
						</table>';
			$this->assertEqualsXhtml( $expected, $result );
		}

		/**
		 * Test de la méthode DefaultTableHelper::details()
		 *
		 * @return void
		 */
		public function testDetails() {
			$fields = array(
				'Apple.id',
				'Apple.color',
			);
			$params = array();

			// 1. Avec un tableau de details vide
			$result = $this->DefaultTable->details( array(), $this->fields, $params );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			// 2. Avec un tableau de details "classique"
			$result = $this->DefaultTable->details( $this->data[0], $fields, $params );
			$expected = '<table id="TableApplesIndex" class="apples index">
							<tbody>
								<tr class="odd">
									<td>Apple.id</td>
									<td class="data integer positive">6</td>
								</tr>
								<tr class="even">
									<td>Apple.color</td>
									<td class="data string ">red</td>
								</tr>
							</tbody>
						</table>';
			$this->assertEqualsXhtml( $expected, $result );

			// 3. Avec un tableau de détails vide à cause des conditions
			$result = $this->DefaultTable->details(
				$this->data[0],
				array(
					'Apple.id' => array(
						'condition' => false
					),
					'Apple.color' => array(
						'condition' => false
					),
				),
				$params
			);
			$expected = null;
			$this->assertEqualsXhtml( $expected, $result );
		}

		/**
		 * Test de la méthode DefaultTableHelper::detailsTbody()
		 *
		 * @return void
		 */
		public function testDetailsTbody() {
			$fields = array(
				'Apple.id' => array( 'label' => 'Id' ),
				'Apple.color',
			);
			$params = array();

			$result = $this->DefaultTable->detailsTbody( array(), $this->fields, $params );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$params['options'] = array( 'Apple' => array( 'color' => array( 'red' => 'Foo' ) ) );
			$result = $this->DefaultTable->detailsTbody( $this->data[0], $fields, $params );
			$expected = '<tbody>
							<tr class="odd">
								<td>Id</td>
								<td class="data integer positive">6</td>
							</tr>
							<tr class="even">
								<td>Apple.color</td>
								<td class="data string ">Foo</td>
							</tr>
						</tbody>';
			$this->assertEqualsXhtml( $expected, $result );
		}

		/**
		 * Test de la méthode DefaultTableHelper::detailsTbody() avec des th
		 *
		 * @return void
		 */
		public function testDetailsTbodyTh() {
			$fields = array(
				'Apple.id',
				'Apple.color',
			);
			$params = array( 'th' => true );

			$result = $this->DefaultTable->detailsTbody( array(), $this->fields, $params );
			$expected = null;
			$this->assertEquals( $expected, $result, var_export( $result, true ) );

			$params['options'] = array( 'Apple' => array( 'color' => array( 'red' => 'Foo' ) ) );
			$result = $this->DefaultTable->detailsTbody( $this->data[0], $fields, $params );
			$expected = '<tbody>
							<tr class="odd">
								<th>Apple.id</th>
								<td class="data integer positive">6</td>
							</tr>
							<tr class="even">
								<th>Apple.color</th>
								<td class="data string ">Foo</td>
							</tr>
						</tbody>';
			$this->assertEqualsXhtml( $expected, $result );
		}

		/**
		 * Test de la méthode DefaultTableHelper::detailsTbody() avec une condition
		 * pour l'affichage d'une ligne
		 *
		 * @return void
		 */
		public function testDetailsTbodyCondition() {
			$fields = array(
				'Apple.id' => array(
					'condition' => '"#Apple.id#" != "6"'
				),
				'Apple.color',
			);

			$result = $this->DefaultTable->detailsTbody( $this->data[0], $fields );
			$expected = '<tbody>
							<tr class="odd">
								<td>Apple.color</td>
								<td class="data string ">red</td>
							</tr>
						</tbody>';
			$this->assertEqualsXhtml( $expected, $result );
		}

		/**
		 * Test de la méthode DefaultTableHelper::tr()
		 *
		 * @return void
		 */
		public function testTr() {
			$_SESSION['Auth']['Permissions']['Module:Apples'] = true;

			// 1. Avec le type list
			$fields = array(
				'Apple.id',
				'Apple.color',
				'Apple.code' => array(
					'type' => 'list'
				)
			);
			$params = array(
				'options' => array(
					'Apple' => array(
						'code' => array(
							'0401' => 'Aucune difficulté',
							'0402' => 'Santé',
							'0403' => 'Reconnaissance de la qualité de travailleur handicapé',
							'0404' => 'Lecture, écriture ou compréhension du français',
							'0405' => 'Démarches et formalités administratives',
							'0406' => 'Endettement',
							'0407' => 'Autres'
						)
					)
				)
			);

			$result = $this->DefaultTable->tr( 0, $this->data[0], Hash::normalize( $fields ), $params );
			$expected = '<tr class="odd">
				<td class="data integer positive">6</td>
				<td class="data string ">red</td>
				<td class="data list text">
					<ul>
						<li>Santé</li>
						<li>Lecture, écriture ou compréhension du français</li>
						<li>Démarches et formalités administratives</li>
					</ul>
				</td>
			</tr>';
			$this->assertEqualsXhtml( $expected, $result );

			// 2. Avec condition vraie
			$base = Router::url( '/' );
			$fields = array(
				'Apple.id',
				'Apple.color',
				'/Apples/view/#Apple.id#' => array(
					'condition' => '( "#Apple.id#" % 2 == 0 )'
				)
			);

			$result = $this->DefaultTable->tr( 0, $this->data[0], Hash::normalize( $fields ), $params );
			$expected = '<tr class="odd">
				<td class="data integer positive">6</td>
				<td class="data string ">red</td>
				<td class="action"><a href="'.$base.'apples/view/6" title="/Apples/view/6" class="apples view">/Apples/view</a></td>
			</tr>';
			$this->assertEqualsXhtml( $expected, $result );

			// 3. Avec condition fausse
			$fields = array(
				'Apple.id',
				'Apple.color',
				'/Apples/view/#Apple.id#' => array(
					'condition' => '( "#Apple.id#" % 2 == 1 )'
				)
			);

			$result = $this->DefaultTable->tr( 0, $this->data[0], Hash::normalize( $fields ), $params );
			$expected = '<tr class="odd">
				<td class="data integer positive">6</td>
				<td class="data string ">red</td>
			</tr>';
			$this->assertEqualsXhtml( $expected, $result );
		}

		/**
		 * Test de la méthode DefaultTableHelper::tr()
		 *
		 * @return void
		 */
		public function testDetailsOptions() {
			$fields = array(
				'Apple.id',
				'Apple.code' => array(
					'type' => 'list'
				)
			);
			$params = array(
				'th' => true,
				'options' => array(
					'Apple' => array(
						'code' => array(
							'0401' => 'Aucune difficulté',
							'0402' => 'Santé',
							'0403' => 'Reconnaissance de la qualité de travailleur handicapé',
							'0404' => 'Lecture, écriture ou compréhension du français',
							'0405' => 'Démarches et formalités administratives',
							'0406' => 'Endettement',
							'0407' => 'Autres'
						)
					)
				)
			);

			$result = $this->DefaultTable->details( $this->data[0], $fields, $params );
			$expected = '<table id="TableApplesIndex" class="apples index">
				<tbody>
					<tr class="odd">
						<th>Apple.id</th>
						<td class="data integer positive">6</td>
					</tr>
					<tr class="even">
						<th>Apple.code</th>
						<td class="data list text">
							<ul>
								<li>Santé</li>
								<li>Lecture, écriture ou compréhension du français</li>
								<li>Démarches et formalités administratives</li>
							</ul>
						</td>
					</tr>
				</tbody>
			</table>';
			$this->assertEqualsXhtml( $expected, $result );
		}
	}
?>