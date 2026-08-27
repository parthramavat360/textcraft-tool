<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Gradient_Generator extends TextCraft_Tool_Base {
    public function get_name(): string { return 'gradient_generator_52'; }
    public function get_title(): string { return 'Gradient Generator'; }
    public function get_icon(): string { return 'eicon-full-width'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Create beautiful CSS gradients with multiple color stops. Adjust angle, colors, and type to generate the perfect gradient for your project.</div>
        <div class="tc-tool-actions-bar">
            <div class="tc-modes" data-group="gg-type">
                <button class="tc-btn tc-btn--ghost sel" data-val="linear">Linear</button>
                <button class="tc-btn tc-btn--ghost" data-val="radial">Radial</button>
                <button class="tc-btn tc-btn--ghost" data-val="conic">Conic</button>
            </div>
            <button class="tc-btn tc-btn--outline" id="gg-copy">Copy CSS</button>
        </div>
        <div class="tc-input-group">
            <label class="tc-label">Angle (degrees)</label>
            <input type="range" class="tc-input" id="gg-angle" min="0" max="360" value="135" style="padding:0">
            <span id="gg-angle-val" style="font-size:13px;color:#6b7280">135deg</span>
        </div>
        <div id="gg-colors">
            <div class="tc-input-group gg-color-row" style="display:flex;gap:8px;align-items:center">
                <input type="color" class="tc-input" id="gg-color1" value="#667eea" style="width:50px;height:40px;padding:2px;cursor:pointer">
                <input type="text" class="tc-input" id="gg-stop1" placeholder="0%" value="0%" style="width:80px">
                <button class="tc-btn tc-btn--outline gg-remove" style="padding:4px 8px;font-size:12px">X</button>
            </div>
            <div class="tc-input-group gg-color-row" style="display:flex;gap:8px;align-items:center">
                <input type="color" class="tc-input" id="gg-color2" value="#764ba2" style="width:50px;height:40px;padding:2px;cursor:pointer">
                <input type="text" class="tc-input" id="gg-stop2" placeholder="100%" value="100%" style="width:80px">
                <button class="tc-btn tc-btn--outline gg-remove" style="padding:4px 8px;font-size:12px">X</button>
            </div>
        </div>
        <div class="tc-actions">
            <button class="tc-btn tc-btn--outline" id="gg-add-color">+ Add Color</button>
            <button class="tc-btn tc-btn--primary" id="gg-generate">Generate</button>
        </div>
        <div class="tc-result-panel" id="gg-result" style="display:none">
            <div class="tc-result-header">
                <span class="tc-status-chip" id="gg-status">Gradient</span>
            </div>
            <div class="tc-result-body">
                <div id="gg-preview" style="height:200px;border-radius:8px;border:1px solid rgba(128,128,128,0.2)"></div>
                <pre class="tctp-code-block" style="margin-top:12px"><code id="gg-css"></code></pre>
            </div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
