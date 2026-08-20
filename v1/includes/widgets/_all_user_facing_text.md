# User-Facing Text Extraction — textcraft-tools Widgets

## widget-all-tools-page.php

### Title/Heading
- hero_badge default: "74 Free Online Text Utilities & Tools"
- hero_title_plain default: "All"
- hero_title_gradient default: "Tools"
- hero_subtitle default: "A complete collection of free online text utilities, random generators, and browser-based tools. No account needed, no ads, and no data ever leaves your device — 100% private and secure."
- "Search Bar" (section label)
- "Page Hero" (section label)

### Labels (controls panel)
- "Badge Text"
- "Title (plain part)"
- "Title (gradient word)"
- "Subtitle"
- "Show Hero" | "Yes" / "No"
- "Search Bar" | "Show Search Bar"
- "Placeholder Text"
- "No Results Title"
- "No Results Hint"
- "Tools List"
- "Icon / Emoji"
- "Tool Name"
- "Short Description"
- "Tool URL"
- "Category"
- "Hero" (style section)
- "Tool Cards" (style section)
- "Category Headings" (style section)

### Placeholders
- "Search free online tools…" (search_placeholder default)
- placeholder for tool_url: "https://example.com/tools/my-tool/"

### Default values
- "Tool Name" (tool_name default)
- "What this tool does." (tool_desc default)
- "Case Conversion Tools" (tool_cat default)

### Category dropdown options
- "PDF Tools"
- "Image Compression Tools"
- "Image & Media Conversion Tools"
- "Case Conversion Tools"
- "Text Cleaning Tools"
- "Text Generators & Writing Tools"
- "Random Generators"
- "Text Translators & Counters"

### Empty states / No results
- "No matching tools found" (no_results_title default)
- "Try a different search term or browse the categories below" (no_results_hint default)
- "No tools found" (fallback)
- "Try a different search term" (fallback)
- "Uncategorised" (fallback)

### Other visible text
- hero section: "All" + "Tools" (rendered as h1)
- Search input aria-label: "Search tools"
- Render fallback: "Search free online tools…"
- "74 tools pre-loaded. Edit any card below, or add new tools. The Category field groups cards under the matching heading. Leave URL blank to disable the link."
- aria-label for cards: tool name
- Style labels: "Min Card Width", "Card Gap", "Card Background", "Card Border", "Hover Border & Glow", "Icon Box Background", "Icon Box Size", "Icon Font Size", "Tool Name Color", "Description Color", "Heading Color", "Badge Accent", "Section Bottom Gap", "Title Color", "Subtitle Color", "Gradient Start", "Gradient End"

### Tool card data (default_tools array)
- 74 tool entries with icon, name, desc, url
- Example: "PDF Compressor" — "Compress PDF files online with preview, compression levels, and instant browser download"
- Full range of tool names and descriptions extracted (see tool names/descs in raw data)

---

## widget-apa-format.php

### Title/Heading
- get_title: "APA Format Generator"

### Description/Intro
- "Generate APA 7th edition citations for websites, books, journal articles, and videos. Fill in the fields and get a perfectly formatted reference - no account needed, no data sent to any server."
- "Free Online APA Format Generator - Cite Sources in APA Style"

### Source Type tabs
- "Website"
- "Book"
- "Journal"
- "Video"

### Labels
- "Source Type"
- "Author Last Name"
- "Author First Initial"
- "Publication Year"
- "Article Title"
- "Website Name"
- "Page Title"
- "URL"
- "Book Title"
- "Publisher"
- "Edition (optional)"
- "Journal Name"
- "Volume"
- "Issue"
- "DOI / URL"
- "Channel / Creator Name"
- "Video Title"
- "Platform"
- "Upload Year"
- "Generated Citation"
- "Entry Format"
- "Tag Color"
- "Accent Color"

### Preset/Text
- "Tag Line" | default: "Free Online APA Format Generator"
- "Title Color", "Border Color", "Card Background" etc.

### Buttons
- "Generate Citation"
- "Copy"
- "Download .txt"

### Empty states
- "Fill in the fields above and click Generate Citation."

### Other visible text
- "Shown in a monospaced badge."
- "A short description of this feature."
- "A quick reference to all seven text case formats - learn when and how to use each one for better writing, coding, and SEO."

---

## widget-ascii-art.php

### Title/Heading
- get_title: "PixelScript – ASCII Art Generator"

### Description/Intro
- "Convert any image to detailed ASCII art online — free browser-based image to text art generator"
- "Free Online Image to ASCII Art Converter - Make ASCII Art from Photos"

### Labels
- "Output Width (chars)"
- "Color Mode" (options: "Coloured", "Monochrome")
- "ASCII Output"
- "Image Preview"
- "Character Set" (options: "Standard (Dense)", "Simple (High contrast)", "Block characters", "Minimal (. : | #)")
- "Font Size (preview)"
- "OCR Language"
- "Background Color" (options: "Light BG", "Dark BG")
- "Output Mode" (options: "Standard", "Posterised")
- "Invert"
- "Flip Upside Down"

### Buttons
- "Convert to ASCII"
- "Download .txt"
- "Copy"
- "Print"
- "Reset"
- "Clear"

### Drop zone text
- "Click to upload or drag and drop an image"
- "Upload an image above, then click Convert to ASCII."
- "Uploaded image preview"

### Other
- "Supports PNG, JPG, GIF, WebP - all processing is done in your browser, nothing is uploaded"
- "Width", "Height", "Size"

---

## widget-case-converter.php

### Title/Heading
- "Case Converter" / "Text Case Guide & Reference"

### Description/Intro
- "Convert text between uppercase, lowercase, sentence case, title case, and more. This free online case changer works entirely in your browser - no data is sent to any server."
- "Free Online Case Converter - Change Text Case Instantly"

### Labels
- "Input Text"
- "Converted"
- "Case conversion options"

### Case mode buttons
- "UPPERCASE"
- "lowercase"
- "Title Case"
- "Sentence case"
- "aLtErNaTiNg"
- "Inverse Case"
- "Capitalized Case"

### Buttons
- "Convert"
- "Copy"
- "Clear"

### Stats
- "Words", "Characters", "Lines", "Sentences"

### Other text
- "Type or paste your text here to convert case."
- "What Does Each Case Mean?"
- Case descriptions for each mode (see raw data)

---

## widget-character-remover.php

### Title/Heading
- "Character Remover"

### Description/Intro
- "Remove unwanted characters from your text in seconds. Choose from quick presets or type your own - all processing is done securely in your browser."
- "Free Online Character Remover - Delete Unwanted Characters from Text"

### Labels
- "Input Text"
- "Characters to Remove"
- "Case-sensitive removal"
- "Presets"
- "Show Preset Buttons"

### Buttons
- "Remove Characters"
- "Copy"
- "Clear"

### Quick preset buttons
- "Numbers (0-9)", "Punctuation", "Special Chars", "Whitespace", "Vowels", "Consonants", "Alphabet", "Quotes", "Braces", "Symbols"

### Placeholder
- "Paste or type your text here to remove specific characters."
- "Type characters to remove, e.g. @#\$ or spaces"

### Other
- "Characters Removed:", "Matches Found:", "Chars"

---

## widget-delete-pdf-pages.php

### Title/Heading
- "Delete PDF Pages"

### Description/Intro
- "Delete PDF pages online — select and remove pages locally in your browser"
- "Free Online PDF Page Remover - Delete PDF Pages"

### Labels
- "Select Pages to Delete"
- "Delete Mode" (options: "Delete Selected", "Keep Only Selected Pages")
- "Delete Page Range"
- "Delete Specific Pages"
- "Apply to Specific Pages"

### Buttons
- "Delete Selected"
- "Delete Pages"
- "Download PDF"
- "Download All"
- "Reset"
- "Undo"

### Drop zone
- "Click to upload or drag & drop a PDF file"
- "PDF files only - up to 10 files at once"

### Info text
- "Select specific pages to remove - all processing is done locally in your browser for privacy"
- "Leave empty for all pages, or specify: 1,3,5-8"
- "Examples: 1,3,5 or 1-10 or 1,3,5-8"
- "e.g. 1-3, 5, 7-9"

### Status
- "Uploading PDF: 0%"
- "Pages Deleted"
- "Loaded PDF Files"
- "Total Pages"

---

## widget-duplicate-line.php

### Title/Heading
- "Duplicate Line Remover"

### Description/Intro
- "Delete duplicate lines from any text - free online duplicate remover"
- "Free Online Duplicate Line Remover - Remove Repeated Lines"

### Labels
- "Your Text / List"
- "Duplicates Removed"
- "Options" (checkboxes: "Sort alphabetically", "Trim whitespace", "Remove blank lines", "Ignore case")
- "Separator" (options: "New Line", "Comma", "Space", "Custom")

