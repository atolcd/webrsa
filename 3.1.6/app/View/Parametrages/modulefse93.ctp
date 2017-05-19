<?php
	echo $this->Default3->titleForLayout();
?>
<table>
	<thead>
		<tr>
			<th>Nom de table</th>
			<th colspan="2" class="action">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach( $links as $label => $link ) {
				echo $this->Xhtml->tableCells(
					array(
						h( $label ),
						$this->Xhtml->viewLink(
							'Voir la table',
							$link,
							$this->Permissions->check( $link['controller'], $link['action'] )
						)
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			}
		?>
	</tbody>
</table>
<?php
	echo $this->Default3->actions(
	array(
		"/Parametrages/index" => array(
			'class' => 'back',
			'disabled' => !$this->Permissions->check( 'parametrages', 'index' )
		),
	)
);
?>