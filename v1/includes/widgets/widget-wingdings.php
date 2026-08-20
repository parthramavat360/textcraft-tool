<?php
/**
 * Widget: Wingdings Translator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Wingdings extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_wingdings'; }
    public function get_title(): string { return esc_html__( 'Wingdings Translator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-star'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Convert text to Wingdings symbols and back to regular text. A fun free online tool for creating secret messages, puzzles, and decorative text — all processed in your browser.', 'textcraft-tools' )
            . '</p>';

        // ── Direction buttons ─────────────────────────────────────────────
        echo '<div class="tc-d-flex tc-gap-8 tc-mb-20">';
        echo '<button type="button" class="tc-btn tc-btn--primary tc-wing-dir active" data-dir="toWing">🔤 → ✡️ ' . esc_html__( 'Text to Wingdings', 'textcraft-tools' ) . '</button>';
        echo '<button type="button" class="tc-btn tc-btn--ghost tc-wing-dir" data-dir="fromWing">✡️ → 🔤 ' . esc_html__( 'Wingdings to Text', 'textcraft-tools' ) . '</button>';
        echo '</div>';

        // ── Input textarea ────────────────────────────────────────────────
        $this->render_textarea(
            'tc-wing-input',
            esc_html__( 'Input Text', 'textcraft-tools' ),
            esc_html__( 'Type or paste your text here…', 'textcraft-tools' ),
            7
        );

        // ── Action buttons ────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-wing-translate', 'label' => '✡️ ' . esc_html__( 'Translate', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-wing-copy',      'label' => '📋 ' . esc_html__( 'Copy',      'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-wing-clear',     'label' => '🗑️ ' . esc_html__( 'Clear',     'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Wingdings output display ──────────────────────────────────────
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label" id="tc-wing-out-label">' . esc_html__( 'Wingdings Output', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-wing-output" class="tc-wing-output">';
        echo '<span class="tc-text-14 tc-text-muted">' . esc_html__( 'Wingdings translation will appear here…', 'textcraft-tools' ) . '</span>';
        echo '</div>';

        // ── Unicode symbols row ───────────────────────────────────────────
        echo '<div class="tc-card-surface tc-mt-12 tc-p-12-16">';
        echo '<span class="tc-text-11 tc-text-muted tc-font-semibold tc-text-uppercase tc-tracking-06">' . esc_html__( 'Unicode Symbols:', 'textcraft-tools' ) . '</span>';
        echo '<div id="tc-wing-unicode-text" class="tc-text-20 tc-mt-6 tc-leading-18 tc-break-all"></div>';
        echo '</div>';

        // ── Wingdings Symbol Reference ────────────────────────────────────
        echo '<div class="tc-card-surface tc-mt-32 tc-p-24">';
        echo '<h3 class="tc-text-20 tc-mb-16">' . esc_html__( 'Wingdings Symbol Reference', 'textcraft-tools' ) . '</h3>';
        echo '<div id="tc-wing-ref" class="tc-wing-ref"></div>';
        echo '</div>';

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var WMAP = {
        ' ':' ','!':'✏','"':'✂','#':'✁','$':'👓','%':'🔔','&':'🕭','\'':'📋',
        '(':'📁',')':'📂','*':'📄','+':'🗒',',':'🗓','-':'📅','.':'📆','/':'📇',
        '0':'🗂','1':'🗃','2':'🗄','3':'⌚','4':'🖥','5':'🖨','6':'🖱','7':'🖲',
        '8':'💾','9':'💿',':':'📀',';':'🎥','<':'📺','=':'📷','>':'📸','?':'📞',
        '@':'☎','A':'✉','B':'📧','C':'📨','D':'📩','E':'📪','F':'📫','G':'📬',
        'H':'📭','I':'📮','J':'🗳','K':'✏','L':'✒','M':'🖊','N':'🖋','O':'🖌',
        'P':'🖍','Q':'🔍','R':'🔎','S':'🔏','T':'🔐','U':'🔒','V':'🔓','W':'🔑',
        'X':'🗝','Y':'🔨','Z':'🪓','[':'⛏','\\':'⚒','^':'🗡','_':'⚔',
        '`':'🛡','a':'🔧','b':'🔩','c':'⚙','d':'🗜','e':'⚖','f':'🔗','g':'⛓',
        'h':'🪝','i':'🧲','j':'🔫','k':'💣','l':'🪤','m':'🔪','n':'🗺','o':'🧭',
        'p':'🌐','q':'🗾','r':'⏱','s':'⏲','t':'⏰','u':'⌛','v':'⏳','w':'📡',
        'x':'🔋','y':'🪫','z':'🔌','{':'💡','|':'🔦','}':'🕯','~':'💰'
    };

    // Reverse map
    var RMAP = {};
    Object.keys(WMAP).forEach(function(k){
        var v = WMAP[k];
        if (v && v !== ' ') RMAP[v] = k;
    });

    var inp       = document.getElementById('tc-wing-input');
    var wOut      = document.getElementById('tc-wing-output');
    var uText     = document.getElementById('tc-wing-unicode-text');
    var uRow      = document.getElementById('tc-wing-unicode-row');
    var outLabel  = document.getElementById('tc-wing-out-label');
    var dir       = 'toWing';

    if (!inp) return;

    // Char count
    inp.addEventListener('input', function(){
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = inp.value.length + ' characters';
    });

    // Direction buttons
    document.querySelectorAll('.tc-wing-dir').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-wing-dir').forEach(function(b){
                b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost');
            });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            dir = btn.dataset.dir;
            outLabel.textContent = dir === 'toWing' ? 'Wingdings Output' : 'Text Output';
        });
    });

    // Translate
    document.getElementById('tc-wing-translate').addEventListener('click', function(){
        var text = inp.value;
        if (!text.trim()) {
            wOut.innerHTML = '<span class="tc-text-14 tc-text-muted">Please enter some text to translate.</span>';
            uText.textContent = '';
            return;
        }

        if (dir === 'toWing') {
            var symbols = Array.from(text).map(function(ch){
                return WMAP[ch] || WMAP[ch.toUpperCase()] || ch;
            }).join('');
            wOut.style.fontFamily = "'Wingdings', serif";
            wOut.style.fontSize   = '24px';
            wOut.textContent      = symbols;
            uText.textContent     = symbols;
            uRow.style.display    = 'block';
        } else {
            var result = text;
            Object.keys(RMAP).forEach(function(sym){
                result = result.split(sym).join(RMAP[sym]);
            });
            wOut.style.fontFamily = 'var(--font-body, sans-serif)';
            wOut.style.fontSize   = '16px';
            wOut.textContent      = result;
            uText.textContent     = result;
            uRow.style.display    = 'block';
        }
    });

    // Copy
    document.getElementById('tc-wing-copy').addEventListener('click', function(){
        var text = wOut.textContent;
        if (!text) return;
        navigator.clipboard.writeText(text).then(function(){
            var btn = document.getElementById('tc-wing-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy'; }, 2000);
        });
    });

    // Clear
    document.getElementById('tc-wing-clear').addEventListener('click', function(){
        inp.value = '';
        wOut.innerHTML = '<span class="tc-text-14 tc-text-muted">Wingdings translation will appear here…</span>';
        uText.textContent  = '';
        uRow.style.display = 'none';
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = '0 characters';
    });

    // ── Build Symbol Reference grid ───────────────────────────────────
    var ref = document.getElementById('tc-wing-ref');
    if (ref) {
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('').forEach(function(ch){
            var sym = WMAP[ch];
            if (sym && sym.trim()) {
                var div = document.createElement('div');
                div.className = 'tc-wing-ref-cell';
                div.innerHTML = '<div class="tc-text-20">' + sym + '</div>'
                              + '<div class="tc-text-10 tc-text-muted tc-mt-2">' + ch + '</div>';
                ref.appendChild(div);
            }
        });
    }

    // Hide unicode row initially
    uRow.style.display = 'none';
})();
JS
        );
    }
}