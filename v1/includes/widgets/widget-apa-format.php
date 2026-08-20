<?php
/**
 * Widget: APA Format Generator
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Apa_Format extends TextCraft_Base_Widget {
    public function get_name(): string  { return 'textcraft_apa_format'; }
    public function get_title(): string { return esc_html__( 'APA Format Generator', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-library-open'; }
    protected function render_tool_content( array $settings ): void {

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Generate APA 7th edition citations for websites, books, journal articles, and videos. Fill in the fields and get a perfectly formatted reference — no account needed, no data sent to any server.', 'textcraft-tools' )
            . '</p>';

        // --- Source Type tab buttons ---
        echo '<div class="tc-mb-20">';
        echo '<label class="tc-section-label tc-mb-8 tc-label-spaced">' . esc_html__( 'Source Type', 'textcraft-tools' ) . '</label>';
        echo '<div class="tc-d-flex tc-gap-8 tc-flex-wrap" id="tc-apa-tabs">';
        $tabs = [
            ['type' => 'website', 'label' => '🌐 ' . esc_html__( 'Website', 'textcraft-tools' ),  'active' => true],
            ['type' => 'book',    'label' => '📚 ' . esc_html__( 'Book', 'textcraft-tools' ),     'active' => false],
            ['type' => 'journal', 'label' => '📄 ' . esc_html__( 'Journal', 'textcraft-tools' ),  'active' => false],
            ['type' => 'video',   'label' => '🎥 ' . esc_html__( 'Video', 'textcraft-tools' ),    'active' => false],
        ];
        foreach ( $tabs as $tab ) {
            $cls = $tab['active'] ? 'tc-btn tc-btn--primary tc-apa-tab active' : 'tc-btn tc-btn--ghost tc-apa-tab';
            printf(
                '<button class="%s" data-type="%s">%s</button>',
                esc_attr( $cls ),
                esc_attr( $tab['type'] ),
                $tab['label']
            );
        }
        echo '</div></div>';

        // --- Website Form ---
        echo '<div id="tc-apa-form-website" class="tc-apa-form">';
        echo '<div class="tc-apa-fields">';
        $this->apa_field( 'tc-w-last',  esc_html__( 'Author Last Name', 'textcraft-tools' ),    'Smith' );
        $this->apa_field( 'tc-w-first', esc_html__( 'Author First Initial', 'textcraft-tools' ), 'J.' );
        $this->apa_field( 'tc-w-year',  esc_html__( 'Publication Year', 'textcraft-tools' ),    '2024' );
        $this->apa_field( 'tc-w-title', esc_html__( 'Page Title', 'textcraft-tools' ),           'Article or page title', true );
        $this->apa_field( 'tc-w-site',  esc_html__( 'Website Name', 'textcraft-tools' ),         'Website or organization name', true );
        $this->apa_field( 'tc-w-url',   esc_html__( 'URL', 'textcraft-tools' ),                  'https://example.com/page', true );
        echo '</div></div>';

        // --- Book Form ---
        echo '<div id="tc-apa-form-book" class="tc-apa-form tc-hidden">';
        echo '<div class="tc-apa-fields">';
        $this->apa_field( 'tc-b-last',      esc_html__( 'Author Last Name', 'textcraft-tools' ),    'Johnson' );
        $this->apa_field( 'tc-b-first',     esc_html__( 'Author First Initial', 'textcraft-tools' ), 'M.' );
        $this->apa_field( 'tc-b-year',      esc_html__( 'Publication Year', 'textcraft-tools' ),    '2023' );
        $this->apa_field( 'tc-b-title',     esc_html__( 'Book Title', 'textcraft-tools' ),           'Title of the book', true );
        $this->apa_field( 'tc-b-edition',   esc_html__( 'Edition (optional)', 'textcraft-tools' ),   '3rd ed.' );
        $this->apa_field( 'tc-b-publisher', esc_html__( 'Publisher', 'textcraft-tools' ),            'Publisher name' );
        echo '</div></div>';

        // --- Journal Form ---
        echo '<div id="tc-apa-form-journal" class="tc-apa-form tc-hidden">';
        echo '<div class="tc-apa-fields">';
        $this->apa_field( 'tc-j-last',    esc_html__( 'Author Last Name', 'textcraft-tools' ),    'Williams' );
        $this->apa_field( 'tc-j-first',   esc_html__( 'Author First Initial', 'textcraft-tools' ), 'A.' );
        $this->apa_field( 'tc-j-year',    esc_html__( 'Year', 'textcraft-tools' ),                '2024' );
        $this->apa_field( 'tc-j-title',   esc_html__( 'Article Title', 'textcraft-tools' ),        'Title of the article', true );
        $this->apa_field( 'tc-j-journal', esc_html__( 'Journal Name', 'textcraft-tools' ),         'Journal of Example Studies', true );
        $this->apa_field( 'tc-j-vol',     esc_html__( 'Volume', 'textcraft-tools' ),              '12' );
        $this->apa_field( 'tc-j-issue',   esc_html__( 'Issue', 'textcraft-tools' ),               '3' );
        $this->apa_field( 'tc-j-pages',   esc_html__( 'Pages', 'textcraft-tools' ),               '45–67' );
        $this->apa_field( 'tc-j-doi',     esc_html__( 'DOI / URL', 'textcraft-tools' ),            'https://doi.org/...', true );
        echo '</div></div>';

        // --- Video Form ---
        echo '<div id="tc-apa-form-video" class="tc-apa-form tc-hidden">';
        echo '<div class="tc-apa-fields">';
        $this->apa_field( 'tc-v-channel',  esc_html__( 'Channel / Creator Name', 'textcraft-tools' ), 'Channel Name', true );
        $this->apa_field( 'tc-v-year',     esc_html__( 'Upload Year', 'textcraft-tools' ),             '2024' );
        $this->apa_field( 'tc-v-date',     esc_html__( 'Month & Day', 'textcraft-tools' ),             'March 15' );
        $this->apa_field( 'tc-v-title',    esc_html__( 'Video Title', 'textcraft-tools' ),              'Title of the video', true );
        $this->apa_field( 'tc-v-platform', esc_html__( 'Platform', 'textcraft-tools' ),                'YouTube', true );
        $this->apa_field( 'tc-v-url',      esc_html__( 'URL', 'textcraft-tools' ),                     'https://youtube.com/watch?v=...', true );
        echo '</div></div>';

        // --- Generate + Copy buttons ---
        $this->render_button_row([
            ['id' => 'tc-apa-generate', 'label' => '📚 ' . esc_html__( 'Generate Citation', 'textcraft-tools' ), 'variant' => 'primary'],
            ['id' => 'tc-apa-copy',     'label' => '📋 ' . esc_html__( 'Copy', 'textcraft-tools' ),              'variant' => 'ghost'],
            ['id' => 'tc-apa-clear',    'label' => '🗑️ ' . esc_html__( 'Clear', 'textcraft-tools' ),             'variant' => 'danger'],
        ]);

        // --- Output area ---
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">' . esc_html__( 'Generated Citation', 'textcraft-tools' ) . '</span></div>';
        echo '<div id="tc-apa-output" class="tc-apa-output tc-text-14">';
        echo esc_html__( 'Fill in the fields above and click Generate Citation.', 'textcraft-tools' );
        echo '</div>';

        // --- Inline JS ---
        $this->render_inline_script( <<<'JS'
(function(){
    var currentType = 'website';
    var forms = {website:'tc-apa-form-website', book:'tc-apa-form-book', journal:'tc-apa-form-journal', video:'tc-apa-form-video'};

    // Tab switching
    document.querySelectorAll('.tc-apa-tab').forEach(function(tab){
        tab.addEventListener('click', function(){
            document.querySelectorAll('.tc-apa-tab').forEach(function(t){
                t.classList.remove('active','tc-btn--primary');
                t.classList.add('tc-btn--ghost');
            });
            tab.classList.add('active','tc-btn--primary');
            tab.classList.remove('tc-btn--ghost');
            Object.values(forms).forEach(function(id){ document.getElementById(id).style.display = 'none'; });
            currentType = tab.dataset.type;
            document.getElementById(forms[currentType]).style.display = 'block';
        });
    });

    function g(id){ var el = document.getElementById(id); return el ? el.value.trim() : ''; }

    // Generate
    document.getElementById('tc-apa-generate').addEventListener('click', function(){
        var citation = '';
        if (currentType === 'website') {
            var last=g('tc-w-last'), first=g('tc-w-first'), year=g('tc-w-year'), title=g('tc-w-title'), site=g('tc-w-site'), url=g('tc-w-url');
            var author = last ? last + (first ? ', ' + first : '') : 'Author';
            citation = author+' ('+(year||'n.d.')+').<em> '+(title||'Title of page')+'</em>. '+(site||'Website Name')+'. '+(url||'URL');
        } else if (currentType === 'book') {
            var last=g('tc-b-last'), first=g('tc-b-first'), year=g('tc-b-year'), title=g('tc-b-title'), edition=g('tc-b-edition'), pub=g('tc-b-publisher');
            var author = last ? last + (first ? ', ' + first : '') : 'Author';
            var ed = edition ? ' (' + edition + ')' : '';
            citation = author+' ('+(year||'n.d.')+').<em> '+(title||'Book Title')+ed+'</em>. '+(pub||'Publisher')+'.';
        } else if (currentType === 'journal') {
            var last=g('tc-j-last'), first=g('tc-j-first'), year=g('tc-j-year'), title=g('tc-j-title'), journal=g('tc-j-journal'), vol=g('tc-j-vol'), issue=g('tc-j-issue'), pages=g('tc-j-pages'), doi=g('tc-j-doi');
            var author = last ? last + (first ? ', ' + first : '') : 'Author';
            var issueStr = issue ? '('+issue+')' : '';
            var pagesStr = pages ? ', '+pages : '';
            citation = author+' ('+(year||'n.d.')+').'+(title||'Article Title')+'. <em>'+(journal||'Journal Name')+'</em>, <em>'+(vol||'Vol')+issueStr+'</em>'+pagesStr+'. '+(doi||'https://doi.org/xxxxx');
        } else if (currentType === 'video') {
            var channel=g('tc-v-channel'), year=g('tc-v-year'), date=g('tc-v-date'), title=g('tc-v-title'), platform=g('tc-v-platform'), url=g('tc-v-url');
            var dateStr = date ? ', '+date : '';
            citation = (channel||'Channel Name')+' ('+(year||'n.d.')+dateStr+'). <em>'+(title||'Video Title')+'</em> [Video]. '+(platform||'YouTube')+'. '+(url||'URL');
        }
        document.getElementById('tc-apa-output').innerHTML = citation;
    });

    // Copy
    document.getElementById('tc-apa-copy').addEventListener('click', function(){
        var text = document.getElementById('tc-apa-output').innerText;
        if (!text) return;
        navigator.clipboard.writeText(text).then(function(){
            var btn = document.getElementById('tc-apa-copy');
            btn.textContent = '✅ Copied!';
            setTimeout(function(){ btn.textContent = '📋 Copy'; }, 2000);
        });
    });

    // Clear — clears all form fields across all tabs
    document.getElementById('tc-apa-clear').addEventListener('click', function(){
        ['tc-w-last','tc-w-first','tc-w-year','tc-w-title','tc-w-site','tc-w-url',
         'tc-b-last','tc-b-first','tc-b-year','tc-b-title','tc-b-edition','tc-b-publisher',
         'tc-j-last','tc-j-first','tc-j-year','tc-j-title','tc-j-journal','tc-j-vol','tc-j-issue','tc-j-pages','tc-j-doi',
         'tc-v-channel','tc-v-year','tc-v-date','tc-v-title','tc-v-platform','tc-v-url'
        ].forEach(function(id){ var el=document.getElementById(id); if(el) el.value=''; });
        document.getElementById('tc-apa-output').innerHTML = 'Fill in the fields above and click Generate Citation.';
    });
})();
JS
        );
    }

    /**
     * Helper: render a single APA input field.
     */
    private function apa_field( string $id, string $label, string $placeholder = '', bool $wide = false ): void {
        $cls = $wide ? 'tc-apa-field tc-apa-field--wide' : 'tc-apa-field';
        printf(
            '<div class="%s"><label for="%s">%s</label><input type="text" id="%s" placeholder="%s"></div>',
            esc_attr( $cls ),
            esc_attr( $id ),
            $label,
            esc_attr( $id ),
            esc_attr( $placeholder )
        );
    }
}
