<?php
/**
 * Widget: Speech to Text
 * Convert speech to text using the browser's Web Speech Recognition API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Speech_To_Text extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'speech_to_text'; }
    public function get_title(): string { return 'Speech to Text'; }
    public function get_icon(): string { return 'eicon-microphone'; }

    public function get_keywords(): array {
        return ['speech to text', 'voice to text', 'dictation', 'voice typing', 'transcribe audio', 'online speech recognition'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert your voice to text instantly. Click the microphone, speak, and watch your words appear in real-time. Works in Chrome, Edge, and Safari.
        </div>

        <div class="tc-stt-controls">
            <button class="tc-stt-mic-btn" id="tc-stt-mic" type="button">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                    <line x1="12" y1="19" x2="12" y2="23"/>
                    <line x1="8" y1="23" x2="16" y2="23"/>
                </svg>
            </button>
            <div class="tc-stt-status" id="tc-stt-status">Click the microphone to start</div>
            <div class="tc-stt-lang-wrap">
                <select class="tc-rsz-select" id="tc-stt-lang">
                    <option value="en-US">English (US)</option>
                    <option value="en-GB">English (UK)</option>
                    <option value="es-ES">Spanish</option>
                    <option value="fr-FR">French</option>
                    <option value="de-DE">German</option>
                    <option value="it-IT">Italian</option>
                    <option value="pt-BR">Portuguese (BR)</option>
                    <option value="ja-JP">Japanese</option>
                    <option value="ko-KR">Korean</option>
                    <option value="zh-CN">Chinese (Simplified)</option>
                    <option value="hi-IN">Hindi</option>
                    <option value="ar-SA">Arabic</option>
                    <option value="ru-RU">Russian</option>
                    <option value="nl-NL">Dutch</option>
                    <option value="sv-SE">Swedish</option>
                </select>
            </div>
        </div>

        <div class="tc-stt-toggle-row">
            <label class="tc-rsz-toggle">
                <input type="checkbox" class="tc-rsz-toggle-input" id="tc-stt-continuous" checked>
                <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                <span class="tc-rsz-toggle-text"><b>Continuous (don't stop after pause)</b></span>
            </label>
            <label class="tc-rsz-toggle">
                <input type="checkbox" class="tc-rsz-toggle-input" id="tc-stt-punct">
                <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                <span class="tc-rsz-toggle-text"><b>Auto punctuation</b></span>
            </label>
        </div>

        <textarea class="tc-textarea" id="tc-stt-output" placeholder="Your speech will appear here..." rows="8"></textarea>

        <div class="tc-stt-actions">
            <button class="tc-btn tc-btn--ghost" id="tc-stt-copy" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Copy Text
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-stt-clear" type="button">Clear</button>
            <button class="tc-btn tc-btn--ghost" id="tc-stt-download" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download .txt
            </button>
        </div>

        <div class="tc-stt-stat-cards">
            <div class="tc-stt-stat-card">
                <span class="tc-stt-stat-num" id="tc-stt-stat-words">0</span>
                <span class="tc-stt-stat-label">Words</span>
            </div>
            <div class="tc-stt-stat-card">
                <span class="tc-stt-stat-num" id="tc-stt-stat-chars">0</span>
                <span class="tc-stt-stat-label">Characters</span>
            </div>
            <div class="tc-stt-stat-card">
                <span class="tc-stt-stat-num" id="tc-stt-stat-time">0:00</span>
                <span class="tc-stt-stat-label">Duration</span>
            </div>
        </div>

        <div class="tc-stt-supported" id="tc-stt-supported" style="display:none">
            <p>⚠️ <b>Speech Recognition is not supported in this browser.</b> Please use Chrome, Edge, or Safari for the best experience.</p>
        </div>

        <?php
    }

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 · Transcript</h3>
                    <span id="tc-status-chip">Ready</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Words</span><b id="tc-stat-orig">0</b></div>
                        <div><span>Characters</span><b id="tc-stat-comp">0</b></div>
                        <div class="saved"><span>Duration</span><b id="tc-stat-saved">0:00</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Transcript</h4>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <div id="tc-stt-preview" class="tc-stt-preview-box">
                            <p class="tc-stt-placeholder">Your transcript will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}
