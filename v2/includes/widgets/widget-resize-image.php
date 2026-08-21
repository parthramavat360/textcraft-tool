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

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Resize Mode</h4>
                <div class="tc-rsz-mode-cards">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="pixels">
                        <span class="tc-rsz-mode-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="3" y1="9" x2="21" y2="9"/></svg>
                        </span>
                        <span class="tc-rsz-mode-text">
                            <b>By Pixels</b>
                            <span>Set exact width &amp; height</span>
                        </span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="percent">
                        <span class="tc-rsz-mode-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M8 16l8-8"/><circle cx="8.5" cy="8.5" r="1" fill="currentColor"/><circle cx="15.5" cy="15.5" r="1" fill="currentColor"/></svg>
                        </span>
                        <span class="tc-rsz-mode-text">
                            <b>By Percentage</b>
                            <span>Scale up or down</span>
                        </span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-rsz-pixels-opts">
                <h4 class="tc-rsz-heading">Dimensions</h4>
                <div class="tc-rsz-dims">
                    <div class="tc-rsz-dim-field">
                        <label class="tc-rsz-dim-label">Width</label>
                        <div class="tc-rsz-dim-input">
                            <input type="number" class="tc-rsz-num" id="tc-rsz-width" placeholder="1920" min="1" max="10000">
                            <span class="tc-rsz-unit">px</span>
                        </div>
                    </div>
                    <button class="tc-rsz-lock tc-rsz-lock--on" id="tc-rsz-lock" type="button" title="Aspect ratio locked">
                        <svg class="tc-rsz-lock-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <svg class="tc-rsz-lock-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                    </button>
                    <div class="tc-rsz-dim-field">
                        <label class="tc-rsz-dim-label">Height</label>
                        <div class="tc-rsz-dim-input">
                            <input type="number" class="tc-rsz-num" id="tc-rsz-height" placeholder="1080" min="1" max="10000">
                            <span class="tc-rsz-unit">px</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section tc-hide" id="tc-rsz-percent-opts">
                <h4 class="tc-rsz-heading">Scale</h4>
                <div class="tc-rsz-scale-presets" id="tc-rsz-presets">
                    <button class="tc-rsz-preset" type="button" data-val="25">25%</button>
                    <button class="tc-rsz-preset" type="button" data-val="50">50%</button>
                    <button class="tc-rsz-preset sel" type="button" data-val="75">75%</button>
                    <button class="tc-rsz-preset" type="button" data-val="100">100%</button>
                    <button class="tc-rsz-preset" type="button" data-val="150">150%</button>
                    <button class="tc-rsz-preset" type="button" data-val="200">200%</button>
                </div>
                <div class="tc-rsz-custom-scale">
                    <input type="number" class="tc-rsz-num tc-rsz-num--sm" id="tc-rsz-percent" placeholder="100" min="1" max="500" value="100">
                    <span class="tc-rsz-unit">%</span>
                    <span class="tc-rsz-scale-hint">of original size</span>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Output Format</h4>
                <div class="tc-rsz-format-row">
                    <button class="tc-rsz-fmt sel" type="button" data-val="original">
                        <span class="tc-rsz-fmt-icon">&#9881;</span>
                        <span>Same</span>
                    </button>
                    <button class="tc-rsz-fmt" type="button" data-val="image/jpeg">
                        <span class="tc-rsz-fmt-icon">JPG</span>
                        <span>JPEG</span>
                    </button>
                    <button class="tc-rsz-fmt" type="button" data-val="image/png">
                        <span class="tc-rsz-fmt-icon">PNG</span>
                        <span>PNG</span>
                    </button>
                    <button class="tc-rsz-fmt" type="button" data-val="image/webp">
                        <span class="tc-rsz-fmt-icon">W</span>
                        <span>WebP</span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section tc-hide" id="tc-rsz-quality-wrap">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-rsz-quality-val">92%</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Low</span>
                    <input type="range" class="tc-rsz-slider" id="tc-rsz-quality" min="10" max="100" value="92">
                    <span class="tc-rsz-slider-max">Max</span>
                </div>
            </div>

            <div class="tc-rsz-toggles">
                <label class="tc-rsz-toggle">
                    <input type="checkbox" class="tc-rsz-toggle-input" id="tc-rsz-lock-ratio" checked>
                    <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    <span class="tc-rsz-toggle-text">
                        <b>Maintain aspect ratio</b>
                        <span>Width &amp; height scale proportionally</span>
                    </span>
                </label>
                <label class="tc-rsz-toggle">
                    <input type="checkbox" class="tc-rsz-toggle-input" id="tc-rsz-no-enlarge" checked>
                    <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    <span class="tc-rsz-toggle-text">
                        <b>Do not enlarge</b>
                        <span>Skip if already smaller than target</span>
                    </span>
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
