<?php
/**
 * Widget: Image Resizer
 * Resize JPG, PNG, WebP, GIF images by pixels or percentage.
 * 100% client-side using canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Resize_Image extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'resize_image'; }
    public function get_title(): string { return 'Image Resizer'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['resize image', 'image resizer', 'resize jpg', 'resize png', 'change image size', 'image dimensions', 'scale image'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Resize JPG, PNG, WebP or GIF images by exact pixels or percentage. Batch resize multiple images at once with ZIP download.
        </div>

        <?php $this->render_drop_zone('tc-rsz-drop', 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif', 'Drag & drop images here or click to browse'); ?>
        <?php $this->render_file_row('tc-rsz-file'); ?>

        <div class="tc-options-grid">
            <div class="tc-opt-group">
                <label class="tc-opt-label">Resize Mode</label>
                <div class="tc-modes" data-group="rsz-mode">
                    <button class="tc-btn tc-btn--ghost sel" type="button" data-val="pixels">By Pixels</button>
                    <button class="tc-btn tc-btn--ghost" type="button" data-val="percent">By Percentage</button>
                </div>
            </div>

            <div class="tc-opt-group" id="tc-rsz-pixels-opts">
                <label class="tc-opt-label">Dimensions (px)</label>
                <div class="tc-dim-row">
                    <div class="tc-dim-field">
                        <span class="tc-dim-pre">W</span>
                        <input type="number" class="tc-input tc-input--sm" id="tc-rsz-width" placeholder="Width" min="1" max="10000">
                    </div>
                    <button class="tc-lock-btn" id="tc-rsz-lock" type="button" title="Lock aspect ratio">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </button>
                    <div class="tc-dim-field">
                        <span class="tc-dim-pre">H</span>
                        <input type="number" class="tc-input tc-input--sm" id="tc-rsz-height" placeholder="Height" min="1" max="10000">
                    </div>
                </div>
            </div>

            <div class="tc-opt-group tc-hide" id="tc-rsz-percent-opts">
                <label class="tc-opt-label">Scale</label>
                <div class="tc-presets" id="tc-rsz-presets">
                    <button class="tc-btn tc-btn--ghost" type="button" data-val="25">25%</button>
                    <button class="tc-btn tc-btn--ghost" type="button" data-val="50">50%</button>
                    <button class="tc-btn tc-btn--ghost" type="button" data-val="75">75%</button>
                    <button class="tc-btn tc-btn--ghost" type="button" data-val="150">150%</button>
                    <button class="tc-btn tc-btn--ghost" type="button" data-val="200">200%</button>
                </div>
                <div class="tc-dim-row" style="margin-top:10px">
                    <div class="tc-dim-field" style="flex:1">
                        <input type="number" class="tc-input tc-input--sm" id="tc-rsz-percent" placeholder="100" min="1" max="500" value="100">
                        <span class="tc-dim-suf">%</span>
                    </div>
                </div>
            </div>

            <div class="tc-opt-group">
                <label class="tc-opt-label">Output Format</label>
                <div class="tc-modes" data-group="rsz-format">
                    <button class="tc-btn tc-btn--ghost sel" type="button" data-val="original">Same as Original</button>
                    <button class="tc-btn tc-btn--ghost" type="button" data-val="image/jpeg">JPG</button>
                    <button class="tc-btn tc-btn--ghost" type="button" data-val="image/png">PNG</button>
                    <button class="tc-btn tc-btn--ghost" type="button" data-val="image/webp">WebP</button>
                </div>
            </div>

            <div class="tc-opt-group tc-hide" id="tc-rsz-quality-wrap">
                <label class="tc-opt-label">Quality</label>
                <input type="range" class="tc-range" id="tc-rsz-quality" min="10" max="100" value="92">
                <span class="tc-range-val" id="tc-rsz-quality-val">92%</span>
            </div>

            <div class="tc-check-row">
                <label class="tc-check">
                    <input type="checkbox" class="tc-check-input" id="tc-rsz-lock-ratio" checked>
                    <span class="tc-check-box"></span>
                    Maintain aspect ratio
                </label>
                <label class="tc-check">
                    <input type="checkbox" class="tc-check-input" id="tc-rsz-no-enlarge" checked>
                    <span class="tc-check-box"></span>
                    Do not enlarge if smaller
                </label>
            </div>
        </div>

        <?php $this->render_progress_bar('tc-rsz-progress', 'Resizing...'); ?>

        <?php $this->render_actions('tc-rsz-resize', 'Resize Image', 'tc-rsz-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-rsz-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Resized</span><span class="tc-stat-value" id="tc-rsz-stat-resized">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Dimensions</span><span class="tc-stat-value" id="tc-rsz-stat-dims">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
