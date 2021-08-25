<ul class="actions">
	<li class="action">
		<?php
			echo $this->Xhtml->link (
				__d ('sitescovs58', '/Parametrages/index'),
				array ('controller' => 'sitescovs58', 'action' => 'index'),
				array (
					'class' => 'back link',
					'enabled' => true
				)
			);
		?>
    </li>
    <li class="action">
		<?php
			echo $this->Xhtml->link (
				__d ('canton', 'Canton.onglet.general'),
				array ('controller' => 'sitescovs58', 'action' => 'edit', $id),
				array (
					'class' => 'edit link',
					'enabled' => true
				)
			);
		?>
    </li>
    <li class="action">
		<?php
			echo $this->Xhtml->link (
				__d ('canton', 'Canton.onglet.adresse'),
				array ('controller' => 'sitescovs58', 'action' => 'adresse', $id),
				array (
					'class' => 'edit link',
					'enabled' => true
				)
			);
		?>
    </li>
</ul>
<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Sitecov58.id',
				'Sitecov58.name',
				'Sitecov58.lib_adresse',
				'Sitecov58.num_voie',
				'Sitecov58.type_voie' => array('type' => 'select', 'options' => $options['type_voie'], 'empty' => true ),
				'Sitecov58.nom_voie',
				'Sitecov58.code_postal',
				'Sitecov58.ville',
				'Sitecov58.code_insee',
				'Sitecov58.actif' => array(
					'type' => 'checkbox',
				),
				'Zonegeographique.Zonegeographique' => array(
					'fieldset' => true,
					'label' => 'Zones géographiques',
					'multiple' => 'checkbox',
					'class' => 'col3'
				)
			)
		)
	);
?>
<script type="text/javascript">
//<![CDATA[
	document.observe( 'dom:loaded', function() {
		insertButtonsCocherDecocher(
			$$( 'fieldset' )[0],
			"input[name=\"data[Zonegeographique][Zonegeographique][]\"]"
		);
	} );
//]]>
</script>