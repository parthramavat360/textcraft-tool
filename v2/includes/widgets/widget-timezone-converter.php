<?php
/**
 * Widget: Time Zone Converter
 * Compare times across multiple time zones.
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

defined('ABSPATH') || exit;

class Widget_Timezone_Converter extends TextCraft_Tool_Base {

    protected bool $show_preview = false;

    public function get_name(): string { return 'timezone_converter'; }
    public function get_title(): string { return 'Time Zone Converter'; }
    public function get_icon(): string { return 'eicon-clock'; }

    public function get_keywords(): array {
        return ['timezone converter', 'time zone', 'world clock', 'time difference', 'utc converter'];
    }

    protected function render_tool_content(array $settings): void {
        ?>
        <div class="tc-tool-desc">
            Compare the current time across multiple time zones. Select your source time zone and see the equivalent time worldwide.
        </div>

        <div class="tc-rsz-options">
            <div class="tc-rsz-section">
                <div class="tc-rsz-toggles">
                    <div class="tc-input-group">
                        <label class="tc-label"><b>Your Time Zone:</b></label>
                        <select class="tc-select" id="tc-tz-source" style="width:250px"></select>
                    </div>
                </div>
            </div>
        </div>

        <div class="tc-tz-grid" id="tc-tz-grid"></div>

        <?php
    }
}
