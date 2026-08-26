<?php
/**
 * Widget: HTML to Image
 * Convert HTML/CSS code to PNG or JPG image.
 * 100% client-side using canvas API + foreignObject.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Html_To_Image extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'html_to_image'; }
    public function get_title(): string { return 'HTML to Image'; }
    public function get_icon(): string { return 'eicon-code'; }

    public function get_keywords(): array {
        return ['html to image', 'html to png', 'screenshot html', 'html screenshot', 'code to image', 'capture html'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert HTML and CSS code to a PNG or JPG image. Paste your code, set dimensions, and generate an image. All processing happens in your browser — no server needed.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">HTML Code</h4>
                <div class="tc-html-editor-wrap">
                    <textarea class="tc-html-editor" id="tc-html-code" rows="12" placeholder="&lt;div style=&quot;padding: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white; font-family: sans-serif;&quot;&gt;&#10;  &lt;h1&gt;Hello World&lt;/h1&gt;&#10;  &lt;p&gt;This is a screenshot of HTML code&lt;/p&gt;&#10;&lt;/div&gt;" autocomplete="off" spellcheck="false"></textarea>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Output Dimensions</h4>
                <div class="tc-svg-dims-row">
                    <div class="tc-rsz-dim-field">
                        <label class="tc-rsz-dim-label">Width</label>
                        <div class="tc-rsz-dim-input">
                            <input type="number" class="tc-rsz-num" id="tc-html-w" value="800" min="50" max="4096">
                            <span class="tc-rsz-unit">px</span>
                        </div>
                    </div>
                    <button class="tc-rsz-lock tc-rsz-lock--on" id="tc-html-lock" type="button" title="Aspect ratio locked">
                        <svg class="tc-rsz-lock-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <svg class="tc-rsz-lock-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                    </button>
                    <div class="tc-rsz-dim-field">
                        <label class="tc-rsz-dim-label">Height</label>
                        <div class="tc-rsz-dim-input">
                            <input type="number" class="tc-rsz-num" id="tc-html-h" value="400" min="50" max="4096">
                            <span class="tc-rsz-unit">px</span>
                        </div>
                    </div>
                </div>
                <div class="tc-svg-preset-row">
                    <button class="tc-svg-preset-btn" type="button" data-w="800" data-h="600">800×600</button>
                    <button class="tc-svg-preset-btn" type="button" data-w="1080" data-h="1080">1080²</button>
                    <button class="tc-svg-preset-btn" type="button" data-w="1920" data-h="1080">1920×1080</button>
                    <button class="tc-svg-preset-btn" type="button" data-w="1080" data-h="1920">1080×1920</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Output Format</h4>
                <div class="tc-rsz-format-row">
                    <button class="tc-rsz-fmt sel" type="button" data-val="image/png">
                        <span class="tc-rsz-fmt-icon">PNG</span>
                        <span>PNG</span>
                    </button>
                    <button class="tc-rsz-fmt" type="button" data-val="image/jpeg">
                        <span class="tc-rsz-fmt-icon">JPG</span>
                        <span>JPEG</span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-html-quality-section" style="display:none">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-html-quality-val">92%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Low</span>
                    <input type="range" class="tc-rsz-slider" id="tc-html-quality" min="10" max="100" value="92">
                    <span class="tc-rsz-slider-max">Max</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Scale <span class="tc-rsz-quality-badge" id="tc-html-scale-val">1x</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1x</span>
                    <input type="range" class="tc-rsz-slider" id="tc-html-scale" min="1" max="4" value="1" step="1">
                    <span class="tc-rsz-slider-max">4x</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-html-progress', 'Rendering...'); ?>

        <?php $this->render_actions('tc-html-apply', 'Generate Image', 'tc-html-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">HTML Size</span><span class="tc-stat-value" id="tc-html-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Image Size</span><span class="tc-stat-value" id="tc-html-stat-out">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Dimensions</span><span class="tc-stat-value" id="tc-html-stat-dims">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-html-result" id="tc-html-result">
            <p style="color:#64748b;padding:12px 0">Image will appear here after you click Generate.</p>
        </div>
        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Result</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>HTML Size</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Image Size</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Dimensions</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">HTML Code</button>
                            <button data-tab="result">Image</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-html-code-preview" id="tc-html-preview-orig" style="background:#0d1321;border-radius:8px;padding:16px;overflow:auto;max-height:300px">
                            <p style="color:#64748b">Enter HTML code and click Generate</p>
                        </div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <?php $this->render_result_content($settings); ?>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
