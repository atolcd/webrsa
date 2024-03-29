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
					<?php
					if ($this->Permissions->check( 'didacticiels', 'display' )) {
					?>
					<td>
						<?php
							echo $this->Xhtml->link(
								__d('droit', 'controllers/Didacticiels'),
								array(
									'controller'=>'didacticiels',
									'action'=>'index',
								),
								array(
									'enabled' => $this->Permissions->check( 'didacticiels', 'display' ),
									'target' => '_blank'
								)
							);
						?>
					</td>
					<?php
					}
					?>
					<?php
					if ($this->Permissions->check( 'tutoriels', 'view' )) {
					?>
					<td>
						<?php
							echo $this->Xhtml->link(
								__d('droit', 'controllers/Tutoriels'),
								array(
									'controller'=>'tutoriels',
									'action'=>'view',
								),
								array(
									'enabled' => $this->Permissions->check( 'tutoriels', 'view' ),
								)
							);
						?>
					</td>
					<?php
					}
					?>
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
					<td class="dossier_locked">
					<?php
						echo $this->Xhtml->link(
							$jetons_count,
							array(
								'controller'=>'users',
								'action'=>'delete_jetons',
								$this->Session->read( 'Auth.User.id' ),
								'full_base' => true,
							),
							array(),
							'La libération des dossiers nécessite un rechargement de la page, toutes les modifications non sauvegardées seront perdues. Voulez-vous continuer ?'
						);
					?>
					</td>
				<?php } ?>
					<td class="reset_cache">
						<?php
							echo $this->Xhtml->link(
								'',
								array(
									'controller'=>'caches',
									'action'=>'reinitializeCache',
								),
								array(
									'title' => __d('cache', 'Cache.title'),
									'enabled' => $this->Permissions->check( 'caches', 'reinitializeCache' ),
								),
								__d('cache', 'Cache.confirm')
							);
						?>
					</td>
                </tr>
            </tbody>
        </table>
    </div>
<?php endif;?>
