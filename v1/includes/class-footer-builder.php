<?php
/**
 * TextCraft Tools — Premium Footer Builder
 *
 * Renders a luxury 3-tier footer that replaces the Hello Elementor default footer.
 * Hooked into wp_footer via textcraft-tools.php.
 *
 * @package TextCraft_Tools
 */

namespace TextCraft_Tools;

defined( 'ABSPATH' ) || exit;

class Footer_Builder {

    public static function init(): void {
        add_filter( 'hello_elementor_display_header_footer', [ __CLASS__, 'maybe_hide_theme_footer' ], 999 );
        add_action( 'wp_footer', [ __CLASS__, 'render_premium_footer' ], 1 );
    }

    public static function maybe_hide_theme_footer( bool $display ): bool {
        // We only want to hide the footer, not the header.
        // Since this filter controls both, we can't use it directly.
        // Instead, we'll use CSS to hide #site-footer.
        return $display;
    }

    public static function render_premium_footer(): void {
        $site_url = home_url();
        $home_url = $site_url;
        ?>
        <footer id="tc-footer" class="tc-footer" role="contentinfo" aria-label="Site footer">
            <div class="tc-footer-inner">

                <!-- ═══ TOP CTA BAR ═══ -->
                <div class="tc-footer-cta">
                    <div class="tc-footer-cta-glow"></div>
                    <div class="tc-footer-cta-content">
                        <h2 class="tc-footer-cta-title">Ready to Simplify Your Work?</h2>
                        <p class="tc-footer-cta-desc">Explore 65+ free browser-based tools for text, PDF, image conversion, generators, formatting, productivity and more.</p>
                        <div class="tc-footer-cta-buttons">
                            <a href="<?php echo esc_url( $site_url . '/' ); ?>" class="tc-btn tc-btn-primary">Explore All Tools</a>
                            <a href="<?php echo esc_url( $site_url . '/contact-us/' ); ?>" class="tc-btn tc-btn-ghost">Contact Us</a>
                        </div>
                    </div>
                </div>

                <!-- ═══ MAIN FOOTER GRID ═══ -->
                <div class="tc-footer-grid">

                    <!-- Column 1: Brand -->
                    <div class="tc-footer-column tc-footer-brand">
                        <div class="tc-footer-logo">
                            <span class="tc-footer-logo-icon">✦</span>
                            <span class="tc-footer-logo-text">TextCraft Tools</span>
                        </div>
                        <p class="tc-footer-brand-desc">TextCraft Tools is a collection of fast, browser-based utilities designed to simplify text editing, PDF processing, image conversion, and everyday productivity without requiring software installation.</p>
                        <ul class="tc-footer-badges" aria-label="Trust badges">
                            <li><span class="tc-badge-icon">🔒</span> Privacy First</li>
                            <li><span class="tc-badge-icon">🌐</span> Browser Based</li>
                            <li><span class="tc-badge-icon">✓</span> No Signup</li>
                            <li><span class="tc-badge-icon">♾</span> Free Forever</li>
                        </ul>
                    </div>

                    <!-- Column 2: Popular Tools -->
                    <div class="tc-footer-column tc-footer-tools">
                        <h3 class="tc-footer-title">Popular Tools</h3>
                        <ul class="tc-footer-links">
                            <li><a href="<?php echo esc_url( $site_url . '/pdf-compressor/' ); ?>">PDF Compressor</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/pdf-to-word-converter/' ); ?>">PDF to Word</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/jpg-compressor/' ); ?>">JPG Compressor</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/png-compressor/' ); ?>">PNG Compressor</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/webp-compressor/' ); ?>">WebP Compressor</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/image-to-text-ocr/' ); ?>">Image to Text</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/remove-background-from-image/' ); ?>">Background Remover</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/word-frequency-counter/' ); ?>">Word Counter</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/password-generator/' ); ?>">Password Generator</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/uuid-generator/' ); ?>">UUID Generator</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/case-converter/' ); ?>">Case Converter</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/sentence-case-converter/' ); ?>">Sentence Case</a></li>
                        </ul>
                        <a href="<?php echo esc_url( $site_url . '/' ); ?>" class="tc-footer-view-all">View All Tools →</a>
                    </div>

                    <!-- Column 3: Quick Links -->
                    <div class="tc-footer-column">
                        <h3 class="tc-footer-title">Quick Links</h3>
                        <ul class="tc-footer-links">
                            <li><a href="<?php echo esc_url( $site_url . '/' ); ?>">Home</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/about-us/' ); ?>">About Us</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/free-online-text-tools/' ); ?>">All Tools</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/tools/' ); ?>">Categories</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/blog/' ); ?>">Blog</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/contact-us/' ); ?>">Contact Us</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/accessibility-statement/' ); ?>">Accessibility Statement</a></li>
                        </ul>
                    </div>

                    <!-- Column 4: Legal -->
                    <div class="tc-footer-column">
                        <h3 class="tc-footer-title">Legal</h3>
                        <ul class="tc-footer-links">
                            <li><a href="<?php echo esc_url( $site_url . '/privacy-policy/' ); ?>">Privacy Policy</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/terms-and-conditions/' ); ?>">Terms &amp; Conditions</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/disclaimer/' ); ?>">Disclaimer</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/dmca-policy/' ); ?>">DMCA Policy</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/cookie-policy/' ); ?>">Cookie Policy</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/copyright-notice/' ); ?>">Copyright Notice</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/editorial-policy/' ); ?>">Editorial Policy</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/advertising-policy/' ); ?>">Advertising Policy</a></li>
                        </ul>
                    </div>

                    <!-- Column 5: Support -->
                    <div class="tc-footer-column">
                        <h3 class="tc-footer-title">Support</h3>
                        <ul class="tc-footer-links">
                            <li><a href="<?php echo esc_url( $site_url . '/report-a-bug/' ); ?>">Report Bug</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/suggest-a-tool/' ); ?>">Suggest Tool</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/request-a-tool/' ); ?>">Request Tool</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/feedback/' ); ?>">Feedback</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/xml-sitemap/' ); ?>">XML Sitemap</a></li>
                            <li><a href="<?php echo esc_url( $site_url . '/sitemap/' ); ?>">HTML Sitemap</a></li>
                        </ul>
                    </div>

                </div>

                <!-- ═══ BOTTOM FOOTER ═══ -->
                <div class="tc-footer-divider"></div>
                <div class="tc-footer-bottom">
                    <div class="tc-footer-copyright">
                        <p>&copy; 2026 <a href="/" title="TextCraft Tools">TextCraft Tools</a>. All Rights Reserved. Powered by <a href="https://hptechhub.com/" target="_blank" rel="noopener noreferrer" title="HP TechHub">HP TechHub</a>.</p>
                        <p class="tc-footer-made-with">Made for creators, students, professionals and businesses.</p>
                    </div>
                    <div class="tc-footer-legal-links">
                        <a href="<?php echo esc_url( $site_url . '/privacy-policy/' ); ?>">Privacy Policy</a>
                        <span class="tc-footer-sep">·</span>
                        <a href="<?php echo esc_url( $site_url . '/terms-and-conditions/' ); ?>">Terms</a>
                        <span class="tc-footer-sep">·</span>
                        <a href="<?php echo esc_url( $site_url . '/cookie-policy/' ); ?>">Cookies</a>
                        <span class="tc-footer-sep">·</span>
                        <a href="<?php echo esc_url( $site_url . '/dmca-policy/' ); ?>">DMCA</a>
                    </div>
                    <div class="tc-footer-social">
                        <a href="https://github.com/textcraft" target="_blank" rel="noopener noreferrer" aria-label="GitHub" class="tc-social-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg></a>
                        <a href="https://linkedin.com/company/textcraft" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="tc-social-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
                        <a href="https://facebook.com/textcraft" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="tc-social-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        <a href="https://x.com/textcraft" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)" class="tc-social-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                    </div>
                </div>

            </div>
        </footer>
        <?php
    }
}
