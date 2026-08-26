<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Keyword_Density_Checker extends TextCraft_Tool_Base {
    protected bool $show_preview = false;
    public function get_name(): string { return 'keyword_density_checker'; }
    public function get_title(): string { return 'Keyword Density Checker'; }
    public function get_icon(): string { return 'eicon-chart-bar'; }
    public function get_keywords(): array { return ['keyword density','seo','keyword checker','density checker','keyword analysis','word frequency']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Analyze your content for keyword density, find top keywords, bigrams, and trigrams. Optimize your SEO with real-time density analysis.</div>

        <div class="tctp-tool-body">
            <div class="tctp-ctrls">
                <div class="tc-input-group" style="flex:1;">
                    <label class="tc-label">Paste or type your article text</label>
                    <textarea class="tctp-input tctp-textarea" id="kdc-text" rows="12" placeholder="Paste your article, blog post, or content here to analyze keyword density..."></textarea>
                </div>
                <div class="tctp-flex" style="gap:8px; align-items:center;">
                    <div class="tc-input-group" style="width:140px;">
                        <label class="tc-label">Min word length</label>
                        <select class="tc-select" id="kdc-min-length">
                            <option value="3">3+ letters</option>
                            <option value="4" selected>4+ letters</option>
                            <option value="5">5+ letters</option>
                            <option value="6">6+ letters</option>
                        </select>
                    </div>
                    <div class="tc-input-group" style="width:160px;">
                        <label class="tc-label">Stop words filter</label>
                        <select class="tc-select" id="kdc-stopwords">
                            <option value="remove" selected>Remove stop words</option>
                            <option value="keep">Keep stop words</option>
                        </select>
                    </div>
                </div>
            </div>

            <button class="tc-btn tc-btn--primary" id="kdc-analyze" disabled>
                <i class="fa-solid fa-chart-bar"></i> Analyze Keywords
            </button>

            <?php $this->render_progress_bar('Analyzing keyword density...'); ?>

            <div class="tctp-result" id="kdc-result" style="display:none;">
                <div class="tctp-rsz-tabs">
                    <button class="tctp-rsz-tab sel" data-tab="summary">Summary</button>
                    <button class="tctp-rsz-tab" data-tab="single">Single Words</button>
                    <button class="tctp-rsz-tab" data-tab="phrases">Phrases</button>
                    <button class="tctp-rsz-tab" data-tab="details">Details</button>
                </div>

                <div class="tctp-rsz-tab-panel" id="kdc-summary">
                    <div class="tctp-kdc-stats" id="kdc-stats"></div>
                    <div class="tctp-kdc-density-bar" id="kdc-density-bar"></div>
                </div>
                <div class="tctp-rsz-tab-panel" id="kdc-single" style="display:none;">
                    <div class="tctp-kdc-table" id="kdc-single-table"></div>
                </div>
                <div class="tctp-rsz-tab-panel" id="kdc-phrases" style="display:none;">
                    <div class="tctp-kdc-phrase-section" id="kdc-phrases-container"></div>
                </div>
                <div class="tctp-rsz-tab-panel" id="kdc-details" style="display:none;">
                    <div class="tctp-kdc-table" id="kdc-details-table"></div>
                </div>

                <div style="margin-top:12px;">
                    <button class="tc-btn tc-btn--ghost tc-btn--sm" data-copy="kdc-result">
                        <i class="fa-regular fa-clipboard"></i> Copy Report
                    </button>
                </div>
            </div>
        </div>
    <?php }
}