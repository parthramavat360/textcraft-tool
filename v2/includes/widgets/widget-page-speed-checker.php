<?php
/**
 * Widget: Page Speed Checker
 * Google PageSpeed Insights analyzer with scores and Core Web Vitals.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Page_Speed_Checker extends TextCraft_Tool_Base {

    protected bool $show_preview = true;

    public function get_name(): string { return 'page_speed_checker'; }
    public function get_title(): string { return 'Page Speed Checker'; }
    public function get_icon(): string { return 'eicon-speed'; }

    public function get_keywords(): array {
        return ['page speed', 'page speed checker', 'website speed test', 'page load time', 'core web vitals', 'lighthouse', 'performance checker', 'speed test'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Analyze any website for performance, accessibility, SEO, and best practices using Google PageSpeed Insights. Get Core Web Vitals scores and actionable recommendations. Free, no API key required.
        </div>

        <div class="tc-rsz-options">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Analyze Website</h4>
                <div class="tc-ps-input-row">
                    <div class="tc-input-group tc-ps-url-group">
                        <label class="tc-label">Enter URL</label>
                        <input type="url" class="tc-input tc-ps-url" id="tc-ps-url" value="" placeholder="https://example.com">
                    </div>
                    <div class="tc-input-group">
                        <label class="tc-label">Device</label>
                        <div class="tc-rsz-mode-cards tc-ps-strategy-cards">
                            <button class="tc-rsz-mode-card sel" type="button" data-val="mobile">
                                <span class="tc-rsz-mode-text"><b><i class="fa-solid fa-mobile-screen"></i> Mobile</b></span>
                            </button>
                            <button class="tc-rsz-mode-card" type="button" data-val="desktop">
                                <span class="tc-rsz-mode-text"><b><i class="fa-solid fa-desktop"></i> Desktop</b></span>
                            </button>
                        </div>
                    </div>
                </div>
                <button class="tc-btn tc-btn--primary tc-ps-analyze" id="tc-ps-analyze" type="button">
                    <i class="fa-solid fa-play"></i> Analyze
                </button>
            </div>

            <div class="tc-rsz-section tc-ps-loading-section" id="tc-ps-loading" style="display:none">
                <h4 class="tc-rsz-heading">Analyzing...</h4>
                <div class="tc-ps-loading-bar">
                    <div class="tc-ps-loading-progress" id="tc-ps-progress"></div>
                </div>
                <p class="tc-ps-loading-text" id="tc-ps-loading-text">Connecting to Google PageSpeed Insights...</p>
            </div>

        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div id="tc-ps-result">
            <p style="color:#64748b;padding:12px 0">Enter a URL and click Analyze to see results</p>
        </div>
        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Results</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Scores</button>
                            <button data-tab="result">Details</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div id="tc-ps-scores"></div>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <div id="tc-ps-details"></div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
