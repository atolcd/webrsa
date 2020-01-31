<?php
	echo $this->element('default_index');

	echo $this->Default3->index(
		$contratsinsertion,
		$this->Translator->normalize(
			array(
				//'Contratinsertion.total',
				'Contratinsertion.totalCumulCER',
				'Contratinsertion.forme_ci',
				'Contratinsertion.num_contrat_66',
				'Contratinsertion.dd_ci',
				'Contratinsertion.df_ci',
				'Contratinsertion.date_saisi_ci',
				'Contratinsertion.decision_ci',
				'Contratinsertion.datedecision',
				'Contratinsertion.positioncer',
			)
			+ WebrsaAccess::links(
				array(
					'/Contratsinsertion/view/#Contratinsertion.id#',
					'/Contratsinsertion/edit/#Contratinsertion.id#',
					'/Proposdecisionscers66/propositionsimple/#Contratinsertion.id#' => array(
						'condition' => "'#Contratinsertion.forme_ci#' === 'S'",
						'class' => 'button valider'
					),
					'/Proposdecisionscers66/propositionparticulier/#Contratinsertion.id#' => array(
						'condition' => "'#Contratinsertion.forme_ci#' !== 'S'",
						'class' => 'button valider'
					),
					'/Contratsinsertion/ficheliaisoncer/#Contratinsertion.id#',
					'/Contratsinsertion/notifbenef/#Contratinsertion.id#',
					'/Contratsinsertion/notificationsop/#Contratinsertion.id#' => array(
						'class' => 'button notifop'
					),
					'/Contratsinsertion/impression/#Contratinsertion.id#',
					'/Contratsinsertion/notification/#Contratinsertion.id#',
					'/Contratsinsertion/reconduction_cer_plus_55_ans/#Contratinsertion.id#' => array(
						'class' => 'button reconduction'
					),
					'/Contratsinsertion/cancel/#Contratinsertion.id#',
					'/Contratsinsertion/filelink/#Contratinsertion.id#'
				)
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
			'innerTable' => $this->Translator->normalize(
				array(
					'Contratinsertion.motifannulation' => array(
						'condition' => "'#Contratinsertion.decision_ci#' == 'A' || '#Contratinsertion.positioncer#' == 'annule'"
					),
					'Contratinsertion.duree_engag',
				)
			)
		)
	);
?>
<br>
<br>
<br>
<h2><?php echo (__d ('contratsinsertion_cg66', 'Contratinsertion.reglegestion.texte')); ?></h2>
<ul>
	<li><?php echo (__d ('contratsinsertion_cg66', 'Contratinsertion.reglegestion.epmaintien')); ?></li>
	<li><?php echo (__d ('contratsinsertion_cg66', 'Contratinsertion.reglegestion.epauditionmaintien')); ?></li>
	<li><?php echo (__d ('contratsinsertion_cg66', 'Contratinsertion.reglegestion.epdates')); ?></li>
	<li><?php echo (__d ('contratsinsertion_cg66', 'Contratinsertion.reglegestion.reorientation')); ?></li>
</ul>
<br>
<h2><?php echo (__d ('contratsinsertion_cg66', 'Contratinsertion.reglegestion.epmaintien')); ?></h2>
<ul>
<?php
if (! empty($datesEpParcoursDecisionMaintien)){
	foreach ($datesEpParcoursDecisionMaintien as $item) {
		$date = new DateTime ($item);
		echo ('<li>'.$date->format ('d/m/Y').'</li>');
	}
}
?>
</ul>
<br>
<h2><?php echo (__d ('contratsinsertion_cg66', 'Contratinsertion.reglegestion.epauditionmaintien')); ?></h2>
<ul>
<?php
if (! empty($datesEpAuditionsDecisionMaintien)){
	foreach ($datesEpAuditionsDecisionMaintien as $item) {
		$date = new DateTime ($item);
		echo ('<li>'.$date->format ('d/m/Y').'</li>');
	}
}
?>
</ul>
<br>
<h2><?php echo (__d ('contratsinsertion_cg66', 'Contratinsertion.reglegestion.reorientation')); ?></h2>
<ul>
<?php
if (! empty($datesReorientationAutrePoleEmploi)){
	foreach ($datesReorientationAutrePoleEmploi as $item) {
		$date = new DateTime ($item);
		echo ('<li>'.$date->format ('d/m/Y').'</li>');
	}
}
?>
</ul>