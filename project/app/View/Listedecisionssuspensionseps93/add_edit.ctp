<?php
	echo $this->Default3->titleForLayout( $this->request->data );

	echo $this->FormValidator->generateJavascript();

	echo "<br/>";
	echo "<br/>";
	echo sprintf(__d("listedecisionssuspensionseps93", "rappel_code"), $code);
	echo "<br/>";
	echo sprintf(__d("listedecisionssuspensionseps93", "rappel_courrier"), $courrier);
	echo "<br/>";
	echo "<br/>";
	//Afficher le "code" et le courrier associé

	echo $this->Default3->form(
		$this->Translator->normalize([
			'Listedecisionsuspensionsep93.id',
			'Listedecisionsuspensionsep93.libelle',
			'Listedecisionsuspensionsep93.premier_niveau',
			'Listedecisionsuspensionsep93.deuxieme_niveau',
		])
	);

	echo $this->Observer->disableFormOnSubmit();