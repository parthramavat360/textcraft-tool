<?php
/**
 * Widget: Fancy Text Generator
 * Converts text into 25+ Unicode font styles instantly in the browser.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Fancy_Text extends TextCraft_Tool_Base {

    public function get_name(): string { return 'fancy_text'; }
    public function get_title(): string { return 'Fancy Text Generator'; }
    public function get_icon(): string { return 'eicon-text'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert your text into 25+ unique Unicode font styles. Type or paste any text below and instantly see all the fancy versions. Click any style to copy it to your clipboard.
        </div>

        <div class="tc-fancy-search-wrap">
            <div class="tc-input-group">
                <label class="tc-label">Enter text</label>
                <svg class="tc-fancy-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="tc-input" id="tc-ft-search" placeholder="Search styles..." autocomplete="off">
            </div>
        </div>

        <?php $this->render_textarea('tc-ft-input', 'Type or paste your text here...', 5); ?>

        <div class="tc-fancy-grid" id="tc-ft-grid"></div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-fancy-result" id="tc-ft-result">
            <p class="tc-fancy-result-hint">Generated styles will appear below as you type. Click any style card to copy it.</p>
        </div>
        <?php
    }
}
