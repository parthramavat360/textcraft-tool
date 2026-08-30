<?php
/**
 * Widget: GIF Compressor
 * Premium redesign — colors, resize, frame skip, loop, output name, clear all.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Gif_Compressor extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'gif_compressor'; }
    public function get_title(): string { return 'GIF Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['gif compressor', 'reduce gif size', 'compress gif', 'optimize gif'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress animated and static GIF images. Reduce colors, resize dimensions, and skip frames to shrink file size. If the result is larger than the original, we keep the original.
        </div>

        <?php $this->render_drop_zone('tc-gif-drop', 'image/gif,.gif', 'Drag & drop a GIF image here or click to browse'); ?>
        <?php $this->render_file_row('tc-gif-file'); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-gif-colors">
                    Colors: <span id="tc-gif-colors-val">64</span>
                </label>
                <input type="range" class="tc-range" id="tc-gif-colors" min="4" max="256" value="64" step="4">
                <p class="tc-lvl-hint">Fewer colors makes the GIF much smaller but reduces quality.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-gif-scale">
                    Resize: <span id="tc-gif-scale-val">100%</span>
                </label>
                <input type="range" class="tc-range" id="tc-gif-scale" min="25" max="100" value="100" step="5">
                <p class="tc-lvl-hint">Reduce the frame dimensions to shrink file size.</p>
            </div>
        </div>

        <div class="tc-input-group">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-gif-skip">
                    Frame Skip: <span id="tc-gif-skip-val">None</span>
                </label>
                <input type="range" class="tc-range" id="tc-gif-skip" min="0" max="5" value="0">
                <p class="tc-lvl-hint">Drop frames to reduce file size (may make animation choppier).</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-gif-loop" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" style="font-family:'Space Grotesk',system-ui,sans-serif">
                    <b>Loop</b>
                    <small>Play the animation on repeat.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-gif-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-gif-name" placeholder="my-animation">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-gif-progress', 'Compressing...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-gif-compress" type="button">Compress GIF</button>
            <button class="tc-btn tc-btn--ghost" id="tc-gif-download" type="button" style="display:none">Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-gif-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-gif-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-gif-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-gif-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}
}
