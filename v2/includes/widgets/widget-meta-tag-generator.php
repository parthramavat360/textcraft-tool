<?php
/**
 * Widget: Meta Tag Generator
 * SEO meta tag builder with Open Graph and Twitter Cards.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Meta_Tag_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'meta_tag_generator'; }
    public function get_title(): string { return 'Meta Tag Generator'; }
    public function get_icon(): string { return 'eicon-code'; }

    public function get_keywords(): array {
        return ['meta tag generator', 'seo meta tags', 'meta tags', 'open graph', 'twitter card', 'meta description', 'html meta tags'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Generate HTML meta tags for SEO, Open Graph, and Twitter Cards. Fill in the fields below and copy the generated HTML code. Optimized for social media sharing and search engines.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Basic Meta Tags</h4>
                <div class="tc-mtg-fields">
                    <div class="tc-input-group">
                        <label class="tc-label">Page Title (50-60 chars ideal)</label>
                        <input type="text" class="tc-input" id="tc-mtg-title" value="" placeholder="My Page Title">
                        <span class="tc-mtg-count" id="tc-mtg-title-count">0 chars</span>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Meta Description (150-160 chars ideal)</label>
                        <textarea class="tc-input" id="tc-mtg-description" rows="3" placeholder="A compelling description of your page content"></textarea>
                        <span class="tc-mtg-count" id="tc-mtg-desc-count">0 chars</span>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Keywords (comma separated)</label>
                        <input type="text" class="tc-input" id="tc-mtg-keywords" value="" placeholder="keyword1, keyword2, keyword3">
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Page URL</label>
                        <input type="url" class="tc-input" id="tc-mtg-url" value="" placeholder="https://example.com/page">
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Author</label>
                        <input type="text" class="tc-input" id="tc-mtg-author" value="" placeholder="Author Name">
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Open Graph (Facebook/LinkedIn)</h4>
                <div class="tc-mtg-fields">
                    <div class="tc-input-group">
                        <label class="tc-label">OG Type</label>
                        <div class="tc-rsz-mode-cards tc-mtg-type-cards">
                            <button class="tc-rsz-mode-card sel" type="button" data-val="website"><span class="tc-rsz-mode-text"><b>website</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="article"><span class="tc-rsz-mode-text"><b>article</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="product"><span class="tc-rsz-mode-text"><b>product</b></span></button>
                        </div>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">OG Image URL (1200x630 recommended)</label>
                        <input type="url" class="tc-input" id="tc-mtg-image" value="" placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Site Name</label>
                        <input type="text" class="tc-input" id="tc-mtg-site-name" value="" placeholder="My Website">
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Twitter Card</h4>
                <div class="tc-mtg-fields">
                    <div class="tc-input-group">
                        <label class="tc-label">Card Type</label>
                        <div class="tc-rsz-mode-cards tc-mtg-card-cards">
                            <button class="tc-rsz-mode-card sel" type="button" data-val="summary_large_image"><span class="tc-rsz-mode-text"><b>Summary Large</b><span>1200x630 image</span></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="summary"><span class="tc-rsz-mode-text"><b>Summary</b><span>144x144 image</span></span></button>
                        </div>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Twitter Handle (@username)</label>
                        <input type="text" class="tc-input" id="tc-mtg-twitter" value="" placeholder="@username">
                    </div>
                </div>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Additional Settings</h4>
                <div class="tc-mtg-fields">
                    <div class="tc-input-group">
                        <label class="tc-label">Robots</label>
                        <div class="tc-rsz-mode-cards tc-mtg-robots-cards">
                            <button class="tc-rsz-mode-card sel" type="button" data-val="index, follow"><span class="tc-rsz-mode-text"><b>index, follow</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="noindex, nofollow"><span class="tc-rsz-mode-text"><b>noindex</b></span></button>
                            <button class="tc-rsz-mode-card" type="button" data-val="index, nofollow"><span class="tc-rsz-mode-text"><b>index, nofollow</b></span></button>
                        </div>
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Canonical URL</label>
                        <input type="url" class="tc-input" id="tc-mtg-canonical" value="" placeholder="https://example.com/canonical-page">
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Theme Color</label>
                        <div class="tc-premium-color-picker" data-picker="tc-mtg-theme-color">
                            <label class="tc-pcp-swatch" for="tc-mtg-theme-color"><span class="tc-pcp-swatch-fill" data-swatch="tc-mtg-theme-color"></span></label>
                            <span class="tc-pcp-hex"></span>
                            <input type="color" class="tc-pcp-input" id="tc-mtg-theme-color" value="#0b1220">
                            <div class="tc-pcp-swatches" data-palette="tc-mtg-theme-color">
                                <button class="tc-pcp-csw" data-val="#0b1220" type="button"></button>
                                <button class="tc-pcp-csw" data-val="#ffffff" type="button"></button>
                                <button class="tc-pcp-csw" data-val="#ff0000" type="button"></button>
                                <button class="tc-pcp-csw" data-val="#0ea5e9" type="button"></button>
                                <button class="tc-pcp-csw" data-val="#22c55e" type="button"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div id="tc-mtg-result">
            <p style="color:#64748b;padding:12px 0">Fill in the fields to generate meta tags</p>
        </div>
        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Generated HTML</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Preview</button>
                            <button data-tab="result">HTML Code</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div class="tc-mtg-preview" id="tc-mtg-preview"></div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div class="tc-mtg-code-area">
                            <textarea class="tc-input tc-mtg-code-output" id="tc-mtg-code" readonly rows="16"></textarea>
                            <div class="tc-mtg-code-actions">
                                <button class="tc-btn tc-btn--primary" id="tc-mtg-copy" type="button">
                                    <i class="fa-regular fa-copy"></i> Copy HTML
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
