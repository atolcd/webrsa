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
 * @package       Cake.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
echo $this->Html->css( 'AnalyseSql.analysesql' );
?>
<h2><?php echo __d('cake_dev', 'Database Error'); ?></h2>
<p class="error">
	<strong><?php echo __d('cake_dev', 'Error'); ?>: </strong>
	<?php echo h($error->getMessage()); ?>
</p>
<?php if (!empty($error->queryString)) : ?>
	<div class="notice action">
		<strong><?php echo __d('cake_dev', 'SQL Query'); ?>: </strong>
		<a href="javascript:analyseSql(this);" class="view" id="analyseSqlLink">Analyse</a>
		<input type="hidden" id="dump_sql_to_analyse" value="<?php echo h($error->queryString); ?>">
		<p>
			<?php echo  $error->queryString; ?>
		</p>
		<pre style="display:none;" id="analysed_error_pdo"></pre>
	</div>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
		<strong><?php echo __d('cake_dev', 'SQL Query Params'); ?>: </strong>
		<?php echo  Debugger::dump($error->params); ?>
<?php endif; ?>
<p class="notice">
	<strong><?php echo __d('cake_dev', 'Notice'); ?>: </strong>
	<?php echo __d('cake_dev', 'If you want to customize this error message, create %s', APP_DIR . DS . 'View' . DS . 'Errors' . DS . 'pdo_error.ctp'); ?>
</p>
<?php echo $this->element('exception_stack_trace'); 
?>
<script>
	function analyseSql() {
		var image = '<?php echo $this->Html->image('/analyse_sql/img/ajax-loader_gray.gif');?>',
			url = '<?php echo Router::url( array( 'plugin' => 'analyse_sql', 'controller' => 'analysesqls', 'action' => 'ajax_analyse' ) ); ?>',
			failureMsg = '<?php echo addslashes(__d('analysesql', 'onFailure'));?>',
			exceptionMsg = '<?php echo addslashes(__d('analysesql', 'onException'));?>',
			sql = $('dump_sql_to_analyse').value,
			pre = $('analysed_error_pdo');
		
		$('analyseSqlLink').remove();
		
		analyse( sql, pre, url, image, failureMsg, exceptionMsg );
	}
	
</script>