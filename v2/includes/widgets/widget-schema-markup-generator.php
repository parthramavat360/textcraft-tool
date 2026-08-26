<?php
/**
 * Widget: Schema Markup Generator
 * JSON-LD structured data builder for SEO.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Schema_Markup_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'schema_markup_generator'; }
    public function get_title(): string { return 'Schema Markup Generator'; }
    public function get_icon(): string { return 'eicon-code'; }

    public function get_keywords(): array {
        return ['schema markup', 'json ld', 'structured data', 'schema generator', 'rich snippets', 'seo schema', 'json-ld generator'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate JSON-LD structured data for your website. Choose a schema type, fill in the fields, and copy the generated JSON-LD code. Helps search engines understand your content for rich snippets.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Schema Type</h4>
                <div class="tc-rsz-mode-cards tc-schema-type-cards">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="article">
                        <span class="tc-rsz-mode-text"><b>Article</b><span>Blog posts & news</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="product">
                        <span class="tc-rsz-mode-text"><b>Product</b><span>E-commerce items</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="local-business">
                        <span class="tc-rsz-mode-text"><b>Local Business</b><span>Physical businesses</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="faq">
                        <span class="tc-rsz-mode-text"><b>FAQ</b><span>FAQ page schema</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="howto">
                        <span class="tc-rsz-mode-text"><b>How-To</b><span>Step-by-step guides</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="event">
                        <span class="tc-rsz-mode-text"><b>Event</b><span>Events & concerts</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="recipe">
                        <span class="tc-rsz-mode-text"><b>Recipe</b><span>Cooking recipes</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="organization">
                        <span class="tc-rsz-mode-text"><b>Organization</b><span>Company info</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="breadcrumb">
                        <span class="tc-rsz-mode-text"><b>Breadcrumb</b><span>Navigation path</span></span>
                    </button>
                </div>
            </div>

            <div class="tc-rsz-section" id="tc-schema-fields-container">
                <!-- Fields rendered by JS -->
            </div>

        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div id="tc-schema-result">
            <p style="color:#64748b;padding:12px 0">Select a schema type and fill in the fields</p>
        </div>
        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Generated JSON-LD</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">JSON-LD</button>
                            <button data-tab="result">How to Use</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-schema-code-area">
                            <textarea class="tc-input tc-schema-code" id="tc-schema-code" readonly rows="18"></textarea>
                            <div class="tc-schema-code-actions">
                                <button class="tc-btn tc-btn--primary" id="tc-schema-copy" type="button">
                                    <i class="fa-regular fa-copy"></i> Copy JSON-LD
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div class="tc-schema-help">
                            <h4>How to Add to Your Website</h4>
                            <ol>
                                <li>Copy the generated JSON-LD code</li>
                                <li>Paste it inside the <code>&lt;head&gt;</code> section of your HTML</li>
                                <li>Use Google's <a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener">Rich Results Test</a> to validate</li>
                            </ol>
                            <h4>Example</h4>
                            <pre>&lt;script type="application/ld+json"&gt;
{
  "@context": "https://schema.org",
  "@type": "Article",
  ...
}
&lt;/script&gt;</pre>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
