<?php
/**
 * TextCraft_Tool_Base — abstract foundation for individual tool widgets.
 *
 * Provides:
 *  - Common Elementor panel controls (title, subtitle, badge).
 *  - Shared render helpers (workspace layout, drop zone, file rows, progress bars, textareas, stats).
 *  - SEO infrastructure (JSON-LD schema, FAQ accordion, related tools).
 *  - Automatic CSS variable injection for accent color.
 *
 * Child widgets must implement:
 *  - get_name()            — unique snake_case slug (e.g., 'case_converter')
 *  - get_title()           — human-readable widget name
 *  - get_icon()            — Elementor icon class
 *  - render_tool_content() — the inner tool-specific HTML
 *  - render_result_content() — the result/output HTML (optional, default shows textarea)
 *
 * @package TextCraft_Tools_Pro
 */

declare(strict_types=1);

namespace TextCraft_Tools_Pro;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

abstract class TextCraft_Tool_Base extends Widget_Base {

    // ── Elementor identity ─────────────────────────────────────

    public function get_categories(): array {
        return ['textcraft-tools'];
    }

    public function get_keywords(): array {
        return ['textcraft', 'text', 'tool', 'converter', 'free', 'online utility', 'browser-based'];
    }

    // ── Panel controls ─────────────────────────────────────────

