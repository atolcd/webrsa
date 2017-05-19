<div class="aere">
	<?php if( !empty( $typeaideapre ) ):?>
	<?php
// 	debug( $typeaideapre );
		$tmp = array(
			'Typeaideapre66.objetaide' => Set::classicExtract( $typeaideapre, 'Typeaideapre66.objetaide' ),
			'Typeaideapre66.plafond' => $isapre === '1' 
				? Hash::get($typeaideapre, 'Typeaideapre66.plafond')
				: Hash::get($typeaideapre, 'Typeaideapre66.plafondadre')
		);
		echo $this->Default->view(
			Hash::expand( $tmp ),
			array(
				'Typeaideapre66.plafond' => array( 'type' => 'money' )
			),
			array(
				'class' => 'inform'
			)
		);
	?>
	<?php endif;?>
	<table class="wide noborder">
		<tr>
			<td class="noborder">
				<?php if( !empty( $piecesadmin ) ):?>
					<?php
						echo $this->Default->subform(
							array(
								'Pieceaide66.Pieceaide66' => array( 'label' => 'Pièces administratives', 'multiple' => 'checkbox', 'options' => $piecesadmin, 'empty' => false )
							)
						);
					?>
				<?php endif;?>
			</td>

			<td class="noborder">
				<?php if( !empty( $piecescomptable ) ):?>
					<?php
						echo $this->Default->subform(
							array(
								'Piececomptable66.Piececomptable66' => array( 'label' => 'Cocher les pièces comptables à fournir', 'multiple' => 'checkbox', 'options' => $piecescomptable, 'empty' => false )
							)
						);
					?>
				<?php endif;?>
			</td>
		</tr>
	</table>
</div>