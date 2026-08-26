<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Text_To_Handwriting extends TextCraft_Tool_Base {
    protected bool $show_preview = true;
    public function get_name(): string { return 'text_to_handwriting'; }
    public function get_title(): string { return 'Text to Handwriting'; }
    public function get_icon(): string { return 'eicon-editor-bold'; }
    public function get_keywords(): array { return ['text to handwriting','handwriting generator','cursive text','handwriting font','write like handwriting']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Convert typed text into realistic handwriting. Choose from multiple handwriting styles, adjust pen color, paper style, and download as an image.</div>

        <div class="tc-input-group">
            <label class="tc-label">Your text</label>
            <textarea class="tc-textarea" id="tc-th-text" rows="6" placeholder="Type or paste your text here..."></textarea>
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Handwriting Style</h4>
                <?php $this->render_mode_buttons('th-style', ['cursive'=>'Cursive','print'=>'Print','messy'=>'Messy','neat'=>'Neat','elegant'=>'Elegant'], 'cursive'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Pen Color</h4>
                <div class="tc-th-colors">
                    <button class="tc-th-color active" data-color="#1a1a2e" style="background:#1a1a2e" title="Black"></button>
                    <button class="tc-th-color" data-color="#0000ff" style="background:#0000ff" title="Blue"></button>
                    <button class="tc-th-color" data-color="#006400" style="background:#006400" title="Green"></button>
                    <button class="tc-th-color" data-color="#8b0000" style="background:#8b0000" title="Red"></button>
                    <button class="tc-th-color" data-color="#4b0082" style="background:#4b0082" title="Purple"></button>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Paper Style</h4>
                <?php $this->render_mode_buttons('th-paper', ['lined'=>'Lined','blank'=>'Blank','grid'=>'Grid','dotted'=>'Dotted'], 'lined'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Font Size <span class="tc-rsz-quality-badge" id="tc-th-size-val">18</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">12</span>
                    <input type="range" class="tc-rsz-slider" id="tc-th-size" min="12" max="32" step="1" value="18">
                    <span class="tc-rsz-slider-max">32</span>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-th-progress', 'Generating...'); ?>
        <?php $this->render_actions('tc-th-generate', 'Generate Handwriting', '', ''); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-th-chars">0</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-th-words">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Lines</span><span class="tc-stat-value" id="tc-th-lines">0</span></div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void { ?>
        <div class="tc-result-col"><div class="tc-panel">
            <div class="tc-panel-head"><h3>2 &middot; Handwriting Preview</h3><span id="tc-status-chip">Ready</span></div>
            <div class="tc-panel-body">
                <div class="tc-stats">
                    <div><span>Style</span><b id="tc-stat-orig">&mdash;</b></div>
                    <div><span>Pen Color</span><b id="tc-stat-comp">&mdash;</b></div>
                    <div class="saved"><span>Paper</span><b id="tc-stat-saved">&mdash;</b></div>
                </div>
                <div class="tc-tabs-header">
                    <h4>Preview</h4>
                    <div class="tc-tabs">
                        <button class="on" data-tab="original">Handwriting</button>
                        <button data-tab="result">Download</button>
                    </div>
                </div>
                <div class="tc-preview" data-tab-content="original" id="tc-th-output">
                    <div class="tc-th-empty">Type text and click Generate to create handwriting.</div>
                </div>
                <div class="tc-preview is-hidden" data-tab-content="result" id="tc-th-download">
                    <div class="tc-th-dl" id="tc-th-dl">
                        <button class="tc-btn tc-btn--accent" id="tc-th-dl-png">Download PNG</button>
                        <button class="tc-btn tc-btn--ghost" id="tc-th-copy-img">Copy Image</button>
                    </div>
                </div>
            </div>
        </div></div>
    <?php }
}
