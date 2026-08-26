<?php
/**
 * Widget: Markdown Table Generator
 * Visual table builder that outputs Markdown table syntax.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Markdown_Table_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'markdown_table_generator'; }
    public function get_title(): string { return 'Markdown Table Generator'; }
    public function get_icon(): string { return 'eicon-table'; }

    public function get_keywords(): array {
        return ['markdown table', 'table generator', 'md table', 'create table'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Build a Markdown table visually. Set columns and rows, then copy the generated Markdown syntax.
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <div class="tc-input-group">
                        <label class="tc-label"><b>Columns:</b></label>
                        <select class="tc-select" id="tc-mtg-cols" style="width:80px">
                            <option value="2">2</option>
                            <option value="3" selected>3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label"><b>Rows:</b></label>
                        <select class="tc-select" id="tc-mtg-rows" style="width:80px">
                            <option value="2">2</option>
                            <option value="3" selected>3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label"><b>Alignment:</b></label>
                        <select class="tc-select" id="tc-mtg-align" style="width:120px">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="tc-mtg-grid" id="tc-mtg-grid"></div>

        <div class="tc-mtg-actions">
            <button class="tc-btn tc-btn--accent" id="tc-mtg-generate" type="button">Generate Table</button>
            <button class="tc-btn tc-btn--ghost" id="tc-mtg-copy" type="button">Copy Markdown</button>
            <button class="tc-btn tc-btn--ghost" id="tc-mtg-download" type="button">Download .md</button>
        </div>

        <div class="tc-mtg-output-wrap">
            <h4 class="tc-mp-col-title">Generated Markdown</h4>
            <pre class="tc-cf-output" id="tc-mtg-output"><code>Markdown table will appear here</code></pre>
        </div>

        <?php
    }
}
