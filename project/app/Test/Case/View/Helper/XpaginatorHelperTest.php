<?php
	/**
	 * Code source de la classe XpaginatorHelperTest.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Case.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'View', 'View' );
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'XpaginatorHelper', 'View/Helper' );

	/**
	 * Classe XpaginatorHelperTest.
	 *
	 * @package app.Test.Case.Model.Behavior
	 */
	class XpaginatorHelperTest extends CakeTestCase
	{
		/**
		 * Fixtures utilisés par ces tests unitaires.
		 *
		 * @var array
		 */
		public $fixtures = array(
			'core.Article'
		);

		/**
		 * Préparation du test.
		 *
		 * @return void
		 */
		public function setUp() {
			parent::setUp();
			$controller = null;
			$this->View = new View( $controller );
			$this->Xpaginator = new XpaginatorHelper( $this->View );
			$this->Apple = ClassRegistry::init( 'Article' );

			$this->Xpaginator->request = new CakeRequest( null, false );
			$this->Xpaginator->request->addParams(
				array(
					'controller' => 'articles',
					'action' => 'index',
					'paging' => array(
						'Article' => array(
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
								'conditions' => array()
							),
							'paramType' => 'named'
						)
					)
				)
			);
		}

		/**
		 * Nettoyage postérieur au test.
		 *
		 * @return void
		 */
		public function tearDown() {
			parent::tearDown();
			unset( $this->View, $this->Xpaginator );
		}

		/**
		 * Test de la méthode XpaginatorHelper::sort()
		 *
		 * @return void
		 */
		public function testSort() {
			$result = $this->Xpaginator->sort( 'Article.title' );
			$expected = '<a href="/index/page:1/sort:Article.title/direction:asc">Article.title</a>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}

		/**
		 * Test de la méthode XpaginatorHelper::paginationBlock()
		 *
		 * @return void
		 */
		public function testPaginationBlock() {
			Configure::write( 'Optimisations.Articles_index.progressivePaginate', false );
			$result = $this->Xpaginator->paginationBlock( 'Article', array() );
			$expected = '<p class="pagination counter">Page 2 sur 7, montrant 9 enregistrements parmi 62 au total, à partir de l\'enregistrement n° 21 et jusqu\'au n° 40</p><p class="pagination links"><span><a href="/index/page:1" rel="first">&lt;&lt;</a></span> <span class="prev">&lt;</span> <span><a href="/index/page:1">1</a></span> | <span class="current">2</span> | <span><a href="/index/page:3">3</a></span> | <span><a href="/index/page:4">4</a></span> | <span><a href="/index/page:5">5</a></span> | <span><a href="/index/page:6">6</a></span> | <span><a href="/index/page:7">7</a></span> <span class="next"><a href="/index/page:3" rel="next">&gt;</a></span> <span><a href="/index/page:7" rel="last">&gt;&gt;</a></span></p>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );

			Configure::write( 'Optimisations.Articles_index.progressivePaginate', true );
			$this->Xpaginator->request->params['paging']['Article']['pageCount'] = 3;
			$this->Xpaginator->request->params['paging']['Article']['count'] = 41;
			$result = $this->Xpaginator->paginationBlock( 'Article', array() );
			$expected = '<p class="pagination counter">Résultats 21 - 40 sur au moins 41 résultats.</p><p class="pagination links"><span><a href="/index/page:1" rel="first">&lt;&lt;</a></span> <span class="prev">&lt;</span> <span><a href="/index/page:1">1</a></span> | <span class="current">2</span> | <span><a href="/index/page:3">3</a></span> <span class="next"><a href="/index/page:3" rel="next">&gt;</a></span></p>';
			$this->assertEquals( $result, $expected, var_export( $result, true ) );
		}
	}
?>