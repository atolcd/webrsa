<?php
	/**
	 *
	 * PHP 5
	 *
	 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
	 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
	 * @link          http://cakephp.org CakePHP(tm) Project
	 * @package       Cake.View.Layouts
	 * @since         CakePHP(tm) v 0.10.0.1076
	 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
	 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php echo $this->Html->charset(); ?>
		<title>
			<?php
				if( isset( $this->pageTitle ) && !empty( $this->pageTitle ) ) {
					echo $this->pageTitle;
				}
				else {
					echo $title_for_layout;
				}
			?>
		</title>
		<?php
			if( Configure::read( 'debug' ) ) {
				echo $this->Html->css( array( 'all.reset' ), 'stylesheet', array( 'media' => 'all' ) );
				echo $this->Html->css( array( 'all.base' ), 'stylesheet', array( 'media' => 'all' ) );
				echo $this->Html->css( array( 'bootstrap.custom' ), 'stylesheet', array( 'media' => 'all' ) ); // Ajoute quelques styles issu de bootstrap
				echo $this->Html->css( array( 'screen.generic', 'screen.search' ), 'stylesheet', array( 'media' => 'screen,presentation' ) );
				echo $this->Html->css( array( 'print.generic' ), 'stylesheet', array( 'media' => 'print' ) );
				echo $this->Html->css( array( 'menu' ), 'stylesheet', array( 'media' => 'all' ) );
				echo $this->Html->css( array( 'popup' ), 'stylesheet', array( 'media' => 'all' ) );
				echo $this->Html->css( 'Configuration.configuration_parser' );
				echo $this->Html->css( 'AnalyseSql.analysesql' );

				echo $this->Html->script( 'prototype' );
				echo $this->Html->script( 'webrsa.extended.prototype' );
				echo $this->Html->script( 'tooltip.prototype' );
				echo $this->Html->script( 'webrsa.common.prototype' );
				echo $this->html->script( 'webrsa.additional' );
				echo $this->Html->script( 'webrsa.validaterules' );
				echo $this->Html->script( 'webrsa.validateforms' );
				echo $this->Html->script( 'Configuration.prototype.configuration-parser' );

			}
			else {
				echo $this->Html->css( array( 'webrsa' ), 'stylesheet' );
				echo $this->Html->script( 'webrsa' );
			}

			echo $this->fetch( 'meta' );
			echo $this->fetch( 'css' );
			echo $this->fetch( 'script' );
		?>

		<!-- TODO: à la manière de cake, dans les vues qui en ont besoin -->
		<script type="text/javascript">
		<!--//--><![CDATA[//><!--
			// prototype
			document.observe( "dom:loaded", function() {
				<?php
					$backAllowed = true;

					$pagesBackNotAllowed = array(
						'Recours::gracieux',
						'Recours::contentieux',
						'Contratsinsertion::valider',
						'Ajoutdossiers::wizard',
						'Ajoutdossiers::confirm',
						'Users::login',
					);

					if( ( in_array( $this->action, array( 'add', 'edit', 'delete' ) ) )
						|| 0 === strpos( $this->request->params['controller'], 'cohorte' )
						|| 0 === strpos($this->request->params['action'], 'cohorte')
						|| in_array( $this->name.'::'.$this->action, $pagesBackNotAllowed )
					) {
						$backAllowed = false;
					}
				?>
				<?php if( !$backAllowed && Configure::read( 'debug' ) == 0 ):?>
				window.history.forward();
				<?php endif;?>

				var baseUrl = '<?php echo Router::url( '/' );?>';
				<?php if ( isset( $urlmenu ) ) { ?>
					var urlmenu = '<?php echo $urlmenu ?>';
				<?php } else { ?>
					var urlmenu = null;
				<?php } ?>
				make_treemenus( baseUrl, <?php echo  Configure::read( 'UI.menu.large' ) ? 'true' : 'false' ;?>, urlmenu );
				make_folded_forms();
				mkTooltipTables();
				make_external_links();

				<?php if( isset( $useAlerteFinSession ) && $useAlerteFinSession ):?>
				if( '<?php echo $useAlerteFinSession;?>' ) {
					var sessionTime = parseInt('<?php echo readTimeout(); ?>');
					var warning5minutes = sessionTime - (5*60);
					setTimeout(alert5minutes, warning5minutes*1000);
					setTimeout(sessionEnd, sessionTime*1000);
				}
				<?php endif;?>

				<?php if (Configure::read('textarea.auto_resize.all')
					|| Configure::read('textarea.auto_resize.'.$this->request->params['controller'].'.all')
					|| Configure::read('textarea.auto_resize.'.$this->request->params['controller'].'.'.$this->action)
				): ?>

				$$('textarea').each(function(element) {
					makeTextareaAutoExpandable(element);
				});

				<?php endif;?>
			} );

			<?php if( isset( $useAlerteFinSession ) && $useAlerteFinSession ):?>
			function alert5minutes() {
				$('alertEndSession').show();
			}

			function sessionEnd() {
				var baseUrl = '<?php echo Router::url( array( 'controller' => 'users', 'action' => 'logout' ) ); ?>';
				location.replace(baseUrl);
			}
			<?php endif;?>
		//--><!]]>
		</script>
		<!--[if IE]>
			<style type="text/css" media="screen, presentation">
				.treemenu { position: relative; }
				.treemenu, .treemenu *, #pageMenu, #pageWrapper { zoom: 1; }
			</style>
		<![endif]-->
	</head><?php $departement = Configure::read( 'Cg.departement' );?>
	<body class="<?php echo 'cg'.$departement.' '.Inflector::underscore( $this->name ).' '.$this->action;?>">

	<?php if (Configure::read('UI.beforeLogo.text') || Configure::read('UI.afterLogo.text')):?>
		<div style="position: relative">
			<div class="beforeLogo"><?php echo Configure::read('UI.beforeLogo.text');?></div>
			<div class="afterLogo"><?php echo Configure::read('UI.afterLogo.text');?></div>
		</div>
	<?php endif;?>

<?php if( isset( $useAlerteFinSession ) && $useAlerteFinSession ):?>
	<div id="alertEndSession" style="display: none;">
		<div id="popups" style="z-index: 1000;">
			<div id="popup_0">
				<div class="hideshow">
					<div class="fade" style="z-index: 31"></div>
					<div class="popup_block">
						<div class="popup">
							<a href="#" onclick="$('alertEndSession').hide(); return false;"><?php echo $this->Xhtml->image('icon_close.png', array('class' => 'cntrl', 'alt' => 'close')); ?></a>
							<div id="popup-content">Attention votre session expire dans 5 minutes.</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif;?>

		<div id="pageWrapper"<?php if( Configure::read( 'UI.menu.large' ) ) { echo ' class="treemenu_large"'; } ?>>
			<div id="pageHeader">
				&nbsp;
			</div>
			<?php
				if( $this->Session->check( 'Auth.User.username' ) ) {
					echo $this->element( 'menu', array(), array( 'cache' => array( 'time' => '+1 day', 'key' => $this->Session->read( 'Auth.User.username' ), 'config' => 'views' ) ) );
					echo $this->element( 'cartouche' );
				}
			?>
			<div id="pageContent"><div id="incrustation_erreur"></div>
				<?php
					foreach ( array_keys((array)$this->Session->read('Message')) as $key ) {
						if ($this->Session->check( 'Message.'.$key ) ) {
							echo $this->Session->flash($key);
						}
					}

					if( isset( $dossierMenu ) ) {
						echo $this->element( 'dossier_menu', array( 'dossierMenu' => $dossierMenu ) );
						echo '<div class="with_treemenu">';
					}
					echo $this->fetch( 'content' );
					if( isset( $dossierMenu ) ) {
						echo '</div><div class="clearer"><hr /></div>';
					}
				?>
			</div>
			<div id="pageFooter"<?php if( Configure::read( 'debug' ) > 0 ) { echo ' style="color: black;"'; }?>>
				webrsa v. <?php echo app_version();?> 2009 - 2017 @ Libriciel SCOP.
				<?php
					if( Configure::read( 'debug' ) > 0 ) {
						echo '( CG '.$departement;
						echo ', BDD '.ClassRegistry::init( 'User' )->getDataSource()->config['database'];
						echo ', '.$this->Html->link( 'requêtes SQL', '#', array( 'onclick' => '$( "sqldump" ).toggle();return false;', 'id' => 'SqlDumpToggler' ) );
						echo " )\n";
					}
					echo sprintf(
						"Page construite en %s secondes. %s / %s. %s modèles",
						number_format( microtime( true ) - $_SERVER['REQUEST_TIME'] , 2, ',', ' ' ),
						byteSize( memory_get_peak_usage( false ) ),
						byteSize( memory_get_peak_usage( true ) ),
						count( ClassRegistry::mapKeys() )
					);
				?>
				(CakePHP v. <?php echo Configure::version();?>)
			</div>
		</div>
		<?php
			echo $this->element( 'evidence' );

			echo $this->fetch( 'scriptBottom' );

			if( Configure::read( 'debug' ) > 0 ) {
				echo $this->Html->tag( 'div', $this->element( 'sql_dump' ), array( 'id' => 'sqldump', 'style' => 'display: none' ) );
			}

			if (Configure::read('Module.Datepicker.enabled')) {
				echo $this->element('Cake2Datepicker.datepicker_auto', array('lang' => 'fr'));
			}
		?>
		<?php if( Configure::read( 'debug' ) > 0 ): ?>
		<script type="text/javascript">
			//<![CDATA[
			$( 'SqlDumpToggler' ).innerHTML = getCakeQueriesCount() + ' ' + $( 'SqlDumpToggler' ).innerHTML;
			//]]>
		</script>
		<?php endif; ?>
		<?php
	if (Configure::read('Module.DisplayValidationErrors.enabled')) {
		echo $this->DisplayValidationErrors->into('p.error');
	}
		?>
	</body>
</html>