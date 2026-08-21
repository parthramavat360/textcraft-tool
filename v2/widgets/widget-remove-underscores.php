<?php
/**
 * Widget: Remove Underscores
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Remove_Underscores extends TextCraft_Tool_Base {

    public function get_name(): string { return 'remove_underscores'; }
    public function get_title(): string { return 'Remove Underscores'; }
    public function get_icon(): string { return 'eicon-minus'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Remove underscores from text. Replace them with spaces or strip them out entirely.
        </div>

        <div class="tc-modes" data-group="ru-mode">
            <button class="tc-btn tc-btn--ghost sel" data-val="space" type="button">Replace with Space</button>
            <button class="tc-btn tc-btn--ghost" data-val="remove" type="button">Remove All</button>
        </div>

        <?php $this->render_textarea('tc-ru-input', 'Paste text containing underscores...', 8); ?>

        <?php $this->render_actions('tc-ru-convert', 'Remove Underscores', 'tc-ru-copy', 'Copy'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-ru-result">
            <textarea class="tc-textarea" id="tc-ru-output" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