### Buttons
- "Remove Duplicates"
- "Copy Result"
- "Download .txt"
- "Sort Lines"
- "Clear All"

### Placeholder
- "Paste or type text containing duplicate lines."

### Empty states
- "Unique Lines"
- "Duplicates"

### Other
- "Remove repeated lines from any text in one click..."

---

## widget-duplicate-word.php

### Title/Heading
- "Duplicate Word Finder"

### Description/Intro
- "Find duplicate words in any text with a single click. See frequency bars for every word and ignore common stop words - all processing stays private in your browser."
- "Free Online Duplicate Word Finder - Detect Repeated Words"

### Labels
- "Your Text"
- "Duplicate Words"
- "Unique Words"
- "Ignore common words (the, a, is.)"
- "Case-sensitive"
- "Min Word Length"

### Buttons
- "Find Duplicates"
- "Copy"
- "Clear"

### Placeholder
- "Paste or type text to find duplicate words and analyse word frequency."

### Other
- "Total Words", "Unique Words", "Duplicates", "Matches Found"
- "Least frequent", "Most frequent", "Longest"
- "Top Words"

---

## widget-em-dash-remover.php

### Title/Heading
- "Em Dash Remover"

### Description/Intro
- "Remove or replace em and en dashes in your text - free online dash remover"
- "Free Online Em Dash Remover - Replace or Remove Dashes"

### Labels
- "Input Text"
- "Replace With"
- "Replace all occurrences"
- "Normalize Unicode (smart quotes → straight)"

### Buttons
- "Remove"
- "Copy"
- "Clear"

### Options
- Dash replacement options: "Nothing (remove)", "Hyphen (-)", "Space", "Custom."

### Placeholder
- "Type or paste your text here."

### Other
- "Replacements Made"

---

## widget-features-section.php

### Title/Heading
- "Features Section"

### Labels
- "Feature Cards"
- "Feature Title" (default: "Why TextCraft Tools?")
- "Show Badge"
- "Tag Line" (default: "100% Private & Secure")
- "Header"
- "Section Header"
- "Card Title"
- "Description"
- "Icon / Emoji"

### Feature cards (default)
- "Built for Speed, Privacy & Simplicity" — "No bloat, no distractions. Just the fastest, most private free online text tools and random generators - all running in your browser."
- "Accessible by Design" — "Built with ARIA labels, keyboard shortcuts, and full screen-reader support - every free tool is usable by everyone."
- "Instant Results" — "Results appear the moment you click. No loading, no waiting - pure browser-side speed for every online tool."
- "Live Statistics" — "Character, word, sentence, and line counts update live as you type or convert in any browser-based tool."
- "100% Private & Secure" — "Your data never leaves your device. All processing happens locally in your browser - no uploads, no servers, total privacy."
- "Explore More Free Online Tools"

### Style labels
- "Accent Color", "Card Background", "Card Border Color", "Card Border Radius", "Card Padding", "Card Gap", "Icon Box Size", "Icon Font Size", "Card Title Color", "Description Color", "Badge Accent", "Section Padding", "Heading Color", "Tag Color", "Card Radius", "Section"

---

## widget-find-replace.php

### Title/Heading
- "Find and Replace Text"

### Description/Intro
- "Search and replace words or phrases in any text. Supports case-sensitive matching, whole-word mode, and regular expressions. Your text stays private and is processed entirely in your browser."
- "Free Online Find and Replace - Search and Replace Text Instantly"

### Labels
- "Find"
- "Replace With"
- "Use regex"
- "Case-sensitive"
- "Whole word only"
- "Replace all occurrences"

### Buttons
- "Replace"
- "Replace All"
- "Copy"
- "Clear"

### Placeholder
- "Paste or type the text you want to search through."

### Stats
- "Matches Found", "Replacements Made"

### Other
- "Input Text", "Replace all occurrences"

---

## widget-gif-compressor.php

### Title/Heading
- "GIF Compressor"

### Description/Intro
- "Compress GIF images online with previews, tab cache, and ZIP batch download"
- "Free Online JPG to GIF Converter - Browser-Based Image Tool" (note: title mismatch)

### Labels
- "Image Quality"
- "Max Image Side"
- "Width"
- "Output Format" (options: "Animated GIF", "Individual GIFs", "All frames in one", "One GIF per image")
- "Loop Count" (options: "Loop forever", "Play once", "Play 3 times", "Play 5 times", "Play 10 times")
- "Frame Delay in Milliseconds"
- "Colour Depth (GIF palette)"
- "Colour Levels (posterise)"
- "Enable dithering (better gradients)"
- "Keep original size"
- "Resize (optional)"

### Drop zone
- "Click to upload or drag & drop GIF images"

### Buttons
- "Compress GIF"
- "Download All (ZIP)"
- "Download"
- "Clear All"

### Info text
- "GIF supports a maximum of 256 colors - fewer colors produce smaller file sizes..."

### Status
- "Images Loaded", "Total Files", "Compressed Size", "Original Size"

### Other
- "Compressing..."
- "Ready"
- "Uploading files: 0%"

---

## widget-heic-to-jpg.php

### Title/Heading
- "HEIC to JPG Converter"

### Description/Intro
- "Convert HEIC to JPG online - transform iPhone HEIC photos into universal JPEG format"
- "Free Online HEIC to JPG Converter - Browser-Based Image Tool"

### Labels
- "Output JPG Quality"
- "Background Color"
- "Choose a background color to fill transparent areas before exporting to JPG - white works best for most images"
- "Keep converted HEIC files in browser cache"
- "Add "converted_" prefix to filenames"

### Drop zone
- "Click to upload or drag & drop HEIC files"
- "HEIC and HEIF images only - converted locally in your browser"

### Buttons
- "Convert All"
- "Download All (ZIP)"
- "Download"

### Info text
- "HEIC export depends on your browser support..."
- "HEIC support varies by browser..."

### Status
- "Checking HEIC export support..."
- "Converted HEIC Files"
- "Converting..."
- "Total HEIC Size"

---

## widget-heic-to-png.php

### Title/Heading
- get_title: "HEIC to PNG Converter"

### Description/Intro
- "Convert HEIC to PNG online - transform iPhone HEIC photos into lossless PNG format"
- "Free Online HEIC to JPG Converter" (note: description may differ)

### Labels
- "Output Image Quality"
- "Background Color"
- "HEIC does not support transparency in this browser-based workflow..."
- "Keep converted HEIC files in browser cache"
- "Add "converted_" prefix to filenames"

### Drop zone
- "Click to upload or drag & drop HEIC files"

### Buttons
- "Convert All to PNG"
- "Download All (ZIP)"
- "Download"

### Info text
- "PNG keeps transparency when available..."

### Status
- "Converted HEIC Files", "Converting..."

---

## widget-heic-to-svg.php

### Title/Heading
- "HEIC to SVG Converter"

### Description/Intro
- "Convert HEIC to SVG online - transform iPhone HEIC photos into SVG wrapper files"
- "Free Online PNG to SVG Converter" (note: label)

### Labels
- "SVG Output Mode" (options: "Embedded", "Base64 image tag")
- "Make SVG responsive (no fixed size)"
- "Include basic SVG description metadata"
- "Include generation timestamp"
- "SVG Accessibility Label"
- "Output JPG Quality" (maybe wrong — need re-check)
- "Add "converted_" prefix to filenames"
- "Background Color"
- "Choose a background color to fill transparent areas..."

### Drop zone
- "Click to upload or drag & drop HEIC files"

### Buttons
- "Convert All"
- "Download All (ZIP)"
- "Download"

### Info text
- "This exports an SVG wrapper containing the converted image data..."
- "Responsive SVG uses a viewBox..."

### Status
- "Converted SVG Files"

---

## widget-image-to-text.php

### Title/Heading
- "TextLens — Image to Text"

### Description/Intro
- "Extract text from images and photos using browser-based OCR — no uploads required"
- "Free Online Background Remover from Image" (note: incorrect description)

### Labels
- "OCR Language"
- "Extracted Text"
- "Uploaded image preview"

### Drop zone
- "Click to upload or drag and drop an image"
- "Upload an image - click to browse or drag and drop"
- "PNG, JPG, GIF, WebP, BMP, TIFF - all processed in your browser"

### Buttons
- "Extract Text"
- "Copy"
- "Download .txt"
- "Clear"

### Status
- "Initialising OCR engine..."
- "Loading images..."
- "Confidence"
- "Best results: clear photos, high-contrast scans, printed text"

### Empty states
- "Extracted text will appear here after OCR completes."

---

## widget-invisible-text.php

### Title/Heading
- "Invisible Text Generator"

