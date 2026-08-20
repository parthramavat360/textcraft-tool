(function(){ 'use strict';
var cards = document.querySelectorAll('.tc-invis-card-copy'); if (!cards.length) return;
var typeSelect = document.getElementById('tc-it-type');
var countInput = document.getElementById('tc-it-count');
var btnGenerate = document.getElementById('tc-it-generate');

var charMap = {
    'space': '\u00A0',
    'zero-width-space': '\u200B',
    'zero-width-joiner': '\u200D',
    'zero-width-non-joiner': '\u200C',
    'soft-hyphen': '\u00AD',
    'ogham-space': '\u1680',
    'en-quad': '\u2000',
    'em-quad': '\u2001',
    'en-space': '\u2002',
    'em-space': '\u2003',
    'three-per-em': '\u2004',
    'four-per-em': '\u2005',
    'six-per-em': '\u2006',
    'figure-space': '\u2007',
    'punctuation-space': '\u2008',
    'thin-space': '\u2009',
    'hair-space': '\u200A',
    'narrow-no-break': '\u202F',
    'mathematical-space': '\u205F',
    'ideographic-space': '\u3000',
    'braille-empty': '\u2800',
    'zero-width-joiner-dup': '\uFEFF'
};

cards.forEach(function(card){
    card.addEventListener('click', function(){
        var type = card.getAttribute('data-type') || (typeSelect ? typeSelect.value : 'space');
        var char = charMap[type] || '\u200B';
        if(TCTP && TCTP.copyText) TCTP.copyText(char);
    });
});

if(btnGenerate){
    btnGenerate.addEventListener('click', function(){
        var type = typeSelect ? typeSelect.value : 'space';
        var count = parseInt(countInput.value, 10) || 1;
        var char = charMap[type] || '\u200B';
        var result = '';
        for(var i = 0; i < count; i++) result += char;
        if(TCTP && TCTP.copyText) TCTP.copyText(result);
    });
}
})();