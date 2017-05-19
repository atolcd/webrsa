
describe("Formulaire", function() {
	it("FormValidator.getModelName( name )", function() {
		expect(FormValidator.getModelName('data[model1][field1]')).toEqual( 'model1' );
		expect(FormValidator.getModelName('data[ma_date][monchamp][day]')).toEqual( 'ma_date' );
		expect(FormValidator.getModelName('data[search][ma_date][monchamp][day]')).toEqual( 'ma_date' );
		expect(FormValidator.getModelName('data[cohorte][0][ma_date][monchamp][day]')).toEqual( 'ma_date' );
	});
	
	it("FormValidator.getFieldName( name )", function() {
		expect(FormValidator.getFieldName('data[model1][field1]')).toEqual( 'field1' );
		expect(FormValidator.getFieldName('data[ma_date][monchamp][day]')).toEqual( 'monchamp' );
		expect(FormValidator.getFieldName('data[search][ma_date][monchamp][day]')).toEqual( 'monchamp' );
		expect(FormValidator.getFieldName('data[cohorte][0][ma_date][monchamp][day]')).toEqual( 'monchamp' );
	});
	
	it("FormValidator.getThirdParam( name )", function() {
		expect(FormValidator.getThirdParam('data[model1][field1]')).toEqual( null );
		expect(FormValidator.getThirdParam('data[ma_date][monchamp][day]')).toEqual( 'day' );
		expect(FormValidator.getThirdParam('data[search][ma_date][monchamp][day]')).toEqual( 'day' );
		expect(FormValidator.getThirdParam('data[cohorte][0][ma_date][monchamp][day]')).toEqual( 'day' );
	});
	
	it("FormValidator.showHeaderError()", function() {
		var headerError = $$('#incrustation_erreur>p.error');
		expect( headerError.length <= 0 ).toEqual( true );
		FormValidator.showHeaderError();
		headerError = $$('#incrustation_erreur>p.error');
		expect( headerError.length <= 0 ).toEqual( false );
	});

	it("FormValidator.getDate( name )", function() {
		expect( FormValidator.getDate( 'data[model3][champ1][day]' ) ).toEqual( '11-11-2015' );
		expect( FormValidator.getDate( 'data[cohorte][0][model3][champ1][year]' ) ).toEqual( '11-11-2015' );
		expect( FormValidator.getDate( 'data[model3][champ2][month]' ) ).toEqual( '31-02-2015' );
		expect( FormValidator.getDate( 'data[cohorte][1][model3][champ1][year]' ) ).toEqual( '31-02-2015' );
	});

	it("FormValidator.getValue( editable )", function() {
		// Champ date
		expect( FormValidator.getValue( $('model3champ1day') ) ).toEqual( '11-11-2015' );
		$('model3champ1month').value = '12';
		expect( FormValidator.getValue( $('model3champ1day') ) ).toEqual( '11-12-2015' );
		expect( FormValidator.getValue( $('model3champ2month') ) ).toEqual( '31-02-2015' );
		expect( FormValidator.getValue( $('cohorte1model3champ1month') ) ).toEqual( '31-02-2015' );
		
		// Champ text
		expect( FormValidator.getValue( $('model1champ1') ) ).toEqual( '' );
		$('model1champ1').value = 'toto';
		expect( FormValidator.getValue( $('model1champ1') ) ).toEqual( 'toto' );
		
		// Bouton Radio
		expect( FormValidator.getValue( $('model3champ3__1') ) ).toEqual( 'toto' );
		expect( FormValidator.getValue( $('model3champ3__2') ) ).toEqual( 'toto' );
		$('model3champ3__2').checked = true;
		expect( FormValidator.getValue( $('model3champ3__3') ) ).toEqual( 'tata' );
		
		// Select
		expect( FormValidator.getValue( $('model3champ4') ) ).toEqual( '' );
		$('model3champ4').value = 'toto';
		expect( FormValidator.getValue( $('model3champ4') ) ).toEqual( 'toto' );
	});
	
	it("FormValidator.validate( editable, onchange=undefined )", function() {
		expect( FormValidator.validate( $('model3champ1day') ) ).toEqual( true );
		expect( FormValidator.validate( $('model3champ2day') ) ).toEqual( false );
		expect( FormValidator.validate( $('model3champ3__3') ) ).toEqual( true );
		expect( FormValidator.validate( $('model1champ1') ) ).toEqual( false );
		$('model1champ1').value = '1';
		expect( FormValidator.validate( $('model1champ1') ) ).toEqual( true );
		expect( FormValidator.validate( null ) ).toEqual( true );
	});
	
	it("FormValidator.suffix( value, separator='_' )", function() {
		expect( FormValidator.suffix( '11_4' ) ).toEqual( '4' );
		expect( FormValidator.suffix( '11+4', '+' ) ).toEqual( '4' );
		expect( FormValidator.suffix( '12+-+5', '+-+' ) ).toEqual( '5' );
	});
});
