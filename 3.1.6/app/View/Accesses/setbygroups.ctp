<?php
	$title = 'Définition des habilitations des groupes par Controller';
	$this->pageTitle = $title;
?>

<h1><?php echo $title;?></h1>
<?php echo $this->Xform->input(false, array(
	'label' => false, 'empty' => true, 'options' => $controllers, 'id' => 'controller', 'name' => false)
);?>

<form method="post" id="access-setter"></form>

<script>
	function accesTotal(input) {
		if (input.checked) {
			input.up('tr').select('input[type="checkbox"]').each(function(element){
				element.checked = true;
				element.disable();
			});
			input.up('tr').select('input[type="hidden"]').each(function(element){
				element.setValue(1);
			});
			input.enable();
		} else {
			input.up('tr').select('input[type="checkbox"]').each(function(element){
				element.enable();
			});
			input.up('tr').select('input[type="hidden"]').each(function(element){
				element.setValue(0);
			});
		}
		
	}
	
	$('controller').observe('change', function() {
		$('access-setter').innerHTML = 'LOADING... <img src="<?php echo $this->webroot; ?>img/loading.gif"/>';
		new Ajax.Updater(
			'access-setter',
			'<?php echo Router::url( array( "action" => "ajax_getform" ) ); ?>',
			{
				asynchronous:true,
				evalScripts:true,
				parameters:{
					'controller': $('controller').getValue()
				},
				requestHeaders:['X-Update', 'access-setter'],
				onComplete:function() {
					$$('input.module_checkbox').each(function(input){
						input.observe('change', function(event){
							accesTotal(event.target);
						});
						accesTotal(input);
					});
				}
			}
		);
	});
</script>