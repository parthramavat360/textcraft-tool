<?php
/**
 * Widget: Phonetic Spelling Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Phonetic_Spelling extends TextCraft_Tool_Base {

    public function get_name(): string { return 'phonetic_spelling'; }
    public function get_title(): string { return 'Phonetic Spelling Generator'; }
    public function get_icon(): string { return 'eicon-megaphone'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate phonetic spellings for any text. Choose between simplified pronunciation guides, NATO alphabet, or sound-alike representations. Useful for clear verbal communication.
        </div>

        <?php $this->render_textarea('tc-ps-input', 'Enter text to generate phonetic spelling...', 6); ?>

        <div class="tc-input-group">
            <label class="tc-label">Phonetic Mode</label>
            <?php $this->render_mode_buttons('ps-mode', [
                'simplified' => 'Simplified',
                'nato'       => 'NATO Alphabet',
                'soundalike' => 'Sound-Alike',
            ], 'simplified'); ?>
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Options</label>
            <div class="tc-checkboxes">
                <label class="tc-check"><input type="checkbox" id="tc-ps-syllables" checked> Break into syllables</label>
                <label class="tc-check"><input type="checkbox" id="tc-ps-stress"> Mark stressed syllables</label>
            </div>
        </div>

        <?php $this->render_actions('tc-ps-generate', 'Generate Phonetic Spelling', 'tc-ps-copy', 'Copy'); ?>

        <div class="tc-label" style="margin-top:16px">Phonetic Spelling</div>
        <textarea class="tc-textarea" id="tc-ps-output" rows="8" readonly placeholder="Phonetic spelling will appear here..."></textarea>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-ps-result">
            <textarea class="tc-textarea" id="tc-ps-result-text" placeholder="Result will appear here..." readonly rows="8"></textarea>
        </div>
        <?php
    }
}
