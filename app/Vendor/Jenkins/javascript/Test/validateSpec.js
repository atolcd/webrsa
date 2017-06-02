describe("Validation", function() {
	it("Doit être alphanumérique", function() {
		expect(Validation.alphaNumeric('frferrf')).toEqual( true );
		expect(Validation.alphaNumeric('12234')).toEqual( true );
		expect(Validation.alphaNumeric('1w2e2r3t4y')).toEqual( true );
		expect(Validation.alphaNumeric('0')).toEqual( true );
		expect(Validation.alphaNumeric('abçďĕʑʘπй')).toEqual( true );
		expect(Validation.alphaNumeric('ˇˆๆゞ')).toEqual( true );
		expect(Validation.alphaNumeric('אกあアꀀ豈')).toEqual( true );
		expect(Validation.alphaNumeric('ǅᾈᾨ')).toEqual( true );
		expect(Validation.alphaNumeric('ÆΔΩЖÇ')).toEqual( true );
		
		expect(Validation.alphaNumeric('12 234')).toEqual( false );
		expect(Validation.alphaNumeric('dfd 234')).toEqual( false );
		expect(Validation.alphaNumeric('\n')).toEqual( false );
		expect(Validation.alphaNumeric('\t')).toEqual( false );
		expect(Validation.alphaNumeric(' ')).toEqual( false );
		expect(Validation.alphaNumeric('')).toEqual( false );
	});
	
	it("Doit être numérique", function() {
		expect(Validation.numeric('frferrf')).toEqual( false );
		expect(Validation.numeric('12234')).toEqual( true );
		expect(Validation.numeric('122.34')).toEqual( true );
		expect(Validation.numeric('122,34')).toEqual( true );
		expect(Validation.numeric('12,2,34')).toEqual( false );
	});
	
	it("Ne doit pas être vide", function() {
		expect(Validation.notEmpty('abcdefg')).toEqual( true );
		expect(Validation.notEmpty('   fasdf   ')).toEqual( true );
		expect(Validation.notEmpty('fooo°blablabla')).toEqual( true );
		expect(Validation.notEmpty('abçďĕʑʘπй')).toEqual( true );
		expect(Validation.notEmpty('José')).toEqual( true );
		expect(Validation.notEmpty('é')).toEqual( true );
		expect(Validation.notEmpty('π')).toEqual( true );
		expect(Validation.notEmpty('ǅᾈᾨ')).toEqual( true );
		expect(Validation.notEmpty('ÆΔΩЖÇ')).toEqual( true );
		
		expect(Validation.notEmpty('\t ')).toEqual( false );
		expect(Validation.notEmpty('')).toEqual( false );
		expect(Validation.notEmpty('            ')).toEqual( false );
	});
	
	it("La taille est comprise entre...", function() {
		expect(Validation.between('abcdefg', 1, 7)).toEqual( true );
		expect(Validation.between('', 0, 7)).toEqual( true );
		expect(Validation.between(54689, 1, 7)).toEqual( true );
		
		expect(Validation.between('abcdefg', 1, 6)).toEqual( false );
		expect(Validation.between('ÆΔΩЖÇ', 1, 3)).toEqual( false );
		expect(Validation.between('ÆΔΩЖÇ', 7, 8)).toEqual( false );
	});
	
	it("La chaine est dans l'array", function() {
		expect(Validation.inList('one', ['one', 'two'])).toEqual( true );
		expect(Validation.inList('two', ['one', 'two'])).toEqual( true );
		expect(Validation.inList('2', [1,2,3], false)).toEqual( true );
		
		expect(Validation.inList('2', [1,2,3])).toEqual( false );
		expect(Validation.inList('three', ['one', 'two'])).toEqual( false );
		expect(Validation.inList('1one', [1,2,3,4])).toEqual( false );
		expect(Validation.inList('one', [1,2,3,4])).toEqual( false );
	});
	
	it("Le numéro est bien entre...", function() {
		expect(Validation.range(20, 1, 100)).toEqual( true );
		expect(Validation.range(.5, 0, 100)).toEqual( true );
		expect(Validation.range(5)).toEqual( true );
		expect(Validation.range(-5, -10, 1)).toEqual( true );
		
		expect(Validation.range(.5, 1, 100)).toEqual( false );
		expect(Validation.range(20, 100, 1)).toEqual( false );
		expect(Validation.range(20, 20, 100)).toEqual( false );
		expect(Validation.range('word')).toEqual( false );
	});
	
	it("Le numéro est bien entre (inclusive)...", function() {
		expect(Validation.inclusiveRange(20, 1, 100)).toEqual( true );
		expect(Validation.inclusiveRange(.5, 0, 100)).toEqual( true );
		expect(Validation.inclusiveRange(5)).toEqual( true );
		expect(Validation.inclusiveRange(-5, -10, 1)).toEqual( true );
		expect(Validation.inclusiveRange(20, 20, 100)).toEqual( true );
		
		expect(Validation.inclusiveRange(.5, 1, 100)).toEqual( false );
		expect(Validation.inclusiveRange(20, 100, 1)).toEqual( false );
		expect(Validation.inclusiveRange('word')).toEqual( false );
	});
	
	it("Le numéro de sécu a une bonne synthaxe", function() {
		expect(Validation.ssn(185128408704872)).toEqual( true );
		expect(Validation.ssn('185128408704872')).toEqual( true );
		
		expect(Validation.ssn(585128408704872)).toEqual( false );
	});
	
	it("La date est valide", function() {
		expect(Validation.date('2015-02-23')).toEqual( true );
		expect(Validation.date('2015-02-23 15:35:00')).toEqual( true );
		expect(Validation.date('2015/02/23 15:35:00')).toEqual( true );
		expect(Validation.date('2015-02-23T15:35:03.389Z')).toEqual( true );
		expect(Validation.date('2015.02.23T15:35:03.389Z')).toEqual( true );
		expect(Validation.date('23.02.2015T15:35:03.389Z')).toEqual( true );
		expect(Validation.date('22 03 02' , ['dmy'])).toEqual( true );
		expect(Validation.date('22.03.02' , ['DmY'])).toEqual( true );
		expect(Validation.date('23.02.15T15:35:03.389Z' , ['DmY'])).toEqual( true );
		expect(Validation.date('30.03.02')).toEqual( true ); // 1930
		expect(Validation.date('29.03.02')).toEqual( true ); // 2029
		expect(Validation.date('23/02/2015')).toEqual( true );
		expect(Validation.date('23-02-2015')).toEqual( true );
		expect(Validation.date('23/02/2015 15:35:00')).toEqual( true );
		expect(Validation.date('15:35:00')).toEqual( true );
		
		expect(Validation.date('2015-02-30')).toEqual( false );
		expect(Validation.date('2015-02-23 21:61:56')).toEqual( false );
		expect(Validation.date('2015-02-23 21:59:56', ['dmy'])).toEqual( false );
	});
	
	it("Le numéro de telephone est valide", function() {
		expect(Validation.phoneFr('0409801509')).toEqual( true );
		expect(Validation.phoneFr(0509801509)).toEqual( true );
		expect(Validation.phoneFr('0909801509')).toEqual( true );
		expect(Validation.phoneFr('01 09 80 15 09')).toEqual( true );
		expect(Validation.phoneFr('06.09.80.15.09')).toEqual( true );
		expect(Validation.phoneFr('07 09 801 509')).toEqual( true );
		expect(Validation.phoneFr('3605')).toEqual( true );
		expect(Validation.phoneFr('3949')).toEqual( true );
		expect(Validation.phoneFr('15')).toEqual( true );
		expect(Validation.phoneFr('112')).toEqual( true );
		expect(Validation.phoneFr('118 718')).toEqual( true );
		
		expect(Validation.phoneFr(9999999999)).toEqual( false );
		expect(Validation.phoneFr(1187189)).toEqual( false );
	});
	
	it("La syntaxe de l'email est correcte", function() {
		expect(Validation.email('abc.efg@domain.com')).toEqual( true );
		expect(Validation.email('efg@domain.com')).toEqual( true );
		expect(Validation.email('abc-efg@domain.com')).toEqual( true );
		expect(Validation.email('abc_efg@domain.com')).toEqual( true );
		expect(Validation.email('raw@test.ra.ru')).toEqual( true );
		expect(Validation.email('abc-efg@domain-hyphened.com')).toEqual( true );
		expect(Validation.email("p.o'malley@domain.com")).toEqual( true );
		expect(Validation.email('abc+efg@domain.com')).toEqual( true );
		expect(Validation.email('abc&efg@domain.com')).toEqual( true );
		expect(Validation.email('abc.efg@12345.com')).toEqual( true );
		expect(Validation.email('abc.efg@12345.co.jp')).toEqual( true );
		expect(Validation.email('abc@g.cn')).toEqual( true );
		expect(Validation.email('abc@x.com')).toEqual( true );
		expect(Validation.email('henrik@sbcglobal.net')).toEqual( true );
		expect(Validation.email('sani@sbcglobal.net')).toEqual( true );

		// all ICANN TLDs
		expect(Validation.email('abc@example.aero')).toEqual( true );
		expect(Validation.email('abc@example.asia')).toEqual( true );
		expect(Validation.email('abc@example.biz')).toEqual( true );
		expect(Validation.email('abc@example.cat')).toEqual( true );
		expect(Validation.email('abc@example.com')).toEqual( true );
		expect(Validation.email('abc@example.coop')).toEqual( true );
		expect(Validation.email('abc@example.edu')).toEqual( true );
		expect(Validation.email('abc@example.gov')).toEqual( true );
		expect(Validation.email('abc@example.info')).toEqual( true );
		expect(Validation.email('abc@example.int')).toEqual( true );
		expect(Validation.email('abc@example.jobs')).toEqual( true );
		expect(Validation.email('abc@example.mil')).toEqual( true );
		expect(Validation.email('abc@example.mobi')).toEqual( true );
		expect(Validation.email('abc@example.museum')).toEqual( true );
		expect(Validation.email('abc@example.name')).toEqual( true );
		expect(Validation.email('abc@example.net')).toEqual( true );
		expect(Validation.email('abc@example.org')).toEqual( true );
		expect(Validation.email('abc@example.pro')).toEqual( true );
		expect(Validation.email('abc@example.tel')).toEqual( true );
		expect(Validation.email('abc@example.travel')).toEqual( true );
		expect(Validation.email('someone@st.t-com.hr')).toEqual( true );

		// gTLD's
		expect(Validation.email('example@host.local')).toEqual( true );
		expect(Validation.email('example@x.org')).toEqual( true );
		expect(Validation.email('example@host.xxx')).toEqual( true );

		// strange, but technically valid email addresses
		expect(Validation.email('S=postmaster/OU=rz/P=uni-frankfurt/A=d400/C=de@gateway.d400.de')).toEqual( true );
		expect(Validation.email('customer/department=shipping@example.com')).toEqual( true );
		expect(Validation.email('$A12345@example.com')).toEqual( true );
		expect(Validation.email('!def!xyz%abc@example.com')).toEqual( true );
		expect(Validation.email('_somename@example.com')).toEqual( true );

		// invalid addresses
		expect(Validation.email('abc@example')).toEqual( false );
		expect(Validation.email('abc@example.c')).toEqual( false );
		expect(Validation.email('abc@example.com.')).toEqual( false );
		expect(Validation.email('abc.@example.com')).toEqual( false );
		expect(Validation.email('abc@example..com')).toEqual( false );
		expect(Validation.email('abc@example.com.a')).toEqual( false );
		expect(Validation.email('abc;@example.com')).toEqual( false );
		expect(Validation.email('abc@example.com;')).toEqual( false );
		expect(Validation.email('abc@efg@example.com')).toEqual( false );
		expect(Validation.email('abc@@example.com')).toEqual( false );
		expect(Validation.email('abc efg@example.com')).toEqual( false );
		expect(Validation.email('abc,efg@example.com')).toEqual( false );
		expect(Validation.email('abc@sub,example.com')).toEqual( false );
		expect(Validation.email("abc@sub'example.com")).toEqual( false );
		expect(Validation.email('abc@sub/example.com')).toEqual( false );
		expect(Validation.email('abc@yahoo!.com')).toEqual( false );
		expect(Validation.email("Nyrée.surname@example.com")).toEqual( false );
		expect(Validation.email('abc@example_underscored.com')).toEqual( false );
		expect(Validation.email('raw@test.ra.ru....com')).toEqual( false );
	});
	
	it("C'est un entier", function() {
		expect(Validation.integer(123)).toEqual( true );
		
		expect(Validation.integer(123.4)).toEqual( false );
		expect(Validation.integer('foo')).toEqual( false );
		expect(Validation.integer(null)).toEqual( false );
	});
	
	it("C'est un boolean", function() {
		expect(Validation.boolean(true)).toEqual( true );		
		expect(Validation.boolean('true')).toEqual( true );
		expect(Validation.boolean('1')).toEqual( true );
		expect(Validation.boolean(1)).toEqual( true );
		
		expect(Validation.boolean('foo')).toEqual( false );
		expect(Validation.boolean('2')).toEqual( false );
		expect(Validation.boolean(2)).toEqual( false );
		expect(Validation.boolean(-1)).toEqual( false );
		expect(Validation.boolean(null)).toEqual( false );
	});
	
	it("Formulaire vide", function() {		
		expect(Validation.allEmpty(['', ''])).toEqual( true );
		expect(Validation.allEmpty([null, null])).toEqual( true );
		
		expect(Validation.allEmpty([null, ' '])).toEqual( false );
	});
	
	// le champ 1 n'est pas vide si le champ 2 est(true) ou pas(false) egal à foo ou bar
	it("Not empty if...", function() {		
		// 'foo', 'bar' -> N'est pas vide
		expect(Validation.notEmptyIf('', '', true, ['foo', 'bar'])).toEqual( true );
		
		// '', 'bar' -> Peut être vide si contien foo ou bar, c'est le cas
		expect(Validation.notEmptyIf('', 'bar', false, ['foo', 'bar'])).toEqual( true );
		
		// '', 'bar' -> Peut être vide si ne contien pas toto ou titi, c'est le cas
		expect(Validation.notEmptyIf('', 'bar', true, ['toto', 'titi'])).toEqual( true );
		
		// Mauvaises synthaxes
		expect(Validation.notEmptyIf(null, 'bar', true, ['foo', 'bar'])).toEqual( false );
		expect(Validation.notEmptyIf('', 'bar', true, null)).toEqual( false );
		expect(Validation.notEmptyIf('', 'bar', false, 'bar')).toEqual( false );
		
		// '', 'bar' -> Peut être vide si ne contien pas foo ou bar, ce n'est pas le cas
		expect(Validation.notEmptyIf('', 'bar', true, ['foo', 'bar'])).toEqual( false );
		
		// '', 'bar' -> Peut être vide si contien toto ou titi, c'est le cas
		expect(Validation.notEmptyIf('', 'bar', false, ['toto', 'titi'])).toEqual( false );
	});
	
	it("Comparaison de dates", function() {
		expect(Validation.compareDates('01.01.1900', '01.01.2000', '<')).toEqual( true );
		expect(Validation.compareDates('02.01.1900', '01.01.1900', '>')).toEqual( true );
		expect(Validation.compareDates('01.01.1900', '01.01.1900', '>=')).toEqual( true );
		expect(Validation.compareDates('02.01.1900', '01.01.1900', '>=')).toEqual( true );
		expect(Validation.compareDates('01.01.1900', '01.01.1900', '<=')).toEqual( true );
		expect(Validation.compareDates('01.01.1900', '02.01.1900', '<=')).toEqual( true );
		expect(Validation.compareDates('01.01.1900', '01.01.1900', '==')).toEqual( true );
		expect(Validation.compareDates('01.01.1900', '01.01.1900', '===')).toEqual( true );
		expect(Validation.compareDates('01.01.1900', '02.01.1900', '!=')).toEqual( true );
		expect(Validation.compareDates('23.02.2015T15:35:04.390Z', '23.02.2015T15:35:03.389Z', '>')).toEqual( true );
		
		expect(Validation.compareDates('01.01.1900', '01.01.2000', '>')).toEqual( false ); // <
		expect(Validation.compareDates('01.01.1900', '01.01.1900', '>')).toEqual( false ); // ==
		expect(Validation.compareDates('01.01.1900', '02.01.1900', '>=')).toEqual( false ); // <
		expect(Validation.compareDates('01.01.1900', '01.01.1900', '!=')).toEqual( false ); // ==
		expect(Validation.compareDates('01.01.1900', '01.01.1900', '!==')).toEqual( false ); // ==
		expect(Validation.compareDates(null, '01.01.2000', '>')).toEqual( false );
		expect(Validation.compareDates('01.01.1900', '01.01.2000', '*')).toEqual( false );
	});
});