### Description/Intro
- "Generate invisible Unicode characters - zero-width spaces, Braille blanks, and more. Use them for usernames, social media profiles, or anywhere you need blank text. All generated right in your browser, securely and privately."
- "Free Online Invisible Text Generator - Create Blank Characters"

### Labels
- "Generate Multiple Invisible Characters"
- "Unicode Symbols:"
- "Character Set" (options: "Zero Width Space (U+200B)", "Braille Blank (U+2800)", "Hangul Filler (U+3164)", "Block characters", "Colour blocks")
- "Entry Format" (options: "Abbreviated | Jan", "Full Name | January", "Name + Number | January (1)", "Month Number | 01", "UPPERCASE | JANUARY", "Number (no pad) | 1")
- "How Many" (number input)
- "Separator:" (options: "Space", "New Line", "Comma", "Dash", "Custom")
- "All 12 Months"

### Buttons
- "Generate Months"
- "Copy All"
- "Copy"

### Other text
- "Click any Copy button above to copy invisible characters..."
- "Paste any emoji or icon character."

---

## widget-jpg-compressor.php

### Title/Heading
- "JPG Compressor"

### Description/Intro
- "Compress JPG images online with previews, tab cache, and ZIP batch download"
- "Free Online JPG to PDF Converter" (note: wrong description)

### Labels
- "JPG Quality"
- "Max Image Side"
- "Quality"
- "Resize (optional)"
- "Keep original size"
- "Compression"
- "Images"
- "Add "converted_" prefix to filenames"
- "Show file size savings"

### Drop zone
- "Click to upload or drag & drop JPG files"
- "JPG and JPEG files - convert up to 20 images at once - processed entirely in your browser with no server uploads"

### Buttons
- "Compress JPG"
- "Download All (ZIP)"
- "Clear"

### Info text
- "Higher quality produces sharper JPG images, but results in larger file sizes..."
- "Images are saved in this browser tab so they remain after reload..."

### Status
- "Compressed Size", "Original Size", "Images Loaded", "Ready to compress"

---

## widget-jpg-to-avif.php

### Title/Heading
- "AviForge — JPG to AVIF"

### Description/Intro
- "Convert JPG to AVIF online - next-gen image format for better compression"
- "Free Online JPG to AVIF Converter - Browser-Based Image Tool"

### Labels
- "Output JPG Quality"
- "AVIF Quality"
- "Quality (CRF - lower = better)"
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add "converted_" prefix to filenames"
- "Extra Options"

### Drop zone
- "Click to upload or drag & drop JPG files"
- "JPG and JPEG files - convert up to 20 images at once - processed entirely in your browser"
- "Supports JPG, JPEG, PNG, and WebP - converted in your browser with nothing uploaded to any server"
- "Supports JPG, JPEG, PNG, and WebP - converted to AVIF in your browser with nothing uploaded to any server"

### Buttons
- "Convert All"
- "Download All (ZIP)"
- "Download"

### Status
- "Converted JPG Files", "Converting...", "Total Size"

---

## widget-jpg-to-gif.php

### Title/Heading
- "MotionConvert — JPG to GIF"

### Description/Intro
- "Convert JPG to GIF online - browser-based image format conversion tool"
- "Free Online JPG to GIF Converter - Browser-Based Image Tool"

### Labels
- "Output Format" (options: "Animated GIF", "Individual GIFs", "All frames in one", "One GIF per image")
- "Frame Delay in Milliseconds"
- "Loop Count" (options: "Loop forever", "Play once", "Play 3 times", "Play 5 times", "Play 10 times")
- "Colour Depth (GIF palette)"
- "Colour Levels (posterise)"
- "Enable dithering (better gradients)"
- "Resize (optional)"
- "Max Image Side"
- "Resize to width"
- "Keep original size"
- "Max Image Side"
- "Add custom prefix"

### Drop zone
- "Upload JPG images - click to browse or drag and drop to convert to GIF online"
- "JPG and JPEG files - up to 20 images - processed entirely in your browser with no server uploads"

### Buttons
- "Convert to GIF"
- "Download All as ZIP"
- "Download"

### Info text
- "Single images become static GIFs - multiple images create an animated GIF..."

### Status
- "Loading images: 0%"
- "Converting..."
- "Loaded Images"

---

## widget-jpg-to-heic.php

### Title/Heading
- "AppleFrame — JPG to HEIC"

### Description/Intro
- "Convert JPG to HEIC online — create Apple-compatible HEIC files from your images"
- "Free Online JPG to HEIC Converter - Browser-Based Image Tool"

### Labels
- "Output HEIC Quality"
- "Background Color"
- "Transparent areas are filled with your chosen background color before JPG export..."
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add "converted_" prefix to filenames"
- "Keep converted HEIC files in browser cache"

### Drop zone
- "Click to upload or drag & drop JPG files"

### Buttons
- "Convert to HEIC"
- "Download All (ZIP)"
- "Download"

### Info text
- "HEIC export depends on your browser support..."

### Status
- "Converted JPG Files", "Checking HEIC export support..."

---

## widget-jpg-to-pdf.php

### Title/Heading
- "JPG to PDF"

### Description/Intro
- "Combine multiple JPG images into a single PDF document - all done securely in your browser"
- "Free Online JPG to PDF Converter - Convert Images to PDF"

### Labels
- "Output PDF Size"
- "Quality"
- "Higher quality preserves more image detail but creates a larger PDF..."
- "Compression Level"
- "Output Compression Level (0 = largest file, 9 = smallest file)"

### Drop zone
- "Click to upload or drag & drop JPG images"
- "Click or drag JPG images to convert them to PDF"
- "Up to 20 JPG and JPEG files - processed entirely in your browser with no server uploads"

### Buttons
- "Convert to PDF"
- "Download PDF"
- "Reset"

### Status
- "Converting...", "Loaded Images", "Total Pages"

---

## widget-jpg-to-png.php

### Title/Heading
- "SnapConvert — JPG to PNG"

### Description/Intro
- "Convert JPG to PNG online — batch convert images to lossless PNG format"
- "Free Online JPG to PNG Converter - Browser-Based Image Tool"

### Labels
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add "snapconvert_" prefix to filenames"
- "Keep converted PNG files in browser cache"

### Drop zone
- "Upload JPG images - click to browse or drag and drop to convert to PNG online"
- "JPG and JPEG files - convert up to 20 images at once - processed entirely in your browser"

### Buttons
- "Convert to PNG"
- "Download All (ZIP)"
- "Download"

### Status
- "Converted PNG Files", "Converting...", "Total PNG Size"

---

## widget-jpg-to-svg.php

### Title/Heading
- "VectorTrace — JPG to SVG"

### Description/Intro
- "Convert JPG to SVG online — transform images into vector-style output"
- "Free Online JPG to SVG Converter - Browser-Based Image Tool"

### Labels
- "SVG Output Mode" (options: "Embedded", "Base64 image tag")
- "Make SVG responsive (no fixed size)"
- "Include basic SVG description metadata"
- "Include generation timestamp"
- "SVG Accessibility Label"
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add custom prefix"

### Drop zone
- "Upload JPG images - click to browse or drag and drop to convert to SVG online"
- "JPG and JPEG files - up to 20 images - processed entirely in your browser with no server uploads"

### Buttons
- "Convert to SVG"
- "Download All (ZIP)"
- "Download"

### Info text
- "SVG embeds your image as a base64 data URI..."

### Status
- "Converted SVG Files"

---

## widget-jpg-to-webp.php

### Title/Heading
- "SwiftWebP — JPG to WebP"

### Description/Intro
- "Convert JPG to WebP online — create smaller, faster-loading web images instantly"
- "Free Online JPG to WebP Converter - Browser-Based Image Tool"

### Labels
- "WebP Quality"
- "Output WebP Quality"
- "80-90% is the sweet spot for web use - great balance of quality and file size reduction"
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add "swiftwebp_" prefix to filenames"
- "Try higher-fidelity WebP export when browser supports it"

### Drop zone
- "Upload JPG images - click to browse or drag and drop to convert to WebP online"
- "JPG and JPEG files - up to 20 images - processed entirely in your browser with no server uploads"

### Buttons
- "Convert to WebP"
- "Download All (ZIP)"
- "Download"

### Status
- "Converted JPG Files", "Converting...", "Total JPG Size"

---

## widget-nato-phonetic.php

### Title/Heading
- "NATO Phonetic Alphabet"

### Description/Intro
- "Translate text into the official NATO phonetic alphabet - Alpha, Bravo, Charlie. Perfect for spelling out names, call signs, codes, or any communication that needs crystal-clear clarity."
- "Free Online NATO Phonetic Alphabet Translator - Spell Words Clearly"

