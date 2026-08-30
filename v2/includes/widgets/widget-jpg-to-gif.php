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

    protected bool $premium = true;

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

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Color Palette</label>
            <div class="tc-modes" data-group="j2gif-colors" style="margin-top:8px">
                <button class="tc-btn tc-btn--ghost" data-val="16" type="button" style="font-family:'Space Grotesk',system-ui,sans-serif">16</button>
                <button class="tc-btn tc-btn--ghost" data-val="32" type="button" style="font-family:'Space Grotesk',system-ui,sans-serif">32</button>
                <button class="tc-btn tc-btn--ghost" data-val="64" type="button" style="font-family:'Space Grotesk',system-ui,sans-serif">64</button>
                <button class="tc-btn tc-btn--ghost sel" data-val="128" type="button" style="font-family:'Space Grotesk',system-ui,sans-serif">128</button>
                <button class="tc-btn tc-btn--ghost" data-val="256" type="button" style="font-family:'Space Grotesk',system-ui,sans-serif">256</button>
            </div>
            <p class="tc-lvl-hint">More colors = higher quality but larger file.</p>
        </div>

        <div class="tc-input-group">
            <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-j2gif-delay">
                Frame Delay: <span id="tc-j2gif-delay-val">200</span>ms
            </label>
            <div class="tc-range-wrap">
                <span class="tc-range-min">50</span>
                <input type="range" class="tc-range" id="tc-j2gif-delay" min="50" max="2000" value="200" step="50">
                <span class="tc-range-max">2000</span>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-j2gif-quality">
                Encoding Quality: <span id="tc-j2gif-quality-val">10</span>
            </label>
            <div class="tc-range-wrap">
                <span class="tc-range-min">1</span>
                <input type="range" class="tc-range" id="tc-j2gif-quality" min="1" max="100" value="10">
                <span class="tc-range-max">100</span>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-j2gif-ios" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>iOS-Compatible Downscale</b>
                    <small>Auto-downscale large images (4096px max) for iOS compatibility.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-j2gif-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-j2gif-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-j2gif-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-j2gif-convert" type="button">Convert to GIF</button>
            <button class="tc-btn tc-btn--ghost" id="tc-j2gif-download" type="button" style="display:none">Download GIF</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-j2gif-clear" type="button">Clear all</button>
        </div>

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
