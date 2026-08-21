<?php
/**
 * Widget: Image to Text (OCR)
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Image_To_Text extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'image_to_text'; }
    public function get_title(): string { return 'Image to Text (OCR)'; }
    public function get_icon(): string { return 'eicon-document'; }

    public function get_keywords(): array {
        return ['ocr', 'image to text', 'extract text', 'read text from image', 'text recognition'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Extract text from images using Tesseract.js OCR engine. Supports multiple languages and works entirely in your browser.
        </div>

        <?php $this->render_drop_zone('tc-ocr-drop', 'image/*', 'Drag & drop an image here or click to browse'); ?>
        <?php $this->render_file_row('tc-ocr-file'); ?>

        <div class="tc-input-group" style="margin-top:16px">
            <label class="tc-label">OCR Language</label>
            <div class="tc-modes" data-group="ocr-lang">
                <button class="tc-btn tc-btn--ghost sel" data-val="eng" type="button">English</button>
                <button class="tc-btn tc-btn--ghost" data-val="spa" type="button">Spanish</button>
                <button class="tc-btn tc-btn--ghost" data-val="fra" type="button">French</button>
                <button class="tc-btn tc-btn--ghost" data-val="deu" type="button">German</button>
                <button class="tc-btn tc-btn--ghost" data-val="chi_sim" type="button">Chinese</button>
            </div>
        </div>

        <div class="tc-checkboxes">
            <?php $this->render_checkbox('tc-ocr-preprocess', 'Enhance contrast for better accuracy', true); ?>
        </div>

        <?php $this->render_progress_bar('tc-ocr-progress', 'Recognizing text...'); ?>

        <?php $this->render_actions('tc-ocr-extract', 'Extract Text', 'tc-ocr-copy', 'Copy'); ?>

        <?php $this->render_status('tc-ocr-status'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-ocr-result">
            <textarea class="tc-textarea" id="tc-ocr-output" placeholder="Extracted text will appear here..." readonly rows="12"></textarea>
        </div>
        <?php
    }
}
