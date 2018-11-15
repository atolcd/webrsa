<?php
	// $searchFormId
	$searchFormId = isset( $searchFormId ) ? $searchFormId : Inflector::camelize( "{$this->request->params['controller']}_{$this->request->params['action']}_form" );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js', 'cake.prototype.js' ) );
	}

	echo $this->Default3->titleForLayout();

	$actions['/'.Inflector::camelize( $this->request->params['controller'] ).'/'.$this->request->params['action'].'/#toggleform'] =  array(
		'title' => 'Visibilité formulaire', // TODO: nettoyer les fichiers de traduction
		'text' => 'Formulaire', // TODO: nettoyer les fichiers de traduction
		'class' => 'search',
		'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
	);
	echo $this->Default3->actions( $actions );

	echo $this->Form->create(
		'Accueilscorrespondances',
		array(
			'type' => 'post',
			'id' => $searchFormId,
		)
	);
?>
	<fieldset>
		<legend><?php echo (__d( 'accueilscorrespondances', 'Accueilscorrespondances.titre' )); ?></legend>
<?php
	echo $this->Form->input( 'Search.Correspondance.nom', array( 'label' => __d( 'accueilscorrespondances', 'Accueilscorrespondances.nom' ), 'type' => 'text' ) );
	echo $this->Form->input( 'Search.Correspondance.prenom', array( 'label' => __d( 'accueilscorrespondances', 'Accueilscorrespondances.prenom' ), 'type' => 'text' ) );
	echo $this->Form->input( 'Search.Correspondance.groupe', array( 'label' => __d( 'accueilscorrespondances', 'Accueilscorrespondances.groupe' ), 'type' => 'select', 'options' => $groups ) );
?>
	</fieldset>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php
	echo $this->Form->end();
?>


<?php
	if (isset ($users) && count ($users) > 0) {
?>
	<form method="post">
		<table id="TableAccueilscorrespondances" class="dossiers search tooltips" style="width: 100%">
			<thead>
				<tr id="">
					<th id="" class="desc"><?php echo (__d( 'accueilscorrespondances', 'Accueilscorrespondances.utilisateurs' )); ?></th>
					<th id="" class="desc"><?php echo (__d( 'accueilscorrespondances', 'Accueilscorrespondances.referents' )); ?></th>
					<th id="" class="desc"><?php echo (__d( 'accueilscorrespondances', 'Accueilscorrespondances.accueil_reference_affichage' )); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
		foreach ($users as $user) {
?>
				<tr class="odd dynamic">
					<td class="data string "><?php echo ($user['User']['nom'].' '.$user['User']['prenom']); ?></td>
					<td class="data date ">
						<select name="referent_id[<?php echo ($user['User']['id']); ?>]">
							<option value=""></option>
<?php
			foreach ($referents as $referent) {
				$value = 'value="'.$referent['Referent']['id'].'"';
				$nom = $referent['Referent']['nom'].' '.$referent['Referent']['prenom'];
				if (isset ($referent['Structurereferente']['lib_struc'])) {
					$nom .= ' ('.$referent['Structurereferente']['lib_struc'].')';
				}
				$selected = $referent['Referent']['id'] == $user['User']['accueil_referent_id'] ? 'selected="selected"' : '';
?>
							<option <?php echo ($value); ?> <?php echo ($selected); ?>><?php echo ($nom); ?></option>
<?php
			}
?>
						</select>
					</td>
					<td class="data date ">
						<select name="reference_affichage[<?php echo ($user['User']['id']); ?>]">
<?php

			foreach ($options['User']['accueil_reference_affichage'] as $key => $referenceAffichage) {
				$value = 'value="'.$key.'"';
				$nom = $referenceAffichage;
				$selected = $key == $user['User']['accueil_reference_affichage'] ? 'selected="selected"' : '';
?>
							<option <?php echo ($value); ?> <?php echo ($selected); ?>><?php echo ($nom); ?></option>
<?php
			}
?>
						</select>
					</td>
				</tr>
<?php
		}
?>
			</tbody>
		</table>

		<input type="hidden" name="Search.Correspondance.nom" value="<?php echo (isset ($this->request->data['Search']['Correspondance']['nom']) ? $this->request->data['Search']['Correspondance']['nom'] : '') ?>" />
		<input type="hidden" name="Search.Correspondance.prenom" value="<?php echo (isset ($this->request->data['Search']['Correspondance']['prenom']) ? $this->request->data['Search']['Correspondance']['prenom'] : '') ?>" />
		<input type="hidden" name="Search.Correspondance.groupe" value="<?php echo (isset ($this->request->data['Search']['Correspondance']['groupe']) ? $this->request->data['Search']['Correspondance']['groupe'] : '') ?>" />

		<div class="submit noprint">
			<?php echo $this->Form->button(__d( 'accueilscorrespondances', 'Accueilscorrespondances.enregistrer' ), array( 'type' => 'submit' ) );?>
		</div>
	</form>
<?php
	}
?>