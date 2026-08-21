<?php
/**
 * Widget: Roman Numeral Converter
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Roman_Numeral extends TextCraft_Tool_Base {

    public function get_name(): string { return 'roman_numeral'; }
    public function get_title(): string { return 'Roman Numeral Converter'; }
    public function get_icon(): string { return 'eicon-text'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert between standard numbers and Roman numerals. Supports values from 1 to 3999.
        </div>

        <div class="tc-modes" data-group="rn-mode">
            <button class="tc-btn tc-btn--ghost sel" data-val="to_roman" type="button">Number → Roman</button>
            <button class="tc-btn tc-btn--ghost" data-val="from_roman" type="button">Roman → Number</button>
        </div>

        <?php $this->render_textarea('tc-rn-input', 'Enter a number (1–3999) or Roman numeral...', 6); ?>

        <?php $this->render_actions('tc-rn-convert', 'Convert', 'tc-rn-copy', 'Copy'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-rn-result">
            <textarea class="tc-textarea" id="tc-rn-output" placeholder="Result will appear here..." readonly rows="6"></textarea>
        </div>
        <?php
    }
}
