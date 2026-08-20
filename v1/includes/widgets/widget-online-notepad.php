<?php
/**
 * Widget: Online Notepad
 */
declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Online_Notepad extends TextCraft_Base_Widget {

    public function get_name(): string  { return 'textcraft_online_notepad'; }
    public function get_title(): string { return esc_html__( 'Online Notepad', 'textcraft-tools' ); }
    public function get_icon(): string  { return 'eicon-editor-bold'; }

    protected function render_tool_content( array $settings ): void {

        $editor_id = 'tc_editor_' . uniqid();

        echo '<p class="tc-text-14 tc-text-muted tc-mb-16">'
            . esc_html__( 'A free browser-based notepad for quick notes, writing drafts, and more. Your content is auto-saved locally — nothing is ever uploaded to any server.', 'textcraft-tools' )
            . '</p>';

        // Top Bar
        echo '<div class="tc-np-top-bar">';
        echo '<button id="copy-'.$editor_id.'">'.esc_html__('📋 Copy','textcraft-tools').'</button>';
        echo '<button id="download-'.$editor_id.'">'.esc_html__('⬇️ Download','textcraft-tools').'</button>';
        echo '<button id="print-'.$editor_id.'">'.esc_html__('🖨️ Print','textcraft-tools').'</button>';
        echo '<button id="clear-'.$editor_id.'">'.esc_html__('🗑️ Clear','textcraft-tools').'</button>';
        echo '<span id="save-'.$editor_id.'">'.esc_html__('✅ Auto-saved','textcraft-tools').'</span>';
        echo '</div>';

        // Editor
        wp_editor(
            '',
            $editor_id,
            [
                'textarea_rows' => 15,
                'media_buttons' => false,
                'quicktags'     => false,
                'tinymce'       => [
                    'menubar'  => 'file edit view insert format tools help',
                    'toolbar1' => 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link',
                    'toolbar2' => 'removeformat | outdent indent',
                    'branding' => false,
                    'height'   => 400,

                    // ✅ Dark content inside editor
                    'content_style' => 'body { background:#050505; color:#ffffff; font-family:Arial; font-size:14px; }'
                ],
            ]
        );

        // Stats
        echo '<div class="tc-np-stats">
            '.esc_html__('Words','textcraft-tools').': <span id="w-'.$editor_id.'">0</span> |
            '.esc_html__('Characters','textcraft-tools').': <span id="c-'.$editor_id.'">0</span> |
            '.esc_html__('Lines','textcraft-tools').': <span id="l-'.$editor_id.'">0</span>
        </div>';

        $id = esc_js($editor_id);

        $this->render_inline_script( <<<JS
(function(){

let editor;
let key = 'tc_notepad_v1';

// Wait for editor
function waitEditor(){
    if(typeof tinymce === 'undefined' || !tinymce.get('{$id}')){
        setTimeout(waitEditor,300);
        return;
    }

    editor = tinymce.get('{$id}');

    // Load saved content
    let saved = localStorage.getItem(key);
    if(saved) editor.setContent(saved);

    updateStats();

    editor.on('keyup change', ()=>{
        updateStats();

        document.getElementById('save-{$id}').textContent='Saving…';

        clearTimeout(window.timer);
        window.timer=setTimeout(()=>{
            localStorage.setItem(key, editor.getContent());
            document.getElementById('save-{$id}').textContent='Auto-saved';
        },600);
    });
}

// Stats
function updateStats(){
    let text = editor.getContent({format:'text'});

    document.getElementById('w-{$id}').textContent =
        text.trim().split(/\\s+/).filter(Boolean).length;

    document.getElementById('c-{$id}').textContent = text.length;

    document.getElementById('l-{$id}').textContent =
        text ? text.split('\\n').length : 0;
}

// Actions
document.addEventListener('DOMContentLoaded', ()=>{

    waitEditor();

    document.getElementById('copy-{$id}').onclick=()=>{
        navigator.clipboard.writeText(editor.getContent({format:'text'}));
    };

    document.getElementById('download-{$id}').onclick = ()=>{

        // Get content
        let html = editor.getContent();

        // Decode escaped HTML
        let textarea = document.createElement('textarea');
        textarea.innerHTML = html;
        let decoded = textarea.value;

        // Optional: remove <p> wrappers
        decoded = decoded
            .replace(/<p>/g, '')
            .replace(/<\/p>/g, '\\n');

        // Wrap full HTML
        let fullHTML = '<!DOCTYPE html>\\n<html>\\n<head>\\n<meta charset="UTF-8">\\n<title>Document</title>\\n</head>\\n<body>\\n'
            + decoded +
            '\\n</body>\\n</html>';

        let blob = new Blob([fullHTML], {type:'text/html'});

        let a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'notepad.html';
        a.click();
    };

    document.getElementById('print-{$id}').onclick=()=>{
        let w=window.open('');
        w.document.write(editor.getContent());
        w.print();
    };

    document.getElementById('clear-{$id}').onclick=()=>{
        if(confirm('Clear all notes? This cannot be undone.')){
            editor.setContent('');
            localStorage.removeItem(key);
            updateStats();
        }
    };

});

})();
JS
        );
    }
}
