<?php
	if( !empty( $values ) ) {
		echo '<div class="input"><label>Régime de l\'allocataire</label>';
		echo $values['Statutpdo'][0]['libelle'];
		echo '<br />';
		for ($i=1;$i<count($values['Statutpdo']);$i++) {
			echo '<label>&nbsp;</label>'.$values['Statutpdo'][$i]['libelle'].'<br />';
		}
		echo '</div>';
	}
?>