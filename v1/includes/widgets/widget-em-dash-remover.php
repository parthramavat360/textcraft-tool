<?php
/**
 * Widget: Em Dash Remover
 */
declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Em_Dash_Remover extends TextCraft_Base_Widget {

    public function get_name(): string { return 'textcraft_em_dash_remover'; }
    public function get_title(): string { return esc_html__( 'Em Dash Remover', 'textcraft-tools' ); }
    public function get_icon(): string { return 'eicon-minus'; }

    protected function render_tool_content( array $settings ): void {
    ?>

    <div class="tc-ed-wrapper">

        <p class="tc-text-14 tc-text-muted tc-mb-16">Remove or replace em dashes (—), en dashes (–), and hyphens (-) in your text. This free online tool works in your browser — your text never leaves your device.</p>

        <!-- Replace Options -->
        <div class="tc-cr-chars-section tc-mb-20">
            <label class="tc-label">Replace Dashes With</label>

            <div class="tc-cr-presets">
                <button class="tc-btn tc-cr-preset-btn tc-btn--secondary active" data-val="">Nothing (remove)</button>
                <button class="tc-btn tc-cr-preset-btn tc-btn--secondary" data-val=" ">Single Space</button>
                <button class="tc-btn tc-cr-preset-btn tc-btn--secondary" data-val=" - ">Hyphen ( - )</button>
                <button class="tc-btn tc-cr-preset-btn tc-btn--secondary" data-val=", ">Comma</button>
                <button class="tc-btn tc-cr-preset-btn tc-btn--secondary" data-val="custom">Custom…</button>
            </div>

            <input type="text" class="tc-custom tc-d-none tc-mt-10 tc-w-full" placeholder="Type your custom replacement…">
        </div>

        <!-- Options -->
        <div class="tc-options-row">
            <label class="tc-option"><input type="checkbox" class="tc-em" checked> Em Dash (—)</label>
            <label class="tc-option"><input type="checkbox" class="tc-en" checked> En Dash (–)</label>
            <label class="tc-option"><input type="checkbox" class="tc-hyphen"> Hyphen (-)</label>
        </div>

        <!-- Input -->
        <div class="tc-label-row">
            <span class="tc-label">Your Text</span>
            <span class="tc-char-count">0 characters</span>
        </div>

        <textarea class="tc-textarea tc-textarea--input tc-input-area" rows="8" placeholder="Paste your text here — it may contain em dashes and en dashes to remove or replace…"></textarea>

        <!-- Buttons -->
        <div class="tc-actions">
            <button class="tc-remove tc-btn tc-btn--primary">➖ Remove Dashes</button>
            <button class="tc-copy tc-btn tc-btn--ghost">📋 Copy</button>
            <button class="tc-clear tc-btn tc-btn--danger">🗑️ Clear</button>
        </div>

        <!-- Stats -->
        <div class="tc-stats">
            <div class="tc-label">Em: <span class="tc-stat-em">0</span></div>
            <div class="tc-label">En: <span class="tc-stat-en">0</span></div>
            <div class="tc-label">Total: <span class="tc-stat-total">0</span></div>
        </div>

        <!-- Output -->
        <div class="tc-label tc-mt-20"><strong>Result</strong></div>
        <textarea class="tc-textarea tc-textarea--input tc-output-area" rows="8" readonly placeholder="Your cleaned text with dashes processed will appear here."></textarea>

    </div>

    <script>
    (function($){

        function init(scope){

            var inp = scope.find('.tc-input-area');
            var out = scope.find('.tc-output-area');
            var replaceBtns = scope.find('.tc-cr-preset-btn');
            var customInput = scope.find('.tc-custom');
            var replaceVal = '';

            // Count
            inp.on('input', function(){
                scope.find('.tc-char-count').text(inp.val().length + ' characters');
            });

            // Replace buttons
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

            // Remove
            scope.find('.tc-remove').on('click', function(){

                var text = inp.val();
                var em=0, en=0, hy=0;

                if(scope.find('.tc-em').is(':checked')){
                    em = (text.match(/—/g)||[]).length;
                    text = text.split('—').join(replaceVal);
                }

                if(scope.find('.tc-en').is(':checked')){
                    en = (text.match(/–/g)||[]).length;
                    text = text.split('–').join(replaceVal);
                }

                if(scope.find('.tc-hyphen').is(':checked')){
                    hy = (text.match(/-/g)||[]).length;
                    text = text.split('-').join(replaceVal);
                }

                out.val(text);

                scope.find('.tc-stat-em').text(em);
                scope.find('.tc-stat-en').text(en);
                scope.find('.tc-stat-total').text(em+en+hy);
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
                scope.find('.tc-stat-em, .tc-stat-en, .tc-stat-total').text('0');
            });

        }

        // Elementor Hook
        $(window).on('elementor/frontend/init', function(){
            elementorFrontend.hooks.addAction(
                'frontend/element_ready/textcraft_em_dash_remover.default',
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
