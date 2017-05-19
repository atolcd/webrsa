/* global describe, expect, it */
describe("Webrsa", function () {
	// TODO: avec les secondes, sans les secondes, ...
	describe("Date", function() {
		it("Doit comprendre une date au format JJ/MM/AAAA", function () {
			expect(Webrsa.Date.fromText('24/01/1979')).toEqual(new Date( 1979, 0, 24, 0, 0, 0, 0 ));
		});
		it("Doit comprendre une date au format J/M/AA", function () {
			expect(Webrsa.Date.fromText('5/3/81')).toEqual(new Date( 1981, 2, 5, 0, 0, 0, 0 ));
		});
		it("Doit renvoyer NULL lorsque ce n'est pas une date", function () {
			expect(Webrsa.Date.fromText('foo')).toEqual(null);
		});
		it("Doit comprendre une date au format JJ/MM/AAAA à HH:MM:SS", function () {
			expect(Webrsa.Date.fromText('24/01/1979 à 11:09:30')).toEqual(new Date( 1979, 0, 24, 11, 09, 30, 0 ));
		});
		it("Doit comprendre une date au format J/M/AA à H:M:S", function () {
			expect(Webrsa.Date.fromText('24/01/1979 à 6:9:3')).toEqual(new Date( 1979, 0, 24, 6, 9, 3, 0 ));
		});
		it("Doit comprendre une date au format JJ/MM/AAAA HH:MM:SS", function () {
			expect(Webrsa.Date.fromText('24/01/1979 11:09:30')).toEqual(new Date( 1979, 0, 24, 11, 09, 30, 0 ));
		});
		it("Doit comprendre une date au format J/M/AA H:M:S", function () {
			expect(Webrsa.Date.fromText('24/01/1979 6:9:3')).toEqual(new Date( 1979, 0, 24, 6, 9, 3, 0 ));
		});
		it("Doit comprendre une date au format JJ/MM/AAAA HH:MM", function () {
			expect(Webrsa.Date.fromText('24/01/1979 11:09')).toEqual(new Date( 1979, 0, 24, 11, 09, 0, 0 ));
		});
		it("Doit comprendre une date au format J/M/AA H:M", function () {
			expect(Webrsa.Date.fromText('24/01/1979 6:9')).toEqual(new Date( 1979, 0, 24, 6, 9, 0, 0 ));
		});
	});
});

