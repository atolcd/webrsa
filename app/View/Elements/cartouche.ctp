<?php if( $this->Session->check( 'Auth.User' ) ):?>
<?php $nomuser=$this->Session->read('Auth.User.username');?>
    <div id="pageCartouche">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Groupe</th>
                    <th>Service instructeur</th>
                </tr>
            </thead>
            <tbody>
                <tr>
					<?php if( Configure::read( 'Apre.complementaire.query' ) === true ):?>
					<td>
						<?php echo $this->Xhtml->link("QUERY", sprintf( Configure::read( 'Apre.complementaire.queryUrl' ), $nomuser ),array("target" => "_blank")); ?>
					</td>
					<?php endif;?>
					<td>
						<?php
							echo $this->Xhtml->link(
								$this->Session->read('Auth.User.nom' ),
								array(
									'controller'=>'users',
									'action'=>'changepass'
								),
								 array( 'enabled' => $this->Permissions->check( 'users', 'changepass' ) )
							);
						?>
					</td>
                    <td> <?php echo $this->Session->read( 'Auth.User.prenom' ) ;?> </td>
                    <td> <?php echo $this->Session->read( 'Auth.Group.name' ) ;?> </td>
                    <td> <?php echo $this->Session->read( 'Auth.Serviceinstructeur.lib_service' ) ;?> </td>
				<?php if( !Configure::read( 'Jetons2.disabled' ) && Configure::read( 'Etatjetons.enabled' ) && isset($jetons_count) ) {?>
                    <td class="dossier_locked"><a href="#" id="jetons_count" onclick="jetonDelete()"><?php echo $jetons_count;?></a></td>
				<?php } ?>
                </tr>
            </tbody>
        </table>
    </div>
<?php if( !Configure::read( 'Jetons2.disabled' ) && Configure::read( 'Etatjetons.enabled' ) ) {?>
	<script type="text/javascript">
		//<![CDATA[
		function jetonDelete( user_id ) {
			if ( $('jetons_count').innerHTML !== '0'
				&& confirm("La libération des dossiers nécessite un rechargement de la page, toutes les modifications non sauvegardées seront perdues. Voulez-vous continuer ?") ) {
				new Ajax.Request('<?php echo Router::url( array( 'controller' => 'jetons', 'action' => 'ajax_delete' ) ).'/';?>', {
					asynchronous:true,
					evalScripts:true,
					requestHeaders: {Accept: 'application/json'},
					onComplete: function(request, json) {
						if ( json ) {
							$('jetons_count').innerHTML = '0';
							location.reload();
						}
					},
					onFailure: function() {
						$('jetons_count').innerHTML = '?';
					}
				});
			}
		}
		//]]>
	</script>
<?php }?>
<?php endif;?>
