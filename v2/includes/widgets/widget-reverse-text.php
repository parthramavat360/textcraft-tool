<?php
/**
 * Widget: Reverse Text Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Reverse_Text extends TextCraft_Tool_Base {

    public function get_name(): string { return 'reverse_text'; }
    public function get_title(): string { return 'Reverse Text Generator'; }
    public function get_icon(): string { return 'eicon-flip-horizontal'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Reverse text, words, lines, or flip text upside down. Perfect for creating mirror text, puzzles, and social media posts.
        </div>

        <div class="tc-modes" data-group="rev-mode">
            <button class="tc-btn tc-btn--ghost sel" data-val="chars" type="button">Reverse Characters</button>
            <button class="tc-btn tc-btn--ghost" data-val="words" type="button">Reverse Words</button>
            <button class="tc-btn tc-btn--ghost" data-val="lines" type="button">Reverse Lines</button>
            <button class="tc-btn tc-btn--ghost" data-val="flip" type="button">Flip Upside Down</button>
        </div>

        <?php $this->render_textarea('tc-rvt-input', 'Type or paste text to reverse...', 7); ?>

        <?php $this->render_actions('tc-rvt-reverse', 'Reverse', 'tc-rvt-copy', 'Copy'); ?>

        <div class="tc-result-area" style="margin-top:20px">
            <div class="tc-label">Reversed Text</div>
            <textarea class="tc-textarea" id="tc-rvt-output" readonly rows="7"></textarea>
        </div>
        <?php
    }
}