### Labels
- "English Text"
- "NATO Translation"
- "NATO Phonetic Alphabet Reference"
- "NATO Alphabet"
- "Common Phonetic Patterns"
- "Sound Guide"
- "Phonetic Result"
- "Format" (options: "Simplified", "Standard", "Full (8 groups)", "Initials (3)")

### Buttons
- "Translate"
- "Copy"
- "Clear"

### Placeholder
- "Type or paste text to translate. e.g. Hello World"
- "Type or paste words to get their phonetic spelling. e.g. Hello, beautiful, necessary"

### Empty states
- "NATO translation will appear here."

### Other
- "Phonetic spelling will appear here."

---

## widget-online-notepad.php

### Title/Heading
- "Online Notepad"

### Description/Intro
- "A free browser-based notepad for quick notes, writing drafts, and more. Your content is auto-saved locally - nothing is ever uploaded to any server."
- "Free Online Notepad - Write Notes in Your Browser"

### Labels
- "Word Counter"
- "Characters", "Words", "Lines", "Paragraphs", "Reading Time", "Speaking time"
- "Auto-saved" status indicator
- "Content"

### Buttons
- "Download .txt"
- "Copy"
- "Clear"

### Placeholder
- "Type or paste your text here."

### Other
- "Live counting."
- "Word Counter"
- "Character, word, sentence, and line counts update live..."

---

## widget-password-generator.php

### Title/Heading
- "VaultKey — Password Generator"

### Description/Intro
- "Create cryptographically secure passwords with a live strength meter — free online password generator"
- "Free Online Password Generator - Create Strong Secure Passwords"

### Labels
- "Password Length"
- "Number of Passwords"
- "Pool Size"
- "Password Strength"
- "Character Sets" + checkboxes: "UPPERCASE", "lowercase", "Numbers (0-9)", "Symbols"
- "Exclude ambiguous chars (0, O, l, I)"
- "Exclude visually similar (1, |, !)"
- "At least 1 of each selected type"
- "No duplicates in result"
- "Custom Exclude"
- "Show file size savings" (needs verification)
- "Characters to exclude from passwords, e.g. @#&"

### Buttons
- "Generate Password"
- "Copy"
- "Copy All"
- "Generate & Copy"

### Empty states
- "Your secure password will appear here. Click Generate Password to start creating strong, random passwords."

### Other
- "Generated Password(s)"
- "Entropy (bits)"
- "Generations"
- "Presets" (options: "Standard (Dense)", "Strong (16)", "Ultra (32)", "PIN (1000-9999)", "Numeric PIN (6)", "Basic (8)", "Medium (12)", "Alphanumeric only", "Lowercase + numbers", "Scrabble Hand (7)", "Full Alphabet (26)", "Half Year (6)", "Hex (0-9 a-f)", "No Symbols (16)", "SQL IN() Batch", "SQL List", "JSON Array", "Batch of 10", "Batch of 100", "URN", "URN Batch (10)", "Windows GUID {.}", "Lottery (1-49)", "Dice Roll (1-6)", "Coin Flip (0-1)", "Default (A-Z a-z 0-9 _-)", "No Hyphens", "Short", "Comma-separated", "Pipe ( | )", "Percentage (0-100)", "Floating point", "Integers", "Whole numbers", "Decimals", "Odd Only", "Even Only", "Multiples of.", "Custom CIDR Notation", "Sortable", "Random")

---

## widget-pdf-compressor.php

### Title/Heading
- "PDF Compressor"

### Description/Intro
- "Compress PDF files online with preview, compression levels, and instant browser download"
- "Free Online PDF Compressor - Reduce PDF File Size"

### Labels
- "Compression Level"
- "Compression Quality"
- "Quality"
- "0 = No compression, 9 = Maximum compression"
- "Higher quality preserves more detail but produces larger file sizes..."
- "Output Compression Level"
- "Output Compression Level (0 = largest file, 9 = smallest file)"
- "Quality slider description"
- "Light mode retains selectable text when possible..."
- "Mode" (options: "Light mode" / "Dark mode")

### Drop zone
- "Click to upload or drag & drop a PDF file"
- "Click or drag a PDF file to reduce its size"

### Buttons
- "Compress PDF"
- "Download PDF"
- "Download All"

### Status
- "Ready to compress"
- "Compressing..."
- "Original Size", "Compressed Size"
- "Size Reduction"
- "Avg Reduction"

### Info text
- "Reduce PDF file size without losing quality - all processing happens locally in your browser"
- "All processing stays in your browser - nothing is uploaded to any server. Files cached until you close this tab."

---

## widget-pdf-merger.php

### Title/Heading
- "PDF Merger"

### Description/Intro
- "Merge multiple PDF files into one document online - free and secure"
- "Free Online PDF Merger - Combine PDF Files"

### Labels
- "Files Loaded"
- "Total Pages"
- "Sort" (options: "Sort alphabetically", "Sort chronologically", "Sort ascending")
- "Section"
- "Pages"

### Drop zone
- "Click to upload or drag & drop PDF files"
- "Click or drag PDF files to merge them online securely"
- "PDF files only - up to 10 files at once"

### Buttons
- "Merge Files"
- "Download Merged PDF"
- "Clear All"

### Info text
- "Combine multiple PDFs into a single document - at least 2 files required"

### Status
- "Uploading PDFs: 0%"
- "Loading PDF engine."
- "Processed PDF Files"
- "Merging..."

---

## widget-pdf-splitter.php

### Title/Heading
- "PDF Splitter"

### Description/Intro
- "Split a PDF online into multiple documents by range, pages, or file size"
- "Free Online PDF Splitter - Split PDF Files by Pages or Size"

### Labels
- "Split Mode" (options: "Page Ranges", "Pages per Split", "Target Size per File")
- "Page Ranges"
- "Pages per Split"
- "Target Size per File"
- "Apply to Specific Pages"
- "Section"

### Drop zone
- "Click to upload or drag & drop a PDF file"
- "Click or drag a PDF file to split it online"

### Buttons
- "Split Files"
- "Split PDF"
- "Download All"
- "Reset"

### Info text
- "Each output file will contain this many pages. The last file may have fewer."
- "Each split file targets roughly this size. Actual sizes may vary slightly."
- "Enter comma-separated page numbers or ranges. Each range becomes its own PDF file."
- "e.g. 1-3, 5, 7-9"

### Status
- "Uploading PDF: 0%"
- "Processing..."
- "Split Files"
- "Total Pages"
- "Loaded PDF Files"
- "Total Size"

---

## widget-pdf-to-jpg.php

### Title/Heading
- "PDF to JPG"

### Description/Intro
- "Turn every PDF page into a high-quality JPG image - processed privately in your browser"
- "Free Online PDF to JPG Converter - Convert PDF Pages to Images"

### Labels
- "Output JPG Quality"
- "Quality"
- "Higher quality produces sharper JPG images..."
- "Background Color"
- "Choose a background color to fill transparent areas..."
- "Add "converted_" prefix to filenames"

### Drop zone
- "Click or drag a PDF file to convert its pages to JPG images"
- "Click to upload or drag & drop a PDF file"

### Buttons
- "Convert All"
- "Download All (ZIP)"
- "Download"

### Status
- "Uploading PDF: 0%"
- "Converting..."
- "Converted Pages"
- "Total Files"

### Info text
- "Your files stay private - all conversions happen locally in your browser..."

---

## widget-pdf-to-png.php

### Title/Heading
- "PDF to PNG"

### Description/Intro
- "Turn every PDF page into a crisp PNG image - processed privately in your browser, no uploads required"
- "Free Online PDF to PNG Converter - Convert PDF Pages to Images"

### Labels
- "Quality"
- "Higher quality preserves more detail but produces larger files..."
- "Background Color"
- "Choose a background color to fill transparent areas..."
- "Add "converted_" prefix to filenames"
- "Extra Options"

### Drop zone
- "Click or drag a PDF file to convert its pages to PNG images"
- "Click to upload or drag & drop a PDF file"

### Buttons
- "Convert All"
- "Download All (ZIP)"
- "Download"

### Info text
- "PNG export preserves transparency and sharp edges..."
- "Transparent areas are filled with your chosen background color before JPG export..."

### Status
- "Uploading PDF: 0%"
- "Converting..."
- "Converted Pages"

---

## widget-pdf-to-word.php

### Title/Heading
- "PDF to Word Converter"

### Description/Intro
- "Convert PDF to Word DOCX online — editable documents with a fast server-side converter"
- "Free Online PDF to Word Converter - Convert PDF to DOCX"

### Labels
- "Processed PDF Files"
- "Total Pages"
- "Status"

### Drop zone
- "Click to upload or drag & drop PDF files"
- "Click to upload or drag & drop a PDF file"

### Buttons
- "Convert to Word"
- "Download DOCX"
- "Download"
- "Clear All"

### Info text
- "Large files may take several minutes - conversion runs entirely on your device for security"

