<?php
/**
 * SEO Content Data — structured content for every TextCraft Tools Pro widget.
 *
 * Each entry is keyed by the widget's Elementor name (snake_case).
 * Sections: intro, how_to, features, benefits, use_cases, why_choose, faq.
 *
 * @package TextCraftToolsPro
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

return [

    'textcraft_case_converter' => [
        'intro' => [
            'The TextCraft Case Converter is a free online tool that lets you switch text between uppercase, lowercase, sentence case, title case, capitalized case, alternating case, and inverse case instantly. Simply type or paste your text and select the desired case format — the conversion happens in real time as you choose.',
            'Unlike many online text tools, the Case Converter processes everything locally in your browser. No data is uploaded to any server, making it a privacy-focused choice for writers, editors, students, and anyone who needs quick case changes without compromising sensitive content.',
        ],
        'how_to' => [
            ['title' => 'Enter Your Text', 'desc' => 'Type or paste the text you want to convert into the input textarea. The tool displays live character, word, sentence, and line counts as you enter content.'],
            ['title' => 'Choose a Case', 'desc' => 'Click any of the case conversion buttons — uppercase, lowercase, sentence case, title case, capitalized, alternating, or inverse. The converted text appears instantly in the output area.'],
            ['title' => 'Copy or Download', 'desc' => 'Use the Copy button to save the result to your clipboard, or click Download to save it as a text file. Use the Clear button to reset and start a new conversion.'],
        ],
        'features' => [
            ['icon' => "\xE2\x9A\xA1", 'title' => 'Real-Time Conversion', 'desc' => 'Text converts instantly as you select a case format. No page reloads or submit buttons needed.'],
            ['icon' => "\xF0\x9F\x94\x92", 'title' => '100% Private', 'desc' => 'All processing happens in your browser. Your text never reaches any server, keeping your content completely confidential.'],
            ['icon' => "\xF0\x9F\x93\xB1", 'title' => 'Mobile Friendly', 'desc' => 'The tool works seamlessly on phones, tablets, and desktops. The interface adapts to any screen size for convenient use on the go.'],
            ['icon' => "\xF0\x9F\x8E\x81", 'title' => 'Free to Use', 'desc' => 'No registration, no subscription fees, no usage caps. Use the case converter as many times as you need.'],
            ['icon' => "\xF0\x9F\x8E\xAF", 'title' => 'Multiple Case Options', 'desc' => 'Seven different case formats including standard options like uppercase and lowercase plus specialized formats like alternating and inverse case.'],
        ],
        'benefits' => [
            ['title' => 'Saves Time', 'desc' => 'Convert text between multiple case formats instantly without retyping or using complex word processor functions.'],
            ['title' => 'Privacy First', 'desc' => 'Since everything runs client-side, your text never leaves your device. Ideal for private documents, passwords, or confidential writing.'],
            ['title' => 'Keyboard Shortcuts', 'desc' => 'Power users can press Ctrl+Shift+U for uppercase, Ctrl+Shift+L for lowercase, Ctrl+Shift+S for sentence case, or Ctrl+Shift+T for title case.'],
            ['title' => 'Live Statistics', 'desc' => 'The tool automatically counts characters, words, sentences, and lines as you type, giving you useful document metrics at a glance.'],
        ],
        'use_cases' => [
            ['title' => 'Writers and Editors', 'desc' => 'Quickly reformat headlines, titles, and body text without manually retyping or using word processor shortcuts.'],
            ['title' => 'Students', 'desc' => 'Format academic papers and assignments correctly by applying title case or sentence case to references and section headings.'],
            ['title' => 'Content Creators', 'desc' => 'Prepare social media posts, blog titles, and email subject lines in the right case format for different platforms.'],
            ['title' => 'Developers', 'desc' => 'Convert code comments, variable names, or data strings to different case formats during development and debugging.'],
        ],
        'why_choose' => [
            ['title' => 'No Installation Required', 'desc' => 'Works directly in any modern browser with nothing to download or install. Access it from any device with internet access.'],
            ['title' => 'Completely Free', 'desc' => 'All TextCraft tools are free with no premium tiers, usage limits, or account requirements.'],
            ['title' => 'Privacy Guaranteed', 'desc' => 'Your text stays on your device for complete confidentiality. No server-side processing means no data storage or transmission risks.'],
            ['title' => 'User-Friendly Interface', 'desc' => 'Clean, modern design with clear case buttons and real-time feedback makes the tool accessible to everyone regardless of technical skill.'],
        ],
        'faq' => [
            ['question' => 'Can I convert text on my phone or tablet?', 'answer' => 'Yes, the Case Converter is fully responsive and works on all modern smartphones and tablets. The interface adapts to smaller screens while maintaining full functionality including all seven case options.'],
            ['question' => 'Is the Case Converter safe for confidential documents?', 'answer' => 'Absolutely. All text processing happens locally in your browser using JavaScript. No data is transmitted to any server, making it safe for passwords, business documents, or personal writing.'],
            ['question' => 'What is the difference between sentence case and title case?', 'answer' => 'Sentence case capitalises only the first word of each sentence, while title case capitalises every major word. Use sentence case for body text and title case for headlines or document titles.'],
            ['question' => 'Does the tool support keyboard shortcuts?', 'answer' => 'Yes. Keyboard shortcuts are available for the most common conversions: Ctrl+Shift+U for uppercase, Ctrl+Shift+L for lowercase, Ctrl+Shift+S for sentence case, and Ctrl+Shift+T for title case.'],
            ['question' => 'Is there a character limit for text input?', 'answer' => 'There is no enforced character limit, but very large documents may cause performance variation depending on your device. For most standard text inputs the conversion is instant.'],
        ],
    ],

    'textcraft_find_replace' => [
        'intro' => [
            'The TextCraft Find and Replace tool lets you search for specific words, phrases, or characters in your text and replace them with something else. It supports case-sensitive and case-insensitive matching, whole-word matching, and provides a live preview of all changes before you apply them.',
            'This versatile search and replace tool processes everything locally in your browser, making it ideal for editing sensitive documents, code refactoring, and bulk text corrections without exposing your data to external servers. The instant preview shows exactly how many matches will be replaced.',
        ],
        'how_to' => [
            ['title' => 'Enter Your Text', 'desc' => 'Paste the text you want to edit into the input area. The tool shows character count, word count, and line count for your reference.'],
            ['title' => 'Set Find and Replace Values', 'desc' => 'Type the word or phrase to find in the Find field and your replacement text in the Replace field. Toggle case sensitivity and whole-word matching as needed.'],
            ['title' => 'Preview and Apply', 'desc' => 'Review the match count and preview of changes, then click Replace All to apply. Copy the result or download it as a text file.'],
        ],
        'features' => [
            ['icon' => "\xE2\x9A\xA1", 'title' => 'Instant Preview', 'desc' => 'See the number of matches and a preview of replacements instantly as you type in the find field, before applying any changes.'],
            ['icon' => "\xF0\x9F\x8E\xAF", 'title' => 'Advanced Matching Options', 'desc' => 'Toggle case-sensitive matching and whole-word matching for precise control over which occurrences are found and replaced.'],
            ['icon' => "\xF0\x9F\x94\x92", 'title' => 'Private Processing', 'desc' => 'All find and replace operations run locally in your browser. Your text never reaches any server for complete confidentiality.'],
            ['icon' => "\xF0\x9F\x93\x8B", 'title' => 'One-Click Export', 'desc' => 'Copy the edited text to your clipboard or download it as a .txt file with a single click after replacements are applied.'],
            ['icon' => "\xF0\x9F\x8E\x81", 'title' => 'Free Unlimited Use', 'desc' => 'No registration, no subscriptions, no usage limits. Use find and replace as many times as you need across unlimited projects.'],
        ],
        'benefits' => [
            ['title' => 'Bulk Editing Power', 'desc' => 'Replace hundreds of occurrences of a word or phrase across an entire document in one operation instead of manual search and edit.'],
            ['title' => 'Precision Control', 'desc' => 'Case-sensitive and whole-word options ensure you replace exactly what you intend without affecting similar words or partial matches.'],
            ['title' => 'Complete Privacy', 'desc' => 'Keep sensitive documents private with browser-only processing. Perfect for legal, financial, and confidential business editing.'],
            ['title' => 'Error Reduction', 'desc' => 'Preview match counts before applying replacements to verify accuracy, reducing the risk of unintended text changes in your document.'],
        ],
        'use_cases' => [
            ['title' => 'Writers and Editors', 'desc' => 'Fix recurring typos, update character names, or standardise terminology across entire manuscripts and documents.'],
            ['title' => 'Web Developers', 'desc' => 'Perform bulk find and replace operations on code snippets, configuration files, and text-based data during development and refactoring.'],
            ['title' => 'Content Managers', 'desc' => 'Update outdated brand names, product references, or legal disclaimers across multiple content pieces quickly and consistently.'],
            ['title' => 'Data Analysts', 'desc' => 'Standardise terminology, fix formatting inconsistencies, and clean text data before importing into analysis tools and databases.'],
        ],
        'why_choose' => [
            ['title' => 'No Installation Required', 'desc' => 'Open your browser and start editing immediately. No software downloads, plugins, or complex setup needed.'],
            ['title' => 'Always Free', 'desc' => 'Full find and replace functionality with advanced options available at no cost. No premium tiers or usage restrictions.'],
            ['title' => 'Privacy First', 'desc' => 'Client-side processing ensures your content stays on your device. No server transmissions, logging, or data storage.'],
            ['title' => 'Live Preview', 'desc' => 'See match counts update in real time as you type, giving you confidence in your replacements before applying them.'],
        ],
        'faq' => [
            ['question' => 'Does the tool support case-insensitive search?', 'answer' => 'Yes, you can toggle between case-sensitive and case-insensitive matching with a single switch, giving you flexibility in how matches are detected.'],
            ['question' => 'Can I replace whole words only?', 'answer' => 'Yes, the whole-word matching option ensures that only complete words are matched, preventing partial matches within larger words.'],
            ['question' => 'Does the tool show how many matches were found?', 'answer' => 'Yes, the match count is displayed in real time as you type your search term, showing exactly how many occurrences will be replaced.'],
            ['question' => 'Is my text stored or logged by this tool?', 'answer' => 'No. All find and replace operations run locally in your browser. No text is transmitted to, stored on, or logged by any server.'],
            ['question' => 'Can I replace special characters and punctuation?', 'answer' => 'Yes, the tool works with any characters including punctuation, symbols, numbers, and special characters in both the find and replace fields.'],
        ],
    ],

    'textcraft_reverse_text' => [
        'intro' => [
            'TextCraft Reverse Text tool flips your text in multiple ways — reverse characters, reverse words, or reverse entire sentences — all within your browser for complete privacy. No server-side processing means your content stays safely on your device.',
            'This versatile reversing tool offers three distinct modes: character reversal, word order reversal, and sentence order reversal. Each mode gives you a different perspective on your text, useful for puzzles, creative writing, and linguistic analysis.',
        ],
        'how_to' => [
            ['title' => 'Enter Your Text', 'desc' => 'Type or paste your text into the input field. The tool accepts any text including paragraphs, sentences, and special characters.'],
            ['title' => 'Select Reversal Mode', 'desc' => 'Choose between reversing all characters, reversing word order, or reversing sentence order depending on your needs.'],
            ['title' => 'Copy the Result', 'desc' => 'Click the Reverse button to process your text, then copy the result or download it as a text file.'],
        ],
        'features' => [
            ['icon' => "\xE2\x9A\xA1", 'title' => 'Three Reversal Modes', 'desc' => 'Choose from character reversal, word order reversal, or sentence order reversal for different text transformation needs.'],
            ['icon' => "\xF0\x9F\x94\x92", 'title' => 'Client-Side Processing', 'desc' => 'All reversing happens in your browser. Your text is never sent to any server, keeping it completely private.'],
            ['icon' => "\xF0\x9F\x93\xB1", 'title' => 'Works on Mobile', 'desc' => 'Fully responsive design works on phones, tablets, and desktop computers with no app installation required.'],
            ['icon' => "\xF0\x9F\x93\x8B", 'title' => 'Copy and Download', 'desc' => 'Export your reversed text to clipboard or save as a text file with a single click.'],
            ['icon' => "\xF0\x9F\x8E\x81", 'title' => 'Completely Free', 'desc' => 'No registration, no limits, no premium tiers. Use the reverse text tool as many times as you need.'],
        ],
        'benefits' => [
            ['title' => 'Creative Writing Tool', 'desc' => 'Discover new perspectives on your writing by reversing text to check rhythm, flow, and word patterns.'],
            ['title' => 'Puzzle and Game Helper', 'desc' => 'Solve backwards text puzzles, decode hidden messages, and play word games with instant reversal.'],
            ['title' => 'Linguistic Analysis', 'desc' => 'Study word patterns, palindromes, and text symmetry by reversing characters and words.'],
            ['title' => 'Instant Results', 'desc' => 'Get reversed text immediately with no processing delays or server communication.'],
        ],
        'use_cases' => [
            ['title' => 'Writers', 'desc' => 'Check your prose from a different angle by reversing text to spot patterns and improve word variety.'],
            ['title' => 'Students', 'desc' => 'Complete language exercises, study palindromes, and explore text manipulation for educational projects.'],
            ['title' => 'Puzzle Enthusiasts', 'desc' => 'Decode ciphers, solve backwards text riddles, and create puzzles for others to solve.'],
            ['title' => 'Developers', 'desc' => 'Test string manipulation logic, verify palindrome detection algorithms, and debug text processing code.'],
        ],
        'why_choose' => [
            ['title' => 'No Software Needed', 'desc' => 'Works entirely in your browser without downloading or installing any software.'],
            ['title' => 'Always Free', 'desc' => 'Full functionality available at no cost with no usage restrictions.'],
            ['title' => 'Privacy Protected', 'desc' => 'Your text never leaves your browser, ensuring complete confidentiality.'],
            ['title' => 'Easy to Use', 'desc' => 'Simple interface with clear mode selection and instant results.'],
        ],
        'faq' => [
            ['question' => 'What is the difference between the three reversal modes?', 'answer' => 'Character reversal flips every character (hello becomes olleh). Word reversal reverses the order of words (hello world becomes world hello). Sentence reversal reverses the order of sentences in your text.'],
            ['question' => 'Does reversing affect special characters and numbers?', 'answer' => 'Yes, all characters including numbers, symbols, and punctuation are reversed in character mode. Word and sentence modes preserve character order within words and sentences.'],
            ['question' => 'Can I reverse very long texts?', 'answer' => 'The tool handles large texts efficiently in your browser. Very long documents may take slightly longer depending on your device performance.'],
            ['question' => 'Is my text kept private?', 'answer' => 'Absolutely. All processing happens locally in your browser. No text data is transmitted to or stored on any external server.'],
            ['question' => 'Can I reverse text back to its original form?', 'answer' => 'Yes, applying the same reversal mode twice will restore your original text. For example, reversing characters twice returns the original character order.'],
        ],
    ],

    'textcraft_password_generator' => [
        'intro' => [
            'The TextCraft Password Generator creates strong, random passwords with customizable length, character sets, and complexity options. Generate secure passwords entirely in your browser without any data leaving your device.',
            'Choose from uppercase, lowercase, numbers, and symbols, exclude ambiguous characters, and generate multiple passwords at once. The built-in strength meter shows you exactly how secure your generated password is.',
        ],
        'how_to' => [
            ['title' => 'Set Password Options', 'desc' => 'Adjust the password length slider and select which character types to include: uppercase, lowercase, numbers, and symbols.'],
            ['title' => 'Generate Passwords', 'desc' => 'Click Generate to create secure passwords. Adjust the count to generate multiple passwords at once.'],
            ['title' => 'Copy or Download', 'desc' => 'Copy individual passwords to your clipboard or download all generated passwords as a text file.'],
        ],
        'features' => [
            ['icon' => "\xF0\x9F\x94\x92", 'title' => 'Cryptographically Secure', 'desc' => 'Uses the browser Crypto API for true randomness, not predictable pseudo-random generators.'],
            ['icon' => "\xF0\x9F\x8E\xAF", 'title' => 'Customizable Complexity', 'desc' => 'Control length, character types, and exclude ambiguous characters for your specific security needs.'],
            ['icon' => "\xE2\x9A\xA1", 'title' => 'Instant Generation', 'desc' => 'Passwords are created instantly with no server communication or processing delays.'],
            ['icon' => "\xF0\x9F\x93\x8B", 'title' => 'Batch Generation', 'desc' => 'Generate multiple passwords at once for teams, accounts, or security audits.'],
            ['icon' => "\xF0\x9F\x8E\x81", 'title' => 'Always Free', 'desc' => 'No registration, no limits, no premium features. Generate unlimited passwords at no cost.'],
        ],
        'benefits' => [
            ['title' => 'Maximum Security', 'desc' => 'Strong random passwords protect your accounts from brute force and dictionary attacks.'],
            ['title' => 'Complete Privacy', 'desc' => 'Passwords are generated locally and never transmitted, so no one else can see them.'],
            ['title' => 'Saves Time', 'desc' => 'Create complex passwords in seconds instead of struggling to invent them manually.'],
            ['title' => 'Meets Compliance', 'desc' => 'Generate passwords that meet corporate security policies and regulatory requirements.'],
        ],
        'use_cases' => [
            ['title' => 'Individual Users', 'desc' => 'Create strong unique passwords for email, banking, social media, and other online accounts.'],
            ['title' => 'IT Administrators', 'desc' => 'Generate secure passwords for system accounts, service accounts, and emergency access credentials.'],
            ['title' => 'Security Teams', 'desc' => 'Produce batch passwords for employee onboarding, password resets, and security audits.'],
            ['title' => 'Developers', 'desc' => 'Generate API keys, database credentials, and secure tokens during application development.'],
        ],
        'why_choose' => [
            ['title' => 'True Randomness', 'desc' => 'Uses cryptographic random number generation for passwords that cannot be predicted or guessed.'],
            ['title' => 'Zero Risk', 'desc' => 'Since generation happens in your browser, passwords are never exposed to third parties.'],
            ['title' => 'Flexible Options', 'desc' => 'Fine-tune password properties to match any security policy or personal preference.'],
            ['title' => 'No Account Required', 'desc' => 'Start generating secure passwords immediately with no sign-up or registration needed.'],
        ],
        'faq' => [
            ['question' => 'How secure are the generated passwords?', 'answer' => 'The passwords use cryptographically secure random generation via the browser Crypto API, making them as secure as any randomly generated password of the same length and complexity.'],
            ['question' => 'What does excluding ambiguous characters do?', 'answer' => 'It removes characters that look similar, such as 0/O, 1/l/I, and others, to prevent confusion when reading or typing passwords.'],
            ['question' => 'Are my passwords stored anywhere?', 'answer' => 'No. Passwords are generated entirely in your browser and are never sent to any server. They exist only on your screen until you copy or save them.'],
            ['question' => 'How many passwords can I generate at once?', 'answer' => 'You can generate up to 100 passwords simultaneously, making it easy to create batches for multiple accounts or team members.'],
            ['question' => 'What password length is recommended?', 'answer' => 'For most accounts, 16-20 characters provide excellent security. For high-security applications, 24-32 characters are recommended.'],
        ],
    ],

    'textcraft_random_number' => [
        'intro' => [
            'The TextCraft Random Number Generator produces true random numbers within any range you specify. Whether you need a single random integer, multiple unique numbers, or a sequence for lottery picks, this tool generates them instantly in your browser.',
            'Customise the range, count, and format of your random numbers. The tool supports unique number generation to avoid duplicates, making it perfect for lotteries, games, sampling, and any scenario requiring unbiased random selection.',
        ],
        'how_to' => [
            ['title' => 'Set the Range', 'desc' => 'Enter the minimum and maximum values for your random numbers. The tool accepts any integer range.'],
            ['title' => 'Configure Options', 'desc' => 'Choose how many numbers to generate, whether to allow duplicates, and the output format.'],
            ['title' => 'Generate and Export', 'desc' => 'Click Generate to create your random numbers, then copy them to your clipboard or download as a text file.'],
        ],
        'features' => [
            ['icon' => "\xE2\x9A\xA1", 'title' => 'True Random Generation', 'desc' => 'Uses cryptographic random number generation for unbiased, unpredictable results every time.'],
            ['icon' => "\xF0\x9F\x8E\xAF", 'title' => 'Flexible Ranges', 'desc' => 'Set any minimum and maximum range, positive or negative, for complete control over your numbers.'],
            ['icon' => "\xF0\x9F\x94\x92", 'title' => 'Unique Numbers Option', 'desc' => 'Enable unique mode to generate numbers without duplicates, perfect for raffles and sampling.'],
            ['icon' => "\xF0\x9F\x93\x8B", 'title' => 'Multiple Formats', 'desc' => 'Output numbers as a list, comma-separated, one per line, or in custom formats.'],
            ['icon' => "\xF0\x9F\x8E\x81", 'title' => 'Free and Private', 'desc' => 'No registration required. All generation happens in your browser for complete privacy.'],
        ],
        'benefits' => [
            ['title' => 'Fair and Unbiased', 'desc' => 'Cryptographic randomness ensures every number in your range has an equal chance of being selected.'],
            ['title' => 'Instant Results', 'desc' => 'Generate any number of random values instantly with no server delays or processing time.'],
            ['title' => 'Versatile Output', 'desc' => 'Export in multiple formats for easy integration with spreadsheets, applications, and documents.'],
            ['title' => 'Guaranteed Privacy', 'desc' => 'Numbers are generated locally and never transmitted, keeping your selections completely private.'],
        ],
        'use_cases' => [
            ['title' => 'Lottery Players', 'desc' => 'Generate random numbers for lottery picks, raffles, and lucky draws with unique number guarantee.'],
            ['title' => 'Researchers', 'desc' => 'Create random samples, assign participants to groups, and generate randomisation sequences for studies.'],
            ['title' => 'Game Developers', 'desc' => 'Produce random values for game mechanics, loot drops, enemy spawns, and procedural generation.'],
            ['title' => 'Teachers', 'desc' => 'Generate random numbers for classroom activities, math exercises, and student group assignments.'],
        ],
        'why_choose' => [
            ['title' => 'Cryptographic Quality', 'desc' => 'Uses the browser Crypto API for true randomness, suitable for security-sensitive applications.'],
            ['title' => 'No Limitations', 'desc' => 'Generate as many numbers as needed with any range, no caps or restrictions.'],
            ['title' => 'Zero Data Transmission', 'desc' => 'Everything happens locally in your browser, ensuring complete confidentiality.'],
            ['title' => 'Simple Interface', 'desc' => 'Clean, intuitive design makes generating random numbers quick and effortless.'],
        ],
        'faq' => [
            ['question' => 'Are the numbers truly random?', 'answer' => 'Yes, the tool uses the Web Crypto API which provides cryptographically secure random numbers, suitable for any application requiring true randomness.'],
            ['question' => 'Can I generate decimal numbers?', 'answer' => 'The tool currently generates integers. For decimal numbers, you can divide the result by your desired precision factor.'],
            ['question' => 'What is the maximum range?', 'answer' => 'The tool supports ranges from -999,999,999 to 999,999,999, covering virtually any practical use case.'],
            ['question' => 'How does unique mode work?', 'answer' => 'When unique mode is enabled, each number generated is guaranteed to be different from all others in the same batch, with no repeats.'],
            ['question' => 'Can I use this for gambling or betting?', 'answer' => 'The tool generates fair random numbers, but it is designed for educational, development, and general-purpose use. Always gamble responsibly.'],
        ],
    ],

    'textcraft_jpg_to_png' => [
        'intro' => [
            'Convert JPG images to PNG format instantly with the TextCraft JPG to PNG Converter. This free online tool transforms your JPEG photographs and graphics into high-quality PNG files with transparent background support, all processed directly in your browser for complete privacy.',
            'Whether you need lossless image quality, transparency support, or PNG format for web design and graphic projects, this converter handles the transformation quickly and efficiently. No software installation or account registration is required.',
        ],
        'how_to' => [
            ['title' => 'Upload Your JPG Image', 'desc' => 'Select a JPG or JPEG image from your device or drag and drop it into the converter. The tool shows file size and dimensions.'],
            ['title' => 'Start the Conversion', 'desc' => 'Click Convert to transform your JPG image into PNG format using browser-based processing.'],
            ['title' => 'Download the PNG', 'desc' => 'Preview the converted image and download your PNG file with a single click.'],
        ],
        'features' => [
            ['icon' => "\xE2\x9A\xA1", 'title' => 'Instant Conversion', 'desc' => 'Convert JPG to PNG in seconds using efficient browser-based image processing.'],
            ['icon' => "\xF0\x9F\x94\x92", 'title' => '100% Private', 'desc' => 'Images are processed locally in your browser and never uploaded to any server.'],
            ['icon' => "\xF0\x9F\x96\xA5\xEF\xB8\x8F", 'title' => 'Lossless Quality', 'desc' => 'PNG format preserves all image details without compression artifacts.'],
            ['icon' => "\xF0\x9F\x93\xB1", 'title' => 'Mobile Friendly', 'desc' => 'Works on phones, tablets, and desktops with a responsive interface.'],
            ['icon' => "\xF0\x9F\x8E\x81", 'title' => 'Free to Use', 'desc' => 'No registration, no limits, no premium tiers. Convert unlimited images.'],
        ],
        'benefits' => [
            ['title' => 'Lossless Output', 'desc' => 'PNG format preserves every pixel without lossy compression, ideal for graphics and screenshots.'],
            ['title' => 'Transparency Support', 'desc' => 'PNG files support alpha channel transparency for graphic design and web development.'],
            ['title' => 'Complete Privacy', 'desc' => 'Images never leave your browser, making this tool safe for sensitive photos and documents.'],
            ['title' => 'No Installation', 'desc' => 'Works in any modern browser without downloading software or browser extensions.'],
        ],
        'use_cases' => [
            ['title' => 'Web Designers', 'desc' => 'Convert images to PNG for website graphics that need transparency or lossless quality.'],
            ['title' => 'Graphic Designers', 'desc' => 'Prepare images in PNG format for design projects, presentations, and digital artwork.'],
            ['title' => 'Photographers', 'desc' => 'Convert JPG photos to PNG when lossless quality is required for editing or archival.'],
            ['title' => 'Students', 'desc' => 'Convert images for school projects, presentations, and digital assignments.'],
        ],
        'why_choose' => [
            ['title' => 'Fast and Simple', 'desc' => 'Upload, convert, and download in three easy steps with no complex settings.'],
            ['title' => 'Zero Risk', 'desc' => 'Browser-based processing means your images stay completely private on your device.'],
            ['title' => 'High Quality', 'desc' => 'Produces clean PNG files with no compression artifacts or quality loss.'],
            ['title' => 'Always Free', 'desc' => 'Convert as many images as needed without any cost or registration.'],
        ],
        'faq' => [
            ['question' => 'Does converting JPG to PNG increase file size?', 'answer' => 'Yes, PNG files are typically larger than JPG because PNG uses lossless compression. The trade-off is better quality and transparency support.'],
            ['question' => 'Will the conversion reduce image quality?', 'answer' => 'No. The conversion preserves the original image quality. PNG uses lossless compression, so no additional quality loss occurs during conversion.'],
            ['question' => 'Does the tool support batch conversion?', 'answer' => 'Currently, the tool processes one image at a time. You can quickly convert multiple images by repeating the upload and download steps.'],
            ['question' => 'Are my uploaded images stored on a server?', 'answer' => 'No. All image processing happens locally in your browser. Your images are never uploaded to or stored on any external server.'],
            ['question' => 'What is the maximum file size I can convert?', 'answer' => 'The tool supports images up to 50 MB. Larger files may cause slower processing depending on your device capabilities.'],
        ],
    ],

    'textcraft_jpg_to_webp' => [
        'intro' => [
            'Convert JPG images to modern WebP format with the TextCraft JPG to WebP Converter. This free browser-based tool transforms your JPEG photographs into optimised WebP files that offer superior compression and smaller file sizes while maintaining excellent visual quality.',
            'WebP format is widely supported by modern browsers and delivers 25-35% smaller file sizes compared to JPEG at similar quality levels. Use this converter to optimise your images for faster website loading, reduced bandwidth usage, and improved SEO performance.',
        ],
        'how_to' => [
            ['title' => 'Upload Your JPG', 'desc' => 'Select or drag and drop your JPG image into the converter. View file details including size and dimensions.'],
            ['title' => 'Choose Quality Level', 'desc' => 'Adjust the quality slider to balance file size reduction against image quality preservation.'],
            ['title' => 'Download WebP', 'desc' => 'Click Convert and download your optimised WebP file with reduced file size.'],
        ],
        'features' => [
            ['icon' => "\xE2\x9A\xA1", 'title' => 'Browser-Based Conversion', 'desc' => 'Convert images entirely in your browser without uploading to external servers.'],
            ['icon' => "\xF0\x9F\x8E\xAF", 'title' => 'Adjustable Quality', 'desc' => 'Fine-tune the output quality to achieve the perfect balance between file size and visual fidelity.'],
            ['icon' => "\xF0\x9F\x94\x92", 'title' => 'Privacy Focused', 'desc' => 'Images never leave your device during conversion, ensuring complete confidentiality.'],
            ['icon' => "\xF0\x9F\x93\xA6", 'title' => 'Smaller File Sizes', 'desc' => 'WebP format typically produces 25-35% smaller files than JPEG at comparable quality.'],
            ['icon' => "\xF0\x9F\x8E\x81", 'title' => 'Free Unlimited Use', 'desc' => 'No registration, no usage limits. Convert as many images as you need.'],
        ],
        'benefits' => [
            ['title' => 'Faster Websites', 'desc' => 'Smaller image files load faster, improving page speed scores and user experience.'],
            ['title' => 'Bandwidth Savings', 'desc' => 'Reduced file sizes consume less bandwidth, benefiting both site owners and visitors.'],
            ['title' => 'Better SEO', 'desc' => 'Faster page loading contributes to better search engine rankings and Core Web Vitals scores.'],
            ['title' => 'Universal Support', 'desc' => 'WebP is supported by all major browsers including Chrome, Firefox, Safari, and Edge.'],
        ],
        'use_cases' => [
            ['title' => 'Website Owners', 'desc' => 'Optimise images for faster page loading and better user experience across all devices.'],
            ['title' => 'Web Developers', 'desc' => 'Integrate optimised images into web projects for improved performance and reduced hosting costs.'],
            ['title' => 'E-commerce', 'desc' => 'Convert product images to WebP for faster-loading online stores and better conversion rates.'],
            ['title' => 'Content Creators', 'desc' => 'Prepare optimised images for blogs, social media, and digital content platforms.'],
        ],
        'why_choose' => [
            ['title' => 'Modern Format', 'desc' => 'WebP is the current best practice for web images, supported by all major browsers.'],
            ['title' => 'Quality Control', 'desc' => 'Adjustable compression lets you choose the exact quality level for your needs.'],
            ['title' => 'Zero Upload', 'desc' => 'All processing happens locally, keeping your images completely private.'],
            ['title' => 'Instant Results', 'desc' => 'Conversion happens in seconds with no waiting for server processing.'],
        ],
        'faq' => [
            ['question' => 'What quality level should I choose?', 'answer' => 'For photographs, 75-85% quality provides excellent visual quality with significant file size reduction. For graphics with text, 85-95% preserves sharpness.'],
            ['question' => 'Is WebP supported by all browsers?', 'answer' => 'Yes, WebP is supported by Chrome, Firefox, Safari 14+, Edge, and all other modern browsers covering over 95% of global web traffic.'],
            ['question' => 'How much smaller are WebP files compared to JPG?', 'answer' => 'WebP typically produces 25-35% smaller files than JPEG at equivalent visual quality, depending on the image content and compression settings.'],
            ['question' => 'Can I convert WebP back to JPG?', 'answer' => 'Yes, you can use our WebP to JPG converter to transform WebP images back to JPEG format when needed.'],
            ['question' => 'Are my images kept private during conversion?', 'answer' => 'Absolutely. All conversion processing happens locally in your browser. Your images are never uploaded to or stored on any external server.'],
        ],
    ],

    'textcraft_jpg_compressor' => [
        'intro' => [
            'Reduce JPG image file sizes with the TextCraft JPG Compressor. This free online tool optimises JPEG images by reducing their file size while maintaining visual quality, making them perfect for faster website loading, easier email attachments, and efficient storage.',
            'The compressor uses advanced algorithms to strip unnecessary metadata and optimise compression without visible quality loss. Process images directly in your browser for complete privacy and instant results.',
        ],
        'how_to' => [
            ['title' => 'Upload Your JPG', 'desc' => 'Select or drag and drop your JPG image. The tool shows current file size and dimensions.'],
            ['title' => 'Select Compression Level', 'desc' => 'Choose between light, medium, or strong compression to balance file size and quality.'],
            ['title' => 'Download Optimised Image', 'desc' => 'Click Compress and download your optimised JPG with reduced file size.'],
        ],
        'features' => [
            ['icon' => "\xE2\x9A\xA1", 'title' => 'Smart Compression', 'desc' => 'Advanced algorithms reduce file size while preserving visual quality and important details.'],
            ['icon' => "\xF0\x9F\x94\x92", 'title' => 'Private Processing', 'desc' => 'Images are processed locally in your browser without any server uploads.'],
            ['icon' => "\xF0\x9F\x8E\xAF", 'title' => 'Multiple Levels', 'desc' => 'Choose from light, medium, or strong compression for your specific needs.'],
            ['icon' => "\xF0\x9F\x93\xB1", 'title' => 'Before and After', 'desc' => 'See file size reduction percentages and compare original and compressed images.'],
            ['icon' => "\xF0\x9F\x8E\x81", 'title' => 'Free Unlimited', 'desc' => 'Compress as many images as needed with no registration or usage limits.'],
        ],
        'benefits' => [
            ['title' => 'Faster Websites', 'desc' => 'Smaller images load faster, improving page speed and user experience across all devices.'],
            ['title' => 'Storage Savings', 'desc' => 'Compressed images take up less disk space on your devices and cloud storage.'],
            ['title' => 'Email Friendly', 'desc' => 'Reduced file sizes make it easier to send images as email attachments.'],
            ['title' => 'SEO Benefits', 'desc' => 'Faster-loading images improve Core Web Vitals and search engine rankings.'],
        ],
        'use_cases' => [
            ['title' => 'Bloggers', 'desc' => 'Optimise photos and screenshots for faster-loading blog posts and articles.'],
            ['title' => 'E-commerce', 'desc' => 'Compress product images for faster store loading and improved conversion rates.'],
            ['title' => 'Photographers', 'desc' => 'Optimise images for web galleries and portfolios while maintaining quality.'],
            ['title' => 'Business Users', 'desc' => 'Reduce file sizes for presentations, documents, and email attachments.'],
        ],
        'why_choose' => [
            ['title' => 'Quality Preserved', 'desc' => 'Advanced compression maintains visual quality while reducing file sizes significantly.'],
            ['title' => 'Complete Privacy', 'desc' => 'Images never leave your browser, ensuring confidentiality for sensitive photos.'],
            ['title' => 'Instant Results', 'desc' => 'Compression happens in seconds with no waiting for server processing.'],
            ['title' => 'Zero Cost', 'desc' => 'Full compression functionality available at no charge with no restrictions.'],
        ],
        'faq' => [
            ['question' => 'How much can I reduce my JPG file size?', 'answer' => 'Typical compression reduces JPG files by 30-70% depending on the compression level and image content, with minimal visible quality loss.'],
            ['question' => 'Will compression make my images look worse?', 'answer' => 'At light and medium compression levels, quality differences are generally imperceptible. Strong compression may show minor artifacts in detailed areas.'],
            ['question' => 'Can I compress multiple images at once?', 'answer' => 'The tool currently processes one image at a time for optimal results, but you can quickly compress multiple images by repeating the process.'],
            ['question' => 'Does compression remove EXIF data?', 'answer' => 'Yes, metadata including EXIF data is removed during compression to further reduce file size. This includes camera settings, GPS data, and other embedded information.'],
            ['question' => 'Are compressed images suitable for printing?', 'answer' => 'For print use, we recommend light compression or using the original image. Compressed images are optimised for web display and screen viewing.'],
        ],
    ],

    'textcraft_png_compressor' => [
        'intro' => [
            'Reduce PNG image file sizes with the TextCraft PNG Compressor. This free browser-based tool optimises PNG images by applying lossless compression techniques to shrink file sizes without any visible quality loss, ideal for web graphics, screenshots, and transparent images.',
            'PNG files are often larger than necessary due to inefficient encoding. This compressor applies advanced optimisation to reduce file sizes significantly while preserving every pixel of your original image, all processed locally in your browser.',
        ],
        'how_to' => [
            ['title' => 'Upload Your PNG', 'desc' => 'Select or drag and drop your PNG image into the compressor. View the current file size and dimensions.'],
            ['title' => 'Choose Compression', 'desc' => 'Select your preferred compression level. All levels maintain lossless quality for PNG files.'],
            ['title' => 'Download Optimised PNG', 'desc' => 'Click Compress to optimise your image, then download the smaller PNG file.'],
        ],
        'features' => [
            ['icon' => "\xE2\x9A\xA1", 'title' => 'Lossless Compression', 'desc' => 'Reduces file size without any quality loss — every pixel remains identical to the original.'],
            ['icon' => "\xF0\x9F\x94\x92", 'title' => 'Browser-Based', 'desc' => 'All optimisation happens locally in your browser with no server uploads required.'],
            ['icon' => "\xF0\x9F\x93\xA6", 'title' => 'Metadata Removal', 'desc' => 'Strips unnecessary metadata and optimises colour profiles to further reduce file size.'],
            ['icon' => "\xF0\x9F\x8E\xAF", 'title' => 'Transparency Preserved', 'desc' => 'Alpha channel transparency is fully maintained through the compression process.'],
            ['icon' => "\xF0\x9F\x8E\x81", 'title' => 'Free to Use', 'desc' => 'No registration, no limits. Compress unlimited PNG images at no cost.'],
        ],
        'benefits' => [
            ['title' => 'Zero Quality Loss', 'desc' => 'Lossless compression ensures your images look exactly the same after optimisation.'],
            ['title' => 'Faster Loading', 'desc' => 'Smaller PNG files load faster on websites, improving user experience and performance.'],
            ['title' => 'Storage Efficient', 'desc' => 'Reduced file sizes free up disk space on your devices and cloud storage.'],
            ['title' => 'Web Optimised', 'desc' => 'Smaller PNGs improve page speed scores and Core Web Vitals metrics.'],
        ],
        'use_cases' => [
            ['title' => 'Web Developers', 'desc' => 'Optimise UI elements, icons, and graphics for faster web page rendering.'],
            ['title' => 'UI Designers', 'desc' => 'Compress interface assets and design mockups without quality degradation.'],
            ['title' => 'Screenshot Tools', 'desc' => 'Reduce screenshot file sizes for documentation, bug reports, and presentations.'],
            ['title' => 'Content Creators', 'desc' => 'Optimise transparent graphics, logos, and overlays for blogs and social media.'],
        ],
        'why_choose' => [
            ['title' => 'Guaranteed Quality', 'desc' => 'Lossless compression means your images are pixel-perfect after optimisation.'],
            ['title' => 'Complete Privacy', 'desc' => 'Images are processed locally with no external server communication.'],
            ['title' => 'Significant Reduction', 'desc' => 'Achieve 20-60% file size reduction while maintaining identical visual quality.'],
            ['title' => 'Simple Process', 'desc' => 'Upload, compress, and download in three straightforward steps.'],
        ],
        'faq' => [
            ['question' => 'Is PNG compression really lossless?', 'answer' => 'Yes. PNG compression uses lossless algorithms that reduce file size without modifying any pixel data. The output is byte-for-byte identical in visual quality to the input.'],
            ['question' => 'How much file size reduction can I expect?', 'answer' => 'Typical reduction ranges from 20-60% depending on the image content, colour depth, and original encoding efficiency. Simple graphics with fewer colours compress more.'],
            ['question' => 'Will transparency be preserved?', 'answer' => 'Yes, alpha channel transparency is fully preserved during compression. Your transparent PNGs remain transparent after optimisation.'],
            ['question' => 'Can I compress animated PNG files?', 'answer' => 'The tool is optimised for static PNG images. Animated APNG files may not be fully supported at this time.'],
            ['question' => 'Does compression remove image metadata?', 'answer' => 'Yes, unnecessary metadata such as text chunks and colour profiles may be removed to achieve smaller file sizes, while the visual content remains unchanged.'],
        ],
    ],

    'textcraft_pdf_compressor' => [
        'intro' => [
            'PDF Compressor is a fast, secure, and easy-to-use browser-based tool that helps you reduce PDF file size without compromising document quality. Whether you need to email large files, upload documents to websites, share reports, or save storage space, our PDF Compressor makes the process simple and efficient.',
            'Designed for students, professionals, businesses, educators, developers, and everyday users, this free online utility works entirely within your web browser, eliminating the need for software installation, account registration, or expensive desktop applications.',
        ],
        'how_to' => [
            ['title' => 'Upload Your PDF File', 'desc' => 'Click the upload area or drag and drop your PDF document. The tool shows current page count, file size, and document properties.'],
            ['title' => 'Select Compression Level', 'desc' => 'Choose from light, medium, or strong compression levels. The tool displays estimated final file sizes for each option.'],
            ['title' => 'Download Compressed PDF', 'desc' => 'Click Compress to process your document. Download your optimised PDF file with reduced file size.'],
        ],
        'features' => [
            ['icon' => "\xE2\x9A\xA1", 'title' => 'Efficient PDF Compression', 'desc' => 'Reduces PDF file sizes through image optimisation, metadata removal, and efficient stream encoding.'],
            ['icon' => "\xF0\x9F\x94\x92", 'title' => 'Zero Server Uploads', 'desc' => 'All PDF processing happens locally in your browser. Your documents never leave your device.'],
            ['icon' => "\xF0\x9F\x93\x84", 'title' => 'Preserves Document Quality', 'desc' => 'Text remains sharp and searchable after compression. Fonts, hyperlinks, and structure are preserved.'],
            ['icon' => "\xF0\x9F\x8E\x9E\xEF\xB8\x8F", 'title' => 'Multiple Compression Levels', 'desc' => 'Choose from light, medium, or strong compression to balance file size and output quality.'],
            ['icon' => "\xF0\x9F\x8E\x81", 'title' => 'Free Unlimited Documents', 'desc' => 'No registration, no daily limits, no premium fees. Compress as many PDFs as you need.'],
        ],
        'benefits' => [
            ['title' => 'Smaller Email Attachments', 'desc' => 'Compressed PDFs fit within email size limits more easily for convenient document sharing.'],
            ['title' => 'Faster Document Uploads', 'desc' => 'Smaller PDFs upload faster to websites, cloud storage, and client portals.'],
            ['title' => 'Reduced Storage Requirements', 'desc' => 'Compressed PDFs take up less disk space, reducing storage costs and backup times.'],
            ['title' => 'Preserved Document Integrity', 'desc' => 'Pages, formatting, fonts, and hyperlinks remain intact after compression.'],
        ],
        'use_cases' => [
            ['title' => 'Business Professionals', 'desc' => 'Compress PDF reports and proposals before emailing clients.'],
            ['title' => 'Legal Professionals', 'desc' => 'Reduce scanned document PDFs for easier sharing while maintaining readability.'],
            ['title' => 'Students and Academics', 'desc' => 'Compress research papers and theses to meet university file size requirements.'],
            ['title' => 'HR and Admin Staff', 'desc' => 'Optimise scanned application forms and employee documents for digital filing.'],
        ],
        'why_choose' => [
            ['title' => 'Total Privacy', 'desc' => 'Documents are processed locally in your browser with no uploads to external servers.'],
            ['title' => 'Simple and Fast', 'desc' => 'Upload, choose compression level, and download in seconds.'],
            ['title' => 'Always Free', 'desc' => 'All compression levels are available at no cost with no premium upgrades.'],
            ['title' => 'Quality Control', 'desc' => 'Multiple compression levels give you control over quality preservation.'],
        ],
        'faq' => [
            ['question' => 'Will PDF compression reduce the quality of text and images?', 'answer' => 'Text quality remains excellent after compression. Images may see quality reduction depending on the compression level, with strong compression affecting images more.'],
            ['question' => 'Can I compress a password-protected PDF?', 'answer' => 'The tool works with standard unprotected PDFs. Password-protected files should be unlocked first before compression.'],
            ['question' => 'Does compression affect PDF hyperlinks and bookmarks?', 'answer' => 'No, hyperlinks, bookmarks, form fields, and document metadata are all preserved during compression.'],
            ['question' => 'How much can I reduce my PDF file size?', 'answer' => 'Typical compression reduces file size by 30-60% for text-based PDFs and up to 80% for image-heavy documents.'],
            ['question' => 'Is my document data secure during compression?', 'answer' => 'Absolutely. The PDF Compressor processes everything locally in your browser. Your document never leaves your device.'],
        ],
    ],

];
