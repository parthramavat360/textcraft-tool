/**
 * Cheat Sheet — 100% client-side reference content
 */
(function(){
  if(!window.TCTP) return;
  const $ = s => document.querySelector(s);

  const SHEETS = {
    markdown: `<h3>Markdown Cheat Sheet</h3>
<table class="tc-cs-table"><thead><tr><th>Element</th><th>Markdown</th><th>Result</th></tr></thead><tbody>
<tr><td>Heading 1</td><td><code># Heading 1</code></td><td style="font-size:18px;font-weight:700">Heading 1</td></tr>
<tr><td>Heading 2</td><td><code>## Heading 2</code></td><td style="font-size:16px;font-weight:700">Heading 2</td></tr>
<tr><td>Heading 3</td><td><code>### Heading 3</code></td><td style="font-size:14px;font-weight:700">Heading 3</td></tr>
<tr><td>Bold</td><td><code>**bold text**</code></td><td><b>bold text</b></td></tr>
<tr><td>Italic</td><td><code>*italic text*</code></td><td><i>italic text</i></td></tr>
<tr><td>Bold + Italic</td><td><code>***bold italic***</code></td><td><b><i>bold italic</i></b></td></tr>
<tr><td>Strikethrough</td><td><code>~~strikethrough~~</code></td><td><s>strikethrough</s></td></tr>
<tr><td>Code</td><td><code>\`inline code\`</code></td><td><code>inline code</code></td></tr>
<tr><td>Code Block</td><td><code>\`\`\`\\ncode block\\n\`\`\`</code></td><td><pre style="margin:0;padding:4px;background:#0d1321;border-radius:4px">code block</pre></td></tr>
<tr><td>Link</td><td><code>[text](url)</code></td><td><a href="#">text</a></td></tr>
<tr><td>Image</td><td><code>![alt](url)</code></td><td><i>image</i></td></tr>
<tr><td>Unordered List</td><td><code>- item\\n- item</code></td><td>• item</td></tr>
<tr><td>Ordered List</td><td><code>1. item\\n2. item</code></td><td>1. item</td></tr>
<tr><td>Task List</td><td><code>- [x] done\\n- [ ] todo</code></td><td>☑ done ☐ todo</td></tr>
<tr><td>Blockquote</td><td><code>> quote</code></td><td style="border-left:3px solid #0b1220;padding-left:8px;color:#94a3b8;font-style:italic">quote</td></tr>
<tr><td>Horizontal Rule</td><td><code>---</code></td><td><hr style="border:1px solid #1e3050;margin:4px 0"></td></tr>
<tr><td>Table</td><td><code>| H1 | H2 |\\n|---|---|\\n| a | b |</code></td><td><table style="border-collapse:collapse;font-size:12px"><tr><td style="border:1px solid #1e3050;padding:2px 6px;font-weight:700">H1</td><td style="border:1px solid #1e3050;padding:2px 6px;font-weight:700">H2</td></tr><tr><td style="border:1px solid #1e3050;padding:2px 6px">a</td><td style="border:1px solid #1e3050;padding:2px 6px">b</td></tr></table></td></tr>
<tr><td>Footnote</td><td><code>Text[^1]</code></td><td>Text<sup>1</sup></td></tr>
<tr><td>Emoji</td><td><code>:smile:</code></td><td>😀</td></tr>
</tbody></table>`,

    json: `<h3>JSON Cheat Sheet</h3>
<table class="tc-cs-table"><thead><tr><th>Element</th><th>Syntax</th><th>Example</th></tr></thead><tbody>
<tr><td>Object</td><td><code>{ "key": value }</code></td><td><code>{ "name": "John" }</code></td></tr>
<tr><td>Array</td><td><code>[ value, value ]</code></td><td><code>[1, 2, 3]</code></td></tr>
<tr><td>String</td><td><code>"text"</code></td><td><code>"Hello World"</code></td></tr>
<tr><td>Number</td><td><code>42</code> or <code>3.14</code></td><td><code>42</code>, <code>-7</code>, <code>0.5</code></td></tr>
<tr><td>Boolean</td><td><code>true</code> / <code>false</code></td><td><code>true</code></td></tr>
<tr><td>Null</td><td><code>null</code></td><td><code>null</code></td></tr>
<tr><td>Nested Object</td><td><code>{ "k": { "k2": v } }</code></td><td><code>{ "user": { "name": "Jo" } }</code></td></tr>
<tr><td>Array of Objects</td><td><code>[ { "k": v } ]</code></td><td><code>[ {"id": 1}, {"id": 2} ]</code></td></tr>
<tr><td>String (escaped)</td><td><code>"line1\\nline2"</code></td><td><code>"She said \\"hi\\""</code></td></tr>
</tbody></table>
<h4 style="margin-top:16px">Common Patterns</h4>
<pre class="tc-cs-code">// Nested structure
{
  "name": "John",
  "age": 30,
  "active": true,
  "address": {
    "city": "NYC",
    "zip": "10001"
  },
  "hobbies": ["reading", "coding"]
}

// Array of objects
[
  { "id": 1, "name": "Item 1" },
  { "id": 2, "name": "Item 2" }
]

// API response pattern
{
  "status": "success",
  "data": { ... },
  "meta": { "total": 100, "page": 1 }
}</pre>
<h4 style="margin-top:16px">Validation Rules</h4>
<ul style="font-size:13px;color:var(--body);padding-left:20px;line-height:2">
<li>Keys must be double-quoted strings</li>
<li>No trailing commas allowed</li>
<li>No comments allowed</li>
<li>Strings must use double quotes (not single)</li>
<li>Numbers cannot have leading zeros</li>
<li>Unicode is allowed: <code>\\u0041</code> = "A"</li>
</ul>`,

    regex: `<h3>Regex Cheat Sheet</h3>
<table class="tc-cs-table"><thead><tr><th>Pattern</th><th>Matches</th><th>Example</th></tr></thead><tbody>
<tr><td><code>.</code></td><td>Any character (except newline)</td><td><code>a.c</code> → "abc", "a1c"</td></tr>
<tr><td><code>\\d</code></td><td>Digit [0-9]</td><td><code>\\d+</code> → "123"</td></tr>
<tr><td><code>\\w</code></td><td>Word char [a-zA-Z0-9_]</td><td><code>\\w+</code> → "hello_42"</td></tr>
<tr><td><code>\\s</code></td><td>Whitespace</td><td><code>a\\sb</code> → "a b"</td></tr>
<tr><td><code>\\b</code></td><td>Word boundary</td><td><code>\\bcat\\b</code> → "cat" in "the cat"</td></tr>
<tr><td><code>^</code></td><td>Start of string/line</td><td><code>^Hello</code> → "Hello..."</td></tr>
<tr><td><code>$</code></td><td>End of string/line</td><td><code>world$</code> → "...world"</td></tr>
<tr><td><code>*</code></td><td>0 or more</td><td><code>ab*c</code> → "ac", "abc", "abbc"</td></tr>
<tr><td><code>+</code></td><td>1 or more</td><td><code>ab+c</code> → "abc", "abbc" (not "ac")</td></tr>
<tr><td><code>?</code></td><td>0 or 1 (optional)</td><td><code>colou?r</code> → "color", "colour"</td></tr>
<tr><td><code>{n}</code></td><td>Exactly n times</td><td><code>\\d{3}</code> → "123"</td></tr>
<tr><td><code>{n,m}</code></td><td>Between n and m times</td><td><code>\\d{2,4}</code> → "12", "1234"</td></tr>
<tr><td><code>[abc]</code></td><td>Character set (a OR b OR c)</td><td><code>[aeiou]</code> → any vowel</td></tr>
<tr><td><code>[^abc]</code></td><td>Negated set (NOT a,b,c)</td><td><code>[^0-9]</code> → not a digit</td></tr>
<tr><td><code>(abc)</code></td><td>Capture group</td><td><code>(\\d+)</code> → captures number</td></tr>
<tr><td><code>(?:abc)</code></td><td>Non-capturing group</td><td><code>(?:ab)+</code></td></tr>
<tr><td><code>a|b</code></td><td>Alternation (a OR b)</td><td><code>cat|dog</code> → "cat" or "dog"</td></tr>
<tr><td><code>(?=...)</code></td><td>Positive lookahead</td><td><code>\\d(?=px)</code> → "5" in "5px"</td></tr>
<tr><td><code>(?!...)</code></td><td>Negative lookahead</td><td><code>\\d(?!px)</code> → "5" in "5em"</td></tr>
</tbody></table>
<h4 style="margin-top:16px">Flags</h4>
<table class="tc-cs-table"><thead><tr><th>Flag</th><th>Name</th><th>Description</th></tr></thead><tbody>
<tr><td><code>g</code></td><td>Global</td><td>Find all matches, not just first</td></tr>
<tr><td><code>i</code></td><td>Case-insensitive</td><td>Match both upper and lowercase</td></tr>
<tr><td><code>m</code></td><td>Multiline</td><td>^ and $ match start/end of each line</td></tr>
<tr><td><code>s</code></td><td>Dotall</td><td>. matches newlines too</td></tr>
</tbody></table>
<h4 style="margin-top:16px">Common Patterns</h4>
<pre class="tc-cs-code">Email:      /^[\\w.-]+@[\\w.-]+\\.[a-zA-Z]{2,}$/
Phone:      /^\\+?\\d{1,3}?[\\s.-]?\\(?\\d{1,4}\\)?[\\s.-]?\\d{1,4}[\\s.-]?\\d{1,9}$/
URL:        /^https?:\\/\\/[\\w.-]+\\.[a-z]{2,}(\\/\\S*)?$/
IPv4:       /^\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}$/
Hex Color:  /^#?([a-fA-F0-9]{3}|[a-fA-F0-6]{6})$/
Date:       /^\\d{4}-\\d{2}-\\d{2}$/
Username:   /^[a-zA-Z0-9_]{3,20}$/
Password:   /^(?=.*[A-Z])(?=.*[\\d])(?=.*[!@#$%]).{8,}$/</pre>`
  };

  function getSheet(){ return document.querySelector('.tc-modes[data-group="cs-type"] .sel')?.dataset.val || 'markdown'; }

  document.addEventListener('DOMContentLoaded', function(){
    const btn = $('#tc-cs-show');
    if(!btn) return;
    btn.addEventListener('click', function(){
      const type = getSheet();
      const content = SHEETS[type] || SHEETS.markdown;
      const el = $('#tc-cs-content');
      if(el) el.innerHTML = content;
      TCTP.switchToResultTab();
      TCTP.toast(type.charAt(0).toUpperCase() + type.slice(1) + ' cheat sheet loaded','success');
    });
    var typeGroup = document.querySelector('[data-group="cs-type"]');
    if(typeGroup){ TCTP.initModeGroup(typeGroup); }
  });
})();