### Status
- "Converting PDF to DOCX - this may take a moment"
- "Word document ready for download!"
- "PDF loaded and ready to convert to Word"
- "Conversion Complete!"
- "Conversion failed - please try again"
- "No PDF file received."
- "Could not read converted DOCX."
- "Uploading to converter."
- "Converting to DOCX."
- "Opening PDF."
- "Processing..."

### Error messages
- "Please upload a valid PDF file"
- "Uploaded file is not a PDF."
- "PDF exceeds the 50 MB limit."
- "Cannot create temp directory."
- "Failed to move uploaded file."
- "PHP exec() is disabled on this server. Please enable it or contact your hosting provider."
- "Server cannot run LibreOffice. Please contact your hosting provider."
- "LibreOffice (soffice) is not installed. Install it via: sudo apt install libreoffice"
- "LibreOffice conversion failed (exit %d). Log: %s"

---

## widget-phonetic-spelling.php

### Title/Heading
- "Phonetic Spelling Tool"

### Description/Intro
- "Convert English words into phonetic spellings using simplified pronunciation guides or the official NATO alphabet. A helpful free tool for language learners, teachers, and anyone improving their spelling skills."
- "Free Online Phonetic Spelling Tool - Convert Words to Phonetics"

### Labels
- "Type or paste words to get their phonetic spelling. e.g. Hello, beautiful, necessary"
- "Phonetic Result"
- "Format" (options: "Simplified", "Standard", "Full (8 groups)", "Initials (3)")
- "Common Phonetic Patterns"

### Buttons
- "Convert to Phonetic"
- "Copy"
- "Clear"

### Empty states
- "Phonetic spelling will appear here."

---

## widget-pig-latin.php

### Title/Heading
- "Pig Latin Translator"

### Description/Intro
- "Translate English text into Pig Latin for fun, games, or learning. This free online tool converts words using classic Pig Latin rules - move consonants to the end and add suffixes."
- "Free Online Pig Latin Translator - Convert English to Pig Latin"

### Labels
- "English Text"
- "Pig Latin Translation"
- "Pig Latin Rules:"
- "Words starting with consonants → move consonant(s) to end + "ay" (e.g."
- "Words starting with vowels → add "way" or "yay" to end (e.g."
- "Add "way" after vowels"
- "Add "yay" after vowels"

### Buttons
- "Translate to Pig Latin"
- "Copy"
- "Clear"

### Placeholder
- "Type or paste English text here. e.g. Hello my friend, how are you today?"

### Other text
- "Ig-Pay Atin-Lay ill-way appear-way ere-hay."
- "Unicode Symbols:"

---

## widget-plain-text.php

### Title/Heading
- "Plain Text Converter"

### Description/Intro
- "Strip HTML tags, decode HTML entities, and clean up formatted text into plain text. Perfect for copying content from websites, emails, or rich text documents. All processing is done privately in your browser."
- "Free Online Plain Text Converter - Remove Formatting & HTML"

### Labels
- "Input (HTML / Rich Text)"
- "Plain Text Result"
- "Strip HTML tags"
- "Decode HTML entities (&amp; → &)"
- "Collapse multiple spaces into one"
- "Trim extra whitespace"
- "Normalize Unicode (smart quotes → straight)"

### Buttons
- "Convert to Plain Text"
- "Copy"
- "Clear"

### Status
- "Tags Removed", "Characters"

---

## widget-png-compressor.php

### Title/Heading
- "PNG Compressor"

### Description/Intro
- "Compress PNG images online with previews, tab cache, and ZIP batch download"
- "Free Online JPG Compressor" (note: label)

### Labels
- "Compression Level"
- "Quality"
- "Output Compression Level"
- "0 = No compression, 9 = Maximum compression"
- "Higher quality produces sharper PNG images, but leads to larger file sizes..."
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add "converted_" prefix to filenames"
- "Show file size savings"

### Drop zone
- "Click to upload or drag & drop PNG files"
- "Click or drag PNG images to compress"
- "PNG files only - convert up to 20 images at once - processed locally in your browser"

### Buttons
- "Compress PNG"
- "Download All (ZIP)"
- "Clear All"

### Info text
- "PNG compression uses aggressive browser-side resizing and color reduction..."
- "This preference label guides compression behavior..."
- "Images are saved in this browser tab so they remain after reload..."

### Status
- "Compressed Size", "Original Size", "Images Loaded", "Ready to compress"

---

## widget-png-to-heic.php

### Title/Heading
- "AppleSnap — PNG to HEIC"

### Description/Intro
- "Convert PNG to HEIC online — Apple-compatible HEIC image conversion tool"
- "Free Online PNG to HEIC Converter - Browser-Based Image Tool"

### Labels
- "Output JPG Quality"
- "HEIC Quality"
- "Background Color"
- "HEIC does not support transparency..."
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add "converted_" prefix to filenames"
- "Keep converted HEIC files in browser cache"

### Drop zone
- "Upload PNG images - click to browse or drag and drop to convert to HEIC online"
- "PNG files only - convert up to 20 images at once - processed entirely in your browser"

### Buttons
- "Convert to HEIC"
- "Download All (ZIP)"
- "Download"

### Info text
- "HEIC export depends on your browser support..."

### Status
- "Converted PNG Files", "Checking HEIC export support..."

---

## widget-png-to-jpg.php

### Title/Heading
- "PhotoShift — PNG to JPG"

### Description/Intro
- "Convert PNG to JPG online — transform images into compact, shareable JPEG files"
- "Free Online PNG to JPG Converter - Browser-Based Image Tool"

### Labels
- "Output JPG Quality"
- "Background Color"
- "Transparent PNG areas are filled with your chosen background color before JPG export... Your files stay private - no uploads to any server."
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add "converted_" prefix to filenames"

### Drop zone
- "Upload PNG images - click to browse or drag and drop to convert to JPG online"
- "PNG files only - convert up to 20 images at once - processed locally in your browser"

### Buttons
- "Convert to JPG"
- "Download All (ZIP)"
- "Download"

### Info text
- "Export clean JPG copies for smaller file size..."

### Status
- "Converted PNG Files", "Converting...", "Total JPG Size"

---

## widget-png-to-pdf.php

### Title/Heading
- "PNG to PDF"

### Description/Intro
- "Combine multiple PNG images into a single PDF document - entirely processed in your browser for privacy"
- "Free Online PNG to PDF Converter - Convert Images to PDF"

### Labels
- "Output PDF Size"
- "Compression Level"
- "Output Compression Level (0 = largest file, 9 = smallest file)"
- "Higher quality preserves sharper image detail but creates a larger PDF..."
- "Quality"

### Drop zone
- "Click to upload or drag & drop PNG images"
- "Click or drag PNG images to convert them to PDF"
- "PNG files only - convert up to 20 images at once - processed locally in your browser"

### Buttons
- "Convert to PDF"
- "Download PDF"
- "Clear All"

### Status
- "Converting...", "Loaded Images", "Total Pages"

---

## widget-png-to-svg.php

### Title/Heading
- "VectorLift — PNG to SVG"

### Description/Intro
- "Convert PNG to SVG online — transform raster images into vector-style output"
- "Free Online PNG to SVG Converter - Browser-Based Image Tool"

### Labels
- "SVG Output Mode" (options: "Embedded", "Base64 image tag")
- "Make SVG responsive (no fixed size)"
- "Include basic SVG description metadata"
- "Include generation timestamp"
- "SVG Accessibility Label"
- "SVG Output"
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add "converted_" prefix to filenames"

### Drop zone
- "Upload PNG images - click to browse or drag and drop to convert to SVG online"
- "PNG files only - convert up to 20 images at once - processed entirely in your browser"

### Buttons
- "Convert to SVG"
- "Download All (ZIP)"
- "Download"

### Info text
- "SVG output embeds your PNG as a base64 image..."
- "Responsive SVG uses a viewBox..."

### Status
- "Converted SVG Files", "Total SVG Size"

---

## widget-png-to-webp.php

### Title/Heading
- "WebPForge — PNG to WebP"

### Description/Intro
- "Convert PNG to WebP online — create efficient, modern web images"
- "Free Online PNG to WebP Converter - Browser-Based Image Tool"

### Labels
- "WebP Quality"
- "Output WebP Quality"
- "80-90% is the sweet spot..."
- "Higher quality preserves more detail but produces larger files..."
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add "converted_" prefix to filenames"
- "Try higher-fidelity WebP export when browser supports it"
- "Each result card shows both the original PNG and the converted WebP preview..."

### Drop zone
- "Upload PNG images - click to browse or drag and drop to convert to WebP online"
- "PNG files only - convert up to 20 images at once - processed locally in your browser"

### Buttons
- "Convert to WebP"
- "Download All (ZIP)"
- "Download"

