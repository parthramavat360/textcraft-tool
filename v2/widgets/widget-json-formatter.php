<?php
/**
 * Widget: JSON Formatter & Validator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_JSON_Formatter extends TextCraft_Tool_Base {

    public function get_name(): string { return 'json_formatter'; }
    public function get_title(): string { return 'JSON Formatter & Validator'; }
    public function get_icon(): string { return 'eicon-code'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Format, validate, and beautify your JSON data instantly. Paste raw JSON to pretty-print or minify it with proper syntax checking.
        </div>

        <?php $this->render_textarea('tc-jf-input', 'Paste your JSON here...', 12); ?>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Indent Size</label>
                <?php $this->render_select('tc-jf-indent', [
                    '2'  => '2 Spaces',
                    '4'  => '4 Spaces',
                    'tab'=> 'Tab',
                ], 'Choose indent'); ?>
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Sort Keys</label>
                <div class="tc-checkboxes">
                    <label class="tc-check"><input type="checkbox" id="tc-jf-sort-keys"> Sort object keys alphabetically</label>
                </div>
            </div>
        </div>

        <div class="tc-modes" data-group="jf-action">
            <button class="tc-btn tc-btn--ghost sel" data-val="format" type="button">Format (Pretty Print)</button>
            <button class="tc-btn tc-btn--ghost" data-val="minify" type="button">Minify</button>
            <button class="tc-btn tc-btn--ghost" data-val="validate" type="button">Validate Only</button>
        </div>

        <?php $this->render_actions('tc-jf-apply', 'Apply', 'tc-jf-copy', 'Copy'); ?>
        <?php $this->render_status('tc-jf-status'); ?>

        <div class="tc-label" style="margin-top:16px">Formatted Output</div>
        <textarea class="tc-textarea" id="tc-jf-output" rows="12" readonly placeholder="Formatted JSON will appear here..."></textarea>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-jf-result">
            <textarea class="tc-textarea" id="tc-jf-result-text" placeholder="Result will appear here..." readonly rows="10"></textarea>
        </div>
        <?php
    }
}
