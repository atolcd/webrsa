
<?php $this->pageTitle = 'Paramétrages';?>

<h1><?php echo $this->pageTitle;?></h1>


		<?php
				echo $this->Xhtml->link(
					$this->Xhtml->image( 'icons/bullet_toggle_plus2.png', array( 'alt' => '', 'title' => 'Étendre le menu ', 'style' => 'width: 12px;' ) ),
					'#',
					array( 'onclick' => 'treeMenuExpandsAll( \''.Router::url( '/' ).'\' ); return false;', 'id' => 'treemenuToggleLink' ),
					false,
					false
				);
				echo 'Étendre le menu ';
			?>

<div id="liste_parametrages" class='treemenu treemenu_table '>

	<?php
		$menu = $this->Menu->make2( $items);
		if( false === empty( $menu ) ) {
			echo $menu;
		}
		else {
			echo $this->Html->tag( 'p', 'Vous n\'avez pas accès aux éléments de paramétrage.', array( 'class' => 'notice' ) );
		}
	?>
</div>