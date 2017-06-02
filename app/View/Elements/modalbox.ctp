<div id="<?php echo $modalid;?>" style="display: none;">
	<div id="popups2" style="z-index: 1000;">
		<div id="popup_1">
			<div class="hideshow">
				<div class="fade" style="z-index: 31"></div>
				<div class="popup_block">
					<div class="popup">
						<?php if( !isset( $modalclose ) || $modalclose ): ?>
						<a href="#" onclick="$( '<?php echo $modalid;?>' ).hide(); return false;"><?php echo $this->Xhtml->image('icon_close.png', array('class' => 'cntrl', 'alt' => 'close')); ?></a>
						<?php endif; ?>
						<div id="popup-content1">
							<?php
								if( !empty( $modalmessage ) ) {
									echo $this->Html->tag( 'div', $modalmessage, array( 'class' => 'message' ) );
								}
								echo $modalcontent;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>