<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_QR_Reader extends TextCraft_Tool_Base {
    protected bool $show_preview = true;
    public function get_name(): string { return 'qr_reader'; }
    public function get_title(): string { return 'QR Code Reader / Scanner'; }
    public function get_icon(): string { return 'eicon-barcode'; }
    public function get_keywords(): array { return ['qr code reader','qr scanner','scan qr code','read qr','decode qr']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Scan QR codes from your camera or upload an image. Everything happens in your browser — no data is ever sent to any server.</div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Scan Method</h4>
                <?php $this->render_mode_buttons('qr-method', ['camera'=>'Camera','upload'=>'Upload Image'], 'camera'); ?>
            </div>
        </div>

        <div id="tc-qr-camera-area" class="tc-qr-camera-area">
            <div id="tc-qr-reader" class="tc-qr-reader-box">
                <p style="color:var(--muted);font-size:14px;">Click "Start Camera" to begin scanning.</p>
            </div>
            <?php $this->render_actions('tc-qr-start', 'Start Camera', 'tc-qr-stop', 'Stop Camera'); ?>
        </div>

        <div id="tc-qr-upload-area" class="tc-qr-upload-area" style="display:none">
            <?php $this->render_drop_zone('tc-qr-drop', 'image/*'); ?>
        </div>

        <?php $this->render_progress_bar('tc-qr-progress', 'Scanning...'); ?>

        <div class="tc-stats-row">
            <div class="tc-stat-item"><span class="tc-stat-label">Status</span><span class="tc-stat-value" id="tc-qr-status">Ready</span></div>
            <div class="tc-stat-item tc-stat--saved"><span class="tc-stat-label">Scans</span><span class="tc-stat-value" id="tc-qr-count">0</span></div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void {}

    protected function render_result(array $settings): void { ?>
        <div class="tc-result-col"><div class="tc-panel">
            <div class="tc-panel-head"><h3>2 &middot; Scanned Result</h3><span id="tc-status-chip">Ready</span></div>
            <div class="tc-panel-body">
                <div class="tc-stats">
                    <div><span>Type</span><b id="tc-qr-type">&mdash;</b></div>
                    <div><span>Length</span><b id="tc-qr-length">&mdash;</b></div>
                    <div class="saved"><span>Scans</span><b id="tc-qr-total">0</b></div>
                </div>
                <div class="tc-tabs-header">
                    <h4>Result</h4>
                    <div class="tc-tabs">
                        <button class="on" data-tab="original">Decoded Text</button>
                        <button data-tab="result">Scan History</button>
                    </div>
                </div>
                <div class="tc-preview" data-tab-content="original" id="tc-qr-decoded">
                    <div class="tc-qr-empty">Scan a QR code to see the decoded result here.</div>
                </div>
                <div class="tc-preview is-hidden" data-tab-content="result" id="tc-qr-history">
                    <p style="color:var(--muted);font-size:14px;">Scan history will appear here.</p>
                </div>
                <div class="tc-qr-actions" id="tc-qr-actions" style="display:none">
                    <?php $this->render_actions('tc-qr-copy', 'Copy Text', 'tc-qr-open', 'Open Link'); ?>
                </div>
            </div>
        </div></div>
    <?php }
}
