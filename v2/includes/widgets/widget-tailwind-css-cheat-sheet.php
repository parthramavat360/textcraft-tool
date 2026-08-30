<?php
declare(strict_types=1);
namespace TextCraft_Tools_Pro;
defined('ABSPATH') || exit;

class Widget_Tailwind_Css_Cheat_Sheet extends TextCraft_Tool_Base {
    public function get_name(): string { return 'tailwind_css_cheat_sheet'; }
    public function get_title(): string { return 'Tailwind CSS Cheat Sheet'; }
    public function get_icon(): string { return 'eicon-paint-brush-1'; }

    protected function render_tool_content(array $settings): void { ?>
        <div class="tc-tool-desc">Quick reference for Tailwind CSS utility classes. Searchable and copy-ready.</div>

        <div class="tc-input-group" style="margin-bottom:20px">
            <input type="text" class="tc-input" id="tailwind-search" placeholder="Search classes... (e.g. flex, grid, bg-blue)">
        </div>

        <div class="tctp-result" id="tailwind-result" style="display:block">
            <div id="tailwind-content">
                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Layout</h3>
                    <pre class="tctp-code-block"><code>/* Display */
hidden          display: none;
block           display: block;
inline-block    display: inline-block;
flex            display: flex;
grid            display: grid;
inline          display: inline;

/* Position */
relative        position: relative;
absolute        position: absolute;
fixed           position: fixed;
sticky          position: sticky;

/* Overflow */
overflow-hidden     overflow: hidden;
overflow-auto       overflow: auto;
overflow-scroll     overflow: scroll;

/* Z-Index */
z-0           z-index: 0;
z-10          z-index: 10;
z-50          z-index: 50;
z-[999]       z-index: 999;

/* Container */
container mx-auto     max-width + center</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Flexbox</h3>
                    <pre class="tctp-code-block"><code>/* Flex container */
flex              display: flex;
inline-flex       display: inline-flex;
flex-row          flex-direction: row;
flex-col          flex-direction: column;
flex-wrap         flex-wrap: wrap;
flex-nowrap       flex-wrap: nowrap;

/* Justify (main axis) */
justify-start     justify-content: flex-start;
justify-center    justify-content: center;
justify-end       justify-content: flex-end;
justify-between   justify-content: space-between;
justify-around    justify-content: space-around;
justify-evenly    justify-content: space-evenly;

/* Align (cross axis) */
items-start       align-items: flex-start;
items-center      align-items: center;
items-end         align-items: flex-end;
items-stretch     align-items: stretch;

/* Flex grow/shrink */
flex-1            flex: 1 1 0%;
flex-auto         flex: 1 1 auto;
flex-none         flex: none;
flex-grow         flex-grow: 1;
flex-shrink-0     flex-shrink: 0;

/* Gap */
gap-2             gap: 0.5rem;
gap-4             gap: 1rem;
gap-8             gap: 2rem;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Grid</h3>
                    <pre class="tctp-code-block"><code>/* Grid container */
grid              display: grid;
grid-cols-2       grid-template-columns: repeat(2, minmax(0, 1fr));
grid-cols-3       grid-template-columns: repeat(3, minmax(0, 1fr));
grid-cols-4       grid-template-columns: repeat(4, minmax(0, 1fr));
grid-cols-12      grid-template-columns: repeat(12, minmax(0, 1fr));

/* Span */
col-span-2        grid-column: span 2 / span 2;
col-span-3        grid-column: span 3 / span 3;
col-span-full     grid-column: 1 / -1;
row-span-2        grid-row: span 2 / span 2;

/* Template */
grid-rows-3        grid-template-rows: repeat(3, minmax(0, 1fr));</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Spacing</h3>
                    <pre class="tctp-code-block"><code>/* Margin */
m-0        margin: 0;
m-1        margin: 0.25rem;
m-2        margin: 0.5rem;
m-4        margin: 1rem;
m-8        margin: 2rem;
mx-auto    margin-left: auto; margin-right: auto;
mt-4       margin-top: 1rem;
mb-4       margin-bottom: 1rem;
ml-4       margin-left: 1rem;
mr-4       margin-right: 1rem;

/* Padding */
p-0        padding: 0;
p-1        padding: 0.25rem;
p-2        padding: 0.5rem;
p-4        padding: 1rem;
p-6        padding: 1.5rem;
p-8        padding: 2rem;
px-4       padding-left: 1rem; padding-right: 1rem;
py-4       padding-top: 1rem; padding-bottom: 1rem;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Sizing</h3>
                    <pre class="tctp-code-block"><code>/* Width */
w-0          width: 0;
w-1          width: 0.25rem;
w-4          width: 1rem;
w-8          width: 2rem;
w-1/2        width: 50%;
w-full       width: 100%;
w-screen     width: 100vw;
w-fit        width: fit-content;
w-auto       width: auto;

/* Height */
h-0          height: 0;
h-1          height: 0.25rem;
h-4          height: 1rem;
h-8          height: 2rem;
h-full       height: 100%;
h-screen     height: 100vh;
h-fit        height: fit-content;

/* Min/Max */
min-w-0      min-width: 0;
max-w-sm     max-width: 24rem;
max-w-md     max-width: 28rem;
max-w-lg     max-width: 32rem;
max-w-xl     max-width: 36rem;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Typography</h3>
                    <pre class="tctp-code-block"><code>/* Font size */
text-xs       font-size: 0.75rem;
text-sm       font-size: 0.875rem;
text-base     font-size: 1rem;
text-lg       font-size: 1.125rem;
text-xl       font-size: 1.25rem;
text-2xl       font-size: 1.5rem;
text-3xl       font-size: 1.875rem;
text-4xl       font-size: 2.25rem;
text-5xl       font-size: 3rem;

/* Font weight */
font-thin       font-weight: 100;
font-light      font-weight: 300;
font-normal     font-weight: 400;
font-medium     font-weight: 500;
font-semibold   font-weight: 600;
font-bold       font-weight: 700;
font-extrabold  font-weight: 800;

/* Text alignment */
text-left      text-align: left;
text-center    text-align: center;
text-right     text-align: right;
text-justify   text-align: justify;

/* Text color */
text-black      color: #000;
text-white      color: #fff;
text-gray-500   color: #6b7280;
text-red-500    color: #ef4444;
text-blue-500   color: #3b82f6;
text-green-500  color: #22c55e;

/* Line height */
leading-none     line-height: 1;
leading-tight     line-height: 1.25;
leading-normal   line-height: 1.5;
leading-loose    line-height: 2;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Background & Colors</h3>
                    <pre class="tctp-code-block"><code>/* Background color */
bg-black        background-color: #000;
bg-white        background-color: #fff;
bg-gray-100     background-color: #f3f4f6;
bg-blue-500     background-color: #3b82f6;
bg-red-500      background-color: #ef4444;
bg-green-500    background-color: #22c55e;

/* Gradients */
bg-gradient-to-r  background: linear-gradient(to right, ...)
bg-gradient-to-br background: linear-gradient(to bottom right, ...)

/* Border color */
border-gray-200    border-color: #e5e7eb;
border-blue-500    border-color: #3b82f6;
border-red-500     border-color: #ef4444;

/* Ring (outline) */
ring-2            box-shadow: 0 0 0 2px ...
ring-blue-500     box-shadow: 0 0 0 2px #3b82f6;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Borders & Rounded</h3>
                    <pre class="tctp-code-block"><code>/* Border width */
border           border-width: 1px;
border-0         border-width: 0;
border-2         border-width: 2px;
border-4         border-width: 4px;

/* Rounded */
rounded          border-radius: 0.25rem;
rounded-md       border-radius: 0.375rem;
rounded-lg       border-radius: 0.5rem;
rounded-xl       border-radius: 0.75rem;
rounded-2xl      border-radius: 1rem;
rounded-full     border-radius: 9999px;

/* Shadow */
shadow-sm        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
shadow           box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
shadow-md        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
shadow-lg        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
shadow-xl        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Transitions & Animation</h3>
                    <pre class="tctp-code-block"><code>/* Transition */
transition-all        transition: all 150ms ease;
transition-colors    transition: background-color, border-color, color;
transition-opacity   transition: opacity;
transition-shadow    transition: box-shadow;
duration-200         transition-duration: 200ms;
duration-300         transition-duration: 300ms;
duration-500         transition-duration: 500ms;

/* Transform */
scale-0         transform: scale(0);
scale-100       transform: scale(1);
scale-110       transform: scale(1.1);
rotate-45       transform: rotate(45deg);
rotate-90       transform: rotate(90deg);
translate-y-2   transform: translateY(0.5rem);
translate-y-4   transform: translateY(1rem);

/* Hover */
hover:bg-blue-600     background on hover
hover:text-white      text color on hover
hover:scale-105       scale on hover
hover:shadow-lg       shadow on hover

/* Cursor */
cursor-pointer   cursor: pointer;
cursor-default   cursor: default;
cursor-not-allowed cursor: not-allowed;</code></pre>
                </div>

                <div class="tctp-cheat-section" data-searchable>
                    <h3 style="color:#0b1220;margin:20px 0 10px;font-size:16px">Responsive & Responsive Prefixes</h3>
                    <pre class="tctp-code-block"><code>/* Breakpoints */
sm:    @media (min-width: 640px)
md:    @media (min-width: 768px)
lg:    @media (min-width: 1024px)
xl:    @media (min-width: 1280px)
2xl:   @media (min-width: 1536px)

/* Example: responsive flex */
<div class="flex flex-col md:flex-row gap-4">
  <div class="w-full md:w-1/3">Sidebar</div>
  <div class="w-full md:w-2/3">Main</div>
</div>

/* Hide on mobile, show on desktop */
hidden md:block

/* Show on mobile, hide on desktop */
block md:hidden

/* Responsive padding */
p-4 md:p-8 lg:p-12</code></pre>
                </div>
            </div>
        </div>
    <?php }

    protected function render_result_content(array $settings): void { ?>
        <div></div>
    <?php }
}
