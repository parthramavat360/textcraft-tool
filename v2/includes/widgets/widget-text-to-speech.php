<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Text_To_Speech extends TextCraft_Tool_Base {
    public function get_name(): string { return 'text_to_speech'; }
    public function get_title(): string { return 'Text to Speech'; }
    public function get_icon(): string { return 'eicon-volume'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Convert any text to natural-sounding speech using your browser's built-in Web Speech API. Choose from multiple voices, adjust speed, pitch, and volume. Supports 50+ languages.</div>

        <div class="tc-input-group">
            <label class="tc-label">Enter text to speak</label>
            <textarea class="tc-input tc-textarea" id="tts-text" rows="8" placeholder="Type or paste the text you want converted to speech..."></textarea>
        </div>

        <div class="tc-grid-2col">
            <div class="tc-input-group">
                <label class="tc-label">Voice</label>
                <select class="tc-select" id="tts-voice">
                    <option value="">Loading voices...</option>
                </select>
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Language</label>
                <select class="tc-select" id="tts-lang">
                    <option value="en-US">English (US)</option>
                    <option value="en-GB">English (UK)</option>
                    <option value="es-ES">Spanish</option>
                    <option value="fr-FR">French</option>
                    <option value="de-DE">German</option>
                    <option value="it-IT">Italian</option>
                    <option value="pt-BR">Portuguese (Brazil)</option>
                    <option value="ja-JP">Japanese</option>
                    <option value="ko-KR">Korean</option>
                    <option value="zh-CN">Chinese (Simplified)</option>
                    <option value="hi-IN">Hindi</option>
                    <option value="ar-SA">Arabic</option>
                    <option value="ru-RU">Russian</option>
                    <option value="nl-NL">Dutch</option>
                    <option value="pl-PL">Polish</option>
                    <option value="sv-SE">Swedish</option>
                    <option value="tr-TR">Turkish</option>
                </select>
            </div>
        </div>

        <div class="tc-grid-3col">
            <div class="tc-input-group">
                <label class="tc-label">Speed: <strong id="tts-speed-val">1.0</strong>x</label>
                <input type="range" class="tc-range" id="tts-speed" min="0.25" max="3" step="0.25" value="1">
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Pitch: <strong id="tts-pitch-val">1.0</strong></label>
                <input type="range" class="tc-range" id="tts-pitch" min="0" max="2" step="0.1" value="1">
            </div>
            <div class="tc-input-group">
                <label class="tc-label">Volume: <strong id="tts-vol-val">100</strong>%</label>
                <input type="range" class="tc-range" id="tts-vol" min="0" max="100" step="5" value="100">
            </div>
        </div>

        <div class="tc-flex" style="gap:8px;flex-wrap:wrap">
            <button class="tc-btn tc-btn--primary" id="tts-play"><i class="fa-solid fa-play"></i> Speak</button>
            <button class="tc-btn tc-btn--ghost" id="tts-pause" disabled><i class="fa-solid fa-pause"></i> Pause</button>
            <button class="tc-btn tc-btn--ghost" id="tts-stop" disabled><i class="fa-solid fa-stop"></i> Stop</button>
            <button class="tc-btn tc-btn--ghost" id="tts-copy-text"><i class="fa-regular fa-copy"></i> Copy Text</button>
        </div>

        <div class="tctp-result" id="tts-result" style="display:none">
            <div id="tts-status"></div>
        </div>
    <?php }
}
