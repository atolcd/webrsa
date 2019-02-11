<?php
/*
 * Classe GDO_ShapeType
 * ---------------
 * Un GDO_ShapeType est un objet servant à changer le
 * style d'une figure graphique dans un dessin
 * inséré dans le document
 *
 * Version 1.0
 */


class GDO_ShapeType
{

    public $name;
    public $style;
    public $text;

    // }}}
    // {{{ GDO_ShapeType ()

    /**
     * Constructeur
     *
     * @param    string      $name      Non de a figure
     * @param    string      $style     nom du style  à affecter
     * @since    1.0
     * @access   public
     */
    public function __construct($name, $style, $text)
    {
        $this->name= $name;
        $this->style= $style;
        $this->text= $text;
    }
}
