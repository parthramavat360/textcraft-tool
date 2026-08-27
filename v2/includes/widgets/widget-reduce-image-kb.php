<?php
/**
 * Widget: Reduce Image Size to KB
 * Compress an image to an exact target file size (e.g. 20, 50, 100, 200KB)
 * by automatically adjusting the quality. 100% client-side.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Reduce_Image_Kb extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

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

        <div class="tc-kb-options">

            <!-- Target size -->
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Target Size</h4>
                <div class="tc-kb-targets" id="tc-kb-target-tabs">
                    <button class="tc-kb-target sel" type="button" data-kb="50">50 KB</button>
                    <button class="tc-kb-target" type="button" data-kb="100">100 KB</button>
                    <button class="tc-kb-target" type="button" data-kb="200">200 KB</button>
                    <button class="tc-kb-target" type="button" data-kb="500">500 KB</button>
                    <button class="tc-kb-target tc-kb-custom" type="button" data-kb="0">Custom</button>
                </div>
                <div class="tc-kb-custom-row" id="tc-kb-custom-row" style="display:none">
                    <input type="number" class="tc-kb-custom-input" id="tc-kb-custom" min="1" max="10000" value="150" placeholder="e.g. 150">
                    <span class="tc-kb-unit">KB</span>
                </div>
            </div>

            <!-- Output format -->
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Output Format</h4>
                <div class="tc-kb-fmts" id="tc-kb-fmt-tabs">
                    <button class="tc-kb-fmt sel" type="button" data-mime="image/jpeg" data-ext="jpg">JPG</button>
                    <button class="tc-kb-fmt" type="button" data-mime="image/webp" data-ext="webp">WebP</button>
                    <button class="tc-kb-fmt" type="button" data-mime="image/png" data-ext="png">PNG</button>
                </div>
                <p class="tc-kb-outnote">Compressing to a target works best as <b>JPG</b> or <b>WebP</b>. PNG has a hard lower size floor and may not always reach small targets.</p>
            </div>

            <!-- Max width -->
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Max Width (optional) <span class="tc-rsz-quality-badge" id="tc-kb-maxw-val">Off</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">No limit</span>
                    <input type="range" class="tc-rsz-slider" id="tc-kb-maxw" min="0" max="8192" value="0" step="64">
                    <span class="tc-rsz-slider-max">8192px</span>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar( 'tc-kb-progress', 'Reducing image size...' ); ?>

        <?php $this->render_actions( 'tc-kb-reduce', 'Reduce Size', 'tc-kb-download', 'Download' ); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Target</span><span class="tc-stat-value" id="tc-kb-stat-target">—</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-kb-stat-orig">—</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Reduced</span><span class="tc-stat-value" id="tc-kb-stat-out">—</span></div>
        </div>
        <?php
    }

    protected function render_result_content( array $settings ): void {
        ?>
        <div class="tc-kb-result" id="tc-kb-result">
            <div class="tc-kb-result-preview" id="tc-kb-result-preview">Your reduced image will appear here.</div>
            <div class="tc-kb-result-msg" id="tc-kb-result-msg"></div>
        </div>
        <?php
    }
}
