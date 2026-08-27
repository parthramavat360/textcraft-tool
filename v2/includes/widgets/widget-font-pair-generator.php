<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Font_Pair_Generator extends TextCraft_Tool_Base {
    public function get_name(): string { return 'font_pair_generator'; }
    public function get_title(): string { return 'Font Pair Generator'; }
    public function get_icon(): string { return 'eicon-font'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate beautiful font pairings for your next project. Preview heading and body fonts together and export the CSS/Google Fonts link.</div>
        <div class="tc-tool-actions-bar">
            <div class="tc-modes" data-group="fp-style">
                <button class="tc-btn tc-btn--ghost sel" data-val="serif">Serif</button>
                <button class="tc-btn tc-btn--ghost" data-val="sans">Sans-Serif</button>
                <button class="tc-btn tc-btn--ghost" data-val="mono">Monospace</button>
                <button class="tc-btn tc-btn--ghost" data-val="display">Display</button>
            </div>
            <button class="tc-btn tc-btn--outline" id="fp-refresh">Shuffle</button>
            <button class="tc-btn tc-btn--outline" id="fp-copy-css">Copy CSS</button>
        </div>
        <div class="tc-rsz-toggles">
            <label class="tc-rsz-toggle">
                <input type="checkbox" class="tc-rsz-toggle-input" id="fp-google" checked>
                <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                <span class="tc-rsz-toggle-text">Google Fonts</span>
            </label>
        </div>
        <div class="tc-input-group">
            <textarea class="tc-input tc-input--textarea" id="fp-preview-text" rows="3" placeholder="Type text to preview font pair...">The quick brown fox jumps over the lazy dog</textarea>
        </div>
        <div class="tc-result-panel" id="fp-result" style="display:block">
            <div class="tc-result-header">
                <span class="tc-status-chip" id="fp-status">Font Pair</span>
            </div>
            <div class="tc-result-body" id="fp-output">
                <div id="fp-pair-display"></div>
            </div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
