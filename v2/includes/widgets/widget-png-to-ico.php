<?php
/**
 * Widget: PNG to ICO (Favicon Generator)
 * Convert any image to ICO favicon with multiple sizes.
 * 100% client-side using canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_To_Ico extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'png_to_ico'; }
    public function get_title(): string { return 'PNG to ICO'; }
    public function get_icon(): string { return 'eicon-favorite'; }

    public function get_keywords(): array {
        return ['png to ico', 'favicon generator', 'create favicon', 'ico converter', 'icon generator', 'favicon maker'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert any PNG, JPG, or WebP image to a multi-size ICO favicon file. Includes 16×16, 32×32, 48×48, 64×64, 128×128 and 256×256 sizes. All processing happens in your browser — no upload needed.
        </div>

        <?php $this->render_drop_zone('tc-ico-drop', 'image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png,.webp,.gif', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-ico-file'); ?>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section" id="tc-ico-preview-section" style="display:none">
                <h4 class="tc-rsz-heading">Preview</h4>
                <div class="tc-ico-preview-wrap" id="tc-ico-preview-wrap">
                    <canvas id="tc-ico-canvas"></canvas>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Favicon Sizes</h4>
                <div class="tc-ico-sizes-grid">
                    <label class="tc-rsz-toggle"><input type="checkbox" class="tc-rsz-toggle-input tc-ico-size-cb" value="16" checked><span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span><span class="tc-rsz-toggle-text"><b>16×16</b> Small icon</span></label>
                    <label class="tc-rsz-toggle"><input type="checkbox" class="tc-rsz-toggle-input tc-ico-size-cb" value="32" checked><span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span><span class="tc-rsz-toggle-text"><b>32×32</b> Standard</span></label>
                    <label class="tc-rsz-toggle"><input type="checkbox" class="tc-rsz-toggle-input tc-ico-size-cb" value="48" checked><span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span><span class="tc-rsz-toggle-text"><b>48×48</b> Windows large</span></label>
                    <label class="tc-rsz-toggle"><input type="checkbox" class="tc-rsz-toggle-input tc-ico-size-cb" value="64" checked><span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span><span class="tc-rsz-toggle-text"><b>64×64</b> High DPI</span></label>
                    <label class="tc-rsz-toggle"><input type="checkbox" class="tc-rsz-toggle-input tc-ico-size-cb" value="128"><span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span><span class="tc-rsz-toggle-text"><b>128×128</b> Chrome Web Store</span></label>
                    <label class="tc-rsz-toggle"><input type="checkbox" class="tc-rsz-toggle-input tc-ico-size-cb" value="256"><span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span><span class="tc-rsz-toggle-text"><b>256×256</b> Application icon</span></label>
                </div>
                <div class="tc-ico-preset-row">
                    <button class="tc-ico-preset-btn" type="button" data-action="all">Select All</button>
                    <button class="tc-ico-preset-btn" type="button" data-action="web">Web Only</button>
                    <button class="tc-ico-preset-btn" type="button" data-action="none">Deselect All</button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Preview Sizes</h4>
                <div class="tc-ico-result-sizes" id="tc-ico-result-sizes">
                    <p style="color:#64748b;font-size:13px">Click Generate to see previews</p>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar('tc-ico-progress', 'Generating ICO...'); ?>

        <?php $this->render_actions('tc-ico-apply', 'Generate ICO', 'tc-ico-download', 'Download ICO'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Source</span><span class="tc-stat-value" id="tc-ico-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Output</span><span class="tc-stat-value" id="tc-ico-stat-out">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Sizes</span><span class="tc-stat-value" id="tc-ico-stat-sizes">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-ico-result" id="tc-ico-result">
            <p style="color:#64748b;padding:12px 0">ICO file will be generated after you click Generate.</p>
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
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">ICO Sizes</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-ico-result-preview" id="tc-ico-preview-orig" style="display:flex;align-items:center;justify-content:center;min-height:200px;background:#0d1321;border-radius:8px;overflow:hidden">
                            <p style="color:#64748b">Upload an image to see preview</p>
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
