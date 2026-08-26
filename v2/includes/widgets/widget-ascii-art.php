<?php
/**
 * Widget: ASCII Art Generator
 * Premium design with density, format, width slider, invert, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Ascii_Art extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'ascii_art'; }
    public function get_title(): string { return 'ASCII Art Generator'; }
    public function get_icon(): string { return 'eicon-code'; }

    public function get_keywords(): array {
        return ['ascii art', 'image to ascii', 'ascii generator', 'text art', 'pixel art'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert any image into ASCII art text. Choose density, character set, width, and invert. Everything runs in your browser.
        </div>

        <?php $this->render_drop_zone('tc-ascii-drop', 'image/*', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-ascii-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Density</h4>
                <div class="tc-modes" data-group="ascii-density">
                    <button class="tc-btn tc-btn--ghost" data-val="simple" type="button">Simple</button>
                    <button class="tc-btn tc-btn--ghost sel" data-val="medium" type="button">Medium</button>
                    <button class="tc-btn tc-btn--ghost" data-val="detailed" type="button">Detailed</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Character Set</h4>
                <div class="tc-modes" data-group="ascii-format">
                    <button class="tc-btn tc-btn--ghost sel" data-val="blocks" type="button">Blocks</button>
                    <button class="tc-btn tc-btn--ghost" data-val="characters" type="button">Characters</button>
                    <button class="tc-btn tc-btn--ghost" data-val="symbols" type="button">Symbols</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Width <span class="tc-rsz-quality-badge" id="tc-ascii-width-val">120</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">40</span>
                    <input type="range" class="tc-rsz-slider" id="tc-ascii-width" min="40" max="300" value="120" step="5">
                    <span class="tc-rsz-slider-max">300</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Invert</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-ascii-invert">
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span class="tc-rsz-slider-min" style="font-size:12px;opacity:0.6">Reverse brightness (light on dark)</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-ascii-progress', 'Generating...'); ?>

        <?php $this->render_actions('tc-ascii-generate', 'Generate ASCII Art', 'tc-ascii-download', 'Download .txt'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-ascii-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Width</span><span class="tc-stat-value" id="tc-ascii-stat-w">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Characters</span><span class="tc-stat-value" id="tc-ascii-stat-chars">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Lines</span><span class="tc-stat-value" id="tc-ascii-stat-lines">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with ASCII-specific labels.
     */
    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; ASCII Art</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Characters</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Lines</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">ASCII Art</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original image will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">ASCII art will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
