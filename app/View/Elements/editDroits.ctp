<?php
	/*
		Affiche les menus-controlleurs pour la saisie des droits
		Paramètres :
	*/
?>
<script type="text/javascript">
	function GereChkbox(conteneur, a_faire) {
		$( conteneur ).getElementsBySelector( 'input[type="checkbox"]' ).each( function( input ) {
			if (a_faire=='cocher') blnEtat=true;
			else if (a_faire=='decocher') blnEtat=false;
			else {
				if ($(input).checked==true) blnEtat=false;
				else blnEtat=true;
			}

			$(input).checked=blnEtat;
		} );
	}

	document.observe( "dom:loaded", function() {
		var baseUrl = '<?php echo Router::url( '/' );?>';
		make_treemenus_droits( baseUrl, <?php echo ( Configure::read( 'UI.menu.large' ) ? 'true' : 'false' );?> );
	} );
</script>
<input type="button" value="Tout cocher" onclick="GereChkbox('tableEditDroits','cocher');" />&nbsp;&nbsp;&nbsp;
<input type="button" value="Tout décocher" onclick="GereChkbox('tableEditDroits','decocher');" />&nbsp;&nbsp;&nbsp;
<input type="button" value="Inverser la sélection" onclick="GereChkbox('tableEditDroits','inverser');" />
<?php
	function cmpTranslatedTilte( $a, $b ) {
		return ( strcmp( $a['translatedTitle'], $b['translatedTitle'] ) );
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script('droits.js');
	}
	echo '<table cellspacing="0" cellpadding="0" style="margin-top:20px;" class="table" id="tableEditDroits">';

	$parentCtrlAction = '';
	$listeTranslatedCtrlAction = array();
	$row = -1;
	foreach($listeCtrlAction as $rownum => $ctrlAction) {
		if ( $ctrlAction['niveau'] == 0 ) {
			$row++;
			list( $module, $parentCtrlAction ) = explode( ':', $ctrlAction['title'] );
			$listeTranslatedCtrlAction[$row] = $ctrlAction;
			if ( $rownum == 0 ) {
				$listeTranslatedCtrlAction[$row]['translatedTitle'] = '&nbsp;'.__d( 'droit', $ctrlAction['title'] );
			}
			else {
				$listeTranslatedCtrlAction[$row]['translatedTitle'] = __d( 'droit', $ctrlAction['title'] );
			}
		}
		else {
			$listeTranslatedCtrlAction[$row][] = array_merge(
				$ctrlAction,
				array( 'translatedTitle' => __d( 'droit', $parentCtrlAction.':'.$ctrlAction['title'] ) )
			);
		}
	}
	usort($listeTranslatedCtrlAction, "cmpTranslatedTilte");

	$newListeCtrlAction = array();
	$rownum = 0;
	foreach( $listeTranslatedCtrlAction as $ctrlAction ) {
		$newListeCtrlAction[$rownum] = $ctrlAction;
		foreach( $ctrlAction as $row => $subCtrlAction ) {
			if ( is_numeric( $row ) ) {
				$rownum++;
				$newListeCtrlAction[$rownum] = $subCtrlAction;
			}
		}
		$rownum++;
	}

	foreach($newListeCtrlAction as $rownum => $ctrlAction) {
		$classTd = 'niveau'.$ctrlAction['niveau'];
		if ( $ctrlAction['niveau'] == 0 ) {
			$ctrlAction['title'] = '<b>'.$ctrlAction['translatedTitle'].'</b>';
		}
		else {
			$ctrlAction['title'] = $ctrlAction['translatedTitle'];
		}
		$indentation = str_repeat( '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $ctrlAction['niveau'] );
		$optionsCheckBox = array(
			'label' => '',
			'type' => 'checkbox',
			'id' => 'chkBoxDroits'.$rownum,
			'div' => false
		);

		if ( $ctrlAction['nbSousElements'] > 0 ) {
			$optionsCheckBox['onclick'] = 'toggleCheckBoxDroits('.$rownum.', '.$ctrlAction['nbSousElements'].');';
		}
		else {
			$optionsCheckBox['onclick'] = 'syncDroitsEnfantsParents( $(this) );';
		}

		echo '<tr class="'.$classTd.'">';
			echo $this->Xhtml->tag( 'td', ' '.$indentation.$ctrlAction['title'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', array( 'class' => "{$classTd} label children{$ctrlAction['nbSousElements']}" ) );
			if ( $ctrlAction['modifiable'] ) {
				echo $this->Xhtml->tag( 'td', $this->Form->input( 'Droits.'.$ctrlAction['acosAlias'], $optionsCheckBox ), array( 'class' => $classTd ) );
			}
			else
				echo $this->Xhtml->tag( 'td', $this->Form->hidden('Droits.'.$ctrlAction['acosAlias'] ), array( 'class'=>$classTd ) );
		echo '</tr>';
	}
	echo '</table>';
?>