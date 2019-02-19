<?php
	echo $this->element(
		'WebrsaParametrages/add_edit',
		array(
			'fields' => array(
				'Accueilarticle.id',
				'Accueilarticle.title',
				'Accueilarticle.content',
				'Accueilarticle.actif' => array( 'type' => 'checkbox' ),
				'Accueilarticle.publicationto',
				'Accueilarticle.publicationfrom',
			)
		)
	);
?>

<script src="<?php echo ((isset($_SERVER['HTTPS']) ? 'https://' : 'http://').$_SERVER['HTTP_HOST']); ?>/js/tinymce/tinymce.min.js"></script>
<script>
	tinymce.init({
		selector:'textarea',
		plugins: "link",
		toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify",
		toolbar2: "cut copy paste | outdent indent blockquote | link unlink code | undo redo",
//		toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
//		toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
//		toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",
});
</script>