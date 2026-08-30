<?php
/**
 * Widget: PNG to SVG Converter
 * Premium design with detail cards, color mode cards, paths slider, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Svg extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'png_to_svg'; }
    public function get_title(): string { return 'PNG to SVG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['png to svg', 'convert png to svg', 'vectorize png', 'png svg converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert PNG raster images to scalable SVG vector graphics. Trace your images into resolution-independent vectors with full transparency support.
        </div>

        <?php $this->render_drop_zone('tc-p2svg-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-p2svg-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Detail Level</label>
            <div class="tc-rsz-mode-cards" data-group="p2svg-detail">
                <div class="tc-rsz-mode-card sel" data-val="high">
                    <div class="tc-rsz-mode-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                    </div>
                    <div class="tc-rsz-mode-text">
                        <b>High Detail</b>
                        <span>More paths, finer tracing</span>
                    </div>
                </div>
                <div class="tc-rsz-mode-card" data-val="medium">
                    <div class="tc-rsz-mode-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2v20M2 12h20"/></svg>
                    </div>
                    <div class="tc-rsz-mode-text">
                        <b>Medium Detail</b>
                        <span>Balanced quality and size</span>
                    </div>
                </div>
                <div class="tc-rsz-mode-card" data-val="low">
                    <div class="tc-rsz-mode-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="4" width="16" height="16" rx="2"/></svg>
                    </div>
                    <div class="tc-rsz-mode-text">
                        <b>Low Detail</b>
                        <span>Fewer paths, smaller file</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Color Mode</label>
            <div class="tc-rsz-mode-cards" data-group="p2svg-color">
                <div class="tc-rsz-mode-card sel" data-val="embed">
                    <div class="tc-rsz-mode-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    </div>
                    <div class="tc-rsz-mode-text">
                        <b>Embed Image</b>
                        <span>Pixel-perfect, clear output</span>
                    </div>
                </div>
                <div class="tc-rsz-mode-card" data-val="color">
                    <div class="tc-rsz-mode-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 0 20c-2.76 0-5-4.48-5-10S9.24 2 12 2z"/></svg>
                    </div>
                    <div class="tc-rsz-mode-text">
                        <b>Trace Color</b>
                        <span>Vector paths, all colors</span>
                    </div>
                </div>
                <div class="tc-rsz-mode-card" data-val="grayscale">
                    <div class="tc-rsz-mode-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 2v20" opacity=".4"/></svg>
                    </div>
                    <div class="tc-rsz-mode-text">
                        <b>Trace Gray</b>
                        <span>Vector, shades of gray</span>
                    </div>
                </div>
                <div class="tc-rsz-mode-card" data-val="bw">
                    <div class="tc-rsz-mode-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/></svg>
                    </div>
                    <div class="tc-rsz-mode-text">
                        <b>Trace B&W</b>
                        <span>Vector, two-color output</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="tc-input-group">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-p2svg-paths">
                    Max Paths: <span id="tc-p2svg-paths-val">500</span>
                </label>
                <input type="range" class="tc-range" id="tc-p2svg-paths" min="10" max="2000" value="500" step="10">
                <p class="tc-lvl-hint">Higher values keep more detail but increase file size.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-p2svg-transparency" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>Preserve Transparency</b>
                    <small>Keep transparent areas transparent in the output SVG.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-p2svg-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-p2svg-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-p2svg-progress', 'Converting...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-p2svg-convert" type="button">Convert to SVG</button>
            <button class="tc-btn tc-btn--ghost" id="tc-p2svg-download" type="button" style="display:none">Download SVG</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-p2svg-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (PNG)</span><span class="tc-stat-value" id="tc-p2svg-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (SVG)</span><span class="tc-stat-value" id="tc-p2svg-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Format</span><span class="tc-stat-value" id="tc-p2svg-stat-fmt">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with PNG→SVG specific labels.
     */
    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Converted SVG</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original (PNG)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (SVG)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Format</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original PNG</button>
                            <button data-tab="result">Converted SVG</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original PNG will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted SVG will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
