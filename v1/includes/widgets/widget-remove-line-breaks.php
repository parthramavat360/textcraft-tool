<?php
/**
 * Widget: Remove Line Breaks
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;

class Widget_Remove_Line_Breaks extends TextCraft_Base_Widget {

    public function get_name(): string  { return 'textcraft_remove_line_breaks'; }
    public function get_title(): string { return esc_html__( 'Line Break Remover', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-align-stretch'; }

    protected function render_tool_content( array $settings ): void {
    ?>

    <div class="tc-ed-wrapper">

        <p class="tc-text-14 tc-text-muted tc-mb-16">Remove or replace line breaks in your text with spaces, commas, or custom separators. This free online tool runs entirely in your browser for secure and private text processing.</p>

        <!-- Replace Options -->
        <div class="tc-cr-chars-section tc-mb-20">
            <label class="tc-label">Replace Line Breaks With</label>

            <div class="tc-cr-presets tc-mt-10">
                <button class="tc-btn tc-cr-preset-btn tc-btn--secondary active" data-val=" ">Single Space</button>
                <button class="tc-btn tc-cr-preset-btn tc-btn--secondary" data-val="">Nothing</button>
                <button class="tc-btn tc-cr-preset-btn tc-btn--secondary" data-val=", ">Comma + Space</button>
                <button class="tc-btn tc-cr-preset-btn tc-btn--secondary" data-val=" | ">Pipe ( | )</button>
                <button class="tc-btn tc-cr-preset-btn tc-btn--secondary" data-val="custom">Custom…</button>
            </div>

            <input type="text" class="tc-custom tc-d-none tc-mt-10 tc-w-full" placeholder="Custom replacement…">
        </div>

        <!-- Options -->
        <div class="tc-options-row">
            <label class="tc-option"><input type="checkbox" class="tc-opt-blank" checked> Also remove blank lines</label>
            <label class="tc-option"><input type="checkbox" class="tc-opt-trim" checked> Trim trailing spaces</label>
            <label class="tc-option"><input type="checkbox" class="tc-opt-para"> Keep paragraph breaks (double newlines)</label>
        </div>

        <!-- Input -->
        <div class="tc-label-row">
            <span class="tc-label">Your Text</span>
            <span class="tc-char-count">0 characters</span>
        </div>

        <textarea class="tc-textarea tc-textarea--input tc-input-area" rows="9" placeholder="Paste text with line breaks to join or remove…&#10;Line one&#10;Line two&#10;Line three&#10;Line four" spellcheck="false"></textarea>

        <!-- Buttons -->
        <div class="tc-actions">
            <button class="tc-remove tc-btn tc-btn--primary">↩️ Remove Line Breaks</button>
            <button class="tc-copy tc-btn tc-btn--ghost">📋 Copy</button>
            <button class="tc-clear tc-btn tc-btn--danger">🗑️ Clear</button>
        </div>

        <!-- Stats -->
        <div class="tc-stats">
            <div class="tc-label">Lines Removed: <span class="tc-stat-removed">0</span></div>
            <div class="tc-label">Original: <span class="tc-stat-before">0</span></div>
            <div class="tc-label">Result: <span class="tc-stat-after">0</span></div>
        </div>

        <!-- Output -->
        <div class="tc-label tc-mt-20"><strong>Result</strong></div>
        <textarea class="tc-textarea tc-textarea--input tc-output-area" rows="9" readonly placeholder="Your cleaned text without line breaks will appear here after processing."></textarea>

    </div>

    <script>
    (function($){

        function init(scope){

            var inp        = scope.find('.tc-input-area');
            var out        = scope.find('.tc-output-area');
            var replaceBtns = scope.find('.tc-cr-preset-btn');
            var customInput = scope.find('.tc-custom');
            var replaceVal  = ' '; // default: single space

            // Char count
            inp.on('input', function(){
                scope.find('.tc-char-count').text(inp.val().length + ' characters');
            });

            // Replace preset buttons
            replaceBtns.on('click', function(){
                replaceBtns.removeClass('active');
                $(this).addClass('active');

                if($(this).data('val') === 'custom'){
                    customInput.show();
                    replaceVal = customInput.val();
                } else {
                    customInput.hide();
                    replaceVal = $(this).data('val');
                }
            });

            customInput.on('input', function(){
                replaceVal = $(this).val();
            });

            // Remove line breaks
            scope.find('.tc-remove').on('click', function(){

                var text       = inp.val();
                var before     = (text.match(/\n/g) || []).length;
                var keepPara   = scope.find('.tc-opt-para').is(':checked');
                var removeBlank = scope.find('.tc-opt-blank').is(':checked');
                var trim       = scope.find('.tc-opt-trim').is(':checked');

                if(keepPara){
                    // Preserve double newlines as paragraph breaks
                    text = text.replace(/\n\n+/g, '§PARA§');
                    text = text.replace(/\n/g, replaceVal);
                    text = text.replace(/§PARA§/g, '\n\n');
                } else {
                    if(removeBlank){ text = text.replace(/^\s*\n/gm, ''); }
                    text = text.replace(/\r\n/g, replaceVal).replace(/\n/g, replaceVal).replace(/\r/g, replaceVal);
                }

                if(trim){ text = text.replace(/  +/g, ' ').trim(); }

                out.val(text);

                scope.find('.tc-stat-removed').text(before);
                scope.find('.tc-stat-before').text(inp.val().length);
                scope.find('.tc-stat-after').text(text.length);
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
                scope.find('.tc-stat-removed, .tc-stat-before, .tc-stat-after').text('0');
            });
        }

        // Elementor Hook
        $(window).on('elementor/frontend/init', function(){
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/textcraft_remove_line_breaks.default',
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
