<?php $this->pageTitle = 'Paramétrages';?>
<h1><?php echo $this->pageTitle;?></h1>

<div id="liste_parametrages">
	<?php
		$menu = $this->Menu->make2( $items );
		if( false === empty( $menu ) ) {
			echo $menu;
		}
		else {
			echo $this->Html->tag( 'p', 'Vous n\'avez pas accès aux éléments de paramétrage.', array( 'class' => 'notice' ) );
		}
	?>
</div>