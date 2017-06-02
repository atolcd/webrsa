<?php $this->pageTitle = 'Visualisation des situations du dossier';?>

<h1><?php echo $this->pageTitle;?></h1>

<div id="ficheInfoFina">
	<table>
		<tbody>
			<tr class="even">
				<th ><?php echo __d( 'situationdossierrsa', 'Situationdossierrsa.etatdosrsa' );?></th>
				<td><?php echo  h( $etatdosrsa[$situationdossierrsa['Situationdossierrsa']['etatdosrsa']] );?></td>
			</tr>
			<tr class="odd">
				<th ><?php echo __d( 'situationdossierrsa', 'Situationdossierrsa.dtrefursa' );?></th>
				<td><?php echo ( date_short( $situationdossierrsa['Situationdossierrsa']['dtrefursa'] ) );?></td>
			</tr>
			<tr class="even">
				<th ><?php echo __( 'moticlorsa' );?></th>
				<td><?php echo ( isset( $moticlorsa[$situationdossierrsa['Situationdossierrsa']['moticlorsa']] ) ? $moticlorsa[$situationdossierrsa['Situationdossierrsa']['moticlorsa']] : null );?></td>
			</tr>
			<tr class="odd">
				<th ><?php echo __d( 'situationdossierrsa', 'Situationdossierrsa.dtclorsa' );?></th>
				<td><?php echo ( date_short( $situationdossierrsa['Situationdossierrsa']['dtclorsa'] ) );?></td>
			</tr>

		</tbody>
	</table>
</div>