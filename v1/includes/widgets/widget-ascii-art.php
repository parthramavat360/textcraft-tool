<?php
/**
 * Widget: Image to ASCII Art (PixelScript)
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Ascii_Art extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_ascii_art'; }
    public function get_title(): string { return esc_html__( 'ASCII Art Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-image'; }
    protected function render_tool_content( array $settings ): void {

        // ── Upload / drop zone ────────────────────────────────────────────
        echo '<div id="tc-aa-drop" class="tc-drop-zone" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag an image to upload', 'textcraft-tools' ) . '">';
		echo '<div class="tc-drop-icon">🖼️</div>';
        echo '<p class="tc-drop-title">' . esc_html__( 'Upload an image — click to browse or drag and drop', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-drop-desc-sm">' . esc_html__( 'Supports PNG, JPG, GIF, WebP — all processing is done in your browser, nothing is uploaded', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" id="tc-aa-upload" accept="image/*" class="tc-d-none">';
        echo '</div>';

        // ── Image preview ─────────────────────────────────────────────────
        echo '<div id="tc-aa-preview" class="tc-preview-wrap tc-text-center">';
        echo '<img id="tc-aa-img" class="tc-aa-img" alt="' . esc_attr__( 'Uploaded image preview', 'textcraft-tools' ) . '">';
        echo '<p id="tc-aa-info" class="tc-text-12 tc-text-muted tc-mt-8"></p>';
        echo '</div>';

        // ── Controls grid ─────────────────────────────────────────────────
        echo '<div class="tc-settings-grid">';

        // Width slider
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Output Width (chars)', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-flex-row">';
        echo '<input type="range" id="tc-aa-width" min="40" max="220" value="100" class="tc-slider">';
        echo '<span id="tc-aa-wval" class="tc-text-14 tc-accent-value tc-min-w-36">100</span>';
        echo '</div></div>';

        // Character set
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Character Set', 'textcraft-tools' ) . '</label>';
        echo '<select id="tc-aa-set" class="tc-text-input">';
        $char_sets = [
            'standard' => esc_html__( 'Standard (Dense)',       'textcraft-tools' ),
            'simple'   => esc_html__( 'Simple (High contrast)', 'textcraft-tools' ),
            'blocks'   => esc_html__( 'Block characters',       'textcraft-tools' ),
            'numbers'  => esc_html__( 'Numbers (0–9)',          'textcraft-tools' ),
            'minimal'  => esc_html__( 'Minimal (. : | #)',      'textcraft-tools' ),
        ];
        foreach ( $char_sets as $val => $label ) {
            echo '<option value="' . esc_attr( $val ) . '">' . $label . '</option>';
        }
        echo '</select></div>';

        // Colour mode
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Colour Mode', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-6 tc-flex-wrap">';
        $color_modes = [
            [ 'mode' => 'dark',    'label' => esc_html__( 'Dark BG',  'textcraft-tools' ), 'active' => true  ],
            [ 'mode' => 'light',   'label' => esc_html__( 'Light BG', 'textcraft-tools' ), 'active' => false ],
            [ 'mode' => 'colored', 'label' => esc_html__( 'Coloured', 'textcraft-tools' ), 'active' => false ],
        ];
        foreach ( $color_modes as $cm ) {
            $cls = $cm['active'] ? 'tc-btn tc-btn--primary tc-aa-color active' : 'tc-btn tc-btn--ghost tc-aa-color';
            echo '<button class="' . esc_attr( $cls ) . '" data-mode="' . esc_attr( $cm['mode'] ) . '">' . $cm['label'] . '</button>';
        }
        echo '</div></div>';

        // Font size slider
        echo '<div>';
        echo '<label class="tc-label-upper tc-mb-8">' . esc_html__( 'Font Size (preview)', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-flex-row">';
        echo '<input type="range" id="tc-aa-fontsize" min="4" max="14" value="7" class="tc-slider">';
        echo '<span id="tc-aa-fval" class="tc-text-14 tc-accent-value tc-min-w-30">7px</span>';
        echo '</div></div>';

        echo '</div>'; // end controls grid

        // ── Action buttons ────────────────────────────────────────────────
        $this->render_button_row( [
            [ 'id' => 'tc-aa-convert',  'label' => '🎨 ' . esc_html__( 'Convert to ASCII', 'textcraft-tools' ), 'variant' => 'primary' ],
            [ 'id' => 'tc-aa-copy',     'label' => '📋 ' . esc_html__( 'Copy Text',        'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-aa-download', 'label' => '💾 ' . esc_html__( 'Download .txt',    'textcraft-tools' ), 'variant' => 'ghost'   ],
            [ 'id' => 'tc-aa-reset',    'label' => '🗑️ ' . esc_html__( 'Reset',            'textcraft-tools' ), 'variant' => 'danger'  ],
        ] );

        // ── Stats bar ─────────────────────────────────────────────────────
        $this->render_stat_bar( [
            [ 'id' => 'tc-aa-stat-chars', 'label' => esc_html__( 'Chars', 'textcraft-tools' ) ],
            [ 'id' => 'tc-aa-stat-lines', 'label' => esc_html__( 'Lines', 'textcraft-tools' ) ],
            [ 'id' => 'tc-aa-stat-width', 'label' => esc_html__( 'Width', 'textcraft-tools' ) ],
        ] );

        // ── ASCII output area ─────────────────────────────────────────────
		echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'ASCII Output', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-aa-output" class="tc-aa-output">';
        echo '<p class="tc-aa-placeholder tc-mt-40">' . esc_html__( 'Upload an image above, then click Convert to ASCII.', 'textcraft-tools' ) . '</p>';
        echo '</div>';

        // Hidden canvas
		echo '<canvas id="tc-aa-canvas" class="tc-d-none"></canvas>';

        // ── Inline JS ─────────────────────────────────────────────────────
        $this->render_inline_script( <<<'JS'
(function(){
    var CHAR_SETS = {
        standard: '$@B%8&WM#*oahkbdpqwmZO0QLCJUYXzcvunxrjft/\\|()1{}[]?-_+~<>i!lI;:,"^`\'. ',
        simple:   '@#S%?*+;:,. ',
        blocks:   '\u2588\u2593\u2592\u2591 ',
        numbers:  '9876543210 ',
        minimal:  '#|:. ',
    };

    var drop       = document.getElementById('tc-aa-drop');
    var fileInput  = document.getElementById('tc-aa-upload');
    var canvas     = document.getElementById('tc-aa-canvas');
    var ctx        = canvas.getContext('2d', { willReadFrequently: true });
    var outDiv     = document.getElementById('tc-aa-output');
    var btnConvert = document.getElementById('tc-aa-convert');
    var btnCopy    = document.getElementById('tc-aa-copy');
    var btnDL      = document.getElementById('tc-aa-download');

    var uploadedImg = null;
    var colorMode   = 'dark';

    // Disable copy/download until converted
    if (btnCopy)   btnCopy.style.display    = 'none';
    if (btnDL)     btnDL.style.display      = 'none';
    if (btnConvert) btnConvert.disabled     = true;

    // ── Width slider ───────────────────────────────────────────────────
    document.getElementById('tc-aa-width').addEventListener('input', function(){
        document.getElementById('tc-aa-wval').textContent = this.value;
    });

    // ── Font size slider ───────────────────────────────────────────────
    document.getElementById('tc-aa-fontsize').addEventListener('input', function(){
        document.getElementById('tc-aa-fval').textContent = this.value + 'px';
        var pre = outDiv.querySelector('pre');
        if (pre) pre.style.fontSize = this.value + 'px';
    });

    // ── Colour mode buttons ────────────────────────────────────────────
    document.querySelectorAll('.tc-aa-color').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.tc-aa-color').forEach(function(b){
                b.classList.remove('active','tc-btn--primary'); b.classList.add('tc-btn--ghost');
            });
            btn.classList.add('active','tc-btn--primary'); btn.classList.remove('tc-btn--ghost');
            colorMode = btn.dataset.mode;
        });
    });

    // ── Drag & drop ────────────────────────────────────────────────────
    drop.addEventListener('click', function(){ fileInput.click(); });
    drop.addEventListener('dragover', function(e){ e.preventDefault(); drop.style.borderColor = 'var(--tc-accent)'; });
    drop.addEventListener('dragleave', function(){ drop.style.borderColor = 'var(--tc-border)'; });
    drop.addEventListener('drop', function(e){
        e.preventDefault(); drop.style.borderColor = 'var(--tc-border)';
        if (e.dataTransfer.files[0]) loadFile(e.dataTransfer.files[0]);
    });
    fileInput.addEventListener('change', function(){
        if (fileInput.files[0]) loadFile(fileInput.files[0]);
    });

    function loadFile(file) {
        if (!file || !file.type.match(/^image\//)) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = new Image();
            img.onload = function(){
                uploadedImg = img;
                document.getElementById('tc-aa-img').src  = e.target.result;
                document.getElementById('tc-aa-info').textContent = img.width + ' × ' + img.height + 'px — ' + (file.size/1024).toFixed(1) + ' KB';
                document.getElementById('tc-aa-preview').style.display = 'block';
                btnConvert.disabled = false;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // ── Convert ────────────────────────────────────────────────────────
    btnConvert.addEventListener('click', function(){
        if (!uploadedImg) return;
        var cols    = parseInt(document.getElementById('tc-aa-width').value) || 100;
        var chars   = CHAR_SETS[document.getElementById('tc-aa-set').value] || CHAR_SETS.standard;
        var fontSize = parseInt(document.getElementById('tc-aa-fontsize').value) || 7;
        var rows    = Math.round(cols * (uploadedImg.height / uploadedImg.width) * 0.48);

        canvas.width  = cols;
        canvas.height = rows;
        ctx.drawImage(uploadedImg, 0, 0, cols, rows);

        var imgData = ctx.getImageData(0, 0, cols, rows).data;
        var fs      = fontSize + 'px';

        if (colorMode === 'colored') {
            var html = '<pre class="tc-aa-pre" style="font-size:' + fs + ';">';
            for (var r = 0; r < rows; r++) {
                for (var c = 0; c < cols; c++) {
                    var idx  = (r * cols + c) * 4;
                    var R = imgData[idx], G = imgData[idx+1], B = imgData[idx+2];
                    var lum  = 0.299*R + 0.587*G + 0.114*B;
                    var cIdx = Math.floor((lum / 255) * (chars.length - 1));
                    var ch   = chars[cIdx] === ' ' ? '&nbsp;' : chars[cIdx];
                    html += '<span style="color:rgb(' + R + ',' + G + ',' + B + ')">' + ch + '</span>';
                }
                html += '\n';
            }
            html += '</pre>';
            outDiv.innerHTML = html;
            outDiv.dataset.plain = outDiv.querySelector('pre').textContent;
        } else {
            var text = '';
            for (var ri = 0; ri < rows; ri++) {
                for (var ci = 0; ci < cols; ci++) {
                    var pi  = (ri * cols + ci) * 4;
                    var lum = 0.299*imgData[pi] + 0.587*imgData[pi+1] + 0.114*imgData[pi+2];
                    var bright = colorMode === 'light' ? lum : 255 - lum;
                    var cI  = Math.floor((bright / 255) * (chars.length - 1));
                    text += chars[cI];
                }
                text += '\n';
            }
            var bg = colorMode === 'light' ? '#ffffff' : '#050505';
            var fg = colorMode === 'light' ? '#050505' : '#ffffff';
            outDiv.innerHTML = '<pre class="tc-aa-pre tc-aa-pre--mono" style="font-size:' + fs + ';color:' + fg + ';background:' + bg + ';">' + text + '</pre>';
            outDiv.dataset.plain = text;
        }

        document.getElementById('tc-aa-stat-chars').textContent = (cols * rows).toLocaleString();
        document.getElementById('tc-aa-stat-lines').textContent = rows;
        document.getElementById('tc-aa-stat-width').textContent = cols + ' cols';

        if (btnCopy) btnCopy.style.display = 'inline-flex';
        if (btnDL)   btnDL.style.display   = 'inline-flex';
    });

    // ── Copy ──────────────────────────────────────────────────────────
    if (btnCopy) {
        btnCopy.addEventListener('click', function(){
            var text = outDiv.dataset.plain || outDiv.innerText;
            if (!text) return;
            navigator.clipboard.writeText(text).then(function(){
                btnCopy.textContent = '✅ Copied!';
                setTimeout(function(){ btnCopy.textContent = '📋 Copy Text'; }, 2000);
            });
        });
    }

    // ── Download .txt ─────────────────────────────────────────────────
    if (btnDL) {
        btnDL.addEventListener('click', function(){
            var text = outDiv.dataset.plain || outDiv.innerText;
            if (!text) return;
            var blob = new Blob([text], { type: 'text/plain' });
            var a    = document.createElement('a');
            a.href   = URL.createObjectURL(blob);
            a.download = 'pixelscript-ascii.txt';
            a.click();
            URL.revokeObjectURL(a.href);
        });
    }

    // ── Reset ─────────────────────────────────────────────────────────
    document.getElementById('tc-aa-reset').addEventListener('click', function(){
        uploadedImg = null;
        fileInput.value = '';
        document.getElementById('tc-aa-preview').style.display = 'none';
        btnConvert.disabled = true;
        if (btnCopy) btnCopy.style.display = 'none';
        if (btnDL)   btnDL.style.display   = 'none';
        outDiv.innerHTML = '<p class="tc-text-muted tc-text-center tc-mt-40">' + 'Upload an image above, then click Convert to ASCII.' + '</p>';
        ['tc-aa-stat-chars','tc-aa-stat-lines','tc-aa-stat-width'].forEach(function(id){
            document.getElementById(id).textContent = '—';
        });
        outDiv.dataset.plain = '';
    });
})();
JS
        );
    }
}