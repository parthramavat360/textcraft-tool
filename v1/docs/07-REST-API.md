# TextCraft Tools — REST API

## Endpoint: PDF to Word Conversion

### Registration

Two registration points (both in loader + widget):

**In `class-textcraft-loader.php`:**
```php
add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
```

**In `widget-pdf-to-word.php`:**
```php
add_action( 'rest_api_init', function() {
    register_rest_route( 'textcraft-tools/v1', '/pdf-to-word', [
        'methods'             => 'POST',
        'callback'            => [ Widget_Pdf_To_Word::class, 'rest_convert' ],
        'permission_callback' => function() {
            return current_user_can( 'read' );
        },
    ] );
} );
```

### Endpoint Details

| Field | Value |
|-------|-------|
| Method | `POST` |
| URL | `/wp-json/textcraft-tools/v1/pdf-to-word` |
| Namespace | `textcraft-tools/v1` |
| Permission | `current_user_can('read')` |
| Content Type | `multipart/form-data` |

### Request

**Headers:**
```
X-WP-Nonce: {nonce}
Content-Type: multipart/form-data
```

**Body (FormData):**
```
pdf: {file}  // PDF file upload
```

### Validation

1. **File check** — `$files['pdf']` must exist with `UPLOAD_ERR_OK`
2. **Size limit** — 50 MB default (filterable via `textcraft_pdf_to_word_max_size`)
3. **MIME check** — `finfo(FILEINFO_MIME_TYPE)` must return `application/pdf`
4. **exec() check** — PHP `exec()` must not be disabled
5. **LibreOffice check** — `soffice` binary must be found

### Response (Success)

```json
{
    "filename": "converted.docx",
    "bytes": 12345,
    "mime": "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "content": "base64-encoded-docx-content..."
}
```

### Response (Error)

```json
{
    "code": "no_file",
    "message": "No PDF file received.",
    "data": {
        "status": 400
    }
}
```

### Error Codes

| Code | Status | Description |
|------|--------|-------------|
| `no_exec` | 500 | PHP exec() is disabled |
| `no_libreoffice` | 500 | LibreOffice not installed |
| `no_file` | 400 | No PDF file received |
| `too_large` | 413 | PDF exceeds 50 MB limit |
| `invalid_type` | 415 | Uploaded file is not a PDF |
| `tmp_fail` | 500 | Cannot create temp directory |
| `move_fail` | 500 | Failed to move uploaded file |
| `convert_fail` | 500 | LibreOffice conversion failed |
| `read_fail` | 500 | Could not read converted DOCX |

---

## Server-Side Conversion

### LibreOffice Binary Detection

```php
private static function find_soffice(): ?string
```

Checks in order:
1. `textcraft_soffice_path` filter / `TEXTCRAFT_SOFFICE_PATH` constant
2. `/usr/bin/soffice` (Linux apt)
3. `/usr/local/bin/soffice`
4. `/usr/lib/libreoffice/program/soffice`
5. `/opt/libreoffice/program/soffice`
6. `/Applications/LibreOffice.app/Contents/MacOS/soffice` (macOS)
7. `C:\Program Files\LibreOffice\program\soffice.exe` (Windows)
8. `C:\Program Files (x86)\LibreOffice\program\soffice.exe`
9. `which soffice` (PATH fallback)

### Conversion Command

```bash
HOME={tmp_dir} soffice --headless --norestore --nofirststartwizard \
    --infilter="writer_pdf_import" \
    --convert-to docx \
    --outdir {tmp_dir} \
    {pdf_path} 2>&1
```

**Flags:**
- `--headless` — No GUI
- `--norestore` — Skip crash recovery
- `--nofirststartwizard` — Skip first-run wizard
- `--infilter="writer_pdf_import"` — Use Writer PDF import filter (preserves text, not image-based)

### Conversion Flow

1. Validate uploaded file
2. Create temp directory in `sys_get_temp_dir()`
3. Move uploaded PDF to temp dir
4. Run LibreOffice conversion
5. Find output DOCX (may have different name)
6. Read DOCX content
7. Base64 encode
8. Return JSON response
9. Clean up temp directory

---

## Client-Side Integration

### Frontend JavaScript (in widget)

```javascript
async function convertOnServer(file) {
    var fd = new FormData();
    fd.append('pdf', file, file.name);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', cfg.endpoint, true);
    xhr.setRequestHeader('X-WP-Nonce', cfg.nonce);

    xhr.upload.onprogress = function(e) {
        // Track upload progress (0-60%)
    };

    xhr.onload = function() {
        var payload = JSON.parse(xhr.responseText);
        // Decode base64 → Blob
        var raw = atob(payload.content);
        var buf = new Uint8Array(raw.length);
        for (var i = 0; i < raw.length; i++) buf[i] = raw.charCodeAt(i);
        var blob = new Blob([buf], {
            type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        });
        // Offer download
    };

    xhr.send(fd);
}
```

### Config Passed to JS

```php
$this->render_inline_script(
    'window.tcPdfToWordConfig = ' . wp_json_encode([
        'endpoint' => $endpoint,
        'nonce'    => $nonce,
        'i18n'     => [
            'uploading'     => 'Uploading PDF: %d%%',
            'loadingEngine' => 'Loading PDF engine...',
            'converting'    => 'Converting PDF to DOCX...',
            'done'          => 'Word document ready!',
            'failed'        => 'Conversion failed',
        ],
    ]) . ';'
);
```

---

## Filters

| Filter | Purpose | Default |
|--------|---------|---------|
| `textcraft_pdf_to_word_converter` | Custom conversion API | `null` (use LibreOffice) |
| `textcraft_pdf_to_word_soffice_paths` | LibreOffice binary paths | Array of common paths |
| `textcraft_pdf_to_word_max_size` | Max upload size in bytes | `50 * 1024 * 1024` (50MB) |
| `textcraft_soffice_path` | Single soffice path override | `null` |

### Custom Converter Hook

```php
$custom_result = apply_filters(
    'textcraft_pdf_to_word_converter',
    null,           // Default: use LibreOffice
    $input_path,    // PDF path
    $output_path,   // Expected DOCX path
    $file           // Original upload metadata
);
```

Return:
- `WP_Error` — Return error
- `string` — Path to converted DOCX file
- `null` — Use LibreOffice fallback

---

## Security

- Nonce verification via `X-WP-Nonce` header
- `current_user_can('read')` permission check
- MIME type validation via `finfo()`
- File size limit enforced
- Temp directory cleaned up after conversion
- `escapeshellarg()` used for all shell arguments
