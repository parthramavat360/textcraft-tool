<?php
/**
 * Widget: Passport Photo Maker
 * Create official passport-style photos for visas, passports, and ID cards.
 * Choose a preset (US 2×2, 35×45mm, 3.5×4.5cm, 5.1×5.1cm), swap the
 * background colour, position the face, and export a print-ready sheet.
 * 100% client-side using the canvas API.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Passport_Photo extends TextCraft_Tool_Base {

    protected bool $show_preview = true;
    protected string $preview_orig_label = 'Original';
    protected string $preview_result_label = 'Photo';
    protected array $result_stats = [
        ['Preset', 'tc-ppt-stat-preset'],
        ['Output size', 'tc-ppt-stat-size'],
        ['Background', 'tc-ppt-stat-bg'],
    ];

    public function get_name(): string { return 'passport_photo_maker'; }
    public function get_title(): string { return 'Passport Photo Maker'; }
    public function get_icon(): string { return 'eicon-image-rollover'; }

    public function get_keywords(): array {
        return ['passport photo', 'passport photo maker', 'visa photo', 'id photo', '2x2 photo', 'passport size', 'immigration photo', 'photo print sheet', 'background color'];
    }

    protected function render_tool_content( array $settings ): void {
        ?>
        <div class="tc-tool-desc">
            Create correct-size passport, visa, and ID photos in seconds. Crop your photo,
            pick a background colour, and export a ready-to-print sheet — all in your browser.
        </div>

        <?php $this->render_drop_zone( 'tc-ppt-drop', 'image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp', 'Upload your photo here' ); ?>
        <?php $this->render_file_row( 'tc-ppt-file' ); ?>

        <div class="tc-ppt-options tc-imgprem">

            <!-- Preset size -->
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Photo Size</h4>
                <div class="tc-ppt-presets" id="tc-ppt-preset-tabs">
                    <button class="tc-ppt-preset sel" type="button" data-idx="0" data-title="US Passport">
                        <b>2 × 2 in</b>
                        <span>US passport · 51 mm</span>
                    </button>
                    <button class="tc-ppt-preset" type="button" data-idx="1" data-title="35 × 45 mm">
                        <b>35 × 45</b>
                        <span>Visa · Schengen</span>
                    </button>
                    <button class="tc-ppt-preset" type="button" data-idx="2" data-title="3.5 × 4.5 cm">
                        <b>3.5 × 4.5</b>
                        <span>ID · biometric</span>
                    </button>
                    <button class="tc-ppt-preset" type="button" data-idx="3" data-title="51 × 51 mm">
                        <b>51 × 51</b>
                        <span>US visa</span>
                    </button>
                </div>
            </div>

            <!-- Positioning -->
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Crop &amp; Position</h4>
                <div class="tc-ppt-crop-row">
                    <div class="tc-ppt-crop-box">
                        <div class="tc-ppt-crop-stage" id="tc-ppt-crop-stage">
                            <div class="tc-ppt-frame" id="tc-ppt-display-frame">
                                <img id="tc-ppt-crop-img" alt="" hidden>
                                <div class="tc-ppt-guides" id="tc-ppt-guides" hidden>
                                    <span class="tc-ppt-guide-v"></span>
                                    <span class="tc-ppt-guide-h tc-ppt-guide-head"></span>
                                    <span class="tc-ppt-guide-h tc-ppt-guide-eye"></span>
                                </div>
                            </div>
                            <div class="tc-ppt-dim-overlay" id="tc-ppt-dim"></div>
                        </div>
                    </div>
                    <div class="tc-ppt-crop-controls">
                        <label class="tc-ppt-ctrl">
                            <span>Zoom</span>
                            <input type="range" id="tc-ppt-zoom" min="100" max="400" value="160">
                        </label>
                        <span class="tc-ppt-value tc-ppt-zoom-value" id="tc-ppt-zoom-value">160%</span>
                        <label class="tc-ppt-guide-toggle">
                            <input type="checkbox" id="tc-ppt-guides-on">
                            <span class="tc-ppt-guide-switch" aria-hidden="true"></span>
                            <span class="tc-ppt-guide-text">Show face guidelines</span>
                        </label>
                        <span class="tc-ppt-hint">Drag inside the frame to move the face up, down, left or right.</span>
                    </div>
                </div>
            </div>

            <!-- Background -->
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Background Colour</h4>
                <div class="tc-ppt-label-color-row">
                    <span>Colour</span>
                    <div class="tc-premium-color-picker" data-picker="tc-ppt-color">
                        <label class="tc-pcp-swatch" for="tc-ppt-color"><span class="tc-pcp-swatch-fill" data-swatch="tc-ppt-color"></span></label>
                        <span class="tc-pcp-hex"></span>
                        <input type="color" class="tc-pcp-input" id="tc-ppt-color" value="#ffffff">
                        <div class="tc-pcp-swatches" data-palette="tc-ppt-color">
                            <button class="tc-pcp-csw" data-val="#ffffff" type="button"></button>
                            <button class="tc-pcp-csw" data-val="#1f4e9b" type="button"></button>
                            <button class="tc-pcp-csw" data-val="#b71c1c" type="button"></button>
                            <button class="tc-pcp-csw" data-val="#e8e8e8" type="button"></button>
                            <button class="tc-pcp-csw" data-val="#0b1220" type="button"></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom label -->
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Bottom Label</h4>
                <p class="tc-ppt-outnote">Printed on each photo and the print sheet (e.g. your name / DOB).</p>
                <label class="tc-ppt-label-row">
                    <span>Label text</span>
                    <input type="text" class="tc-input tc-pe-text-input" id="tc-ppt-label-text" placeholder="e.g. JOHN A. DOE" maxlength="40" autocomplete="off">
                </label>
                <div class="tc-ppt-label-color-row">
                    <span>Colour</span>
                    <div class="tc-premium-color-picker" data-picker="tc-ppt-label-color">
                        <label class="tc-pcp-swatch" for="tc-ppt-label-color"><span class="tc-pcp-swatch-fill" data-swatch="tc-ppt-label-color"></span></label>
                        <span class="tc-pcp-hex"></span>
                        <input type="color" class="tc-pcp-input" id="tc-ppt-label-color" value="#0b1220">
                        <div class="tc-pcp-swatches" data-palette="tc-ppt-label-color">
                            <button class="tc-pcp-csw" data-val="#0b1220" type="button"></button>
                            <button class="tc-pcp-csw" data-val="#ffffff" type="button"></button>
                            <button class="tc-pcp-csw" data-val="#1f4e9b" type="button"></button>
                            <button class="tc-pcp-csw" data-val="#b71c1c" type="button"></button>
                            <button class="tc-pcp-csw" data-val="#111111" type="button"></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Output -->
            <div class="tc-rsz-section">
                <h4 class="tc-rsz-heading">Print Sheet</h4>
                <p class="tc-ppt-outnote">Exports a 6 × 4 inch sheet (300 DPI) with your photos tiled and trim marks — perfect for photo stores or home printing.</p>
                <div class="tc-ppt-count-row">
                    <span>Photos per sheet</span>
                    <div class="tc-ppt-count-pills" id="tc-ppt-count-pills">
                        <button class="tc-ppt-count sel" type="button" data-count="0">Auto</button>
                        <button class="tc-ppt-count" type="button" data-count="4">4</button>
                        <button class="tc-ppt-count" type="button" data-count="6">6</button>
                        <button class="tc-ppt-count" type="button" data-count="8">8</button>
                    </div>
                </div>
                <div class="tc-ppt-count-row tc-ppt-res-row">
                    <span>Resolution</span>
                    <div class="tc-ppt-count-pills" id="tc-ppt-res-pills">
                        <button class="tc-ppt-count sel" type="button" data-scale="1">1×</button>
                        <button class="tc-ppt-count" type="button" data-scale="2">2× HD</button>
                        <button class="tc-ppt-count" type="button" data-scale="4">4× 4K</button>
                    </div>
                </div>
                <div class="tc-ppt-output-row">
                    <button class="tc-ppt-fmt sel" type="button" data-fmt="image/jpeg" data-ext="jpg">JPG</button>
                    <button class="tc-ppt-fmt" type="button" data-fmt="image/png" data-ext="png">PNG</button>
                </div>
            </div>

        </div>

        <?php $this->render_progress_bar( 'tc-ppt-progress', 'Creating your photo...' ); ?>

        <?php $this->render_actions( 'tc-ppt-make', 'Create Photo', 'tc-ppt-sheet', 'Download Print Sheet' ); ?>

        <?php
    }

    protected function render_result_content( array $settings ): void {
        ?>
        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="tc-ppt-download" type="button">Download Photo</button>
            <button class="tc-btn tc-btn--ghost" id="tc-ppt-copy" type="button">New Photo</button>
        </div>
        <?php
    }
}
