<?php
/**
 * Widget: Font Generator (Mega)
 * 25+ Unicode font styles — Bold, Italic, Small, Upside Down, Strikethrough,
 * Wide, Zalgo, Mirror, Bubble, Double-Struck, Gothic, Typewriter, Big,
 * Underline, Cursive, Superscript, Subscript, Cute, and more.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Font_Generator extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'font_generator'; }
    public function get_title(): string { return 'Font Generator'; }
    public function get_icon(): string { return 'eicon-text'; }

    public function get_keywords(): array {
        return [
            'font generator', 'text generator', 'fancy text', 'unicode fonts',
            'bold text', 'italic text', 'small text', 'upside down text',
            'strikethrough', 'aesthetic text', 'zalgo text', 'bubble text',
            'gothic text', 'cursive text', 'underline text', 'big text',
            'mirror text', 'typewriter text', 'double struck', 'superscript',
            'subscript', 'cute font', 'cool fonts', 'copy paste fonts',
            'tiktok font', 'instagram font', 'discord font',
        ];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Convert your text into 25+ unique Unicode font styles instantly. Type or paste any text below and copy your favorite style with one click.
        </div>

        <div class="tc-font-search-wrap">
            <div class="tc-input-group">
                <label class="tc-label">Search fonts...</label>
                <svg class="tc-font-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="tc-input" id="tc-fgen-search" placeholder="Search styles..." autocomplete="off">
            </div>
        </div>

        <textarea class="tc-textarea" id="tc-fgen-input" placeholder="Type or paste your text here..." rows="4">Hello World</textarea>

        <div class="tc-font-grid" id="tc-fgen-grid"></div>
        <?php
    }

    protected function render_result_content(array $settings): void {
        ?>
        <div class="tc-fgen-result" id="tc-fgen-result">
            <p class="tc-fgen-hint">Generated styles appear below as you type. Click any card to copy.</p>
        </div>
        <?php
    }
}
