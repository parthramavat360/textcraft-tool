<?php
/**
 * Widget: Image to Text (OCR)
 * Premium design with language selector, preprocess toggle, stats.
 * Overridden result panel with OCR-specific labels.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Image_To_Text extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    protected bool $premium = true;

    public function get_name(): string { return 'image_to_text'; }
    public function get_title(): string { return 'Image to Text (OCR)'; }
    public function get_icon(): string { return 'eicon-document'; }

    public function get_keywords(): array {
        return ['ocr', 'image to text', 'extract text', 'read text from image', 'text recognition'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Extract text from images using Tesseract.js OCR engine. Supports 12+ languages and works entirely in your browser &mdash; your images are never uploaded. Smart formatting cleans and structures extracted text automatically.
        </div>

        <?php $this->render_drop_zone('tc-ocr-drop', 'image/*', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-ocr-file'); ?>

        <div class="tc-input-group" >
            <label class="tc-label" >OCR Language</label>
            <div class="tc-modes" data-group="ocr-lang">
                <button class="tc-btn tc-btn--ghost sel" data-val="eng" type="button">English</button>
                <button class="tc-btn tc-btn--ghost" data-val="spa" type="button">Spanish</button>
                <button class="tc-btn tc-btn--ghost" data-val="fra" type="button">French</button>
                <button class="tc-btn tc-btn--ghost" data-val="deu" type="button">German</button>
                <button class="tc-btn tc-btn--ghost" data-val="ita" type="button">Italian</button>
                <button class="tc-btn tc-btn--ghost" data-val="por" type="button">Portuguese</button>
                <button class="tc-btn tc-btn--ghost" data-val="jpn" type="button">Japanese</button>
                <button class="tc-btn tc-btn--ghost" data-val="chi_sim" type="button">Chinese</button>
                <button class="tc-btn tc-btn--ghost" data-val="kor" type="button">Korean</button>
                <button class="tc-btn tc-btn--ghost" data-val="ara" type="button">Arabic</button>
                <button class="tc-btn tc-btn--ghost" data-val="hin" type="button">Hindi</button>
                <button class="tc-btn tc-btn--ghost" data-val="rus" type="button">Russian</button>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-ocr-preprocess" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>Image Preprocessing</b>
                    <small>Auto-enhance contrast &amp; sharpen for better accuracy.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-premium-opt">
                <input type="checkbox" class="tc-switch-input" id="tc-ocr-cleanup" checked>
                <span class="tc-switch" aria-hidden="true"></span>
                <span class="tc-opt-text" >
                    <b>Text Cleanup</b>
                    <small>Fix broken words, join sentences, clean whitespace.</small>
                </span>
            </label>
        </div>

        <div class="tc-input-group">
            <label class="tc-label" >Output Format</label>
            <div class="tc-modes" data-group="ocr-output">
                <button class="tc-btn tc-btn--ghost sel" data-val="text" type="button">Plain Text</button>
                <button class="tc-btn tc-btn--ghost" data-val="hocr" type="button">hOCR (Structured)</button>
            </div>
        </div>

        <div class="tc-input-group">
            <label class="tc-label"  for="tc-ocr-name">Output file name</label>
            <input type="text" class="tc-input"  id="tc-ocr-name" placeholder="my-text">
            <p class="tc-lvl-hint">Leave empty to use your source file name.</p>
        </div>

        <?php $this->render_progress_bar('tc-ocr-progress', 'Recognizing text...'); ?>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-ocr-extract" type="button">Extract Text</button>
            <button class="tc-btn tc-btn--ghost" id="tc-ocr-download" type="button" style="display:none">Download .txt</button>
            <button class="tc-btn tc-btn--ghost tc-btn--clear" id="tc-ocr-clear" type="button">Clear all</button>
        </div>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-ocr-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Words</span><span class="tc-stat-value" id="tc-ocr-stat-words">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Lines</span><span class="tc-stat-value" id="tc-ocr-stat-lines">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Confidence</span><span class="tc-stat-value" id="tc-ocr-stat-conf">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    /**
     * Override result panel with OCR-appropriate labels.
     */
    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Extracted Text</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Words</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Confidence</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original</button>
                            <button data-tab="result">Extracted Text</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">Original image will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">Extracted text will appear here</div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