### Info text
- "Supports transparent PNG images and exports optimized WebP copies locally..."

### Status
- "Converted PNG Files", "Converting...", "Total PNG Size"

---

## widget-random-choice.php

### Title/Heading
- "SpinPick — Choice Picker"

### Description/Intro
- "Randomly pick from any list of choices - free online random choice picker tool"
- "Free Online Random Choice Picker - Make Random Decisions Instantly"

### Labels
- "Your Options (one per line)"
- "Pick Mode" (options: "Pick 1", "Pick 3", "Pick 5")
- "How many to pick."
- "Spins"
- "Total Options"

### Buttons
- "Pick Random"
- "Reset"
- "Copy"

### Empty states
- "Your randomly selected choices will appear here - just add your options and click Pick Random!"

### Other
- "Picked", "Picked Choice(s)"

---

## widget-random-date.php

### Title/Heading
- "DateForge — Date Generator"

### Description/Intro
- "Generate random dates between any two dates - free online random date generator"
- "Free Online Random Date Generator"

### Labels
- "Start Date"
- "End Date"
- "Format"
- "Separator Between Repetitions" (options: "New Line", "Comma", "Space", "Dash", "Custom")
- "How Many Dates"
- "Entry Format" (options: various date formats)
- "No duplicate dates"
- "Weekdays only (Mon-Fri)"
- "Comma-format large numbers"

### Buttons
- "Generate Dates"
- "Copy"
- "Copy All"

### Empty states
- "Your random dates will appear here - pick a range and format, then click Generate Dates to start."

### Presets
- "This Year", "Last Year", "Next 5 Years", "Last 10 Years"

---

## widget-random-ip.php

### Title/Heading
- "IPForge — IP Generator"

### Description/Intro
- "Generate random IPv4 and IPv6 addresses in bulk - free online IP generator tool"
- "Free Online Random IP Generator"

### Labels
- "IP Version" (options: "IPv4", "IPv6", "Mixed (IPv4 + IPv6)")
- "IPv4 Range / Class" (placeholder: "e.g. 203.0.113.0/24")
- "IPv6 Format" (options: "Full (8 groups)", "Compressed (::)", "Link-local (fe80::)", "ULA (fc/fd)")
- "How Many IPs"
- "No duplicate IPs"
- "Include random port"
- "Custom CIDR Notation"

### Buttons
- "Generate IPs"
- "Copy All"
- "Copy"

### Empty states
- "Your random IP addresses will appear here - choose IPv4, IPv6, or Mixed and click Generate IPs to start."

### Presets
- "Range", "Number of .. to Generate"

---

## widget-random-letter.php

### Title/Heading
- "LetterDraw — Letter Generator"

### Description/Intro
- "Generate random letters with case, type, and frequency controls - free online letter generator"
- "Free Online Random Letter Generator"

### Labels
- "Letter Case"
- "Character Type"
- "Letter Type" (options: "Vowels", "Consonants", "Alphabet", "Numbers only", "Punctuation", "Custom")
- "Type letters to include, e.g. AEIOU or BCDF."
- "Letter Frequency"
- "Character Set" (options: "Alphabet", "Lowercase", "UPPERCASE")
- "How Many Letters"
- "No duplicate letters"
- "Comma-format large numbers"
- "Separator Between Repetitions" (options: "New Line", "Comma", "Space", "Dash", "Custom")
- "Case-sensitive removal"
- "Ignore common words"

### Buttons
- "Generate Letters"
- "Copy All"
- "Copy"

### Empty states
- "Your random letters will appear here - choose case, type, and count then click Generate Letters to start."

---

## widget-random-month.php

### Title/Heading
- "MonthSpin — Month Generator"

### Description/Intro
- "Pick random months filtered by season or quarter - free online month generator tool"
- "Free Online Random Month Generator"

### Labels
- "How Many Months"
- "Filter by Season or Quarter"
- "Entry Format" (dropdown options listed below)
- "No duplicate months"
- "Comma-format large numbers"
- "All 12 Months"

### Buttons
- "Generate Months"
- "Copy"

### Empty states
- "Your random months will appear here - filter by season or quarter, then click Generate Months to start."

### Season/Quarter options
- "All 12 Months", "Winter (Dec, Jan, Feb)", "Spring (Mar, Apr, May)", "Summer (Jun, Jul, Aug)", "Autumn (Sep, Oct, Nov)", "Half-Year", "Jan-Jun", "Jul-Dec", "Quarters", "Jan, Feb, Mar", "Apr, May, Jun", "Jul, Aug, Sep", "Oct, Nov, Dec", "Seasons", "Last 10 Years"

### Entry Format options
- "Abbreviated | Jan", "Full Name | January", "Name + Number | January (1)", "Month Number | 01", "UPPERCASE | JANUARY", "Number (no pad) | 1"

---

## widget-random-number.php

### Title/Heading
- "NumForge — Number Generator"

### Description/Intro
- "Generate random integers, decimals, or multiples in any range - free online number generator"
- "Free Online Random Number Generator"

### Labels
- "Minimum Value"
- "Maximum Value"
- "Number Type" (options: "Integers", "Whole numbers", "Decimals", "Floating point", "Multiples of.")
- "Decimal Places"
- "How Many Numbers"
- "Multiple of"
- "No duplicate numbers"
- "Comma-format large numbers"
- "Sort results A-Z"

### Buttons
- "Generate Numbers"
- "Copy All"
- "Copy"

### Empty states
- "Your randomly generated numbers will appear here - adjust your range and click Generate Numbers to get started instantly."

### Presets
- "Range", "Number of .. to Generate", "Min Result", "Max Result"

---

## widget-remove-background.php

### Title/Heading
- "Remove Background from Image"

### Description/Intro
- "Remove backgrounds from images instantly - export as transparent PNG, all processed locally in your browser for privacy"
- "Free Online Background Remover from Image"

### Labels
- "Edge Smoothness"
- "Background Removed"
- "Image Preview"

### Drop zone
- "Click to upload or drag and drop an image"
- "Click or drag an image to remove the background"

### Buttons
- "Remove Background"
- "Download PNG"
- "Download"
- "Reset"

### Info text
- "The first run downloads the AI background-removal model, so it takes a bit longer..."

### Status
- "Processing...", "Processing.", "Preparing..."

### Other
- "Your data never leaves your device..."
- "Supports PNG, JPG, GIF, WebP - all processing is done in your browser, nothing is uploaded"

---

## widget-remove-formatting.php

### Title/Heading
- "Remove Text Formatting"

### Description/Intro
- "Strip Unicode bold, italic, and cursive styling - free online text formatting remover"
- "Free Online Remove Text Formatting - Strip Unicode Styling"

### Buttons
- "Convert"
- "Copy"
- "Clear"

### Labels
- "Options" (checkboxes: "Strip HTML tags", "Decode HTML entities...", "Collapse multiple spaces into one", "Trim extra whitespace", "Normalize Unicode...")
- "Input Text"
- "Result"

### Placeholder
- "Paste or type your text here."

---

## widget-remove-line-breaks.php

### Title/Heading
- "Remove Line Breaks"

### Description/Intro
- "Strip line breaks and join lines together — free online line break remover"
- "Free Online Line Break Remover - Join Lines of Text"

### Labels
- "Options" (checkboxes: "Remove blank lines", "Merge into single paragraph", "Trim extra spaces")
- "Separator" (options: "Space", "New Line", "Comma", "Dash", "Custom")

### Buttons
- "Convert"
- "Copy"
- "Clear"

### Placeholder
- "Paste or type your text here."

---

## widget-remove-underscores.php

### Title/Heading
- "Remove Underscores"

### Description/Intro
- "Replace or remove underscores from your text in one click. Choose spaces, hyphens, or custom replacements. Perfect for cleaning up file names, database fields, and snake_case text."
- "Free Online Underscore Remover - Replace or Remove Underscores"

### Labels
- "Replace Underscores With" (options: "Space", "Dash", "Nothing (remove)", "Custom.")
- "Capitalize words after underscore"
- "Remove leading/trailing underscores"
- "Replace multiple consecutive underscores"
- "Case-sensitive"

### Buttons
- "Remove Underscores"
- "Copy"
- "Clear"

### Placeholder
- "Type or paste your text here."

### Status
- "Underscores Removed"

---

## widget-repeat-text.php

### Title/Heading
- "Repeat Text Generator"

### Description/Intro
- "Repeat any text any number of times — free online text repeater generator"
- "Free Online Text Repeater - Repeat Text Multiple Times"

### Labels
- "Text to Repeat"
- "Repeat Count"
- "Separator" (options: "Space", "New Line", "Comma", "Dash", "Custom")
- "Separator:"

### Buttons
- "Generate"
- "Copy"
- "Clear"

