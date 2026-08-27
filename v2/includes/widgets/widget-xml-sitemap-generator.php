<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Xml_Sitemap_Generator extends TextCraft_Tool_Base {
    public function get_name(): string { return 'xml_sitemap_generator'; }
    public function get_title(): string { return 'XML Sitemap Generator'; }
    public function get_icon(): string { return 'eicon-sitemap'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Generate a valid XML sitemap for your website. Add your site URL or build sitemap content from text, and download the sitemap.xml ready to upload to your server.</div>

        <div class="tc-input-group">
            <label class="tc-label">Base Website URL</label>
            <input type="text" class="tc-input" id="sitemap-base" placeholder="https://yourwebsite.com">
        </div>

        <div class="tc-input-group">
            <label class="tc-label">URLs or Paths (one per line, or paste paths like /about, /contact)</label>
            <textarea class="tc-input tc-input--textarea" id="sitemap-urls" rows="6" placeholder="/&#10;/about&#10;/contact&#10;https://yourwebsite.com/blog/post-1&#10;/blog&#10;/products"></textarea>
        </div>

        <div class="tc-actions">
            <button class="tc-btn tc-btn--primary" id="sitemap-generate">Generate Sitemap</button>
            <button class="tc-btn tc-btn--outline" id="sitemap-download" disabled>Download sitemap.xml</button>
        </div>

        <div class="tc-result-panel" id="sitemap-result" style="display:none">
            <div class="tc-result-header">
                <span class="tc-status-chip" id="sitemap-status">Ready</span>
                <button class="tc-btn tc-btn--outline" id="sitemap-copy">Copy XML</button>
            </div>
            <div class="tc-result-body">
                <pre class="tctp-code-block" style="max-height:400px;overflow:auto"><code id="sitemap-xml"></code></pre>
            </div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
