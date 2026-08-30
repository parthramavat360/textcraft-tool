<?php
/**
 * Widget: Reduce Image Size to KB
 * Premium redesign — target size, output format, max width, output name, clear all.
 * 100% client-side.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Reduce_Image_Kb extends TextCraft_Tool_Base {

    protected bool $show_preview = true;
    protected string $preview_orig_label = 'Original';
    protected string $preview_result_label = 'Reduced';
    protected array $result_stats = [
        ['Target', 'tc-kb-stat-target'],
        ['Original', 'tc-kb-stat-orig'],
        ['Reduced', 'tc-kb-stat-out'],
    ];

    protected bool $premium = true;

    public function get_name(): string { return 'reduce_image_kb'; }
    public function get_title(): string { return 'Reduce Image Size to KB'; }
    public function get_icon(): string { return 'eicon-shrink'; }

    public function get_keywords(): array {
        return ['reduce image size', 'compress image to kb', 'image to 100kb', 'image to 50kb', 'image to 200kb', 'reduce image to kb', 'compress image size', 'target file size', 'optimize image'];
    }

    protected function render_tool_content( array $settings ): void {
        ?>
        <div class="tc-tool-desc">
            Reduce any image to an exact target file size — 20 KB, 50 KB, 100 KB, 200 KB, 500 KB or a custom value.
            The quality is tuned automatically so your image lands right on target. Everything runs in your browser.
        </div>

        <?php $this->render_drop_zone( 'tc-kb-drop', 'image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp', 'Drag & drop an image here (JPG, PNG, WebP)' ); ?>
        <?php $this->render_file_row( 'tc-kb-file' ); ?>

        <div class="tc-input-group" style="margin-top:18px">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Target Size</label>
            <div class="tc-modes tc-modes--cards" data-group="kb-target" id="tc-kb-target-tabs">
                <button class="tc-btn tc-btn--ghost tc-kb-target sel" data-kb="50" type="button"><span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">50 KB</span></button>
                <button class="tc-btn tc-btn--ghost tc-kb-target" data-kb="100" type="button"><span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">100 KB</span></button>
                <button class="tc-btn tc-btn--ghost tc-kb-target" data-kb="200" type="button"><span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">200 KB</span></button>
                <button class="tc-btn tc-btn--ghost tc-kb-target" data-kb="500" type="button"><span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">500 KB</span></button>
                <button class="tc-btn tc-btn--ghost tc-kb-target tc-kb-custom" data-kb="0" type="button"><span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">Custom</span></button>
            </div>
            <div class="tc-kb-custom-row" id="tc-kb-custom-row" style="display:none;margin-top:10px">
                <input type="number" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-kb-custom" min="1" max="10000" value="150" placeholder="e.g. 150">
                <span class="tc-kb-unit">KB</span>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif">Output Format</label>
            <div class="tc-modes tc-modes--cards" data-group="kb-fmt" id="tc-kb-fmt-tabs">
                <button class="tc-btn tc-btn--ghost tc-kb-fmt sel" data-mime="image/jpeg" data-ext="jpg" type="button"><span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">JPG</span></button>
                <button class="tc-btn tc-btn--ghost tc-kb-fmt" data-mime="image/webp" data-ext="webp" type="button"><span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">WebP</span></button>
                <button class="tc-btn tc-btn--ghost tc-kb-fmt" data-mime="image/png" data-ext="png" type="button"><span class="tc-card-title" style="font-family:'Space Grotesk',system-ui,sans-serif">PNG</span></button>
            </div>
            <p class="tc-lvl-hint">Compressing to a target works best as <b>JPG</b> or <b>WebP</b>. PNG has a hard lower size floor and may not always reach small targets.</p>
        </div>

        <div class="tc-input-group">
            <div class="tc-range-wrap">
                <label class="tc-range-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-kb-maxw">
                    Max Width: <span id="tc-kb-maxw-val">Off</span>
                </label>
                <input type="range" class="tc-range" id="tc-kb-maxw" min="0" max="8192" value="0" step="64">
                <p class="tc-lvl-hint">Scale wide images down to this width (0 = no limit).</p>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" style="font-family:'Space Grotesk',system-ui,sans-serif" for="tc-kb-name">Output file name</label>
            <input type="text" class="tc-input" style="font-family:'Space Grotesk',system-ui,sans-serif" id="tc-kb-name" placeholder="my-image">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar( 'tc-kb-progress', 'Reducing image size...' ); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-kb-reduce" type="button">Reduce Size</button>
            <button class="tc-btn tc-btn--ghost" id="tc-kb-download" type="button" style="display:none" disabled>Download</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-kb-clear" type="button">Clear all</button>
        </div>

        <?php
    }

    protected function render_result_content( array $settings ): void {
        ?>
        <?php
    }
}