    protected function register_controls(): void {

        // ── Content section ────────────────────────────────────

        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Content', 'textcrafttoolspro'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'tool_title',
            [
                'label'       => esc_html__('Title', 'textcrafttoolspro'),
                'type'        => Controls_Manager::TEXT,
                'default'     => $this->get_title(),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'tool_subtitle',
            [
                'label'       => esc_html__('Subtitle', 'textcrafttoolspro'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => '',
                'rows'        => 2,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'show_badge',
            [
                'label'        => esc_html__('Show Badge', 'textcrafttoolspro'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Yes', 'textcrafttoolspro'),
                'label_off'    => esc_html__('No', 'textcrafttoolspro'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'badge_text',
            [
                'label'     => esc_html__('Badge Text', 'textcrafttoolspro'),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__('Free · Instant · No Signup', 'textcrafttoolspro'),
                'condition' => ['show_badge' => 'yes'],
            ]
        );

        $this->add_control(
            'tool_category',
            [
                'label'       => esc_html__('Category', 'textcrafttoolspro'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'Tools',
                'options'     => [
                    'PDF Tools'       => 'PDF Tools',
                    'Image Tools'     => 'Image Tools',
                    'Text Tools'      => 'Text Tools',
                    'Compression'     => 'Compression',
                    'Generator Tools' => 'Generator Tools',
                    'Developer Tools' => 'Developer Tools',
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'panel_meta',
            [
                'label'       => esc_html__('Panel Meta (right side)', 'textcrafttoolspro'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'description' => esc_html__('e.g., "PDF up to 200 MB"', 'textcrafttoolspro'),
            ]
        );

        $this->register_tool_controls();

        $this->end_controls_section();

        // ── Style section ──────────────────────────────────────

        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Style', 'textcrafttoolspro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'accent_color',
            [
                'label'     => esc_html__('Accent Color', 'textcrafttoolspro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#2563eb',
                'selectors' => [
                    '{{WRAPPER}}' => '--tc-accent: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label'     => esc_html__('Card Background', 'textcrafttoolspro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .tc-panel' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ── SEO Content section ────────────────────────────────

        $this->start_controls_section(
            'section_seo_content',
            [
                'label' => esc_html__('SEO Content', 'textcrafttoolspro'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'seo_override',
            [
                'label'       => esc_html__('Override Default Content', 'textcrafttoolspro'),
                'type'        => Controls_Manager::SWITCHER,
                'label_on'    => esc_html__('Yes', 'textcrafttoolspro'),
                'label_off'   => esc_html__('No (use file data)', 'textcrafttoolspro'),
                'return_value'=> 'yes',
                'default'     => '',
                'description' => esc_html__('Enable to edit SEO content from Elementor.', 'textcrafttoolspro'),
            ]
        );

        // Intro paragraphs
        $intro_repeater = new Repeater();
        $intro_repeater->add_control(
            'paragraph',
            [
                'label'       => esc_html__('Paragraph', 'textcrafttoolspro'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => '',
                'rows'        => 3,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'seo_intro',
            [
                'label'       => esc_html__('Intro Paragraphs', 'textcrafttoolspro'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $intro_repeater->get_controls(),
                'title_field' => '{{{ paragraph }}}',
                'condition'   => ['seo_override' => 'yes'],
            ]
        );

        // FAQ
        $faq_repeater = new Repeater();
        $faq_repeater->add_control(
            'question',
            [
                'label'       => esc_html__('Question', 'textcrafttoolspro'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );
        $faq_repeater->add_control(
            'answer',
            [
                'label'       => esc_html__('Answer', 'textcrafttoolspro'),
                'type'        => Controls_Manager::TEXTAREA,
                'rows'        => 3,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'seo_faq',
            [
                'label'       => esc_html__('FAQ Items', 'textcrafttoolspro'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $faq_repeater->get_controls(),
                'title_field' => '{{{ question }}}',
                'condition'   => ['seo_override' => 'yes'],
            ]
        );

        // Related tools
        $related_repeater = new Repeater();
        $related_repeater->add_control(
            'tool_url',
            [
                'label'       => esc_html__('Tool URL', 'textcrafttoolspro'),
                'type'        => Controls_Manager::URL,
                'label_block' => true,
            ]
        );
        $related_repeater->add_control(
            'tool_name',
            [
                'label'       => esc_html__('Tool Name', 'textcrafttoolspro'),
                'type'        => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'seo_related',
            [
                'label'       => esc_html__('Related Tools', 'textcrafttoolspro'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $related_repeater->get_controls(),
                'title_field' => '{{{ tool_name }}}',
                'condition'   => ['seo_override' => 'yes'],
            ]
        );

        $this->end_controls_section();
    }

    // ── Child widget hook ──────────────────────────────────────

    /**
     * Override in child to add tool-specific Elementor controls.
     */
    protected function register_tool_controls(): void {}

    // ── Main render ────────────────────────────────────────────

    protected function render(): void {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="tc-workspace-wrap<?php echo $this->premium ? ' tc-premium' : ''; ?>">
            <?php $this->render_hero($settings); ?>
            <div class="tc-workspace">
                <div class="tc-panel">
                    <div class="tc-panel-head">
                        <h3><?php echo esc_html($settings['tool_title'] ?? $this->get_title()); ?></h3>
                        <?php if (!empty($settings['panel_meta'])): ?>
                            <span><?php echo esc_html($settings['panel_meta']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="tc-panel-body">
                        <?php $this->render_tool_content($settings); ?>
                    </div>
                </div>
                <?php $this->render_result($settings); ?>
            </div>
            <?php /* $this->render_seo($settings); */ ?>
        </div>
        <?php
    }

    // ── Hero section ───────────────────────────────────────────

    protected function render_hero(array $settings): void {
        $title    = $settings['tool_title'] ?? $this->get_title();
        $subtitle = $settings['tool_subtitle'] ?? '';
        $category = $settings['tool_category'] ?? 'Tools';
        ?>
        <div class="tc-tool-hero">
            <div class="tc-crumbs">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> /
                <a href="<?php echo esc_url(home_url('/tools/')); ?>"><?php echo esc_html($category); ?></a> /
                <span><?php echo esc_html($title); ?></span>
            </div>
            <div class="tc-pills">
                <span class="tc-pill tc-pill--ok"><span class="tc-dot"></span> Runs locally in your browser</span>
                <span class="tc-pill">Free forever</span>
                <span class="tc-pill">No signup</span>
                <?php if ($this->premium): ?>
                    <span class="tc-pill">Median run &lt; 1s</span>
                <?php endif; ?>
            </div>
            <h1><?php echo esc_html($title); ?></h1>
            <?php if ($subtitle): ?>
                <p class="tc-lede"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    // ── Result panel (right column) ────────────────────────────

    /**
     * Set to true in child widgets that need a visual preview area
     * (image converters, PDF tools, compressors, etc.).
     * Text/generator tools should leave this false.
     */
    protected bool $show_preview = false;

    /**
     * Opt-in premium workspace styling. When true, the wrapper gets the
     * unique `tc-premium` class and premium markup extras are rendered.
     * Only specific PDF tools enable this — everything else stays untouched.
     */
    protected bool $premium = false;

    /**
     * Preview tab labels. Child widgets can override to be tool-specific.
     */
    protected string $preview_orig_label = 'Original';
    protected string $preview_result_label = 'Compressed';

    /**
     * Stats shown in the Result panel: each entry is [label, element_id, optional_class].
     * Child widgets override with their own stat rows so values populate correctly.
     */
    protected array $result_stats = [
        ['Original', 'tc-stat-orig'],
        ['Compressed', 'tc-stat-comp'],
        ['Saved', 'tc-stat-saved', 'saved'],
    ];

    protected function render_result(array $settings): void {
        $stats = (array) $this->result_stats;
        ?>
        <div class="tc-result-col">
            <div class="tc-panel">
                <div class="tc-panel-head">
                    <h3>2 &middot; Result</h3>
                    <span id="tc-status-chip">Idle</span>
                </div>
                <div class="tc-panel-body">
                    <div class="tc-stats">
                        <?php foreach ($stats as $stat): ?>
                        <div class="<?php echo esc_attr($stat[2] ?? ''); ?>">
                            <span><?php echo esc_html($stat[0]); ?></span>
                            <b id="<?php echo esc_attr($stat[1]); ?>">—</b>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($this->show_preview): ?>
                    <div class="tc-tabs-header">
                        <h4>Preview</h4>
                        <div class="tc-tabs">
                            <button class="on" data-tab="original"><?php echo esc_html($this->preview_orig_label); ?></button>
                            <button data-tab="result"><?php echo esc_html($this->preview_result_label); ?></button>
                        </div>
                    </div>
                    <div class="tc-preview" data-tab-content="original" id="tc-preview-orig"><?php echo esc_html($this->preview_orig_label); ?> preview will appear here</div>
                    <div class="tc-preview is-hidden" data-tab-content="result" id="tc-preview-result"><?php echo esc_html($this->preview_result_label); ?> preview will appear here</div>
                    <?php endif; ?>
                    <?php $this->render_result_content($settings); ?>
                </div>
            </div>
            <?php $this->render_side_panel($settings); ?>
        </div>
        <?php
    }

    /**
     * Override in child to add extra content below the standard result panel.
     * The standard stats (tc-stat-orig/comp/saved) are always rendered.
     * Preview tabs/area only render if $this->show_preview = true.
     */
    protected function render_result_content(array $settings): void {}

    // ── Side panel (What stays intact / tips) ──────────────────

    protected function render_side_panel(array $settings): void {
        $side_items = $settings['side_list_items'] ?? null;
        if (!$side_items) {
            $side_items = [
                'No file uploads required',
                'Works entirely in your browser',
                'Free forever, no limits',
                'Results appear instantly',
            ];
        }
        ?>
        <div class="tc-panel">
            <div class="tc-panel-head">
                <h3>What stays intact</h3>
            </div>
            <div class="tc-panel-body" style="padding-top:4px">
                <ul class="tc-side-list">
                    <?php foreach ($side_items as $item): ?>
                        <li><em>&checkmark;</em> <?php echo esc_html($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php
    }

    // ── SEO section ────────────────────────────────────────────

    protected function render_seo(array $settings): void {
        return; // Disabled — SEO section removed from all tool pages
        /*
        $seo_data = $this->get_seo_data();
        if (!$seo_data && ($settings['seo_override'] ?? '') !== 'yes') {
            return;
        }

        $intro    = $this->get_intro_paragraphs($settings, $seo_data);
        $faq      = $this->get_faq_items($settings, $seo_data);
        $related  = $this->get_related_tools($settings, $seo_data);
        ?>
        <div class="tc-seo-section">
            <?php if ($intro): ?>
                <div class="tc-seo-intro">
                    <?php foreach ($intro as $p): ?>
                        <p><?php echo esc_html($p); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($faq): ?>
                <div class="tc-seo-faq">
                    <h2>Frequently Asked Questions</h2>
                    <div class="tc-faq-list">
                        <?php foreach ($faq as $item): ?>
                            <details class="tc-faq-item">
                                <summary><?php echo esc_html($item['question']); ?></summary>
                                <p><?php echo esc_html($item['answer']); ?></p>
                            </details>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($related): ?>
                <div class="tc-seo-related">
                    <h2>Related Tools</h2>
                    <div class="tc-related-grid">
                        <?php foreach ($related as $tool): ?>
                            <a href="<?php echo esc_url($tool['url']); ?>" class="tc-related-card">
                                <?php echo esc_html($tool['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php

        // JSON-LD schema
        $this->render_json_ld($settings, $seo_data);
        */
    }

    // ── JSON-LD structured data ────────────────────────────────

    protected function render_json_ld(array $settings, ?array $seo_data): void {
        $title = $settings['tool_title'] ?? $this->get_title();
        $page_url = get_permalink();

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebApplication',
            'name'        => $title,
            'url'         => $page_url,
            'description' => $settings['tool_subtitle'] ?? ($seo_data['description'] ?? ''),
            'applicationCategory' => 'UtilitiesApplication',
            'operatingSystem'     => 'Any',
            'offers' => [
                '@type'         => 'Offer',
                'price'         => '0',
                'priceCurrency' => 'USD',
            ],
        ];

        if ($faq = $this->get_faq_items($settings, $seo_data)) {
            $schema['mainEntity'] = [];
            foreach ($faq as $item) {
                $schema['mainEntity'][] = [
                    '@type'          => 'Question',
                    'name'           => $item['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => $item['answer'],
                    ],
                ];
            }
        }

        ?>
        <script type="application/ld+json">
            <?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
        </script>
        <?php
    }

    // ── SEO data helpers ───────────────────────────────────────

    /**
     * Get SEO data from the data file. Override in child if needed.
     */
    protected function get_seo_data(): ?array {
        static $all_data = null;
        if ($all_data === null) {
            $file = plugin_dir_path(__DIR__) . 'includes/seo-content-data.php';
            if (file_exists($file)) {
                $all_data = require $file;
            } else {
                $all_data = [];
            }
        }
        $slug = $this->get_name();
        return $all_data[$slug] ?? $all_data['textcraft_' . $slug] ?? null;
    }

    protected function get_intro_paragraphs(array $settings, ?array $seo_data): array {
        if (($settings['seo_override'] ?? '') === 'yes' && !empty($settings['seo_intro'])) {
            return array_map(fn($item) => $item['paragraph'], $settings['seo_intro']);
        }
        return $seo_data['intro'] ?? [];
    }

    protected function get_faq_items(array $settings, ?array $seo_data): array {
        if (($settings['seo_override'] ?? '') === 'yes' && !empty($settings['seo_faq'])) {
            return $settings['seo_faq'];
        }
        return $seo_data['faq'] ?? [];
    }

    protected function get_related_tools(array $settings, ?array $seo_data): array {
        if (($settings['seo_override'] ?? '') === 'yes' && !empty($settings['seo_related'])) {
            return array_map(fn($item) => [
                'url'  => $item['tool_url']['url'] ?? '#',
                'name' => $item['tool_name'],
            ], $settings['seo_related']);
        }
        return $seo_data['related'] ?? [];
    }

    // ═══════════════════════════════════════════════════════════
    //  SHARED RENDER HELPERS
    // ═══════════════════════════════════════════════════════════

    /**
     * Render a file upload drop zone.
     */
    protected function render_drop_zone(string $id, string $accept = '*/*', string $label = 'Drag & drop or click to upload'): void {
        ?>
        <div class="tc-drop" id="<?php echo esc_attr($id); ?>">
            <input type="file" class="tc-drop-input" id="<?php echo esc_attr($id); ?>-input" accept="<?php echo esc_attr($accept); ?>" hidden>
            <div class="tc-drop-ic">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            </div>
            <b><?php echo esc_html($label); ?></b>
            <small>Supports most file formats</small>
        </div>
        <?php
    }

    /**
     * Render a file row (shown after file is selected).
     */
    protected function render_file_row(string $id, string $filename = 'document.pdf'): void {
        ?>
        <div class="tc-file-row" id="<?php echo esc_attr($id); ?>" style="display:none">
            <div class="tc-file-ic">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div class="tc-file-meta">
                <b class="tc-file-name"><?php echo esc_html($filename); ?></b>
                <span class="tc-file-size">0 KB</span>
            </div>
            <button class="tc-x" type="button" aria-label="Remove file">×</button>
        </div>
        <?php
    }

    /**
     * Render a progress bar.
     */
    protected function render_progress_bar(string $id, string $label = 'Processing...'): void {
        ?>
        <div class="tc-bar" id="<?php echo esc_attr($id); ?>" style="display:none">
            <div class="tc-bar-fill" style="width:0%"></div>
            <span class="tc-bar-label"><?php echo esc_html($label); ?> <span class="tc-bar-pct">0%</span></span>
        </div>
        <?php
    }

    /**
     * Render action buttons (primary + secondary).
     */
    protected function render_actions(string $primary_id, string $primary_label = 'Process', string $secondary_id = '', string $secondary_label = ''): void {
        ?>
        <div class="tc-actions">
            <button class="tc-btn tc-btn--accent" id="<?php echo esc_attr($primary_id); ?>" type="button">
                <?php echo esc_html($primary_label); ?>
            </button>
            <?php if ($secondary_id): ?>
                <button class="tc-btn tc-btn--ghost" id="<?php echo esc_attr($secondary_id); ?>" type="button">
                    <?php echo esc_html($secondary_label); ?>
                </button>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render mode buttons (pill-style toggle group).
     */
    protected function render_mode_buttons(string $group, array $options, string $default = ''): void {
        ?>
        <div class="tc-modes" data-group="<?php echo esc_attr($group); ?>">
            <?php foreach ($options as $value => $label): ?>
                <button class="tc-btn tc-btn--ghost<?php echo ($value === ($default ?: array_key_first($options))) ? ' sel' : ''; ?>"
                        type="button"
                        data-val="<?php echo esc_attr($value); ?>">
                    <?php echo esc_html($label); ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Render a textarea input.
     */
    protected function render_textarea(string $id, string $placeholder = 'Enter text here...', int $rows = 8): void {
        ?>
        <textarea class="tc-textarea" id="<?php echo esc_attr($id); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" rows="<?php echo esc_attr($rows); ?>"></textarea>
        <?php
    }

    /**
     * Render a select dropdown.
     */
    protected function render_select(string $id, array $options, string $label = ''): void {
        ?>
        <select class="tc-select" id="<?php echo esc_attr($id); ?>">
            <?php if ($label): ?>
                <option value="" disabled selected><?php echo esc_html($label); ?></option>
            <?php endif; ?>
            <?php foreach ($options as $value => $text): ?>
                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($text); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /**
     * Render a range slider with label.
     */
    protected function render_range(string $id, int $min = 0, int $max = 100, int $default = 50, string $label = 'Quality', string $unit = '%'): void {
        ?>
        <div class="tc-range-wrap">
            <label class="tc-range-label" for="<?php echo esc_attr($id); ?>">
                <?php echo esc_html($label); ?>: <span id="<?php echo esc_attr($id); ?>-val"><?php echo esc_html($default . $unit); ?></span>
            </label>
            <input type="range" class="tc-range" id="<?php echo esc_attr($id); ?>" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" value="<?php echo esc_attr($default); ?>">
        </div>
        <?php
    }

    /**
     * Render a checkbox option.
     */
    protected function render_checkbox(string $id, string $label, bool $checked = false): void {
        ?>
        <label class="tc-check">
            <input type="checkbox" class="tc-check-input" id="<?php echo esc_attr($id); ?>"<?php checked($checked); ?>>
            <span class="tc-check-box"></span>
            <?php echo esc_html($label); ?>
        </label>
        <?php
    }

    /**
     * Render a simple status message area.
     */
    protected function render_status(string $id): void {
        ?>
        <div class="tc-status" id="<?php echo esc_attr($id); ?>"></div>
        <?php
    }

    /**
     * Render a stats panel row with labeled stat items.
     *
     * Supports two calling conventions:
     *  - render_stats_panel_row('tc-prefix-stats', ['key' => 'Label', ...])
     *    → generates IDs tc-prefix-stats-key
     *  - render_stats_panel_row([['id' => '...', 'label' => '...'], ...])
     *    → uses explicit IDs
     */
    protected function render_stats_panel_row($arg1, array $arg2 = []): void {
        $items = [];

        if (is_string($arg1)) {
            $container_id = $arg1;
            foreach ($arg2 as $key => $label) {
                $items[] = [
                    'id'    => $container_id . '-' . $key,
                    'label' => $label,
                ];
            }
        } elseif (is_array($arg1)) {
            $items = $arg1;
        }

        if (empty($items)) return;
        ?>
        <div class="tc-stats-row">
            <?php foreach ($items as $item): ?>
                <div class="tc-stat-item">
                    <span class="tc-stat-label"><?php echo esc_html($item['label']); ?></span>
                    <span class="tc-stat-value" id="<?php echo esc_attr($item['id']); ?>">0</span>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    // ── Abstract method for child widgets ──────────────────────

    /**
     * Render the tool-specific content inside the input panel.
     *
     * @param array $settings Elementor settings.
     */
    abstract protected function render_tool_content(array $settings): void;
}