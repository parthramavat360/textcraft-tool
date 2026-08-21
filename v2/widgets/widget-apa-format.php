<?php
/**
 * Widget: APA Format Generator
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Apa_Format extends TextCraft_Tool_Base {

    public function get_name(): string { return 'apa_format'; }
    public function get_title(): string { return 'APA Format Generator'; }
    public function get_icon(): string { return 'eicon-editor-heading'; }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate a properly formatted APA Style title page (7th edition). Fill in the fields below to create a ready-to-use title page.
        </div>

        <div class="tc-field">
            <label class="tc-label" for="tc-apa-title">Paper Title</label>
            <input type="text" class="tc-input" id="tc-apa-title" placeholder="Enter your paper title...">
        </div>

        <div class="tc-field">
            <label class="tc-label" for="tc-apa-author">Author Name(s)</label>
            <input type="text" class="tc-input" id="tc-apa-author" placeholder="First Last, First Last">
        </div>

        <div class="tc-field">
            <label class="tc-label" for="tc-apa-institution">Institution / Affiliation</label>
            <input type="text" class="tc-input" id="tc-apa-institution" placeholder="University or organization name">
        </div>

        <div class="tc-field">
            <label class="tc-label" for="tc-apa-course">Course</label>
            <input type="text" class="tc-input" id="tc-apa-course" placeholder="Course name and number">
        </div>

        <div class="tc-field">
            <label class="tc-label" for="tc-apa-instructor">Instructor</label>
            <input type="text" class="tc-input" id="tc-apa-instructor" placeholder="Instructor name">
        </div>

        <div class="tc-field">
            <label class="tc-label" for="tc-apa-date">Date</label>
            <input type="text" class="tc-input" id="tc-apa-date" placeholder="Month Day, Year">
        </div>

        <div class="tc-field">
            <label class="tc-label" for="tc-apa-running">Running Head (optional)</label>
            <input type="text" class="tc-input" id="tc-apa-running" placeholder="SHORTENED TITLE">
        </div>

        <?php $this->render_actions('tc-apa-generate', 'Generate APA Page', 'tc-apa-copy', 'Copy'); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-apa-result">
            <div class="tc-label">APA Title Page Preview</div>
            <div class="tc-apa-preview" id="tc-apa-preview"></div>
            <textarea class="tc-textarea" id="tc-apa-output" style="display:none" readonly></textarea>
        </div>
        <?php
    }
}
