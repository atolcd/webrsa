<?php
/*
 * Classe GDO_FieldType
 * ---------------
 * Un GDO_FieldType est un objet servant à alimenter la valeur
 * d'un champ utilisateur dans le modèle de document
 *
 * Version 1.0
 */


class GDO_FieldType
{

    public $target;
    public $value;
    public $dataType;

    // }}}
    // {{{ GDO_FieldType ()

    /**
     * Constructeur
     *
     * @param    string      $name      Non du champ utilisateur
     * @param    string      $value     Valeur à insérer
     * @param    string      $sDataType     type de donnée ("string", "number", "date", "text")
     * @since    1.0
     * @access   public
     */
    public function __construct($target, $value, $sDataType)
    {
        $this->target= $target;
        $this->value = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $value); //Remove CTRL-CHAR
        if ($sDataType == "date" || $sDataType == "number" || $sDataType == "text") {
            $this->dataType = $sDataType;
        } else {
            $this->dataType = "string";
        }
    }
}
