<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$line1 = array('Nom technique', 'Traduction');
	
	foreach ($groups as $group) {
		$line1[] = $group;
	}
	
	$this->Csv->addRow($line1);

	foreach($actions as $action) {
		$traduction = __d('droit', $action);
		$row = array($action, $traduction !== $action ? $traduction : '');
		
		foreach ($groups as $group) {
			$row[] = Hash::get($droits, $group.'.'.$action) ? 'X' : '';
		}
		
		$this->Csv->addRow($row);
	}

	echo $this->Csv->render('Liste-droits-'.date('Ymd-His').'.csv');