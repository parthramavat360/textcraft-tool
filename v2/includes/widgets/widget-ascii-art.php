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

    protected bool $premium = true;

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

        <div class="tc-input-group" >
            <label class="tc-label" >Density</label>
            <div class="tc-modes" data-group="ascii-density">
                <button class="tc-btn tc-btn--ghost" data-val="simple" type="button">Simple</button>
                <button class="tc-btn tc-btn--ghost sel" data-val="medium" type="button">Medium</button>
                <button class="tc-btn tc-btn--ghost" data-val="detailed" type="button">Detailed</button>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" >Character Set</label>
            <div class="tc-modes" data-group="ascii-format">
                <button class="tc-btn tc-btn--ghost sel" data-val="blocks" type="button">Blocks</button>
                <button class="tc-btn tc-btn--ghost" data-val="characters" type="button">Characters</button>
                <button class="tc-btn tc-btn--ghost" data-val="symbols" type="button">Symbols</button>
            </div>
        </div>

        <div class="tc-input-group">
            <div class="tc-range-wrap">
                <label class="tc-range-label"  for="tc-ascii-width">
                    Width: <span id="tc-ascii-width-val">120</span>
                </label>
                <input type="range" class="tc-range" id="tc-ascii-width" min="40" max="300" value="120" step="5">
                <p class="tc-lvl-hint">Maximum width of the ASCII art in characters.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-ascii-invert">
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>Invert</b>
                    <small>Reverse brightness (light on dark).</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label"  for="tc-ascii-name">Output file name</label>
            <input type="text" class="tc-input"  id="tc-ascii-name" placeholder="my-ascii-art">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-ascii-progress', 'Generating...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-ascii-generate" type="button">Generate ASCII Art</button>
            <button class="tc-btn tc-btn--ghost" id="tc-ascii-download" type="button" style="display:none">Download .txt</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-ascii-clear" type="button">Clear all</button>
        </div>

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
