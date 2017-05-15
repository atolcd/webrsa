//------------------------------------------------------------------------------
// INFO: il faut étendre Element avant toute utilisation de $
//------------------------------------------------------------------------------
// 1. Ajout de méthodes aux éléments de type formulaire
//------------------------------------------------------------------------------
var WebrsaFormTags = ['BUTTON', 'INPUT', 'OPTGROUP', 'OPTION', 'SELECT', 'TEXTAREA'];

var WebrsaFormMethods = {
	enabled: function (element) {
		var disabled;
		element = $(element);
		disabled = element.readAttribute('disabled');
		return (
			undefined === element.readAttribute('disabled')
			|| null === element.readAttribute('disabled')
		);
	}
};

Element.addMethods(WebrsaFormTags, WebrsaFormMethods);

//------------------------------------------------------------------------------
// 2. Surcharge des méthodes enable et disable
//------------------------------------------------------------------------------
Form.Element.Methods.disable = Form.Element.Methods.disable.wrap(
	function (callOriginal, element) {
		var wrapper = $(element).up([ 'div.input', 'div.checkbox' ]);
		if(wrapper) {
			wrapper.addClassName( 'disabled' );
		}
		return callOriginal(element);
	}
);

Form.Element.Methods.enable = Form.Element.Methods.enable.wrap(
	function (callOriginal, element) {
		var wrapper = $(element).up([ 'div.input', 'div.checkbox' ]);
		if(wrapper) {
			wrapper.removeClassName( 'disabled' );
		}
		return callOriginal(element);
	}
);

Object.extend(Form.Element, Form.Element.Methods);
Element.addMethods(WebrsaFormTags, Form.Element.Methods);