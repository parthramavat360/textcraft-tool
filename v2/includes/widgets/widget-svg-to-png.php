<?php
/**
 * Widget: SVG to PNG
 * Convert SVG images to PNG with custom dimensions.
 * 100% client-side using canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Svg_To_Png extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'svg_to_png'; }
    public function get_title(): string { return 'SVG to PNG'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['svg to png', 'svg converter', 'convert svg', 'svg to image', 'svg rasterize', 'svg export'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert SVG images to high-quality PNG files. Set custom width and height for the output. All processing happens in your browser — no upload needed.
        </div>

        <?php $this->render_drop_zone('tc-svg-drop', 'image/svg+xml,.svg', 'Drag & drop an SVG file here or click to browse'); ?>
        <?php $this->render_file_row('tc-svg-file'); ?>

        <div class="tc-rsz-options tc-imgprem">

            <div class="tc-rsz-section" id="tc-svg-preview-section" style="display:none">
                <h4 class="tc-rsz-heading">Preview</h4>
                <div class="tc-svg-preview-wrap" id="tc-svg-preview-wrap">
                    <div id="tc-svg-display"></div>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-svg-dims-section" style="display:none">
                <h4 class="tc-rsz-heading">Output Dimensions</h4>
                <div class="tc-svg-dims-row">
                    <div class="tc-rsz-dim-field">
                        <label class="tc-rsz-dim-label">Width</label>
                        <div class="tc-rsz-dim-input">
                            <input type="number" class="tc-rsz-num" id="tc-svg-out-w" value="512" min="16" max="8192">
                            <span class="tc-rsz-unit">px</span>
                        </div>
                    </div>
                    <button class="tc-rsz-lock tc-rsz-lock--on" id="tc-svg-lock" type="button" title="Aspect ratio locked">
                        <svg class="tc-rsz-lock-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <svg class="tc-rsz-lock-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                    </button>
                    <div class="tc-rsz-dim-field">
                        <label class="tc-rsz-dim-label">Height</label>
                        <div class="tc-rsz-dim-input">
                            <input type="number" class="tc-rsz-num" id="tc-svg-out-h" value="512" min="16" max="8192">
                            <span class="tc-rsz-unit">px</span>
                        </div>
                    </div>
                </div>
                <div class="tc-svg-preset-row">
                    <button class="tc-svg-preset-btn" type="button" data-w="256" data-h="256">256²</button>
                    <button class="tc-svg-preset-btn" type="button" data-w="512" data-h="512">512²</button>
                    <button class="tc-svg-preset-btn" type="button" data-w="1024" data-h="1024">1024²</button>
                    <button class="tc-svg-preset-btn" type="button" data-w="1920" data-h="1080">1920×1080</button>
                    <button class="tc-svg-preset-btn" type="button" data-w="3840" data-h="2160">4K</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Background</h4>
                <div class="tc-rsz-mode-cards tc-svg-bg-modes">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="transparent">
                        <span class="tc-rsz-mode-text"><b>Transparent</b><span>Keep alpha</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="white">
                        <span class="tc-rsz-mode-text"><b>White</b><span>Solid white</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="black">
                        <span class="tc-rsz-mode-text"><b>Black</b><span>Solid black</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="custom">
                        <span class="tc-rsz-mode-text"><b>Custom</b><span>Pick color</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-svg-custom-color-section" style="display:none">
                <h4 class="tc-rsz-heading">Background Color</h4>
                <div class="tc-premium-color-picker" data-picker="tc-svg-bg-color">
                    <label class="tc-pcp-swatch" for="tc-svg-bg-color"><span class="tc-pcp-swatch-fill" data-swatch="tc-svg-bg-color"></span></label>
                    <span class="tc-pcp-hex"></span>
                    <input type="color" class="tc-pcp-input" id="tc-svg-bg-color" value="#ffffff">
                    <div class="tc-pcp-swatches" data-palette="tc-svg-bg-color">
                        <button class="tc-pcp-csw" data-val="#ffffff" type="button"></button>
                        <button class="tc-pcp-csw" data-val="#0b1220" type="button"></button>
                        <button class="tc-pcp-csw" data-val="#f8fafc" type="button"></button>
                        <button class="tc-pcp-csw" data-val="#e11d48" type="button"></button>
                        <button class="tc-pcp-csw" data-val="#0ea5e9" type="button"></button>
                    </div>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-svg-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-svg-apply', 'Convert to PNG', 'tc-svg-download', 'Download PNG'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Source</span><span class="tc-stat-value" id="tc-svg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-svg-stat-out">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Dimensions</span><span class="tc-stat-value" id="tc-svg-stat-dims">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-svg-result" id="tc-svg-result">
            <p style="color:#64748b;padding:12px 0">PNG will appear here after you click Convert.</p>
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
                        <div><span>Original</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Output</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original SVG</button>
                            <button data-tab="result">PNG Output</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-svg-result-preview" id="tc-svg-preview-orig" style="display:flex;align-items:center;justify-content:center;min-height:200px;background:#0d1321;border-radius:8px;overflow:hidden">
                            <p style="color:#64748b">Upload an SVG to see preview</p>
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
