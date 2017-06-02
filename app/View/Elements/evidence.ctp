<?php
	$configureKey = sprintf( 'Evidence.%s.%s', Inflector::camelize( $this->request->params['controller'] ), $this->request->params['action'] );
	$config = (array)Configure::read( $configureKey );
?>
<?php if( !empty( $config ) ): ?>
<script type="text/javascript">
//<![CDATA[
document.observe( 'dom:loaded', function() {
	try {
<?php
	foreach( Hash::normalize( (array)Hash::get( $config, 'fields' ) ) as $selector => $params ) {
		$params = (array)$params + array( 'title' => false, 'class' => 'evidence' );
		echo sprintf( "Evidence.setQuestionParams( '%s', %s );\n", $selector, json_encode( $params ) );
	}

	foreach( Hash::normalize( (array)Hash::get( $config, 'options' ) ) as $selector => $params ) {
		$params = (array)$params + array( 'title' => false, 'class' => 'evidence' );
		echo sprintf( "Evidence.setOptionParams( '%s', %s );\n", $selector, json_encode( $params ) );
	}
?>
	} catch(exception) {
		console.log(exception);
	}
 } );
//]]>
</script>
<?php endif; ?>