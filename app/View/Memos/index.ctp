<?php 
	$this->pageTitle = 'Mémos concernant la personne';
	$defaultParams = array('options' => !empty($options) ? $options : array(), 'paginate' => false);
	echo $this->element('default_index');
	
	// Formatage des textes de mémos pour la liste
	if (!empty($memos)) {
		foreach (Hash::extract($memos, '{n}.Memo.name') as $key => $value) {
			$memos[$key]['Memo']['name'] = nl2br(
				String::truncate($value, 250)
			);
		}
	}
	
	echo $this->Default3->index(
		$memos,
		$this->Translator->normalize(
			array(
				'Memo.name',
				'Memo.created',
				'Memo.modified',
			) + WebrsaAccess::links(
				array(
					'/Memos/view/#Memo.id#',
					'/Memos/edit/#Memo.id#',
					'/Memos/delete/#Memo.id#' => array('confirm' => true),
					'/Memos/filelink/#Memo.id#' => array('msgid' => __m('/Memos/filelink').' (#Memo.nb_fichiers_lies#)'),
				)
			)
		),
		$defaultParams
	);