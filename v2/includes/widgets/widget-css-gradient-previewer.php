<?php
/**
 * Widget: CSS Gradient Previewer
 * Visual CSS gradient builder with live preview.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_CSS_Gradient_Previewer extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'css_gradient_previewer'; }
    public function get_title(): string { return 'CSS Gradient Previewer'; }
    public function get_icon(): string { return 'eicon-gradient'; }

    public function get_keywords(): array {
        return ['css gradient', 'gradient generator', 'linear gradient', 'radial gradient', 'conic gradient', 'gradient css', 'gradient tool'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Create beautiful CSS gradients with live preview. Choose linear, radial, or conic gradients, add color stops, and copy the generated CSS. All processing happens in your browser.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Gradient Type</h4>
                <div class="tc-rsz-mode-cards tc-grad-type-cards">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="linear">
                        <span class="tc-rsz-mode-text"><b>Linear</b><span>Straight line</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="radial">
                        <span class="tc-rsz-mode-text"><b>Radial</b><span>Circle/ellipse</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="conic">
                        <span class="tc-rsz-mode-text"><b>Conic</b><span>Rotation around center</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section tc-grad-angle-section">
                <h4 class="tc-rsz-heading">Angle</h4>
                <div class="tc-grad-slider-row">
                    <input type="range" class="tc-bs-range" id="tc-grad-angle" min="0" max="360" value="180">
                    <span class="tc-bs-val" id="tc-grad-angle-val">180deg</span>
                </div>
            </div>

            <div class="tc-rsz-section tc-grad-shape-section" style="display:none">
                <h4 class="tc-rsz-heading">Shape & Position</h4>
                <div class="tc-grad-row">
                    <div class="tc-input-group">
                        <label class="tc-label">Shape</label>
                        <div class="tc-rsz-mode-cards tc-grad-shape-cards">
                            <button class="tc-rsz-mode-card sel" type="button" data-val="circle">
                                <span class="tc-rsz-mode-text"><b>Circle</b></span>
                            </button>
                            <button class="tc-rsz-mode-card" type="button" data-val="ellipse">
                                <span class="tc-rsz-mode-text"><b>Ellipse</b></span>
                            </button>
                        </div>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Position</label>
                        <div class="tc-rsz-mode-cards tc-grad-pos-cards">
                            <button class="tc-rsz-mode-card" type="button" data-val="center"><span class="tc-rsz-mode-text"><b>Center</b></span></button>
                            <button class="tc-rsz-mode-card sel" type="button" data-val="top"><span class="tc-rsz-mode-text"><b>Top</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="bottom"><span class="tc-rsz-mode-text"><b>Bottom</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="left"><span class="tc-rsz-mode-text"><b>Left</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="right"><span class="tc-rsz-mode-text"><b>Right</b></span></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Presets</h4>
                <div class="tc-grad-presets" id="tc-grad-presets">
                    <button class="tc-grad-preset-swatch" type="button" data-colors="#667eea,#764ba2" data-angle="135" title="Purple Dream"></button>
                    <button class="tc-grad-preset-swatch" type="button" data-colors="#f093fb,#f5576c" data-angle="135" title="Pink Flame"></button>
                    <button class="tc-grad-preset-swatch" type="button" data-colors="#4facfe,#00f2fe" data-angle="135" title="Ocean Blue"></button>
                    <button class="tc-grad-preset-swatch" type="button" data-colors="#43e97b,#38f9d7" data-angle="135" title="Emerald"></button>
                    <button class="tc-grad-preset-swatch" type="button" data-colors="#fa709a,#fee140" data-angle="135" title="Sunset"></button>
                    <button class="tc-grad-preset-swatch" type="button" data-colors="#a18cd1,#fbc2eb" data-angle="135" title="Lavender"></button>
                    <button class="tc-grad-preset-swatch" type="button" data-colors="#fccb90,#d57eeb" data-angle="135" title="Peach"></button>
                    <button class="tc-grad-preset-swatch" type="button" data-colors="#e0c3fc,#8ec5fc" data-angle="135" title="Sky"></button>
                    <button class="tc-grad-preset-swatch" type="button" data-colors="#f5576c,#ff6b6b" data-angle="135" title="Red Alert"></button>
                    <button class="tc-grad-preset-swatch" type="button" data-colors="#0ba360,#3cba92" data-angle="135" title="Forest"></button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Color Stops</h4>
                <div class="tc-grad-stops" id="tc-grad-stops"></div>
                <button class="tc-btn tc-btn--primary tc-grad-add-stop" id="tc-grad-add-stop" type="button">
                    <i class="fa-solid fa-plus"></i> Add Color Stop
                </button>
            </div>

        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div id="tc-grad-result">
            <p style="color:#64748b;padding:12px 0">Add color stops to create a gradient</p>
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
                        <div class="tc-grad-preview-area" id="tc-grad-preview"></div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div class="tc-grad-code-area">
                            <div class="tc-input-group">
                                <label class="tc-label">Generated CSS</label>
                                <textarea class="tc-input tc-grad-code-output" id="tc-grad-code" readonly rows="6"></textarea>
                            </div>
                            <div class="tc-grad-code-actions">
                                <button class="tc-btn tc-btn--primary" id="tc-grad-copy-css" type="button">
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
