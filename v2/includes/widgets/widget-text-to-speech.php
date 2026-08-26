<?php
/**
 * Widget: Text to Speech
 * Convert text to speech using the browser's Web Speech API.
 * Choose voice, adjust pitch and rate, play/pause/stop.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Text_To_Speech extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'text_to_speech'; }
    public function get_title(): string { return 'Text to Speech'; }
    public function get_icon(): string { return 'eicon-microphone'; }

    public function get_keywords(): array {
        return ['text to speech', 'tts', 'text to voice', 'read text aloud', 'speech synthesizer', 'text reader', 'online tts'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert any text to natural-sounding speech instantly. Choose from dozens of voices, adjust speed and pitch, and listen right in your browser.
        </div>

        <textarea class="tc-textarea" id="tc-tts-input" placeholder="Type or paste the text you want to hear spoken aloud..." rows="6">Hello! This is a text to speech demo. You can change the voice, speed, and pitch to customize how it sounds.</textarea>

        <div class="tc-rsz-options" style="margin-top:16px">

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Voice</h4>
                <select class="tc-rsz-select" id="tc-tts-voice" style="width:100%">
                    <option value="" disabled selected>Loading voices...</option>
                </select>
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Speed: <span id="tc-tts-rate-val">1.0×</span></h4>
                <input type="range" class="tc-rsz-slider" id="tc-tts-rate" min="0.5" max="3" step="0.1" value="1" style="width:100%">
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Pitch: <span id="tc-tts-pitch-val">1.0</span></h4>
                <input type="range" class="tc-rsz-slider" id="tc-tts-pitch" min="0" max="2" step="0.1" value="1" style="width:100%">
            </div>

            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Volume: <span id="tc-tts-vol-val">100%</span></h4>
                <input type="range" class="tc-rsz-slider" id="tc-tts-vol" min="0" max="100" step="5" value="100" style="width:100%">
            </div>

        </div>

        <div class="tc-tts-controls">
            <button class="tc-btn tc-btn--accent" id="tc-tts-play" type="button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                Play
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-tts-pause" type="button" disabled>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                Pause
            </button>
            <button class="tc-btn tc-btn--ghost" id="tc-tts-stop" type="button" disabled>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/></svg>
                Stop
            </button>
        </div>

        <div class="tc-tts-status" id="tc-tts-status"></div>
        <?php
    }
}
