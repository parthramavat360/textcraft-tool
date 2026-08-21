<?php
/**
 * Widget: UUID & ID Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Uuid_Generator extends TextCraft_Tool_Base {

    public function get_name(): string { return 'uuid_generator'; }
    public function get_title(): string { return 'UUID & ID Generator'; }
    public function get_icon(): string { return 'eicon-fingerprint'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate universally unique identifiers (UUIDs), ULIDs, or NanoIDs for use in databases, APIs, or any application needing unique values.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">ID Type</label>
            <?php $this->render_mode_buttons('uid-type', [
                'uuid_v4' => 'UUID v4',
                'uuid_v1' => 'UUID v1',
                'ulid'    => 'ULID',
                'nanoid'  => 'NanoID',
            ], 'uuid_v4'); ?>
        </div>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">How Many</label>
                <input type="number" class="tc-input" id="tc-uid-count" value="10" min="1" max="1000">
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Format Options</label>
                <div class="tc-checkboxes">
                    <label class="tc-check"><input type="checkbox" id="tc-uid-uppercase"> Uppercase</label>
                    <label class="tc-check"><input type="checkbox" id="tc-uid-no-dash"> Remove dashes</label>
                </div>
            </div>
        </div>

        <div class="tc-input-group" id="tc-uid-nanoid-len" style="display:none">
            <label class="tc-label">NanoID Length: <strong id="tc-uid-nanoid-len-val">21</strong></label>
            <input type="range" class="tc-range" id="tc-uid-nanoid-range" min="6" max="64" value="21">
        </div>

        <?php $this->render_actions('tc-uid-generate', 'Generate IDs', 'tc-uid-copy', 'Copy All'); ?>

        <div class="tc-label" style="margin-top:16px">Generated IDs</div>
        <textarea class="tc-textarea" id="tc-uid-output" rows="10" readonly placeholder="Your generated IDs will appear here..."></textarea>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-uid-result">
            <textarea class="tc-textarea" id="tc-uid-result-text" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
