<?php
	// Conditions d'accès aux tags
	$departement = (int)Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );
	$utilisateursAutorises = (array)Configure::read( 'acces.recherche.tag' );
	$viewTag = false;

	foreach ($utilisateursAutorises as $utilisateurAutorise) {
		if ($utilisateurAutorise == $user_type) {
			$viewTag = true;
			break;
		}
	}

	if ($departement != 93) {
		$viewTag = true;
	}
	// Conditions d'accès aux tags

	// "Pagination" un peu spéciale: on veut simplement le nombre de résultats, pas passer de page en page
	$pagination = null;
	if( isset( $results ) ) {
		$paging = Hash::get( $this->request->params, 'paging.Personne' );
		$format = 'Nombre de pages: %s - Nombre de résultats: %s.';
		if( Hash::get( $this->request->data, 'Search.Pagination.nombre_total' ) != '1' ) {
			$page = Hash::get( $paging, 'page' );
			$count = Hash::get( $paging, 'count' );
			$limit = Hash::get( $paging, 'limit' );
			if( ( $count > ( $limit * $page ) ) ) {
				$format = 'Nombre de pages: au moins %s - Nombre de résultats: au moins %s.';
			}
		}

		$pagination = $this->Html->tag(
			'p',
			sprintf( $format, $this->Locale->number( $paging['pageCount'] ), $this->Locale->number( $paging['count'] ) ),
			array( 'class' => 'pagination counter' )
		);
	}

	// id du formulaire de cohorte
	$cohorteFormId = Inflector::camelize( "{$this->request->params['controller']}_{$this->request->params['action']}_cohorte" );

	// Boutons "Tout cocher"
	$buttons = null;
	if( isset( $results ) ) {
		$buttons = $this->Form->button( 'Tout valider', array( 'type' => 'button', 'onclick' => "return toutChoisir( $( '{$cohorteFormId}' ).getInputs( 'radio' ), 'Orienté', true );" ) );
		$buttons .= $this->Form->button( 'Tout mettre en attente', array( 'type' => 'button', 'onclick' => "return toutChoisir( $( '{$cohorteFormId}' ).getInputs( 'radio' ), 'En attente', true );" ) );
	}
?>

<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend><?php echo __m( 'Search.Parcours' ); ?></legend>
	<?php
		echo $this->Xform->input( 'Search.Personne.has_dsp', array( 'type' => 'select', 'options' => $options['Personne']['has_dsp'], 'empty' => true, 'label' => __m( 'Search.Personne.has_dsp' ) ) );
		echo $this->Xform->input( 'Search.Orientstruct.propo_algo', array( 'type' => 'select', 'options' => $options['Orientstruct']['propo_algo'], 'empty' => true, 'label' => __m( 'Search.Orientstruct.propo_algo' ) ) )
	?>
</fieldset>
<?php
if ($viewTag) {
	require_once ('tag.ctp');
}
$this->end();
?>

<?php
	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'paginate' => false,
			'exportcsv' => false,
			'beforeResults' => $pagination,
			'afterResults' => $buttons
		)
	);
?>
<?php if( isset( $results ) ): ?>
	<script type="text/javascript">
		var structAuto = new Array();
	<?php foreach( $options['structuresAutomatiques'] as $typeId => $structureAutomatique ): ?>
			if (structAuto["<?php echo $typeId; ?>"] == undefined) {
				structAuto["<?php echo $typeId; ?>"] = new Array();
			}
		<?php foreach( $structureAutomatique as $codeInsee => $structure ): ?>
				structAuto["<?php echo $typeId; ?>"]["<?php echo $codeInsee; ?>"] = "<?php echo $structure; ?>";
		<?php endforeach; ?>
	<?php endforeach; ?>

		function selectStructure(index) {
			var typeOrient = $F('Cohorte' + index + 'OrientstructTypeorientId');
			var codeinsee = $F('Cohorte' + index + 'AdresseNumcom');
			if ((structAuto[typeOrient] != undefined) && (structAuto[typeOrient][codeinsee] != undefined)) {
				$('Cohorte' + index + 'OrientstructStructurereferenteId').value = structAuto[typeOrient][codeinsee];
			}
		}

		document.observe("dom:loaded", function () {
			var indexes = new Array(<?php echo "'".implode( "', '", array_keys( $results ) )."'"; ?>);

			indexes.each(function (index) {
				/* Dépendance des deux champs "select" */
				dependantSelect(
						'Cohorte' + index + 'OrientstructStructurereferenteId',
						'Cohorte' + index + 'OrientstructTypeorientId'
						);

				/* Structures automatiques suivant le code Insée */
				// Initialisation
				if ($F('Cohorte' + index + 'OrientstructStructurereferenteId') == '') {
					selectStructure(index);
				}

				// Traquer les changements
				Event.observe($('Cohorte' + index + 'OrientstructTypeorientId'), 'change', function () {
					selectStructure(index);
				});
			});
		});
	</script>
<?php endif; ?>