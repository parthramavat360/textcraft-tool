<?php
/**
 * Widget: Flexbox Playground
 * Interactive flexbox layout visualizer.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Flexbox_Playground extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'flexbox_playground'; }
    public function get_title(): string { return 'Flexbox Playground'; }
    public function get_icon(): string { return 'eicon-flexbox'; }

    public function get_keywords(): array {
        return ['flexbox', 'flexbox playground', 'css flexbox', 'flex layout', 'flexbox visualizer', 'flexbox tutorial'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Learn and experiment with CSS Flexbox layout. Adjust container and item properties, see live results, and copy the generated CSS. Perfect for learning flexbox visually.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Container Properties</h4>
                <div class="tc-fb-controls">
                    <div class="tc-input-group">
                        <label class="tc-label">display</label>
                        <div class="tc-rsz-mode-cards tc-fb-display-cards">
                            <button class="tc-rsz-mode-card sel" type="button" data-val="flex"><span class="tc-rsz-mode-text"><b>flex</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="inline-flex"><span class="tc-rsz-mode-text"><b>inline-flex</b></span></button>
                        </div>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">flex-direction</label>
                        <div class="tc-rsz-mode-cards tc-fb-dir-cards">
                            <button class="tc-rsz-mode-card sel" type="button" data-val="row"><span class="tc-rsz-mode-text"><b>row</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="row-reverse"><span class="tc-rsz-mode-text"><b>row-reverse</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="column"><span class="tc-rsz-mode-text"><b>column</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="column-reverse"><span class="tc-rsz-mode-text"><b>column-reverse</b></span></button>
                        </div>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">flex-wrap</label>
                        <div class="tc-rsz-mode-cards tc-fb-wrap-cards">
                            <button class="tc-rsz-mode-card sel" type="button" data-val="nowrap"><span class="tc-rsz-mode-text"><b>nowrap</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="wrap"><span class="tc-rsz-mode-text"><b>wrap</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="wrap-reverse"><span class="tc-rsz-mode-text"><b>wrap-reverse</b></span></button>
                        </div>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">justify-content</label>
                        <div class="tc-rsz-mode-cards tc-fb-justify-cards">
                            <button class="tc-rsz-mode-card sel" type="button" data-val="flex-start"><span class="tc-rsz-mode-text"><b>flex-start</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="flex-end"><span class="tc-rsz-mode-text"><b>flex-end</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="center"><span class="tc-rsz-mode-text"><b>center</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="space-between"><span class="tc-rsz-mode-text"><b>space-between</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="space-around"><span class="tc-rsz-mode-text"><b>space-around</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="space-evenly"><span class="tc-rsz-mode-text"><b>space-evenly</b></span></button>
                        </div>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">align-items</label>
                        <div class="tc-rsz-mode-cards tc-fb-align-cards">
                            <button class="tc-rsz-mode-card" type="button" data-val="flex-start"><span class="tc-rsz-mode-text"><b>flex-start</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="flex-end"><span class="tc-rsz-mode-text"><b>flex-end</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="center"><span class="tc-rsz-mode-text"><b>center</b></span></button>
                            <button class="tc-rsz-mode-card sel" type="button" data-val="stretch"><span class="tc-rsz-mode-text"><b>stretch</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="baseline"><span class="tc-rsz-mode-text"><b>baseline</b></span></button>
                        </div>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">gap</label>
                        <div class="tc-fb-slider-row">
                            <input type="range" class="tc-bs-range" id="tc-fb-gap" min="0" max="50" value="10">
                            <span class="tc-bs-val" id="tc-fb-gap-val">10px</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Items</h4>
                <div class="tc-fb-items-header">
                    <button class="tc-btn tc-btn--primary" id="tc-fb-add-item" type="button"><i class="fa-solid fa-plus"></i> Add Item</button>
                    <button class="tc-btn tc-btn--ghost" id="tc-fb-remove-item" type="button"><i class="fa-solid fa-minus"></i> Remove Item</button>
                </div>
                <div class="tc-fb-items-list" id="tc-fb-items-list"></div>
            </div>

        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div id="tc-fb-result">
            <p style="color:#64748b;padding:12px 0">Add items and adjust properties</p>
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
                        <div class="tc-fb-preview-container" id="tc-fb-preview-container"></div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div class="tc-fb-code-area">
                            <div class="tc-input-group">
                                <label class="tc-label">Container CSS</label>
                                <textarea class="tc-input tc-fb-code-output" id="tc-fb-code" readonly rows="10"></textarea>
                            </div>
                            <div class="tc-fb-code-actions">
                                <button class="tc-btn tc-btn--primary" id="tc-fb-copy-css" type="button">
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
