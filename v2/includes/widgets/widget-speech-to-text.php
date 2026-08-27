<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Speech_To_Text extends TextCraft_Tool_Base {
    public function get_name(): string { return 'speech_to_text'; }
    public function get_title(): string { return 'Speech to Text'; }
    public function get_icon(): string { return 'eicon-microphone'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Convert your voice to text in real-time using your browser's Web Speech Recognition API. Speak naturally and watch your words appear instantly. Supports 50+ languages.</div>

        <div class="tc-flex" style="gap:12px;align-items:center;margin-bottom:16px;flex-wrap:wrap">
            <div class="tc-input-group" style="width:200px">
                <label class="tc-label">Language</label>
                <select class="tc-select" id="stt-lang">
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
                </select>
            </div>
            <div class="tc-checkboxes" style="padding-top:18px">
                <label class="tc-check"><input type="checkbox" id="stt-continuous" checked> Continuous</label>
                <label class="tc-check"><input type="checkbox" id="stt-interim" checked> Show live text</label>
            </div>
        </div>

        <div style="text-align:center;margin:20px 0">
            <button class="tc-btn tc-btn--primary tc-btn--lg" id="stt-start" style="border-radius:50px;padding:16px 40px;font-size:16px">
                <i class="fa-solid fa-microphone"></i> Start Listening
            </button>
        </div>

        <div id="stt-status" style="text-align:center;margin:10px 0;color:#94a3b8;font-size:14px"></div>

        <div class="tc-input-group">
            <label class="tc-label">Recognized Text</label>
            <textarea class="tc-input tc-textarea" id="stt-output" rows="10" readonly placeholder="Your speech will appear here as text..."></textarea>
        </div>

        <div class="tc-flex" style="gap:8px;margin-top:12px">
            <button class="tc-btn tc-btn--ghost" id="stt-copy"><i class="fa-regular fa-copy"></i> Copy Text</button>
            <button class="tc-btn tc-btn--ghost" id="stt-clear"><i class="fa-solid fa-trash"></i> Clear</button>
            <button class="tc-btn tc-btn--ghost" id="stt-download"><i class="fa-solid fa-download"></i> Download TXT</button>
        </div>
    <?php }
}
