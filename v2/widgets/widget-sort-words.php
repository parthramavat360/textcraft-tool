<?php
/**
 * Widget: Sort Words & Lines
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Sort_Words extends TextCraft_Tool_Base {

    public function get_name(): string { return 'sort_words'; }
    public function get_title(): string { return 'Sort Words & Lines'; }
    public function get_icon(): string { return 'eicon-sort-alpha-asc'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Sort words or lines alphabetically, by length, or randomly. Useful for organizing lists, creating sorted data, or shuffling text.
        </div>

        <div class="tc-modes" data-group="sw-sort">
            <button class="tc-btn tc-btn--ghost sel" data-val="alpha_asc" type="button">A → Z</button>
            <button class="tc-btn tc-btn--ghost" data-val="alpha_desc" type="button">Z → A</button>
            <button class="tc-btn tc-btn--ghost" data-val="length_asc" type="button">Short → Long</button>
            <button class="tc-btn tc-btn--ghost" data-val="length_desc" type="button">Long → Short</button>
            <button class="tc-btn tc-btn--ghost" data-val="random" type="button">Random</button>
        </div>

        <div class="tc-field-row">
            <?php $this->render_checkbox('sw-lines', 'Sort lines instead of words', true); ?>
            <?php $this->render_checkbox('sw-case', 'Case-sensitive sorting', false); ?>
        </div>

        <?php $this->render_textarea('tc-sw-input', 'Paste words or lines to sort...', 8); ?>

        <?php $this->render_actions('tc-sw-sort', 'Sort', 'tc-sw-copy', 'Copy'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-sw-result">
            <textarea class="tc-textarea" id="tc-sw-output" placeholder="Sorted result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
