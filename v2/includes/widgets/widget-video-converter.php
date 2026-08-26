<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Video_Converter extends TextCraft_Tool_Base {
    protected bool $show_preview = true;
    public function get_name(): string { return 'video_converter'; }
    public function get_title(): string { return 'Video Converter'; }
    public function get_icon(): string { return 'eicon-video-player'; }
    public function get_keywords(): array {
        return ['video converter','convert video','mp4 to webm','video format','video to mp3','change video format'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert video files between formats entirely in your browser. Supports MP4, WebM, AVI, MKV, MOV, GIF, and audio extraction to MP3, WAV. Zero uploads &mdash; your files never leave your device.
        </div>
        <?php $this->render_drop_zone('tc-vid-drop', 'video/*,.mp4,.webm,.avi,.mkv,.mov,.flv,.wmv,.m4v,.3gp', 'Drag &amp; drop a video here or click to browse'); ?>
        <?php $this->render_file_row('tc-vid-file'); ?>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Output Format</h4>
                <div class="tc-rsz-mode-cards tc-vid-formats">
                    <button class="tc-rsz-mode-card sel" type="button" data-val="mp4">
                        <span class="tc-rsz-mode-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="5 3 19 12 5 21 5 3"/></svg></span>
                        <span class="tc-rsz-mode-text"><b>MP4</b><span>H.264 Universal</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="webm">
                        <span class="tc-rsz-mode-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="5 3 19 12 5 21 5 3"/></svg></span>
                        <span class="tc-rsz-mode-text"><b>WebM</b><span>VP8/VP9 Web</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="avi">
                        <span class="tc-rsz-mode-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="5 3 19 12 5 21 5 3"/></svg></span>
                        <span class="tc-rsz-mode-text"><b>AVI</b><span>Legacy format</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="mov">
                        <span class="tc-rsz-mode-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="5 3 19 12 5 21 5 3"/></svg></span>
                        <span class="tc-rsz-mode-text"><b>MOV</b><span>QuickTime</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="gif">
                        <span class="tc-rsz-mode-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/></svg></span>
                        <span class="tc-rsz-mode-text"><b>GIF</b><span>Animated image</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="mp3">
                        <span class="tc-rsz-mode-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></span>
                        <span class="tc-rsz-mode-text"><b>MP3</b><span>Extract audio</span></span>
                    </button>
                    <button class="tc-rsz-mode-card" type="button" data-val="wav">
                        <span class="tc-rsz-mode-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg></span>
                        <span class="tc-rsz-mode-text"><b>WAV</b><span>Uncompressed</span></span>
                    </button>
                </div>
            </div>
            <div class="tc-rsz-section" id="tc-vid-resolution-section">
                <h4 class="tc-rsz-heading">Resolution</h4>
                <div class="tc-rsz-mode-cards tc-vid-resolution" style="grid-template-columns:1fr 1fr 1fr 1fr 1fr;">
                    <button class="tc-rsz-mode-card" type="button" data-val="original"><span class="tc-rsz-mode-text"><b>Original</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="1920"><span class="tc-rsz-mode-text"><b>1080p</b></span></button>
                    <button class="tc-rsz-mode-card sel" type="button" data-val="1280"><span class="tc-rsz-mode-text"><b>720p</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="854"><span class="tc-rsz-mode-text"><b>480p</b></span></button>
                    <button class="tc-rsz-mode-card" type="button" data-val="640"><span class="tc-rsz-mode-text"><b>360p</b></span></button>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Quality <span class="tc-rsz-quality-badge" id="tc-vid-quality-val">23 CRF</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">Smaller</span>
                    <input type="range" class="tc-rsz-slider" id="tc-vid-quality" min="15" max="35" value="23">
                    <span class="tc-rsz-slider-max">Better</span>
                </div>
            </div>
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Frame Rate <span class="tc-rsz-quality-badge" id="tc-vid-fps-val">30</span></h4>
                <div class="tc-rsz-slider-wrap">
                    <span class="tc-rsz-slider-min">10</span>
                    <input type="range" class="tc-rsz-slider" id="tc-vid-fps" min="10" max="60" value="30">
                    <span class="tc-rsz-slider-max">60</span>
                </div>
            </div>
            <div class="tc-rsz-toggles">
                <label class="tc-rsz-toggle">
                    <input type="checkbox" class="tc-rsz-toggle-input" id="tc-vid-mute">
                    <span class="tc-rsz-toggle-track"><span class="tc-rsz-toggle-thumb"></span></span>
                    <span class="tc-rsz-toggle-text"><b>Remove audio</b><span>Strip all audio tracks</span></span>
                </label>
            </div>
            <div class="tc-rsz-section" id="tc-vid-trim-section">
                <h4 class="tc-rsz-heading">Trim (Optional)</h4>
                <div style="display:flex;gap:12px;align-items:center;">
                    <div class="tc-input-group" style="flex:1;">
                        <label class="tc-label">Start (s)</label>
                        <input type="number" class="tc-input" id="tc-vid-trim-start" min="0" step="0.1" value="0">
                    </div>
                    <div class="tc-input-group" style="flex:1;">
                        <label class="tc-label">End (s)</label>
                        <input type="number" class="tc-input" id="tc-vid-trim-end" min="0" step="0.1" value="0" placeholder="0 = full">
                    </div>
                </div>
            </div>
        </div>
        <?php $this->render_progress_bar('tc-vid-progress', 'Converting...'); ?>
        <?php $this->render_actions('tc-vid-convert', 'Convert Video', 'tc-vid-download', 'Download'); ?>
        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Original</span><span class="tc-stat-value" id="tc-vid-stat-orig">-</span></div>
            <div class="tc-stat-item"><span class="tc-stat-label">Converted</span><span class="tc-stat-value" id="tc-vid-stat-conv">-</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Saved</span><span class="tc-stat-value" id="tc-vid-stat-saved">-</span></div>
        </div>
        <?php
    }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void {
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Converted Video</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <div><span>Original</span><b id="tc-stat-orig">&mdash;</b></div>
                        <div><span>Converted</span><b id="tc-stat-comp">&mdash;</b></div>
                        <div class="saved"><span>Saved</span><b id="tc-stat-saved">&mdash;</b></div>
                    </div>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original">Original Video</button>
                            <button data-tab="result">Converted Video</button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig">
                        <p style="color:var(--muted);font-size:14px;">Original video will appear here after selection.</p>
                    </div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result">
                        <p style="color:var(--muted);font-size:14px;">Converted video will appear here after processing.</p>
                    </div>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }
}