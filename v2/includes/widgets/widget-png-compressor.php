<?php
/**
 * Widget: PNG Compressor
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Png_Compressor extends TextCraft_Tool_Base {

    public function get_name(): string { return 'png_compressor'; }
    public function get_title(): string { return 'PNG Compressor'; }
    public function get_icon(): string { return 'eicon-image-bold'; }

    public function get_keywords(): array {
        return ['png compressor', 'reduce png size', 'compress png online', 'optimize png'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compress PNG images losslessly at full original resolution — no quality loss, transparency preserved. Optional downscale available below.
        </div>

        <?php $this->render_drop_zone('tc-png-drop', 'image/png,.png', 'Drag & drop PNG images here or click to browse'); ?>
        <?php $this->render_file_row('tc-png-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <label class="tc-label">Compression Level</label>
            <div class="tc-modes" data-group="png-level">
                <button class="tc-btn tc-btn--ghost" data-val="1" type="button">Light</button>
                <button class="tc-btn tc-btn--ghost sel" data-val="2" type="button">Balanced</button>
                <button class="tc-btn tc-btn--ghost" data-val="3" type="button">Strong</button>
            </div>
        </div>

        <div class="tc-checkboxes">
            <label class="tc-check"><input type="checkbox" id="tc-png-resize"> Resize to max 1200px</label>
        </div>

        <?php $this->render_progress_bar('tc-png-progress', 'Compressing...'); ?>

        <?php $this->render_actions('tc-png-compress', 'Compress', 'tc-png-download', 'Download'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-png-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Compressed</span><span class="tc-stat-value" id="tc-png-stat-comp">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-png-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-png-result">
            <div class="tc-preview" id="tc-png-preview">Upload a file to see preview</div>
        </div>
        <?php
    }
}