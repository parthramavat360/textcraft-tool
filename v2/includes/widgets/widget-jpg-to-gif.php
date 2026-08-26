<?php
/**
 * Widget: JPG to GIF Converter
 * Premium design with color cards, delay, quality, iOS downscale, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Jpg_To_Gif extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'jpg_to_gif'; }
    public function get_title(): string { return 'JPG to GIF Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['jpg to gif', 'convert jpg to gif', 'jpeg to gif', 'gif converter', 'image to gif'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert JPG/JPEG images to GIF format. Choose color palette for quality vs file size, adjust frame delay and encoding quality.
        </div>

        <?php $this->render_drop_zone('tc-j2gif-drop', 'image/jpeg,.jpg,.jpeg', 'Drag & drop JPG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-j2gif-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Color Palette</h4>
                <div class="tc-modes" data-group="j2gif-colors">
                    <button class="tc-btn tc-btn--ghost" data-val="16" type="button">16</button>
                    <button class="tc-btn tc-btn--ghost" data-val="32" type="button">32</button>
                    <button class="tc-btn tc-btn--ghost" data-val="64" type="button">64</button>
                    <button class="tc-btn tc-btn--ghost sel" data-val="128" type="button">128</button>
                    <button class="tc-btn tc-btn--ghost" data-val="256" type="button">256</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Frame Delay <span class="tc-rsz-quality-badge" id="tc-j2gif-delay-val">200</span>ms</h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">50</span>
                    <input type="range" class="tc-rsz-slider" id="tc-j2gif-delay" min="50" max="2000" value="200" step="50">
                    <span class="tc-rsz-slider-max">2000</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Encoding Quality <span class="tc-rsz-quality-badge" id="tc-j2gif-quality-val">10</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">1</span>
                    <input type="range" class="tc-rsz-slider" id="tc-j2gif-quality" min="1" max="100" value="10">
                    <span class="tc-rsz-slider-max">100</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Options</h4>
                <div class="tc-rsz-slider-wrap">
                    <label class="tc-rsz-toggle">
                        <input type="checkbox" class="tc-rsz-toggle-input" id="tc-j2gif-ios" checked>
                        <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    </label>
                    <span style="font-size:12px;opacity:0.6">Auto-downscale large images on iOS (4096px max)</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-j2gif-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-j2gif-convert', 'Convert to GIF', 'tc-j2gif-download', 'Download GIF'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (JPG)</span><span class="tc-stat-value" id="tc-j2gif-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (GIF)</span><span class="tc-stat-value" id="tc-j2gif-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-j2gif-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Converted GIF</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original (JPG)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (GIF)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original JPG</button>
                            <button data-tab="result">Converted GIF</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original JPG will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted GIF will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