### Placeholder
- "Type or paste your text here."

### Status
- "Repetitions"

---

## widget-reverse-text.php

### Title/Heading
- "Reverse Text Generator"

### Description/Intro
- "Reverse text, words, lines, or flip text upside down with this fun free online tool. Perfect for creating mirror text, puzzles, and social media posts. All processing is done in your browser."
- "Free Online Reverse Text Generator - Flip Text, Words & Lines"

### Labels
- "Reverse Mode" (options: "Reverse Characters", "Reverse Words", "Reverse Lines", "Flip Upside Down")
- "Input Text"
- "Reversed Text"

### Buttons
- "Reverse"
- "Copy"
- "Clear"

### Placeholder
- "Type or paste your text here."

---

## widget-roman-numeral.php

### Title/Heading
- "Roman Numeral Dates"

### Description/Intro
- "Convert dates and numbers to Roman numerals — free online Roman numeral converter"
- "Free Online Roman Numeral Converter - Dates, Numbers & Numerals"

### Labels
- "Mode" (options: "Date to Roman", "Number to Roman", "Roman to Number")
- "Date to Roman"
- "Number (1-3999)"
- "Number to Roman"
- "Roman to Number"
- "Enter a number between 1 and 3999."
- "e.g. MMXXIV or MCMXCVIII"
- "Roman Numeral Reference"

### Buttons
- "Convert"
- "Copy"
- "Clear"

### Fields
- Day/Month/Year dropdowns for Date mode
- Roman Numeral input field

### Other
- "20th Century"
- "Roman Numeral Reference"

---

## widget-rotate-pdf.php

### Title/Heading
- "Rotate PDF"

### Description/Intro
- "Rotate PDF pages by 90°, 180°, or 270° - all done securely in your browser"
- "Free Online PDF Rotator - Rotate PDF Pages Instantly"

### Labels
- "Rotation Angle" (options: "90°", "180°", "270°")
- "Apply to Specific Pages"
- "Page Ranges"
- "Leave empty for all pages, or specify: 1,3,5-8"

### Drop zone
- "Click or drag PDF files to rotate their pages"
- "Click to upload or drag & drop a PDF file"
- "PDF files only - up to 10 files at once"

### Buttons
- "Rotate PDF"
- "Download PDF"
- "Download All"
- "Reset"

### Status
- "Uploading...", "Total Pages", "Processed PDF Files", "Uploading PDFs: 0%"
- "Loading PDF engine."

---

## widget-sentence-case.php

### Title/Heading
- "Sentence Case Converter"

### Description/Intro
- "Capitalize the first letter of each sentence - free online sentence case converter"
- "Free Online Sentence Case Converter - Capitalise Sentences Automatically"

### Labels
- "Options" (checkboxes: "Preserve abbreviations (NASA, USA.)", "Preserve proper nouns", "Always capitalise "I"", "Lowercase all words")
- "Input Text"
- "Result"

### Buttons
- "Convert"
- "Copy"
- "Clear"

### Placeholder
- "Paste or type your text here to convert it to sentence case automatically."

### Empty states
- "Sentence case result will appear here."

### Other
- "Automatically capitalise the first letter of every sentence..."

---

## widget-sentence-counter.php

### Title/Heading
- "Online Sentence Counter"

### Description/Intro
- "Count sentences, words, characters, paragraphs, and more with this free online text analyser. Get live statistics, reading time, speaking time, and advanced metrics - all processed privately in your browser."
- "Free Online Sentence Counter - Count Sentences, Words & Characters"

### Labels
- "Live Statistics"
- "Advanced Statistics"
- "Characters", "Words", "Sentences", "Paragraphs", "Lines", "Total Characters", "Total Words"
- "Chars (no spaces)"
- "Reading Time"
- "Speaking time"
- "Avg chars / word"
- "Avg words / sentence"
- "Longest word"
- "Top Word"
- "All Word Frequencies"

### Buttons
- "Export CSV"
- "Clear"
- "Copy"

### Placeholder
- "Paste or type your text here to count sentences, words, characters, paragraphs, and reading time..."

---

## widget-seo-cases-section.php

### Title/Heading
- "SEO Cases Section"

### Labels
- "Text Case Guide & Reference"
- "Case Cards"
- "Case Name"
- "Description"
- "Case Name Color"
- "Description Color"
- "Card Background"
- "Accent Color"
- "Card Border Color"
- "Example Badge Accent"
- "Sets the text colour and tint of the monospaced example badge."
- "Icon / Emoji"
- "Show Badge"
- "Section Header"
- "Header"
- "Tag Line"
- "Title HTML Tag"
- "Card Border Radius"
- "Card Radius"

### Default case cards
- "Sentence case" — "Capitalises the first letter of every sentence while keeping the rest lowercase..."
- "lowercase" — "Converts every letter to its small form..."
- "UPPERCASE" — "Converts every letter to its capital form..."
- "Title Case" — "Follows AP/Chicago style rules..."
- "Capitalized Case" — "Capitalises the first letter of every word regardless of type..."
- "aLtErNaTiNg" — "Alternates between lowercase and uppercase on every character..."
- "Inverse Case" — "Flips the case of every single letter..."

### Other
- "A short description of this case format."
- "Example Output"
- "Visual Overview"

---

## widget-sort-words.php

### Title/Heading
- "Sort Words Alphabetically"

### Description/Intro
- "Sort words, lines, or comma-separated values alphabetically (A-Z or Z-A), by length, or randomly. Remove duplicates and trim whitespace - all securely processed in your browser."
- "Free Online Word Sorter - Sort Words & Lines Alphabetically"

### Labels
- "Sort Order" (buttons: "A-Z", "Z-A", "Length", "Random")
- "Sort By"
- "Options" (checkboxes: "Sort results A-Z", "Remove Duplicates", "Trim whitespace", "Case-sensitive")
- "Your Text / List"
- "Sorted Result"

### Buttons
- "Sort"
- "Copy"
- "Clear"

### Placeholder
- "Paste or type your text here."

### Empty states
- "Sorted list will appear here."

### Other
- "Before", "After", "After Sort", "Length", "Random"

---

## widget-svg-compressor.php

### Title/Heading
- "SVG Compressor"

### Description/Intro
- "Compress SVG images online with previews, tab cache, and ZIP batch download"
- "Free Online JPG Compressor" (note: title mismatch)

### Labels
- "Optimization Level"
- "Precision slider"
- "Compression"
- "Images"
- "Output"

### Drop zone
- "Click to upload or drag & drop SVG images"
- "Click or drag SVG images to compress"

### Buttons
- "Compress SVG"
- "Download All (ZIP)"
- "Download"
- "Clear All"

### Info text
- "Compress SVG markup locally in your browser while keeping editable SVG files"
- "SVGs are saved in this browser tab so they remain after reload..."
- "Lower values create fewer colors and a simpler SVG..."

### Status
- "Compressed Size", "Original Size", "Size Reduction"
- "Loading images: 0%"
- "Uploading files: 0%"
- "Images Loaded", "Ready to compress"

---

## widget-title-case.php

### Title/Heading
- "Title Case Converter"

### Description/Intro
- "Apply proper AP, APA, Chicago, and MLA title case rules — free online title case converter"
- "Free Online Title Case Converter - AP/Chicago Style Headlines"

### Labels
- "Word Counter"
- "Words", "Characters", "Lines", "Sentences", "Reading Time", "Speaking time"

### Buttons
- "Convert"
- "Copy"
- "Clear"

### Placeholder
- "Type or paste your text here to convert to title case."

### Other text
- "Convert any text to title case following AP and Chicago style rules..."

---

## widget-tools-grid-section.php

### Title/Heading
- "Tools Grid Section"

### Labels
- "Section Header"
- "Tool Cards"
- "Card Title"
- "Description"
- "Link URL"
- "Leave blank to show a "Coming soon" badge."
- "Icon / Emoji"
- "Show Badge"
- "Title HTML Tag"
- "Header"
- "Tag Line" (default: "Explore More Free Online Tools")
- "More Tools"

### Buttons
- "Card Title Color", "Card Desc Color", "Card Background", "Card Border Color", "Card Border Radius", "Card Radius", "Card Padding", "Card Gap", "Grid auto-fills columns...", "Icon Box Size", "Icon Font Size", "Icon Box Background", "Accent Color", "Section Padding", "Heading Color"

### Other
- "Coming soon" badge text
- "Explore More Free Online Tools"
- "New!" (badge)

---

## widget-uuid-generator.php

### Title/Heading
- "UniqueForge — UUID Generator"

### Description/Intro
- "Generate UUID v1, v4, v5, ULID, and NanoID identifiers in bulk — free online UUID generator"
- "Free Online UUID Generator - Generate UUIDs, ULIDs & NanoIDs"

