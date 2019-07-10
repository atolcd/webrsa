<?php

	echo $this->Default3->titleForLayout();
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if (empty($titrescreanciers) ){
		echo "<br><h2>".__m('Upload File')."</h2>"
		?>
<div class="content">

		<?php echo $this->Form->create($uploadData, array ('type' => 'file')); ?>

		<fieldset id="filecontainer-piecejointe" class="noborder invisible">
			<div id="file-uploader-piecejointe" >
				<?php echo $this->Form->input(__m('File'), array ('type' => 'file', 'class' => 'file-uploader-piecejointe')); ?>
			<br>
			</div>
	    </fieldset>
            <?php echo $this->Form->button(__m('Upload File'), array ('type'=>'submit', 'class' => 'form-controlbtn btn-default ')); ?>

        <?php echo $this->Form->end(); ?>
</div>
<?php

	}else{
		echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate' ) );

		foreach ($titrescreanciers as $key => $element){
					$vals[$key.'.id'] = array('type'=>'hidden','value'=>$element['Titrecreancier']['id']);
					$vals[$key.'.creance_id'] = array('type'=>'hidden','value'=>$element['Titrecreancier']['creance_id']);
					$vals[$key.'.numtitr'] = array('type'=>'hidden','value'=>$element['Titrecreancier']['mnttitr']);
					$vals[$key.'.dtbordereau'] = array('type'=>'hidden','value'=>$element['Titrecreancier']['dtbordereau']);
					$vals[$key.'.numbordereau'] = array('type'=>'hidden','value'=>$element['Titrecreancier']['numbordereau']);
					$vals[$key.'.numtier'] = array('type'=>'hidden','value'=>$element['Titrecreancier']['numtier']);
					$vals[$key.'.etat'] = array('type'=>'hidden','value'=>$element['Titrecreancier']['etat']);
			}
		echo $this->Default3->subform(
			$vals,
			array(
				'options' => $options
			)
		);

		echo $this->Default3->index(
				$titrescreanciers,
				$this->Translator->normalize(
					array(
						'Titrecreancier.dtemissiontitre',
						'Titrecreancier.nom',
						'Titrecreancier.mnttitr',
						'Titrecreancier.numtitr',
						'Titrecreancier.dtbordereau',
						'Titrecreancier.numbordereau',
						'Titrecreancier.numtier',
					)
				),
				array(
					'paginate' => false,
					'options' => $options,
					'empty_label' => __m('Titrecreancier::index::emptyLabel'),
				)
			);
			echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
			echo $this->Default3->DefaultForm->end();
			echo $this->Default3->index(
				$abandonedlines,
				$this->Translator->normalize(
					array(
						'ligneperdu.0',
						'ligneperdu.1',
						'ligneperdu.2',
					)
				),
				array(
					'paginate' => false,
					'options' => $options,
					'empty_label' => __m('Titrecreancier::index::emptyLabel'),
				)
			);
	}

	echo $this->Observer->disableFormOnSubmit( Inflector::camelize( "titrecreancier_{$this->request->params['action']}_form" ) );
