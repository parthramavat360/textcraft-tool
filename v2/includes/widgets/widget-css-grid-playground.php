<?php
/**
 * Widget: CSS Grid Playground
 * Interactive CSS Grid layout visualizer.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_CSS_Grid_Playground extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'css_grid_playground'; }
    public function get_title(): string { return 'CSS Grid Playground'; }
    public function get_icon(): string { return 'eicon-grid'; }

    public function get_keywords(): array {
        return ['css grid', 'grid layout', 'grid playground', 'css grid visualizer', 'grid template', 'grid layout tool'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Learn and experiment with CSS Grid layout. Set grid template columns/rows, add items with spans, and copy the generated CSS. Perfect for learning CSS Grid visually.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Grid Container</h4>
                <div class="tc-grid-controls">
                    <div class="tc-input-group">
                        <label class="tc-label">grid-template-columns</label>
                        <input type="text" class="tc-input" id="tc-grid-cols" value="repeat(3, 1fr)" placeholder="e.g. repeat(3, 1fr) or 1fr 1fr 1fr">
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">grid-template-rows</label>
                        <input type="text" class="tc-input" id="tc-grid-rows" value="auto" placeholder="e.g. auto auto or 100px 100px">
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">gap</label>
                        <div class="tc-fb-slider-row">
                            <input type="range" class="tc-bs-range" id="tc-grid-gap" min="0" max="50" value="10">
                            <span class="tc-bs-val" id="tc-grid-gap-val">10px</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Presets</h4>
                <div class="tc-rsz-mode-cards tc-grid-preset-cards">
                    <button class="tc-rsz-mode-card sel" type="button" data-cols="repeat(3, 1fr)" data-rows="auto">
                        <span class="tc-rsz-mode-text"><b>3 Columns</b><span>Equal width</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-cols="repeat(4, 1fr)" data-rows="auto">
                        <span class="tc-rsz-mode-text"><b>4 Columns</b><span>Equal width</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-cols="200px 1fr 200px" data-rows="auto">
                        <span class="tc-rsz-mode-text"><b>Sidebar</b><span>Sidebar layout</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-cols="repeat(3, 1fr)" data-rows="repeat(3, 100px)">
                        <span class="tc-rsz-mode-text"><b>3x3</b><span>Fixed rows</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-cols="repeat(auto-fit, minmax(200px, 1fr))" data-rows="auto">
                        <span class="tc-rsz-mode-text"><b>Auto Fit</b><span>Responsive</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-cols="repeat(2, 1fr)" data-rows="repeat(2, 1fr)">
                        <span class="tc-rsz-mode-text"><b>2x2</b><span>Square grid</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Grid Items</h4>
                <div class="tc-fb-items-header">
                    <button class="tc-btn tc-btn--primary" id="tc-grid-add-item" type="button"><i class="fa-solid fa-plus"></i> Add Item</button>
                    <button class="tc-btn tc-btn--ghost" id="tc-grid-remove-item" type="button"><i class="fa-solid fa-minus"></i> Remove Item</button>
                </div>
                <div class="tc-grid-items-list" id="tc-grid-items-list"></div>
            </div>

        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div id="tc-grid-result">
            <p style="color:#64748b;padding:12px 0">Add items and configure the grid</p>
        </div>
        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Preview & Code</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Preview</button>
                            <button data-tab="result">CSS Code</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-grid-preview-container" id="tc-grid-preview-container"></div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div class="tc-grid-code-area">
                            <div class="tc-input-group">
                                <label class="tc-label">Generated CSS</label>
                                <textarea class="tc-input tc-grid-code-output" id="tc-grid-code" readonly rows="12"></textarea>
                            </div>
                            <div class="tc-grid-code-actions">
                                <button class="tc-btn tc-btn--primary" id="tc-grid-copy-css" type="button">
                                    <i class="fa-regular fa-copy"></i> Copy CSS
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
