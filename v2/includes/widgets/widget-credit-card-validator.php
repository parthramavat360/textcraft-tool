<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Credit_Card_Validator extends TextCraft_Tool_Base {
    protected bool $show_preview = false;
    public function get_name(): string { return 'credit_card_validator'; }
    public function get_title(): string { return 'Credit Card Validator'; }
    public function get_icon(): string { return 'eicon-credit-card'; }
    public function get_keywords(): array { return ['credit card validator','card number validator','luhn algorithm','card checker','credit card test']; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Validate credit card numbers using the Luhn algorithm. Test if a card number format is valid. Supports Visa, Mastercard, Amex, Discover, Diners, JCB, and UnionPay.</div>

        <div class="tctp-tool-body">
            <div class="tctp-ctrls">
                <div class="tc-input-group" style="flex:1;">
                    <label class="tc-label">Enter credit card number</label>
                    <input type="text" class="tc-input" id="ccv-number" placeholder="1234 5678 9012 3456" maxlength="23" autocomplete="off" inputmode="numeric">
                </div>
                <button class="tc-btn tc-btn--primary" id="ccv-validate" disabled>
                    <i class="fa-solid fa-check-circle"></i> Validate
                </button>
            </div>

            <div class="tc-ccv-quick" style="margin:8px 0;">
                <span class="tc-label" style="margin-bottom:0;">Test numbers:</span>
                <button class="tc-btn tc-btn--ghost tc-btn--sm ccv-test" data-num="4111111111111111">Visa</button>
                <button class="tc-btn tc-btn--ghost tc-btn--sm ccv-test" data-num="5555555555554444">Mastercard</button>
                <button class="tc-btn tc-btn--ghost tc-btn--sm ccv-test" data-num="378282246310005">Amex</button>
                <button class="tc-btn tc-btn--ghost tc-btn--sm ccv-test" data-num="6011111111111117">Discover</button>
                <button class="tc-btn tc-btn--ghost tc-btn--sm ccv-test" data-num="1234567890">Invalid</button>
            </div>

            <?php $this->render_progress_bar('Validating card number...'); ?>

            <div class="tctp-result" id="ccv-result" style="display:none;">
                <div id="ccv-result-card"></div>

                <div class="tctp-result-actions" style="margin-top:12px;">
                    <button class="tc-btn tc-btn--ghost tc-btn--sm" data-copy="ccv-result-text"><i class="fa-regular fa-clipboard"></i> Copy Result</button>
                </div>
            </div>
        </div>

        <script>
        (function(){
            var input=document.getElementById('ccv-number');
            var btn=document.getElementById('ccv-validate');
            if(!input||!btn) return;
            input.addEventListener('input',function(){
                this.value=this.value.replace(/[^0-9\s]/g,'');
                btn.disabled=this.value.replace(/\s/g,'').length<12;
            });
            document.querySelectorAll('.ccv-test').forEach(function(b){
                b.addEventListener('click',function(){
                    input.value=b.getAttribute('data-num');
                    btn.disabled=false;
                    btn.click();
                });
            });
        })();
        </script>
    <?php }
}