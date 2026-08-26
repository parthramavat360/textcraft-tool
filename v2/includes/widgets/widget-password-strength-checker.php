<?php
/**
 * Widget: Password Strength Checker
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Password_Strength_Checker extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'password_strength_checker'; }
    public function get_title(): string { return 'Password Strength Checker'; }
    public function get_icon(): string { return 'eicon-lock'; }

    public function get_keywords(): array {
        return ['password strength', 'password checker', 'password security', 'strong password', 'password test'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Test how strong your password is. Get instant analysis of entropy, crack time estimates, and tips to improve your password security. Everything runs in your browser — your password is never sent anywhere.
        </div>

        <div class="tc-input-group">
            <label class="tc-label">Enter Password to Check</label>
            <div style="position:relative">
                <input type="password" class="tc-input" id="tc-pwc-password" placeholder="Type or paste a password to test..." style="padding-right:44px">
                <button type="button" id="tc-pwc-toggle" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);font-size:18px" title="Show/Hide password">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>

        <div class="tc-pwc-strength-wrap" id="tc-pwc-strength-section" style="display:none">
            <div class="tc-pwc-meter">
                <div class="tc-pwc-meter-fill" id="tc-pwc-meter-fill"></div>
            </div>
            <div class="tc-pwc-strength-text" id="tc-pwc-strength-label"></div>
        </div>

        <div class="tc-pwc-stats" id="tc-pwc-stats" style="display:none">
            <div class="tc-pwc-stat">
                <span class="tc-pwc-stat-label">Score</span>
                <span class="tc-pwc-stat-value" id="tc-pwc-score">0/4</span>
            </div>
            <div class="tc-pwc-stat">
                <span class="tc-pwc-stat-label">Entropy</span>
                <span class="tc-pwc-stat-value" id="tc-pwc-entropy">0 bits</span>
            </div>
            <div class="tc-pwc-stat">
                <span class="tc-pwc-stat-label">Length</span>
                <span class="tc-pwc-stat-value" id="tc-pwc-length">0 chars</span>
            </div>
        </div>

        <div class="tc-pwc-details" id="tc-pwc-details" style="display:none">
            <div class="tc-pwc-detail-section">
                <h4 class="tc-pwc-detail-title">Crack Time Estimates</h4>
                <div class="tc-pwc-crack-grid">
                    <div class="tc-pwc-crack-item">
                        <span class="tc-pwc-crack-label">Online Attack (throttled)</span>
                        <span class="tc-pwc-crack-value" id="tc-pwc-online-slow">—</span>
                    </div>
                    <div class="tc-pwc-crack-item">
                        <span class="tc-pwc-crack-label">Online Attack (unthrottled)</span>
                        <span class="tc-pwc-crack-value" id="tc-pwc-online-fast">—</span>
                    </div>
                    <div class="tc-pwc-crack-item">
                        <span class="tc-pwc-crack-label">Offline (fast hash)</span>
                        <span class="tc-pwc-crack-value" id="tc-pwc-offline-fast">—</span>
                    </div>
                    <div class="tc-pwc-crack-item">
                        <span class="tc-pwc-crack-label">Offline (slow hash)</span>
                        <span class="tc-pwc-crack-value" id="tc-pwc-offline-slow">—</span>
                    </div>
                </div>
            </div>

            <div class="tc-pwc-detail-section">
                <h4 class="tc-pwc-detail-title">Composition Analysis</h4>
                <ul class="tc-pwc-checklist" id="tc-pwc-checklist"></ul>
            </div>

            <div class="tc-pwc-detail-section">
                <h4 class="tc-pwc-detail-title">Tips to Improve</h4>
                <ul class="tc-pwc-tips" id="tc-pwc-tips"></ul>
            </div>
        </div>

        <?php $this->render_actions('tc-pwc-check', 'Check Password', '', ''); ?>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-result-area" id="tc-pwc-result">
            <div class="tc-pwc-result-card" id="tc-pwc-result-card">
                <p style="color:var(--muted);font-size:14px;text-align:center">Enter a password above and click "Check Password" to see the analysis.</p>
            </div>
        </div>
        <?php
    }
}
