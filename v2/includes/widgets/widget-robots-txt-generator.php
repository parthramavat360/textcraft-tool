<?php
/**
 * Widget: Robots.txt Generator
 * Build robots.txt with a visual UI.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Robots_Txt_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'robots_txt_generator'; }
    public function get_title(): string { return 'Robots.txt Generator'; }
    public function get_icon(): string { return 'eicon-file'; }

    public function get_keywords(): array {
        return ['robots.txt generator', 'robots.txt', 'seo robots', 'crawl rules', 'sitemap', 'robots txt'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate a robots.txt file with a visual editor. Add crawl rules for different user agents, set allow/disallow paths, and add your sitemap URL. Copy the generated file content instantly.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Default Settings</h4>
                <div class="tc-rt-fields">
                    <div class="tc-input-group">
                        <label class="tc-label">Sitemap URL</label>
                        <input type="url" class="tc-input" id="tc-rt-sitemap" value="" placeholder="https://example.com/sitemap.xml">
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Crawl Delay (seconds, optional)</label>
                        <input type="number" class="tc-input" id="tc-rt-delay" value="" min="1" max="60" placeholder="10">
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Presets</h4>
                <div class="tc-rsz-mode-cards tc-rt-preset-cards">
                    <button class="tc-rsz-mode-card sel" type="button" data-preset="allow-all">
                        <span class="tc-rsz-mode-text"><b>Allow All</b><span>Open to all crawlers</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-preset="block-all">
                        <span class="tc-rsz-mode-text"><b>Block All</b><span>Block all crawlers</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-preset="block-ai">
                        <span class="tc-rsz-mode-text"><b>Block AI</b><span>Block AI crawlers</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-preset="standard">
                        <span class="tc-rsz-mode-text"><b>Standard</b><span>Common rules</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">User Agent Rules</h4>
                <div class="tc-rt-rules" id="tc-rt-rules"></div>
                <button class="tc-btn tc-btn--primary" id="tc-rt-add-rule" type="button"><i class="fa-solid fa-plus"></i> Add Rule</button>
            </div>

        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div id="tc-rt-result">
            <p style="color:#64748b;padding:12px 0">Configure rules to generate robots.txt</p>
        </div>
        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Generated robots.txt</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Preview</button>
                            <button data-tab="result">Text Output</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-rt-preview" id="tc-rt-preview"></div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div class="tc-rt-code-area">
                            <textarea class="tc-input tc-rt-code-output" id="tc-rt-code" readonly rows="14"></textarea>
                            <div class="tc-rt-code-actions">
                                <button class="tc-btn tc-btn--primary" id="tc-rt-copy" type="button">
                                    <i class="fa-regular fa-copy"></i> Copy robots.txt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
