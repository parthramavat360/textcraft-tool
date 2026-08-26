<?php
/**
 * Widget: Box Shadow Generator
 * Visual CSS box-shadow builder with live preview.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Box_Shadow_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'box_shadow_generator'; }
    public function get_title(): string { return 'Box Shadow Generator'; }
    public function get_icon(): string { return 'eicon-box'; }

    public function get_keywords(): array {
        return ['box shadow generator', 'css box shadow', 'box shadow css', 'shadow generator', 'css shadow', 'box shadow tool'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Create beautiful CSS box shadows with live preview. Adjust offset, blur, spread, color, and inset. Copy the generated CSS instantly. All processing happens in your browser.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Presets</h4>
                <div class="tc-rsz-mode-cards tc-bs-preset-cards">
                    <button class="tc-rsz-mode-card sel" type="button" data-preset="soft">
                        <span class="tc-rsz-mode-text"><b>Soft</b><span>Subtle shadow</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-preset="hard">
                        <span class="tc-rsz-mode-text"><b>Hard</b><span>Crisp edge</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-preset="glow">
                        <span class="tc-rsz-mode-text"><b>Glow</b><span>Neon glow effect</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-preset="neumorphism">
                        <span class="tc-rsz-mode-text"><b>Neumorphism</b><span>Soft UI style</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-preset="floating">
                        <span class="tc-rsz-mode-text"><b>Floating</b><span>Elevated look</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-preset="inset">
                        <span class="tc-rsz-mode-text"><b>Inset</b><span>Pressed in</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Shadow Settings</h4>
                <div class="tc-bs-sliders">
                    <div class="tc-bs-slider-row">
                        <div class="tc-input-group">
                            <label class="tc-label">X Offset</label>
                            <input type="range" class="tc-bs-range" id="tc-bs-x" min="-100" max="100" value="0">
                        </div>
                        <span class="tc-bs-val" id="tc-bs-x-val">0px</span>
                    </div>
                    <div class="tc-bs-slider-row">
                        <div class="tc-input-group">
                            <label class="tc-label">Y Offset</label>
                            <input type="range" class="tc-bs-range" id="tc-bs-y" min="-100" max="100" value="4">
                        </div>
                        <span class="tc-bs-val" id="tc-bs-y-val">4px</span>
                    </div>
                    <div class="tc-bs-slider-row">
                        <div class="tc-input-group">
                            <label class="tc-label">Blur Radius</label>
                            <input type="range" class="tc-bs-range" id="tc-bs-blur" min="0" max="200" value="12">
                        </div>
                        <span class="tc-bs-val" id="tc-bs-blur-val">12px</span>
                    </div>
                    <div class="tc-bs-slider-row">
                        <div class="tc-input-group">
                            <label class="tc-label">Spread</label>
                            <input type="range" class="tc-bs-range" id="tc-bs-spread" min="-100" max="100" value="0">
                        </div>
                        <span class="tc-bs-val" id="tc-bs-spread-val">0px</span>
                    </div>
                    <div class="tc-bs-slider-row">
                        <div class="tc-input-group">
                            <label class="tc-label">Opacity</label>
                            <input type="range" class="tc-bs-range" id="tc-bs-opacity" min="0" max="100" value="25">
                        </div>
                        <span class="tc-bs-val" id="tc-bs-opacity-val">25%</span>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Color & Style</h4>
                <div class="tc-bs-color-row">
                    <div class="tc-input-group">
                        <label class="tc-label">Shadow Color</label>
                        <div class="tc-bs-color-wrap">
                            <input type="color" id="tc-bs-color" value="#000000" class="tc-color-main-input">
                            <input type="text" class="tc-input tc-bs-color-hex" id="tc-bs-color-hex" value="#000000">
                        </div>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Style</label>
                        <div class="tc-rsz-mode-cards tc-bs-style-cards">
                            <button class="tc-rsz-mode-card sel" type="button" data-val="outset">
                                <span class="tc-rsz-mode-text"><b>Outset</b></span>
                            </button>
                            <button class="tc-rsz-mode-card" type="button" data-val="inset">
                                <span class="tc-rsz-mode-text"><b>Inset</b></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Layered Shadows</h4>
                <div class="tc-bs-layer-actions">
                    <button class="tc-btn tc-btn--primary" id="tc-bs-add-layer" type="button">
                        <i class="fa-solid fa-plus"></i> Add Shadow Layer
                    </button>
                    <button class="tc-btn tc-btn--ghost" id="tc-bs-clear-layers" type="button">
                        <i class="fa-solid fa-trash"></i> Clear All
                    </button>
                </div>
                <div class="tc-bs-layers" id="tc-bs-layers"></div>
            </div>

        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div id="tc-bs-result">
            <p style="color:#64748b;padding:12px 0">Adjust the sliders to create a shadow</p>
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
                        <div class="tc-bs-preview-area">
                            <div class="tc-bs-preview-box" id="tc-bs-preview-box">
                                <span>Preview</span>
                            </div>
                        </div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div class="tc-bs-code-area">
                            <div class="tc-input-group">
                                <label class="tc-label">Generated CSS</label>
                                <textarea class="tc-input tc-bs-code-output" id="tc-bs-code" readonly rows="6"></textarea>
                            </div>
                            <div class="tc-bs-code-actions">
                                <button class="tc-btn tc-btn--primary" id="tc-bs-copy-css" type="button">
                                    <i class="fa-regular fa-copy"></i> Copy CSS
                                </button>
                                <button class="tc-btn tc-btn--ghost" id="tc-bs-copy-all" type="button">
                                    <i class="fa-solid fa-layer-group"></i> Copy All Layers
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
