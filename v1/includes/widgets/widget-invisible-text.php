<?php
/**
 * Widget: Invisible Text Generator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Invisible_Text extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_invisible_text'; }
    public function get_title(): string { return esc_html__( 'Invisible Text Generator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-eye-slash-o'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Generate invisible Unicode characters — zero-width spaces, Braille blanks, and more. Use them for usernames, social media profiles, or anywhere you need blank text. All generated right in your browser, securely and privately.', 'textcraft-tools' )
            . '</p>';

        // --- 6 Character cards (real Unicode chars via \u escapes in PHP, rendered as HTML entities) ---
        $cards = [
            [ 'char' => "\u{200B}", 'name_full' => 'Zero Width Space (U+200B)',     'name' => 'Zero Width Space',     'code' => 'U+200B', 'desc' => 'Works on WhatsApp, Discord' ],
            [ 'char' => "\u{2800}", 'name_full' => 'Braille Blank (U+2800)',         'name' => 'Braille Blank',         'code' => 'U+2800', 'desc' => 'Works on most platforms' ],
            [ 'char' => "\u{FEFF}", 'name_full' => 'Zero Width No-Break (U+FEFF)',   'name' => 'Zero Width No-Break',   'code' => 'U+FEFF', 'desc' => 'BOM character, widely supported' ],
            [ 'char' => "\u{200C}", 'name_full' => 'Zero Width Non-Joiner (U+200C)', 'name' => 'Zero Width Non-Joiner', 'code' => 'U+200C', 'desc' => 'Used in typography' ],
            [ 'char' => "\u{00AD}", 'name_full' => 'Soft Hyphen (U+00AD)',           'name' => 'Soft Hyphen',           'code' => 'U+00AD', 'desc' => 'Invisible optional hyphen' ],
            [ 'char' => "\u{3164}", 'name_full' => 'Hangul Filler (U+3164)',         'name' => 'Hangul Filler',         'code' => 'U+3164', 'desc' => 'Popular for game usernames' ],
        ];

        echo '<div class="tc-invis-grid">';
        foreach ( $cards as $card ) {
            echo '<div class="tc-invis-card" data-char="' . esc_attr( $card['char'] ) . '" data-name="' . esc_attr( $card['name_full'] ) . '">';
            echo '<div class="tc-invis-card__name">'  . esc_html( $card['name'] ) . '</div>';
            echo '<div class="tc-invis-card__code">'  . esc_html( $card['code'] ) . '</div>';
            echo '<div class="tc-invis-card__desc">'  . esc_html( $card['desc'] ) . '</div>';
            echo '<button class="tc-btn tc-btn--primary tc-it-card-copy tc-mt-10 tc-w-full">' . esc_html__( 'Copy', 'textcraft-tools' ) . '</button>';
            echo '</div>';
        }
        echo '</div>';

        // --- Generate Multiple section ---
        echo '<div class="tc-invis-gen-card">';
        echo '<label class="tc-invis-gen-label">' . esc_html__( 'Generate Multiple Invisible Characters', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-12 tc-items-center tc-flex-wrap">';

        // Character type select — real Unicode chars as option values
        echo '<div class="tc-d-flex tc-flex-col tc-gap-4">';
        echo '<label class="tc-text-12 tc-text-muted">' . esc_html__( 'Character Type', 'textcraft-tools' ) . '</label>';
        echo '<select id="tc-it-type" class="tc-invis-field">';
        echo '<option value="' . esc_attr( "\u{200B}" ) . '">' . esc_html__( 'Zero Width Space (U+200B)', 'textcraft-tools' ) . '</option>';
        echo '<option value="' . esc_attr( "\u{2800}" ) . '">' . esc_html__( 'Braille Blank (U+2800)', 'textcraft-tools' ) . '</option>';
        echo '<option value="' . esc_attr( "\u{3164}" ) . '">' . esc_html__( 'Hangul Filler (U+3164)', 'textcraft-tools' ) . '</option>';
        echo '</select></div>';

        // Count input
        echo '<div class="tc-d-flex tc-flex-col tc-gap-4">';
        echo '<label class="tc-text-12 tc-text-muted">' . esc_html__( 'Count', 'textcraft-tools' ) . '</label>';
        echo '<input type="number" id="tc-it-count" class="tc-invis-field tc-w-90" value="10" min="1" max="1000">';
        echo '</div>';

        echo '<button class="tc-btn tc-btn--primary tc-align-self-end" id="tc-it-generate">👻 ' . esc_html__( 'Generate & Copy', 'textcraft-tools' ) . '</button>';
        echo '</div></div>';

        // --- Feedback area ---
        echo '<div id="tc-it-feedback" class="tc-invis-feedback">';
        echo '<p class="tc-text-14 tc-text-muted">' . esc_html__( 'Choose a character type above and set the count, then click Generate &amp; Copy to create invisible text ready for usernames, profiles, or anywhere you need blank characters.', 'textcraft-tools' ) . '</p>';
        echo '</div>';

        // --- Inline JS (no resolveChar needed — real chars already in data-char / option values) ---
        $this->render_inline_script( <<<'JS'
(function(){
    // Individual card copy buttons
    document.querySelectorAll('.tc-it-card-copy').forEach(function(btn){
        btn.addEventListener('click', function(){
            var card = btn.closest('.tc-invis-card');
            var char = card.dataset.char;
            var name = card.dataset.name;
            navigator.clipboard.writeText(char).then(function(){
                btn.textContent = '✅ Copied!';
                document.getElementById('tc-it-feedback').innerHTML =
                    '<p class="tc-text-14 tc-font-semibold tc-text-accent-3">✅ <strong>' + name + '</strong> copied to clipboard! Paste it anywhere.</p>';
                setTimeout(function(){ btn.textContent = 'Copy'; }, 2000);
            });
        });
    });

    // Generate & Copy button
    document.getElementById('tc-it-generate').addEventListener('click', function(){
        var char  = document.getElementById('tc-it-type').value;
        var count = Math.min(parseInt(document.getElementById('tc-it-count').value) || 10, 1000);
        var text  = char.repeat(count);
        navigator.clipboard.writeText(text).then(function(){
            document.getElementById('tc-it-feedback').innerHTML =
                '<p class="tc-text-14 tc-font-semibold tc-text-accent-3">✅ ' + count + ' invisible characters copied to clipboard!</p>';
        });
    });
})();
JS
        );
    }
}
