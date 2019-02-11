<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Relance.id',
				'Relance.relancesupport',
				'Relance.relancetype',
				'Relance.relancemode',
				'Relance.nombredejour',
				'Relance.contenu' => array( 'type' => 'textarea' ),
				'Relance.actif' => array( 'type' => 'checkbox' )
			)
		)
	);
?>
	<script type="text/javascript">
		//<![CDATA[
		Event.observe($('RelanceRelancesupport'), 'change', function(e){
			if ($('RelanceRelancesupport').getValue() == 'SMS') {
				$('RelanceRelancemode').setValue('ORANGE_CONTACT_EVERYONE');
			}
			if ($('RelanceRelancesupport').getValue() == 'EMAIL') {
				$('RelanceRelancemode').setValue('EMAIL');
			}
		});
		//]]>
	</script>

	<div>Nombre de caractrères : <span id="compteur"></span></div>
	<script type="text/javascript">
		document.observe('dom:loaded', function(){
			Event.observe('RelanceContenu', 'keyup', compteur);

			// premier appel pour initialiser le compteur
			compteur();
		});

		function compteur(){
			$('compteur').update ($F('RelanceContenu').length );
		}
	</script>
	<br />

	<fieldset>
		<legend>Liste des variables utilisables :</legend>
		<table style="width: 40%;">
			<tr>
				<td colspan="2" style="font-weight: bold; text-align: center;">Pour une relance par SMS ou MAIL</td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Description</td>
				<td style="font-weight: bold;">Code</td>
			</tr>
<?php
	$variables = Configure::read('relances.variables');

	foreach ($variables as $key => $value) {
?>
			<tr>
				<td><?php echo $key ?></td>
				<td><?php echo $value ?></td>
			</tr>
<?php
	}
?>
		</table>
	</fieldset>