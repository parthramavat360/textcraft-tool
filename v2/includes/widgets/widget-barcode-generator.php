<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Barcode_Generator extends TextCraft_Tool_Base {
    protected bool $show_preview = true;
    public function get_name(): string { return 'barcode_generator'; }
    public function get_title(): string { return 'Barcode Generator'; }
    public function get_icon(): string { return 'eicon-barcode'; }
    public function get_keywords(): array { return ['barcode generator','create barcode','code128','ean13','barcode maker']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate barcodes from any text or number. Supports CODE128, EAN-13, EAN-8, UPC-A, CODE39, ITF-14, and MSI formats. Download as SVG or PNG.</div>

        <div class="tc-input-group">
            <label class="tc-label">Value to encode</label>
            <input type="text" class="tc-input" id="tc-bg-value" placeholder="Enter text or numbers to encode...">
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Barcode Format</h4>
                <?php $this->render_mode_buttons('bg-format', ['CODE128'=>'CODE128','EAN13'=>'EAN-13','EAN8'=>'EAN-8','UPCA'=>'UPC-A','CODE39'=>'CODE39','ITF14'=>'ITF-14','MSI'=>'MSI'], 'CODE128'); ?>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-toggles">
                    <label class="tc-rsz-toggle"><input type="checkbox" class="tc-rsz-toggle-input" id="tc-bg-text" checked><span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span><span class="tc-rsz-toggle-text"><b>Show text below barcode</b></span></label>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Width <span class="tc-rsz-quality-badge" id="tc-bg-width-val">2</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-bg-width" min="1" max="4" step="0.5" value="2">
                    <span class="tc-rsz-slider-max">4</span>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Height <span class="tc-rsz-quality-badge" id="tc-bg-height-val">100</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">50</span>
                    <input type="range" class="tc-rsz-slider" id="tc-bg-height" min="50" max="300" step="10" value="100">
                    <span class="tc-rsz-slider-max">300</span>
                </div>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-bg-progress', 'Generating...'); ?>
        <?php $this->render_actions('tc-bg-generate', 'Generate Barcode', '', ''); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Format</span><span class="tc-stat-value" id="tc-bg-stat-format">CODE128</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-bg-stat-chars">0</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Generated</span><span class="tc-stat-value" id="tc-bg-stat-total">0</span></div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void { ?>
        <div class="tc-result-col"><div class="tc-panel">
            <div class="tc-panel-head"><h3>2 &middot; Barcode Preview</h3><span id="tc-status-chip">Ready</span></div>
            <div class="tc-panel-body">
                <div class="tc-stats">
                    <div><span>Format</span><b id="tc-stat-orig">&mdash;</b></div>
                    <div><span>Value</span><b id="tc-stat-comp">&mdash;</b></div>
                    <div class="saved"><span>Generated</span><b id="tc-stat-saved">0</b></div>
                </div>
                <div class="tc-tabs-header">
                    <h4>Preview</h4>
                    <div class="tc-tabs">
                        <button class="on" data-tab="original">Barcode</button>
                        <button data-tab="result">Download</button>
                    </div>
                </div>
                <div class="tc-preview" data-tab-content="original" id="tc-bg-output">
                    <div class="tc-bg-empty">Enter a value and click Generate to create a barcode.</div>
                </div>
                <div class="tc-preview is-hidden" data-tab-content="result" id="tc-bg-download">
                    <div class="tc-bg-dl" id="tc-bg-dl">
                        <button class="tc-btn tc-btn--accent" id="tc-bg-dl-svg">Download SVG</button>
                        <button class="tc-btn tc-btn--ghost" id="tc-bg-dl-png">Download PNG</button>
                        <button class="tc-btn tc-btn--ghost" id="tc-bg-copy-img">Copy Image</button>
                    </div>
                </div>
            </div>
        </div></div>
    <?php }
}
