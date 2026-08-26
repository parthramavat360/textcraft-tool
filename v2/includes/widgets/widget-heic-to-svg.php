<?php
/**
 * Widget: HEIC to SVG Converter
 * Premium design with detail cards, color mode cards, paths slider, preview tabs.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Heic_To_Svg extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'heic_to_svg'; }
    public function get_title(): string { return 'HEIC to SVG Converter'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['heic to svg', 'convert heic to svg', 'heic svg converter', 'vectorize heic'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert Apple HEIC images to SVG vector format. Choose between pixel-perfect embed or vector tracing.
        </div>

        <?php $this->render_drop_zone('tc-h2s-drop', 'image/heic,.heic,.HEIC', 'Drag & drop HEIC images here or click to browse'); ?>
        <?php $this->render_file_row('tc-h2s-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Detail Level</h4>
                <div class="tc-rsz-mode-cards" data-group="h2s-detail">
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

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Color Mode</h4>
                <div class="tc-rsz-mode-cards" data-group="h2s-color">
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

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Max Paths <span class="tc-rsz-quality-badge" id="tc-h2s-paths-val">500</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">10</span>
                    <input type="range" class="tc-rsz-slider" id="tc-h2s-paths" min="10" max="2000" value="500" step="10">
                    <span class="tc-rsz-slider-max">2000</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-h2s-progress', 'Converting...'); ?>

        <?php $this->render_actions('tc-h2s-convert', 'Convert to SVG', 'tc-h2s-download', 'Download SVG'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original (HEIC)</span><span class="tc-stat-value" id="tc-h2s-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted (SVG)</span><span class="tc-stat-value" id="tc-h2s-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Format</span><span class="tc-stat-value" id="tc-h2s-stat-fmt">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with HEIC→SVG specific labels.
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
                        <div><span>Original (HEIC)</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted (SVG)</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Format</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original HEIC</button>
                            <button data-tab="result">Converted SVG</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original HEIC will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Converted SVG will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
