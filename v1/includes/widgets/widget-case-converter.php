<?php
/**
 * Widget: Case Converter
 *
 * Converts text to UPPERCASE, lowercase, Sentence case, Title Case,
 * Capitalized Case, aLtErNaTiNg CaSe, and InVeRsE CaSe.
 * All conversion logic runs client-side via the shared
 * textcraft-case-converter.js library.
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

/** Elementor widget: Case Converter */
class Widget_Case_Converter extends TextCraft_Base_Widget {

	public function get_name(): string  { return 'textcraft_case_converter'; }
	public function get_title(): string { return esc_html__( 'Case Converter', 'textcraft-tools' ); }
	public function get_icon(): string  { return 'eicon-text'; }

	public function get_keywords(): array {
		return [ 'case', 'uppercase', 'lowercase', 'title', 'sentence', 'alternating', 'inverse', 'convert', 'free online text tool', 'case changer' ];
	}

	/** @inheritDoc */
	protected function render_tool_content( array $settings ): void {
		// ── Tool description ────────────────────────────────
		echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
			. esc_html__( 'Convert text between uppercase, lowercase, sentence case, title case, and more. This free online case changer works entirely in your browser — no data is sent to any server.', 'textcraft-tools' )
			. '</p>';

		// ── Case buttons ────────────────────────────────────
		echo '<div class="tc-case-buttons" role="group" aria-label="' . esc_attr__( 'Case conversion options', 'textcraft-tools' ) . '">';

		$cases = [
			[ 'case' => 'uppercase',   'icon' => '🔠', 'label' => 'UPPERCASE',        'preview' => 'HELLO WORLD' ],
			[ 'case' => 'lowercase',   'icon' => '🔡', 'label' => 'lowercase',        'preview' => 'hello world' ],
			[ 'case' => 'sentence',    'icon' => '📝', 'label' => 'Sentence case',    'preview' => 'Hello world.' ],
			[ 'case' => 'title',       'icon' => '📰', 'label' => 'Title Case',       'preview' => 'Hello World' ],
			[ 'case' => 'capitalized', 'icon' => '🅰️', 'label' => 'Capitalized Case', 'preview' => 'Hello World' ],
			[ 'case' => 'alternating', 'icon' => '🔀', 'label' => 'aLtErNaTiNg',     'preview' => 'hElLo wOrLd' ],
			[ 'case' => 'inverse',     'icon' => '🔁', 'label' => 'InVeRsE CaSe',    'preview' => 'hELLO wORLD' ],
		];

		foreach ( $cases as $item ) {
			echo '<button type="button"'
				. ' class="tc-btn-case"'
				. ' data-case="' . esc_attr( $item['case'] ) . '"'
				. ' data-label="' . esc_attr( $item['label'] ) . '"'
				. ' aria-pressed="false">'
				. '<span class="tc-btn-case__icon" aria-hidden="true">' . $item['icon'] . '</span>'
				. '<span class="tc-btn-case__label">' . esc_html( $item['label'] ) . '</span>'
				. '<span class="tc-btn-case__preview">' . esc_html( $item['preview'] ) . '</span>'
				. '</button>';
		}

		echo '</div>'; // .tc-case-buttons

		// ── Active indicator ─────────────────────────────────
		echo '<div class="tc-active-indicator hidden" id="tc-cc-active" aria-live="polite">'
			. esc_html__( 'Active:', 'textcraft-tools' )
			. ' <strong id="tc-cc-active-label"></strong>'
			. '</div>';

		// ── Input textarea ───────────────────────────────────
		$this->render_textarea(
			'tc-cc-input',
			esc_html__( 'Your Text', 'textcraft-tools' ),
			esc_html__( 'Type or paste your text here to convert case…', 'textcraft-tools' ),
			8
		);

		// ── Stat bar ──────────────────────────────────────────
		$this->render_stat_bar( [
			[ 'id' => 'tc-cc-chars',     'label' => esc_html__( 'Characters', 'textcraft-tools' ) ],
			[ 'id' => 'tc-cc-words',     'label' => esc_html__( 'Words',      'textcraft-tools' ) ],
			[ 'id' => 'tc-cc-sentences', 'label' => esc_html__( 'Sentences',  'textcraft-tools' ) ],
			[ 'id' => 'tc-cc-lines',     'label' => esc_html__( 'Lines',      'textcraft-tools' ) ],
		] );

		// ── Action buttons ────────────────────────────────────
		$this->render_button_row( [
			[ 'id' => 'tc-cc-copy',     'label' => '📋 ' . esc_html__( 'Copy',     'textcraft-tools' ), 'variant' => 'ghost' ],
			[ 'id' => 'tc-cc-download', 'label' => '💾 ' . esc_html__( 'Download', 'textcraft-tools' ), 'variant' => 'ghost' ],
			[ 'id' => 'tc-cc-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',    'textcraft-tools' ), 'variant' => 'danger' ],
		] );

