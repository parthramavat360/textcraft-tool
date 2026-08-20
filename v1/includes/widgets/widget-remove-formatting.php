<?php
/**
 * Widget: Remove Text Formatting
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Remove_Formatting extends TextCraft_Base_Widget {

    public function get_name(): string  { return 'textcraft_remove_formatting'; }
    public function get_title(): string { return esc_html__( 'Remove Formatting', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-close'; }

    protected function render_tool_content( array $settings ): void {
    ?>

    <div class="tc-ed-wrapper">

        <p class="tc-text-14 tc-text-muted tc-mb-20"><?php echo esc_html__( 'Strip Unicode formatting from social media text — bold, italic, cursive, and other fancy characters converted back to plain ASCII. Works entirely in your browser for private text processing.', 'textcraft-tools' ); ?></p>

        <!-- Tip Box -->
        <div class="tc-p-14-16 tc-text-13 tc-text-muted tc-tip-box">
            💡 <strong class="tc-text-primary">Tip:</strong> This free tool removes Unicode styling used on social media and converts fancy characters back to plain ASCII. Works for bold, italic, cursive, double-struck, monospace, superscript, subscript, and circled letters.
        </div>

        <!-- Input -->
        <div class="tc-label-row">
            <span class="tc-label">Formatted Text</span>
            <span class="tc-char-count">0 characters</span>
        </div>

        <textarea class="tc-textarea tc-textarea--input tc-input-area" rows="9" placeholder="Paste formatted text here — bold, italic, cursive, and other Unicode styled characters will be stripped to plain text…" spellcheck="false"></textarea>

        <!-- Buttons -->
        <div class="tc-actions">
            <button class="tc-strip tc-btn tc-btn--primary">🗑️ Strip Formatting</button>
            <button class="tc-copy tc-btn tc-btn--ghost">📋 Copy</button>
            <button class="tc-clear tc-btn tc-btn--danger">🗑️ Clear</button>
        </div>

        <!-- Stats -->
        <div class="tc-stats">
            <div class="tc-label">Characters Converted: <span class="tc-stat-conv">0</span></div>
            <div class="tc-label">Before: <span class="tc-stat-before">0</span></div>
            <div class="tc-label">After: <span class="tc-stat-after">0</span></div>
        </div>

        <!-- Output -->
        <div class="tc-label tc-mt-20"><strong>Plain Text Result</strong></div>
        <textarea class="tc-textarea tc-textarea--input tc-output-area" rows="9" readonly placeholder="Your plain text with formatting stripped will appear here after conversion."></textarea>

        <!-- What formatting is removed? -->
        <div class="tc-mt-24 tc-p-20 tc-rounded-8">
            <h2 class="tc-text-18 tc-mb-12">What formatting is removed?</h2>
            <div class="tc-grid-rmf-ref">
                <div class="tc-cell-rmf-ref"><div class="tc-text-16 tc-accent-value">𝗕𝗼𝗹𝗱</div><div class="tc-text-12 tc-text-muted tc-mt-4">Bold (Mathematical)</div></div>
                <div class="tc-cell-rmf-ref"><div class="tc-text-16 tc-accent-value">𝘐𝘵𝘢𝘭𝘪𝘤</div><div class="tc-text-12 tc-text-muted tc-mt-4">Italic (Mathematical)</div></div>
                <div class="tc-cell-rmf-ref"><div class="tc-text-16 tc-accent-value">𝓢𝓬𝓻𝓲𝓹𝓽</div><div class="tc-text-12 tc-text-muted tc-mt-4">Script / Cursive</div></div>
                <div class="tc-cell-rmf-ref"><div class="tc-text-16 tc-accent-value">𝕕𝕠𝕦𝕓𝕝𝕖</div><div class="tc-text-12 tc-text-muted tc-mt-4">Double Struck</div></div>
                <div class="tc-cell-rmf-ref"><div class="tc-text-16 tc-accent-value">𝙼𝚘𝚗𝚘</div><div class="tc-text-12 tc-text-muted tc-mt-4">Monospace</div></div>
                <div class="tc-cell-rmf-ref"><div class="tc-text-16 tc-accent-value">ᴵᴼˢᵘᵖ</div><div class="tc-text-12 tc-text-muted tc-mt-4">Superscript</div></div>
                <div class="tc-cell-rmf-ref"><div class="tc-text-16 tc-accent-value">ₛᵤᵦ</div><div class="tc-text-12 tc-text-muted tc-mt-4">Subscript</div></div>
                <div class="tc-cell-rmf-ref"><div class="tc-text-16 tc-accent-value">Ⓒⓘⓡⓒⓛⓔ</div><div class="tc-text-12 tc-text-muted tc-mt-4">Circled Letters</div></div>
            </div>
        </div>

    </div>

    <script>
    (function($){

        function unicodeToAscii(str){
            var ranges = [
                // Bold A-Z, a-z
                [0x1D400,0x1D419,'A'],[0x1D41A,0x1D433,'a'],
                // Italic A-Z, a-z
                [0x1D434,0x1D44D,'A'],[0x1D44E,0x1D467,'a'],
                // Bold Italic
                [0x1D468,0x1D481,'A'],[0x1D482,0x1D49B,'a'],
                // Script
                [0x1D49C,0x1D4B5,'A'],[0x1D4B6,0x1D4CF,'a'],
                // Bold Script
                [0x1D4D0,0x1D4E9,'A'],[0x1D4EA,0x1D503,'a'],
                // Fraktur
                [0x1D504,0x1D51D,'A'],[0x1D51E,0x1D537,'a'],
                // Double-struck
                [0x1D538,0x1D551,'A'],[0x1D552,0x1D56B,'a'],
                // Bold Fraktur
                [0x1D56C,0x1D585,'A'],[0x1D586,0x1D59F,'a'],
                // Sans
                [0x1D5A0,0x1D5B9,'A'],[0x1D5BA,0x1D5D3,'a'],
                // Sans Bold
                [0x1D5D4,0x1D5ED,'A'],[0x1D5EE,0x1D607,'a'],
                // Sans Italic
                [0x1D608,0x1D621,'A'],[0x1D622,0x1D63B,'a'],
                // Sans Bold Italic
                [0x1D63C,0x1D655,'A'],[0x1D656,0x1D66F,'a'],
                // Monospace
                [0x1D670,0x1D689,'A'],[0x1D68A,0x1D6A3,'a']
            ];
            var numRanges = [
                [0x1D7CE,0x1D7D7,'0'], // Bold digits
                [0x1D7D8,0x1D7E1,'0'], // Double-struck digits
                [0x1D7E2,0x1D7EB,'0'], // Sans digits
                [0x1D7EC,0x1D7F5,'0'], // Sans bold digits
                [0x1D7F6,0x1D7FF,'0']  // Monospace digits
            ];

            var converted = 0;
            var chars = Array.from(str);
            var result = chars.map(function(ch){
                var cp = ch.codePointAt(0);

                for(var i=0; i<ranges.length; i++){
                    var start = ranges[i][0], end = ranges[i][1], base = ranges[i][2];
                    if(cp >= start && cp <= end){
                        converted++;
                        return String.fromCharCode(base.charCodeAt(0) + (cp - start));
                    }
                }
                for(var j=0; j<numRanges.length; j++){
                    var ns = numRanges[j][0], ne = numRanges[j][1];
                    if(cp >= ns && cp <= ne){
                        converted++;
                        return String.fromCharCode('0'.charCodeAt(0) + (cp - ns));
                    }
                }
                // Circled letters Ⓐ-Ⓩ
                if(cp >= 0x24B6 && cp <= 0x24CF){ converted++; return String.fromCharCode('A'.charCodeAt(0) + cp - 0x24B6); }
                // Circled letters ⓐ-ⓩ
                if(cp >= 0x24D0 && cp <= 0x24E9){ converted++; return String.fromCharCode('a'.charCodeAt(0) + cp - 0x24D0); }

                return ch;
            });

            return { text: result.join(''), converted: converted };
        }

        function init(scope){

            var inp = scope.find('.tc-input-area');
            var out = scope.find('.tc-output-area');

            // Char count
            inp.on('input', function(){
                scope.find('.tc-char-count').text(inp.val().length + ' characters');
            });

            // Strip formatting
            scope.find('.tc-strip').on('click', function(){
                var res = unicodeToAscii(inp.val());
                out.val(res.text);
                scope.find('.tc-stat-conv').text(res.converted);
                scope.find('.tc-stat-before').text(inp.val().length);
                scope.find('.tc-stat-after').text(res.text.length);
            });

            // Copy
            scope.find('.tc-copy').on('click', function(){
                navigator.clipboard.writeText(out.val()).then(function(){
                    var btn = scope.find('.tc-copy');
                    btn.text('✅ Copied!');
                    setTimeout(function(){ btn.text('📋 Copy'); }, 2000);
                });
            });

            // Clear
            scope.find('.tc-clear').on('click', function(){
                inp.val('');
                out.val('');
                scope.find('.tc-char-count').text('0 characters');
                scope.find('.tc-stat-conv, .tc-stat-before, .tc-stat-after').text('0');
            });
        }

        // Elementor Hook
        $(window).on('elementor/frontend/init', function(){
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/textcraft_remove_formatting.default',
                function(scope){
                    init($(scope));
                }
            );
        });

    })(jQuery);
    </script>

    <?php
    }
}
