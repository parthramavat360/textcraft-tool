<?php
/**
 * Widget: Border Radius Generator
 * Visual CSS border-radius builder with live preview.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Border_Radius_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'border_radius_generator'; }
    public function get_title(): string { return 'Border Radius Generator'; }
    public function get_icon(): string { return 'eicon-circle'; }

    public function get_keywords(): array {
        return ['border radius', 'border radius css', 'css border radius', 'border radius generator', 'rounded corners', 'css rounded corners'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Create CSS border-radius values with live preview. Set each corner individually or link them together. Copy the generated CSS instantly. All processing happens in your browser.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Corner Radius</h4>
                <div class="tc-br-mode-row">
                    <button class="tc-btn tc-btn--primary" id="tc-br-link-btn" type="button">
                        <i class="fa-solid fa-link"></i> Linked
                    </button>
                    <span class="tc-br-hint">Click to link/unlink all corners</span>
                </div>
                <div class="tc-br-corners">
                    <div class="tc-br-corner">
                        <div class="tc-input-group">
                            <label class="tc-label">Top Left</label>
                            <div class="tc-br-input-row">
                                <input type="range" class="tc-bs-range" id="tc-br-tl" min="0" max="200" value="12">
                                <input type="number" class="tc-input tc-br-num" id="tc-br-tl-num" min="0" max="999" value="12">
                                <span class="tc-br-unit">px</span>
                            </div>
                        </div>
                    </div>
                    <div class="tc-br-corner">
                        <div class="tc-input-group">
                            <label class="tc-label">Top Right</label>
                            <div class="tc-br-input-row">
                                <input type="range" class="tc-bs-range" id="tc-br-tr" min="0" max="200" value="12">
                                <input type="number" class="tc-input tc-br-num" id="tc-br-tr-num" min="0" max="999" value="12">
                                <span class="tc-br-unit">px</span>
                            </div>
                        </div>
                    </div>
                    <div class="tc-br-corner">
                        <div class="tc-input-group">
                            <label class="tc-label">Bottom Right</label>
                            <div class="tc-br-input-row">
                                <input type="range" class="tc-bs-range" id="tc-br-br" min="0" max="200" value="12">
                                <input type="number" class="tc-input tc-br-num" id="tc-br-br-num" min="0" max="999" value="12">
                                <span class="tc-br-unit">px</span>
                            </div>
                        </div>
                    </div>
                    <div class="tc-br-corner">
                        <div class="tc-input-group">
                            <label class="tc-label">Bottom Left</label>
                            <div class="tc-br-input-row">
                                <input type="range" class="tc-bs-range" id="tc-br-bl" min="0" max="200" value="12">
                                <input type="number" class="tc-input tc-br-num" id="tc-br-bl-num" min="0" max="999" value="12">
                                <span class="tc-br-unit">px</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Presets</h4>
                <div class="tc-rsz-mode-cards tc-br-preset-cards">
                    <button class="tc-rsz-mode-card sel" type="button" data-tl="12" data-tr="12" data-br="12" data-bl="12">
                        <span class="tc-rsz-mode-text"><b>Subtle</b><span>12px all</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-tl="16" data-tr="16" data-br="16" data-bl="16">
                        <span class="tc-rsz-mode-text"><b>Medium</b><span>16px all</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-tl="24" data-tr="24" data-br="24" data-bl="24">
                        <span class="tc-rsz-mode-text"><b>Large</b><span>24px all</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-tl="50" data-tr="50" data-br="50" data-bl="50">
                        <span class="tc-rsz-mode-text"><b>Pill</b><span>50px all</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-tl="999" data-tr="999" data-br="999" data-bl="999">
                        <span class="tc-rsz-mode-text"><b>Circle</b><span>Full round</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-tl="30" data-tr="30" data-br="0" data-bl="0">
                        <span class="tc-rsz-mode-text"><b>Top Left</b><span>Top corners only</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-tl="0" data-tr="0" data-br="30" data-bl="30">
                        <span class="tc-rsz-mode-text"><b>Bottom</b><span>Bottom corners</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-tl="30" data-tr="0" data-br="0" data-bl="30">
                        <span class="tc-rsz-mode-text"><b>Left</b><span>Left side</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-tl="0" data-tr="30" data-br="30" data-bl="0">
                        <span class="tc-rsz-mode-text"><b>Right</b><span>Right side</span></span>
                    </button>
                </div>
            </div>

        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div id="tc-br-result">
            <p style="color:#64748b;padding:12px 0">Adjust the sliders to create border radius</p>
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
                        <div class="tc-br-preview-area">
                            <div class="tc-br-preview-box" id="tc-br-preview-box">
                                <span>Preview</span>
                            </div>
                        </div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div class="tc-br-code-area">
                            <div class="tc-input-group">
                                <label class="tc-label">Generated CSS</label>
                                <textarea class="tc-input tc-br-code-output" id="tc-br-code" readonly rows="4"></textarea>
                            </div>
                            <div class="tc-br-code-actions">
                                <button class="tc-btn tc-btn--primary" id="tc-br-copy-css" type="button">
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