		// ── Toast notification ────────────────────────────────
		echo '<div class="tc-toast" id="tc-cc-toast" role="alert" aria-live="assertive">'
			. '<span class="tc-toast__icon">✅</span>'
			. '<span id="tc-cc-toast-msg"></span>'
			. '</div>';

		// ── Inline JavaScript ─────────────────────────────────
		$this->render_inline_script( $this->get_script() );
	}

	/** Return the widget's self-contained JavaScript. */
	private function get_script(): string {
		return <<<'JS'
/* ── TextCraft: Case Converter widget ── */
(function(){
'use strict';

/**
 * Boot: wait for the shared TextCraftCaseConverter library.
 * The library is loaded with defer so it may not be available yet.
 */
function init(){
    if(typeof window.TextCraftCaseConverter === 'undefined'){
        return setTimeout(init, 50);
    }
    boot();
}

function boot(){
    var CC = window.TextCraftCaseConverter;

    // ── DOM refs — scoped to THIS widget's card only ─────
    // Using closest() prevents conflicts when the widget is placed
    // multiple times on the same page.
    var anyBtn = document.querySelector('.tc-btn-case[data-case]');
    if(!anyBtn) return; // widget not on this page

    var card      = anyBtn.closest('.tc-tool-card');
    if(!card) return;

    var inp       = card.querySelector('#tc-cc-input');
    var indicator = card.querySelector('#tc-cc-active');
    var indLabel  = card.querySelector('#tc-cc-active-label');
    var toast     = card.querySelector('#tc-cc-toast');
    var toastMsg  = card.querySelector('#tc-cc-toast-msg');
    var caseBtns  = card.querySelectorAll('.tc-btn-case');

    if(!inp) return;

    // ── State ─────────────────────────────────────────────
    var activeCase  = null;   // currently selected case type
    var lastInput   = '';     // raw text BEFORE conversion (for re-apply on keystroke)
    var toastTimer  = null;

    // ── Toast ─────────────────────────────────────────────
    function showToast(msg, icon){
        icon = icon || '✅';
        if(toastMsg) toastMsg.textContent = msg;
        if(toast){
            toast.querySelector('.tc-toast__icon').textContent = icon;
            toast.classList.add('tc-toast--show');
        }
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function(){
            if(toast) toast.classList.remove('tc-toast--show');
        }, 2800);
    }

    // ── Statistics ────────────────────────────────────────
    function updateStats(text){
        var s   = CC.getStats(text);
        var set = function(id,v){ var el=card.querySelector('#'+id); if(el) el.textContent=v; };
        set('tc-cc-chars',     s.chars);
        set('tc-cc-words',     s.words);
        set('tc-cc-sentences', s.sentences);
        set('tc-cc-lines',     s.lines);
        var cc = card.querySelector('.tc-char-count');
        if(cc) cc.textContent = s.chars + (s.chars===1?' character':' characters');
    }

    // ── Button highlight ──────────────────────────────────
    function highlightBtn(caseType){
        caseBtns.forEach(function(btn){
            var active = btn.dataset.case === caseType;
            btn.classList.toggle('active', active);
            btn.setAttribute('aria-pressed', String(active));
        });
    }

    // ── Core conversion ───────────────────────────────────
    /**
     * Run the chosen conversion on `sourceText` and write to textarea.
     * We always convert from `sourceText` — never from the already-converted
     * value — so calling this on every keystroke does not double-flip.
     *
     * @param {string} caseType
     * @param {string} label      Human label for the indicator badge.
     * @param {string} sourceText The raw text to convert.
     */
    function applyConversion(caseType, label, sourceText){
        if(!sourceText.trim()){
            showToast('Please enter some text to convert.','⚠️');
            return;
        }

        var result = '';
        switch(caseType){
            case 'uppercase':   result = CC.toUpperCase(sourceText);        break;
            case 'lowercase':   result = CC.toLowerCase(sourceText);        break;
            case 'sentence':    result = CC.toSentenceCase(sourceText);     break;
            case 'title':       result = CC.toTitleCase(sourceText);        break;
            case 'capitalized': result = CC.toCapitalizedCase(sourceText);  break;
            case 'alternating': result = CC.toAlternatingCase(sourceText);  break;

            // InVeRsE — the only mode that re-applies to ORIGINAL text.
            // If we re-applied it to already-inverted text we would undo the
            // conversion on every keystroke. `lastInput` holds the original.
            case 'inverse':
                result = CC.toInverseCase(sourceText);
                break;

            default: return;
        }

        inp.value  = result;
        activeCase = caseType;
        updateStats(result);
        highlightBtn(caseType);
        if(indLabel) indLabel.textContent = label;
        if(indicator) indicator.classList.remove('hidden');
    }

    // ── Case button clicks ────────────────────────────────
    caseBtns.forEach(function(btn){
        btn.addEventListener('click', function(){
            // Capture the CURRENT textarea value as the source for this
            // conversion.  For all modes except inverse the textarea already
            // holds the user's raw text.  For inverse we need the pre-flip
            // original, so we store it in lastInput before overwriting.
            lastInput = inp.value;
            applyConversion(
                btn.dataset.case,
                btn.dataset.label || btn.dataset.case,
                lastInput
            );
        });
    });

    // ── Live re-apply on keystroke ────────────────────────
    inp.addEventListener('input', function(){
        // Update stats on every keystroke regardless of active case.
        updateStats(inp.value);

        if(!activeCase) return;

        if(activeCase === 'inverse'){
            // For inverse: the textarea now shows the inverted text.
            // We cannot re-invert it (that would restore the original).
            // Clear active mode so the user starts fresh when they type.
            activeCase = null;
            highlightBtn(null);
            if(indicator) indicator.classList.add('hidden');
            return;
        }

        // For all other modes: re-apply to the CURRENT value so conversion
        // stays live as the user types additional text.
        applyConversion(activeCase, inp.dataset.label || activeCase, inp.value);
    });

    // ── Toolbar actions ───────────────────────────────────
    var btnCopy = card.querySelector('#tc-cc-copy');
    if(btnCopy){
        btnCopy.addEventListener('click', function(){
            if(!inp.value.trim()){ showToast('Nothing to copy — type or paste some text first.','⚠️'); return; }
            navigator.clipboard.writeText(inp.value)
                .then(function(){ showToast('Copied to clipboard!'); })
                .catch(function(){ inp.select(); document.execCommand('copy'); showToast('Copied!'); });
        });
    }

    var btnDownload = card.querySelector('#tc-cc-download');
    if(btnDownload){
        btnDownload.addEventListener('click', function(){
            if(!inp.value.trim()){ showToast('Nothing to download — add some text first.','⚠️'); return; }
            var blob = new Blob([inp.value],{type:'text/plain;charset=utf-8'});
            var url  = URL.createObjectURL(blob);
            var a    = document.createElement('a');
            a.href=url; a.download='converted-text.txt'; a.click();
            URL.revokeObjectURL(url);
            showToast('Downloaded!','📥');
        });
    }

    var btnClear = card.querySelector('#tc-cc-clear');
    if(btnClear){
        btnClear.addEventListener('click', function(){
            inp.value  = '';
            lastInput  = '';
            activeCase = null;
            updateStats('');
            highlightBtn(null);
            if(indicator) indicator.classList.add('hidden');
        });
    }

    // ── Keyboard shortcuts (Ctrl/Cmd + Shift + key) ───────
    // Note: Ctrl+Shift+I opens DevTools in most browsers — we skip it.
    document.addEventListener('keydown', function(e){
        if(!(e.ctrlKey||e.metaKey)||!e.shiftKey) return;
        var map = {
            'u': ['uppercase',   'UPPERCASE'],
            'l': ['lowercase',   'lowercase'],
            's': ['sentence',    'Sentence case'],
            't': ['title',       'Title Case'],
            // 'i' skipped — reserved by browser DevTools
        };
        var entry = map[e.key.toLowerCase()];
        if(!entry) return;
        e.preventDefault();
        lastInput = inp.value;
        applyConversion(entry[0], entry[1], lastInput);
    });

    // ── Initialise stats ──────────────────────────────────
    updateStats('');
}

init();
})();
JS;
	}
}