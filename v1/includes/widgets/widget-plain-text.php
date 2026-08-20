<?php
/**
 * Widget: Plain Text Converter
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Plain_Text extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_plain_text'; }
    public function get_title(): string { return esc_html__( 'Plain Text Converter', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-document-file-o'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Strip HTML tags, decode HTML entities, and clean up formatted text into plain text. Perfect for copying content from websites, emails, or rich text documents. All processing is done privately in your browser.', 'textcraft-tools' )
            . '</p>';

        // --- Checkbox options row ---
        $this->render_options_row([
            ['id' => 'tc-pt-html',        'label' => esc_html__( 'Strip HTML tags', 'textcraft-tools' ),                              'checked' => true],
            ['id' => 'tc-pt-entities',    'label' => esc_html__( 'Decode HTML entities (&amp; → &)', 'textcraft-tools' ),             'checked' => true],
            ['id' => 'tc-pt-blank-lines', 'label' => esc_html__( 'Remove extra blank lines', 'textcraft-tools' ),                     'checked' => false],
            ['id' => 'tc-pt-trim-spaces', 'label' => esc_html__( 'Trim extra spaces', 'textcraft-tools' ),                            'checked' => true],
            ['id' => 'tc-pt-unicode',     'label' => esc_html__( 'Normalize Unicode (smart quotes → straight)', 'textcraft-tools' ),  'checked' => false],
        ]);

        // --- Input textarea ---
        $this->render_textarea(
            'tc-pt-input',
            esc_html__( 'Input (HTML / Rich Text)', 'textcraft-tools' ),
            esc_html__( "Paste HTML, rich text, or formatted content here…\n\n<h1>Hello <b>World</b></h1>\n<p>This is <em>formatted</em> text with &amp; entities.</p>", 'textcraft-tools' ),
            9,
            false,
            'font-family:monospace;'
        );

        // --- Action buttons ---
        $this->render_button_row([
            ['id' => 'tc-pt-convert', 'label' => '📄 ' . esc_html__( 'Convert to Plain Text', 'textcraft-tools' ), 'variant' => 'primary'],
            ['id' => 'tc-pt-copy',    'label' => '📋 ' . esc_html__( 'Copy Result', 'textcraft-tools' ),           'variant' => 'ghost'],
            ['id' => 'tc-pt-clear',   'label' => '🗑️ ' . esc_html__( 'Clear', 'textcraft-tools' ),                 'variant' => 'danger'],
        ]);

        // --- Stats bar ---
        $this->render_stat_bar([
            ['id' => 'tc-pt-tags',   'label' => esc_html__( 'Tags Removed', 'textcraft-tools' )],
            ['id' => 'tc-pt-before', 'label' => esc_html__( 'Before', 'textcraft-tools' )],
            ['id' => 'tc-pt-after',  'label' => esc_html__( 'After', 'textcraft-tools' )],
        ]);

        // --- Output textarea ---
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Plain Text Result', 'textcraft-tools' ) . '</span></div>';
        $this->render_textarea( 'tc-pt-output', '', '', 9, true );

        // --- Inline JS ---
        $this->render_inline_script( <<<'JS'
(function(){
    var inp = document.getElementById('tc-pt-input');
    var out = document.getElementById('tc-pt-output');
    if (!inp) return;

    // Char count
    inp.addEventListener('input', function(){
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = inp.value.length + ' characters';
    });

    // Convert button
    document.getElementById('tc-pt-convert').addEventListener('click', function(){
        var text     = inp.value;
        var before   = text.length;
        var tagCount = 0;

        if (document.getElementById('tc-pt-html').checked) {
            tagCount = (text.match(/<[^>]+>/g) || []).length;
            // Block elements to newlines
            text = text.replace(/<\/(p|div|h[1-6]|li|tr|blockquote)>/gi, '\n');
            text = text.replace(/<br\s*\/?>/gi, '\n');
            text = text.replace(/<[^>]+>/g, '');
        }

        if (document.getElementById('tc-pt-entities').checked) {
            var ta = document.createElement('textarea');
            ta.innerHTML = text;
            text = ta.value;
        }

        if (document.getElementById('tc-pt-unicode').checked) {
            text = text
                .replace(/[\u2018\u2019]/g, "'")
                .replace(/[\u201C\u201D]/g, '"')
                .replace(/\u2026/g, '...')
                .replace(/\u2014/g, '--')
                .replace(/\u2013/g, '-');
        }

        if (document.getElementById('tc-pt-trim-spaces').checked) {
            text = text.replace(/[ \t]+/g, ' ').replace(/^ /gm, '').replace(/ $/gm, '');
        }

        if (document.getElementById('tc-pt-blank-lines').checked) {
            text = text.replace(/\n{3,}/g, '\n\n');
        }

        text = text.trim();
        out.value = text;

        document.getElementById('tc-pt-tags').textContent   = tagCount;
        document.getElementById('tc-pt-before').textContent = before;
        document.getElementById('tc-pt-after').textContent  = text.length;
    });

    // Copy button
    document.getElementById('tc-pt-copy').addEventListener('click', function(){
        if (!out.value) return;
        navigator.clipboard.writeText(out.value).then(function(){
            var btn = document.getElementById('tc-pt-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy Result'; }, 2000);
        });
    });

    // Clear button
    document.getElementById('tc-pt-clear').addEventListener('click', function(){
        inp.value = ''; out.value = '';
        ['tc-pt-tags','tc-pt-before','tc-pt-after'].forEach(function(id){
            document.getElementById(id).textContent = '0';
        });
        var cc = inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if (cc) cc.textContent = '0 characters';
    });
})();
JS
        );
    }
}