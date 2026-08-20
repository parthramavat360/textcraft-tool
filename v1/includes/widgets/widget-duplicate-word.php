<?php
/**
 * Widget: Duplicate Word Finder
 * @package TextCraft_Tools\Widgets
 */
declare( strict_types = 1 );
namespace TextCraft_Tools\Widgets;
defined( 'ABSPATH' ) || exit;
class Widget_Duplicate_Word extends TextCraft_Base_Widget {
    public function get_name(): string { return 'textcraft_duplicate_word'; }
    public function get_title(): string { return esc_html__( 'Duplicate Word Finder', 'textcraft-tools' ); }
    public function get_icon(): string { return 'eicon-search-bold'; }
    protected function render_tool_content( array $settings ): void {
        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'Find duplicate words in any text with a single click. See frequency bars for every word and ignore common stop words — all processing stays private in your browser.', 'textcraft-tools' )
            . '</p>';
        $this->render_options_row([
            ['id'=>'tc-dw-case','label'=>esc_html__('Case-sensitive','textcraft-tools'),'checked'=>false],
            ['id'=>'tc-dw-ignore-common','label'=>esc_html__('Ignore common words (the, a, is…)','textcraft-tools'),'checked'=>true],
        ]);
        $this->render_textarea('tc-dw-input',esc_html__('Your Text','textcraft-tools'),
            esc_html__('Paste or type text to find duplicate words and analyse word frequency…','textcraft-tools'),8);
        $this->render_button_row([
            ['id'=>'tc-dw-find','label'=>'🔍 '.esc_html__('Find Duplicates','textcraft-tools'),'variant'=>'primary'],
            ['id'=>'tc-dw-copy','label'=>'📋 '.esc_html__('Copy','textcraft-tools'),'variant'=>'ghost'],
            ['id'=>'tc-dw-clear','label'=>'🗑️ '.esc_html__('Clear','textcraft-tools'),'variant'=>'danger'],
        ]);
        $this->render_stat_bar([
            ['id'=>'tc-dw-count','label'=>esc_html__('Duplicates','textcraft-tools')],
            ['id'=>'tc-dw-total','label'=>esc_html__('Total Words','textcraft-tools')],
        ]);

        // === Duplicate Words as Tags ===
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">'.esc_html__('Duplicate Words','textcraft-tools').'</span></div>';
        echo '<div id="tc-dw-duplicates-tags" class="tc-dw-tags-wrap"></div>';

        // === NEW: All Word Frequencies as Visual Frequency Bars ===
        echo '<div class="tc-label-row tc-mt-20"><span class="tc-label">'.esc_html__('All Word Frequencies','textcraft-tools').'</span></div>';
        echo '<div id="tc-dw-freq-bars" class="tc-dw-freq-wrap"></div>';

        $this->render_inline_script(<<<'JS'
var inp=document.getElementById('tc-dw-input'),
    tagsContainer=document.getElementById('tc-dw-duplicates-tags'),
    barsContainer=document.getElementById('tc-dw-freq-bars'),
    caseChk=document.getElementById('tc-dw-case'),
    ignoreChk=document.getElementById('tc-dw-ignore-common');

if(inp){
    // Live character counter
    inp.addEventListener('input',function(){
        var cc=inp.closest('.tc-tool-card').querySelector('.tc-char-count');
        if(cc) cc.textContent=inp.value.length+' characters';
    });

    // Common English stop words
    const commonWords = new Set([
        'a','an','the','and','or','but','if','is','are','was','were','be','been','being',
        'to','of','in','on','at','for','with','by','from','up','about','into','over',
        'after','before','this','that','these','those','i','me','my','we','our','you',
        'your','he','his','she','her','it','its','they','them','their'
    ]);

    var dupsText = 'No duplicate words found.';

    // === FIND DUPLICATES BUTTON ===
    document.getElementById('tc-dw-find').addEventListener('click',function(){
        var cs = caseChk.checked;
        var ignoreCommon = ignoreChk.checked;

        var words = inp.value.trim().split(/\s+/).filter(function(w){ return w.length > 0; });
        var map = {};

        words.forEach(function(w){
            var lower = w.toLowerCase();
            if(ignoreCommon && commonWords.has(lower)) return;
            var k = cs ? w : lower;
            map[k] = (map[k]||0) + 1;
        });

        // Duplicates only (count > 1), sorted by frequency desc
        var dups = Object.entries(map)
            .filter(function(e){ return e[1] > 1; })
            .sort(function(a,b){ return b[1] - a[1]; });

        // Text version for Copy button
        dupsText = dups.length 
            ? dups.map(function(e){ return e[0] + ' (' + e[1] + 'x)'; }).join('\n') 
            : 'No duplicates found.';

        // === Render Duplicate Words as beautiful TAGS ===
        tagsContainer.innerHTML = '';
        if(dups.length){
            dups.forEach(function(e){
                var tag = document.createElement('span');
                tag.className = 'tc-tag';
                tag.className = 'tc-dw-tag';
                tag.innerHTML = e[0] + ' <span class="tc-freq-badge">' + e[1] + 'x</span>';
                tagsContainer.appendChild(tag);
            });
        } else {
            var noMsg = document.createElement('span');
            noMsg.className = 'tc-dw-nomsg';
            noMsg.textContent = 'No duplicate words found in your text.';
            tagsContainer.appendChild(noMsg);
        }

        // === Render All Word Frequencies as HORIZONTAL BARS ===
        barsContainer.innerHTML = '';
        var allFreq = Object.entries(map)
            .sort(function(a,b){ 
                return b[1] - a[1] || a[0].localeCompare(b[0]); 
            });

        if(allFreq.length === 0){
            var noMsg = document.createElement('span');
            noMsg.className = 'tc-dw-nomsg';
            noMsg.textContent = 'No words found.';
            barsContainer.appendChild(noMsg);
        } else {
            var maxCount = Math.max(...allFreq.map(function(e){ return e[1]; }));

            allFreq.forEach(function(e){
                var word = e[0];
                var count = e[1];
                var percent = maxCount > 0 ? Math.round((count / maxCount) * 100) : 0;

                // Bar row
                var row = document.createElement('div');
                row.className = 'tc-dw-bar-row';

                // Word label
                var label = document.createElement('div');
                label.className = 'tc-dw-bar-label';
                label.textContent = word;
                row.appendChild(label);

                // Progress bar container
                var barWrap = document.createElement('div');
                barWrap.className = 'tc-dw-bar-track';

                // Progress bar
                var bar = document.createElement('div');
                bar.className = 'tc-dw-bar-fill'; bar.style.width = percent + '%';
                barWrap.appendChild(bar);

                // Count badge (inside bar)
                var countBadge = document.createElement('div');
                countBadge.className = 'tc-dw-bar-badge';
                countBadge.textContent = count + 'x';
                barWrap.appendChild(countBadge);

                row.appendChild(barWrap);
                barsContainer.appendChild(row);
            });
        }

        // Update stats
        document.getElementById('tc-dw-count').textContent = dups.length;
        document.getElementById('tc-dw-total').textContent = words.length;
    });

    // === COPY BUTTON (copies duplicate list) ===
    document.getElementById('tc-dw-copy').addEventListener('click',function(){
        if(dupsText) navigator.clipboard.writeText(dupsText);
    });

    // === CLEAR BUTTON ===
    document.getElementById('tc-dw-clear').addEventListener('click',function(){
        inp.value = '';
        tagsContainer.innerHTML = '';
        barsContainer.innerHTML = '';
        dupsText = 'No duplicate words found.';
    });
}
JS);
    }
}