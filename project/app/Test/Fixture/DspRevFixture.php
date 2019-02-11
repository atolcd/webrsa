<?php
	/**
	 * Code source de la classe DspRevFixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe DspRevFixture.
	 *
	 * @package app.Test.Fixture
	 */
	class DspRevFixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'DspRev',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'dsp_id' => 1,
				'personne_id' => 1,
				'sitpersdemrsa' => null,
				'topisogroouenf' => null,
				'topdrorsarmiant' => null,
				'drorsarmianta2' => null,
				'topcouvsoc' => null,
				'accosocfam' => null,
				'libcooraccosocfam' => null,
				'accosocindi' => null,
				'libcooraccosocindi' => null,
				'soutdemarsoc' => null,
				'nivetu' => '1202',
				'nivdipmaxobt' => null,
				'annobtnivdipmax' => null,
				'topqualipro' => null,
				'libautrqualipro' => null,
				'topcompeextrapro' => null,
				'libcompeextrapro' => null,
				'topengdemarechemploi' => null,
				'hispro' => null,
				'libderact' => null,
				'libsecactderact' => null,
				'cessderact' => null,
				'topdomideract' => null,
				'libactdomi' => null,
				'libsecactdomi' => null,
				'duractdomi' => null,
				'inscdememploi' => null,
				'topisogrorechemploi' => null,
				'accoemploi' => null,
				'libcooraccoemploi' => null,
				'topprojpro' => null,
				'libemploirech' => null,
				'libsecactrech' => null,
				'topcreareprientre' => null,
				'concoformqualiemploi' => null,
				'topmoyloco' => null,
				'toppermicondub' => null,
				'topautrpermicondu' => null,
				'libautrpermicondu' => null,
				'natlog' => '0909',
				'demarlog' => null,
				'created' => null,
				'modified' => null,
				'libformenv' => null,
				'statutoccupation' => null,
				'haspiecejointe' => '0',
				'suivimedical' => null,
				'libderact66_metier_id' => null,
				'libsecactderact66_secteur_id' => null,
				'libactdomi66_metier_id' => null,
				'libsecactdomi66_secteur_id' => null,
				'libemploirech66_metier_id' => null,
				'libsecactrech66_secteur_id' => null,
			)
		);
	}
?>