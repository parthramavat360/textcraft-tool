<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Html_Preview extends TextCraft_Tool_Base {
    public function get_name(): string { return 'html_preview'; }
    public function get_title(): string { return 'HTML Preview / Live Editor'; }
    public function get_icon(): string { return 'eicon-code'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Write HTML, CSS, and JavaScript and see the result live in real time. A full split-pane editor with auto-updating preview, perfect for testing code before publishing.</div>

        <div class="tctp-editor">
            <div class="tctp-editor__head">
                <div class="tctp-editor__tabs">
                    <button class="tctp-editor__tab sel" data-file="html">HTML</button>
                    <button class="tctp-editor__tab" data-file="css">CSS</button>
                    <button class="tctp-editor__tab" data-file="js">JS</button>
                </div>
                <div class="tctp-editor__head-actions">
                    <span class="tctp-editor__auto" id="hp-autolabel"><i class="fa-solid fa-bolt"></i> Auto-run</span>
                    <div class="tctp-editor__seg" id="hp-mode">
                        <button class="tctp-editor__seg-btn sel" data-val="auto">Auto</button>
                        <button class="tctp-editor__seg-btn" data-val="manual">Manual</button>
                    </div>
                </div>
            </div>

            <div class="tctp-editor__body">
                <div class="tctp-editor__pane tctp-editor__pane--code">
                    <div class="tctp-editor__files">
                        <button class="tctp-editor__file sel" data-file="html">index.html</button>
                        <button class="tctp-editor__file" data-file="css">style.css</button>
                        <button class="tctp-editor__file" data-file="js">script.js</button>
                    </div>
                    <textarea class="tctp-editor__code" id="hp-html" spellcheck="false" data-lang="html" aria-label="HTML code"></textarea>
                    <textarea class="tctp-editor__code" id="hp-css" spellcheck="false" data-lang="css" aria-label="CSS code" style="display:none"></textarea>
                    <textarea class="tctp-editor__code" id="hp-js" spellcheck="false" data-lang="javascript" aria-label="JavaScript code" style="display:none"></textarea>
                </div>
                <div class="tctp-editor__pane tctp-editor__pane--preview">
                    <div class="tctp-editor__preview-head">
                        <span class="tctp-editor__dot tctp-editor__dot--r"></span>
                        <span class="tctp-editor__dot tctp-editor__dot--y"></span>
                        <span class="tctp-editor__dot tctp-editor__dot--g"></span>
                        <span class="tctp-editor__preview-title">Preview</span>
                        <button class="tctp-editor__open" id="hp-open">Open New Tab</button>
                    </div>
                    <iframe class="tctp-editor__frame" id="hp-frame" title="Live preview"></iframe>
                </div>
            </div>

            <div class="tctp-editor__foot">
                <span id="hp-status" class="tctp-editor__status tctp-editor__status--ok"><i class="fa-solid fa-circle-check"></i> Ready</span>
                <span class="tctp-editor__meta" id="hp-meta">HTML 0 · CSS 0 · JS 0</span>
                <button class="tc-btn tc-btn--ghost" id="hp-run"><i class="fa-solid fa-play"></i> Run</button>
                <button class="tc-btn tc-btn--ghost" id="hp-copy"><i class="fa-solid fa-copy"></i> Copy</button>
                <button class="tc-btn tc-btn--primary" id="hp-export"><i class="fa-solid fa-download"></i> Export HTML</button>
            </div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
