/**
 * TextCraft Tools — Case Converter Library
 *
 * Exposes window.TextCraftCaseConverter with all text transformation methods.
 * This is the WordPress-compatible version of the original case-converter.js.
 * All functions are pure — no DOM access, no side effects.
 *
 * @package TextCraft_Tools
 * @version 1.0.0
 */

( function ( global ) {

	'use strict';

	// ── Conversion Functions ──────────────────────────────────────────────────

	/**
	 * Convert text to UPPERCASE.
	 * @param {string} text
	 * @returns {string}
	 */
	function toUpperCase( text ) {
		return text.toUpperCase();
	}

	/**
	 * Convert text to lowercase.
	 * @param {string} text
	 * @returns {string}
	 */
	function toLowerCase( text ) {
		return text.toLowerCase();
	}

	/**
	 * Convert text to Sentence case.
	 * First letter of each sentence is capitalised; the rest are lowercase.
	 * @param {string} text
	 * @returns {string}
	 */
	function toSentenceCase( text ) {
		return text
			.toLowerCase()
			.replace( /(^\s*\w|[.!?]\s+\w)/g, function ( match ) {
				return match.toUpperCase();
			} );
	}

	/**
	 * Convert text to Capitalized Case (every word capitalised).
	 * @param {string} text
	 * @returns {string}
	 */
	function toCapitalizedCase( text ) {
		return text
			.toLowerCase()
			.replace( /\b\w/g, function ( char ) {
				return char.toUpperCase();
			} );
	}

	/**
	 * Convert text to Title Case (AP / Chicago style).
	 * Minor words remain lowercase unless they open the title.
	 * @param {string} text
	 * @returns {string}
	 */
	function toTitleCase( text ) {
		var minor = new Set( [
			'a','an','the','and','but','or','for','nor','on','at','to','by',
			'in','of','up','as','it','is','if','vs','via','per','yet','so'
		] );

		return text
			.toLowerCase()
			.split( ' ' )
			.map( function ( word, index ) {
				if ( index === 0 ) {
					return word.replace( /^\w/, function ( c ) { return c.toUpperCase(); } );
				}
				if ( minor.has( word ) ) { return word; }
				return word.replace( /^\w/, function ( c ) { return c.toUpperCase(); } );
			} )
			.join( ' ' );
	}

	/**
	 * Convert text to aLtErNaTiNg CaSe.
	 * @param {string} text
	 * @returns {string}
	 */
	function toAlternatingCase( text ) {
		var toggle = true;
		return text
			.split( '' )
			.map( function ( char ) {
				if ( char.trim() === '' ) { return char; }
				var result = toggle ? char.toLowerCase() : char.toUpperCase();
				toggle = ! toggle;
				return result;
			} )
			.join( '' );
	}

	/**
	 * Convert text to InVeRsE CaSe (each alphabetic character's case is flipped).
	 *
	 * Uses locale-aware toUpperCase/toLowerCase and validates the result changed,
	 * correctly handling accented letters (é→É, ñ→Ñ) and all Unicode letters
	 * that the old ASCII byte-range comparison ( >= 'A' && <= 'Z' ) missed.
	 *
	 * @param {string} text
	 * @returns {string}
	 */
	function toInverseCase( text ) {
		return Array.from( text ).map( function ( char ) {
			var upper = char.toLocaleUpperCase();
			var lower = char.toLocaleLowerCase();

			// Character is uppercase → flip to lowercase.
			if ( char !== lower && char === upper ) { return lower; }

			// Character is lowercase → flip to uppercase.
			if ( char !== upper && char === lower ) { return upper; }

			// Not a cased character (digit, symbol, space, emoji…) → unchanged.
			return char;
		} ).join( '' );
	}

	// ── Statistics ────────────────────────────────────────────────────────────

	/**
	 * Compute text statistics.
	 * @param {string} text
	 * @returns {{ chars: number, words: number, sentences: number, lines: number }}
	 */
	function getStats( text ) {
		var chars     = text.length;
		var words     = text.trim() === '' ? 0 : text.trim().split( /\s+/ ).length;
		var sentences = text.trim() === '' ? 0 : ( text.match( /[.!?]+/g ) || [] ).length;
		var lines     = text === '' ? 0 : text.split( '\n' ).length;
		return { chars: chars, words: words, sentences: sentences, lines: lines };
	}

	// ── Public API ────────────────────────────────────────────────────────────

	global.TextCraftCaseConverter = {
		toUpperCase:      toUpperCase,
		toLowerCase:      toLowerCase,
		toSentenceCase:   toSentenceCase,
		toCapitalizedCase: toCapitalizedCase,
		toTitleCase:      toTitleCase,
		toAlternatingCase: toAlternatingCase,
		toInverseCase:    toInverseCase,
		getStats:         getStats,
	};

} )( window );