### Labels
- "UUID Version" (options: "v1", "v4", "v5", "ULID", "NanoID")
- "v5 Settings - Name-based (SHA-1)"
- "Namespace"
- "Number to Generate"
- "How Many"
- "Format" (options: "Standard", "UPPERCASE", "No Hyphens", "Braces", "URN", "SQL List", "JSON Array")
- "NanoID Settings"
- "UUID Validator"
- "Paste a UUID or ULID to validate against RFC 4122."
- "Output Format"
- "Batch of 10"
- "Batch of 100"
- "Default (A-Z a-z 0-9 _-)"
- "Alphanumeric only"
- "Lowercase + numbers"
- "Hex (0-9 a-f)"
- "Numbers only"
- "Custom."
- "Length"
- "Pool Size"
- "Version"
- "Generated At"
- "Show file size savings"

### Buttons
- "Generate UUIDs"
- "Copy All"
- "Copy"
- "Validate"
- "Generate & Copy"

### Empty states
- "Your generated UUIDs, ULIDs, or NanoIDs will appear here - click Generate UUIDs to start."

### Other
- "Time-based" / "Name-based" / "Random" (version labels)
- "Generated UUIDs"

---

## widget-video-converter.php

### Title/Heading
- "ClipShift — Video Converter"

### Description/Intro
- "Convert videos online between MP4, WebM, AVI, MOV and more — free browser-based converter"
- "Free Online Video Converter"

### Labels
- "Output Format"
- "Video Quality"
- "Encoder Compression"
- "Conversion settings"

### Drop zone
- "Click to upload or drag and drop your video file"
- "MP4, WebM, AVI, MOV, MKV - all processing stays in your browser via FFmpeg.wasm for total privacy"

### Buttons
- "Convert Video"
- "Download"
- "Cancel"

### Status
- "Converting..."
- "Processing..."
- "Uploading to converter."

---

## widget-webp-compressor.php

### Title/Heading
- "WebP Compressor"

### Description/Intro
- "Compress WebP images online with quality control, resizing, previews, and ZIP download"
- "Free Online WebP to JPG Converter" (note: title mismatch)

### Labels
- "WebP Quality"
- "Quality"
- "Output WebP Quality"
- "80-90% is the sweet spot for web use..."
- "Higher quality preserves more detail but produces larger files..."
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add "converted_" prefix to filenames"
- "Show file size savings"
- "Compression"
- "Images"

### Drop zone
- "Click to upload or drag & drop WebP images"
- "Click or drag WebP images to compress"
- "WebP files only - convert up to 20 images at once - processed entirely in your browser"

### Buttons
- "Compress WebP"
- "Download All (ZIP)"
- "Clear"

### Info text
- "Best compression comes from WebP quality reduction plus optional resizing..."
- "Images are saved in this browser tab so they remain after reload..."

### Status
- "Compressed Size", "Original Size", "Images Loaded", "Ready to compress"

---

## widget-webp-to-jpg.php

### Title/Heading
- "PhotoRestore — WebP to JPG"

### Description/Intro
- "Convert WebP to JPG online — restore WebP images back to standard JPEG format"
- "Free Online WebP to JPG Converter - Browser-Based Image Tool"

### Labels
- "Output JPG Quality"
- "Quality"
- "Higher quality produces sharper JPG images, but results in larger file sizes..."
- "Background Color"
- "Choose a background color to fill transparent areas before exporting to JPG..."
- "Transparent WebP areas are filled with your chosen background color before JPG export. Your data never leaves your device."
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add custom prefix"

### Drop zone
- "Upload WebP images - click to browse or drag and drop to convert to JPG online"
- "WebP files only - convert up to 20 images at once - processed entirely in your browser"

### Buttons
- "Convert All"
- "Download All Images (ZIP)"
- "Download"

### Info text
- "Your files stay private - all conversions happen locally in your browser..."

### Status
- "Converted WebP Files", "Converting...", "Loaded WebP Images"

---

## widget-webp-to-png.php

### Title/Heading
- "PixelRestore — WebP to PNG"

### Description/Intro
- "Convert WebP to PNG online — restore WebP images to lossless PNG format"
- "Free Online WebP to PNG Converter - Browser-Based Image Tool"

### Labels
- "Output"
- "Background Color"
- "Transparent PNG areas are preserved when your browser supports WebP alpha channels..."
- "PNG export preserves transparency from your original WebP images..."
- "Resize (optional)"
- "Max Image Side"
- "Keep original size"
- "Add custom prefix"

### Drop zone
- "Upload WebP images - click to browse or drag and drop to convert to PNG online"
- "WebP files only - convert up to 20 images at once - processed entirely in your browser"

### Buttons
- "Convert All to PNG"
- "Download All Images (ZIP)"
- "Download"

### Info text
- "Each result card shows both the original PNG and the converted WebP preview..."

### Status
- "Converted WebP Files", "Converting...", "Loaded WebP Images"

---

## widget-whitespace-remover.php

### Title/Heading
- "Whitespace Remover"

### Description/Intro
- "Remove extra spaces, tabs, leading/trailing whitespace, and non-breaking spaces from your text. Or strip all whitespace entirely. A fast, free online tool that runs safely in your browser."
- "Free Online Whitespace Remover - Remove Extra Spaces & Tabs"

### Labels
- "Options" (checkboxes):
  - "Trim trailing spaces"
  - "Trim leading spaces"
  - "Remove double spaces"
  - "Remove all spaces"
  - "Remove blank lines"
  - "Remove extra blank lines"
  - "Convert tabs to spaces"
  - "Replace \t with a single space"
  - "Remove non-breaking spaces"
  - "Replace &nbsp; (\u00A0) with space"
  - "Strip every whitespace character"
  - "Remove spaces at line start"
  - "Remove spaces at line end"

### Buttons
- "Remove Whitespace"
- "Copy Result"
- "Clear"

### Placeholder
- "Paste or type your text here."

### Stats
- "Spaces Removed", "Total Characters", "Total Lines"

---

## widget-wingdings.php

### Title/Heading
- "Wingdings Translator"

### Description/Intro
- "Convert text to Wingdings symbols and back to regular text. A fun free online tool for creating secret messages, puzzles, and decorative text - all processed in your browser."
- "Free Online Wingdings Translator - Convert Text to Wingdings & Back"

### Labels
- "Direction" (buttons: "Text to Wingdings", "Wingdings to Text")
- "Your Text"
- "Wingdings Output"
- "Wingdings Symbol Reference"

### Buttons
- "Convert"
- "Copy"
- "Clear"

### Placeholder
- "Type or paste your text here."

### Empty states
- "Wingdings translation will appear here."

### Other
- "Unicode Symbols:", "NATO Phonetic Alphabet Reference"

---

## widget-word-cloud.php

### Title/Heading
- "Word Cloud Generator"

### Description/Intro
- "Turn any text into a beautiful word cloud visualisation. Choose from multiple colour themes, control word count and length, and download your cloud as a PNG image - all for free in your browser."
- "Free Online Word Cloud Generator - Create Visual Word Clouds"

### Labels
- "Color Theme" (options: "Purple & Pink", "Ocean Blue", "Forest Green", "Sunset", "Monochrome", "Rainbow")
- "Max Words"
- "Min Word Length"
- "Random Word Length (4-8)"
- "Width"
- "Remove stop words"
- "Ignore common words"

### Buttons
- "Generate Word Cloud"
- "Download"
- "Download PNG"
- "Copy"
- "Clear"

### Placeholder
- "Paste or type text here to generate a stunning word cloud..."

### Other
- "Font Size (preview)", "Color Theme", "Background Color", "Options"

---

## widget-word-frequency.php

### Title/Heading
- "Word Frequency Counter"

### Description/Intro
- "Count word frequency in any text with sortable results — free online word counter tool"
- "Free Online Word Frequency Counter - Analyse Text Repetition"

### Labels
- "Options" (checkboxes):
  - "Case-sensitive"
  - "Ignore common words (the, a, is.)"
  - "Strip punctuation"
  - "Min length:" (number input)
  - "Filter words." (input)
  - "Sort By" (options: "Alphabetical", "Frequency")
  - "Sort order"

### Buttons
- "Analyze Frequency"
- "Export CSV"
- "Copy"
- "Clear"

### Placeholder
- "Paste your text here to analyze word frequency..."

### Results
- "Word Frequency Results"
- "Top Words"
- "All Word Frequencies"
- "Total Words", "Unique Words", "Average"
- "Sentences", "Characters", "Words", "Lines"

---

## Base widget: class-textcraft-base-widget.php

### Common utility strings
- "Click to upload or drag & drop" (upload zone)
- "Uploading files: 0%"
- "All processing stays in your browser..."
- "Your files stay private..."
- "Free • Instant • No Signup"
