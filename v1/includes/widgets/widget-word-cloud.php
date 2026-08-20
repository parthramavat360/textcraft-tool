<?php
/**
 * Widget: Word Cloud Generator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Word_Cloud extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_word_cloud'; }
    public function get_title(): string { return esc_html__( 'Word Cloud Generator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-posts-ticker'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Turn any text into a beautiful word cloud visualisation. Choose from multiple colour themes, control word count and length, and download your cloud as a PNG image — all for free in your browser.', 'textcraft-tools' )
            . '</p>';

        // ── Options grid ──────────────────────────────────────────────────
        echo '<div class="tc-grid-cards-mb">';

        // Color theme
        echo '<div>';
        echo '<label class="tc-wc-label">' . esc_html__( 'Color Theme', 'textcraft-tools' ) . '</label>';
        echo '<select id="tc-wc-theme" class="tc-wc-select">';
        $themes = [
            'purple'  => esc_html__( 'Purple & Pink',    'textcraft-tools' ),
            'ocean'   => esc_html__( 'Ocean Blue',        'textcraft-tools' ),
            'forest'  => esc_html__( 'Forest Green',      'textcraft-tools' ),
            'sunset'  => esc_html__( 'Sunset',            'textcraft-tools' ),
            'mono'    => esc_html__( 'Monochrome',        'textcraft-tools' ),
            'rainbow' => esc_html__( 'Rainbow',           'textcraft-tools' ),
        ];
        foreach ( $themes as $val => $label ) {
            echo '<option value="' . esc_attr( $val ) . '">' . $label . '</option>';
        }
        echo '</select></div>';

        // Max words
        echo '<div>';
        echo '<label class="tc-wc-label">' . esc_html__( 'Max Words', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-wc-maxwords" class="tc-wc-input" value="60" min="10" max="200">';
        echo '</div>';

        // Min word length
        echo '<div>';
        echo '<label class="tc-wc-label">' . esc_html__( 'Min Word Length', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-wc-minlen" class="tc-wc-input" value="3" min="1" max="10">';
        echo '</div>';

        // Checkboxes
        echo '<div class="tc-d-flex tc-flex-col tc-gap-8 tc-flex-items-end">';
echo '<label class="tc-wc-check-label tc-text-13 tc-text-muted">';
echo '<input type="checkbox" id="tc-wc-stop" checked class="tc-accent-checkbox"> ' . esc_html__( 'Remove stop words', 'textcraft-tools' );
echo '</label>';
echo '<label class="tc-wc-check-label tc-text-13 tc-text-muted">';
        echo '<input type="checkbox" id="tc-wc-lowercase" checked class="tc-accent-checkbox"> ' . esc_html__( 'Lowercase all words', 'textcraft-tools' );
        echo '</label>';
        echo '</div>';

        echo '</div>'; // end options grid

        // ── Input textarea ────────────────────────────────────────────────
        $this->render_textarea(
            'tc-wc-input',
            esc_html__( 'Your Text', 'textcraft-tools' ),
            esc_html__( 'Paste or type text here to generate a stunning word cloud. The more text you add, the richer your word cloud becomes — ideal for analysing speeches, articles, or any written content.', 'textcraft-tools' ),
            7,
            false
        );

        // Word count badge (replaces char count)
        echo '<div class="tc-text-right tc-mt-neg-12 tc-mb-12">';
        echo '<span id="tc-wc-wordcount" class="tc-text-12 tc-text-muted">0 ' . esc_html__( 'words', 'textcraft-tools' ) . '</span>';
        echo '</div>';

        // ── Buttons ───────────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-wc-generate', 'label' => '☁️ ' . esc_html__( 'Generate Word Cloud', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-wc-download', 'label' => '💾 ' . esc_html__( 'Download PNG',         'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-wc-clear',    'label' => '🗑️ ' . esc_html__( 'Clear',                'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Canvas container ──────────────────────────────────────────────
        echo '<div id="tc-wc-container" class="tc-wc-canvas-wrap">';
        echo '<canvas id="tc-wc-canvas" class="tc-w-full tc-d-block"></canvas>';
        echo '</div>';

        // ── Frequency tag list ────────────────────────────────────────────
        echo '<div id="tc-wc-freq-list" class="tc-wc-freq-list">';
        echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Top Words', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-wc-freq-tags" class="tc-wc-freq-tags"></div>';
        echo '</div>';

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var inp = document.getElementById('tc-wc-input');
    if (!inp) return;

    // Word count
    inp.addEventListener('input', function(){
        var words = inp.value.trim().split(/\s+/).filter(Boolean);
        document.getElementById('tc-wc-wordcount').textContent = words.length + ' words';
    });

    var STOP = new Set(['the','a','an','and','or','but','in','on','at','to','for','of','with','by','from','up','about','into','over','after','before','is','are','was','were','be','been','being','have','has','had','do','does','did','will','would','could','should','may','might','shall','can','this','that','these','those','it','its','i','my','me','we','our','you','your','he','his','him','she','her','they','their','them','not','no','so','as','if','then','than','too','very','just','also','more','most','other','some','such','well','own','same','even']);

    var THEMES = {
        gold:    ['#ffcc66','#f59e0b','#d4a24c','#b8860b','#e6b347','#c9942e','#a67c2e','#8a6b1e'],
        bronze:  ['#d4a24c','#b8860b','#c9973f','#a67c2e','#e6b347','#d9b873','#9a7d2e','#7a6424'],
        forest:  ['#22c55e','#16a34a','#84cc16','#65a30d','#4ade80','#86efac','#15803d','#166534'],
        sunset:  ['#f97316','#b45309','#f59e0b','#eab308','#fb923c','#fbbf24','#92400e','#d97706'],
        mono:    ['#ffffff','#d8c8aa','#a8997d','#7a6e5a','#4a4238','#2a2720','#1a1814','#0d0b08'],
        rainbow: ['#f97316','#f59e0b','#eab308','#22c55e','#ffcc66','#d4a24c','#b8860b','#a67c2e']
    };

    function getWordFreq(text) {
        var minLen     = parseInt(document.getElementById('tc-wc-minlen').value)    || 3;
        var maxWords   = parseInt(document.getElementById('tc-wc-maxwords').value)  || 60;
        var lowercase  = document.getElementById('tc-wc-lowercase').checked;
        var removeStop = document.getElementById('tc-wc-stop').checked;

        var words = text.match(/\b[a-zA-Z']+\b/g) || [];
        if (lowercase) words = words.map(function(w){ return w.toLowerCase(); });
        words = words.filter(function(w){ return w.length >= minLen; });
        if (removeStop) words = words.filter(function(w){ return !STOP.has(w.toLowerCase()); });

        var freq = {};
        words.forEach(function(w){ freq[w] = (freq[w] || 0) + 1; });
        return Object.entries(freq)
            .sort(function(a,b){ return b[1] - a[1]; })
            .slice(0, maxWords);
    }

    function drawCloud(words) {
        var container = document.getElementById('tc-wc-container');
        var canvas    = document.getElementById('tc-wc-canvas');
        container.style.display = 'block';

        var W = container.offsetWidth || 700;
        var H = Math.round(W * 0.55);
        canvas.width  = W;
        canvas.height = H;
        canvas.style.height = H + 'px';

        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#050505';
        ctx.fillRect(0, 0, W, H);

        var theme   = THEMES[document.getElementById('tc-wc-theme').value] || THEMES.purple;
        var maxFreq = words[0] ? words[0][1] : 1;
        var placed  = [];

        function overlaps(x, y, w, h) {
            return placed.some(function(p){
                return !(x + w < p.x || x > p.x + p.w || y + h < p.y || y > p.y + p.h);
            });
        }

        words.forEach(function(entry, idx){
            var word  = entry[0], freq = entry[1];
            var ratio = freq / maxFreq;
            var size  = Math.round(14 + ratio * (W > 500 ? 54 : 38));
            ctx.font  = (Math.random() > 0.3 ? 700 : 400) + ' ' + size + 'px sans-serif';
            var metrics = ctx.measureText(word);
            var tw = metrics.width + 8;
            var th = size + 8;
            var color = theme[idx % theme.length];

            var cx = W / 2, cy = H / 2;
            var placed_flag = false;
            for (var r = 0; r < Math.max(W, H) / 2; r += 4) {
                for (var a = 0; a < Math.PI * 2; a += 0.3) {
                    var x = cx + r * Math.cos(a) - tw / 2;
                    var y = cy + r * Math.sin(a) - th / 2;
                    if (x < 4 || y < 4 || x + tw > W - 4 || y + th > H - 4) continue;
                    if (!overlaps(x, y, tw, th)) {
                        ctx.fillStyle  = color;
                        ctx.globalAlpha = 0.85 + ratio * 0.15;
                        ctx.fillText(word, x + 4, y + th - 6);
                        ctx.globalAlpha = 1;
                        placed.push({ x: x, y: y, w: tw, h: th });
                        placed_flag = true;
                        break;
                    }
                }
                if (placed_flag) break;
            }
        });
    }

    // Generate
    document.getElementById('tc-wc-generate').addEventListener('click', function(){
        var text = inp.value;
        if (!text.trim()) { alert('Please enter some text to generate a word cloud.'); return; }
        var words = getWordFreq(text);
        if (!words.length) { alert('Not enough unique words found. Try lowering the minimum word length or adding more text.'); return; }
        drawCloud(words);

        // Frequency tags
        var tagsEl = document.getElementById('tc-wc-freq-tags');
        tagsEl.innerHTML = words.slice(0, 20).map(function(e){
            return '<span class="tc-wc-tag tc-inline-flex tc-items-center tc-text-12">'
                + e[0]
                + '<span class="tc-text-10 tc-wc-count">\u00d7' + e[1] + '</span></span>';
        }).join('');
        document.getElementById('tc-wc-freq-list').style.display = 'block';
    });

    // Download PNG
    document.getElementById('tc-wc-download').addEventListener('click', function(){
        var canvas = document.getElementById('tc-wc-canvas');
        if (!canvas.width) { alert('Please generate a word cloud first.'); return; }
        var a = document.createElement('a');
        a.download = 'word-cloud.png';
        a.href = canvas.toDataURL('image/png');
        a.click();
    });

    // Clear
    document.getElementById('tc-wc-clear').addEventListener('click', function(){
        inp.value = '';
        document.getElementById('tc-wc-container').style.display  = 'none';
        document.getElementById('tc-wc-freq-list').style.display  = 'none';
        document.getElementById('tc-wc-wordcount').textContent     = '0 words';
    });
})();
JS
        );
    }
}