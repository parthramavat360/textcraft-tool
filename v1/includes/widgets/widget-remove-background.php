<?php
/**
 * Widget: Remove Background from Image
 *
 * @package TextCraft_Tools\Widgets
 */

declare( strict_types = 1 );

namespace TextCraft_Tools\Widgets;

defined( 'ABSPATH' ) || exit;

class Widget_Remove_Background extends TextCraft_Base_Widget {

	public function get_name(): string {
		return 'textcraft_remove_background';
	}

	public function get_title(): string {
		return esc_html__( 'Background Remover', 'textcraft-tools' );
	}

	public function get_icon(): string {
		return 'eicon-image-before-after';
	}

	protected function render_tool_content( array $settings ): void {
		echo '<div class="tc-rbg" data-remove-background>';
		echo '<div class="tc-jc-drop" role="button" tabindex="0" aria-label="' . esc_attr__( 'Click or drag an image to remove the background', 'textcraft-tools' ) . '">';
		echo '<div class="tc-jc-drop__icon">BG</div>';
		echo '<p class="tc-jc-drop__title">' . esc_html__( 'Click to upload or drag and drop an image', 'textcraft-tools' ) . '</p>';
		echo '<p class="tc-jc-drop__hint">' . esc_html__( 'Remove backgrounds from images instantly — export as transparent PNG, all processed locally in your browser for privacy', 'textcraft-tools' ) . '</p>';
		echo '<input type="file" class="tc-jc-upload" accept="image/png,image/jpeg,image/webp,.png,.jpg,.jpeg,.webp">';
		echo '</div>';

		echo '<div class="tc-jc-options">';
		echo '<div class="tc-jc-option">';
		echo '<label class="tc-label" for="tc-rbg-smooth">' . esc_html__( 'Edge Smoothness', 'textcraft-tools' ) . '</label>';
		echo '<div class="tc-jc-range">';
		echo '<input type="range" id="tc-rbg-smooth" class="tc-jc-quality" min="0" max="8" value="2">';
		echo '<span class="tc-jc-quality-value">2px</span>';
		echo '</div></div>';
		echo '<div class="tc-jc-note">' . esc_html__( 'The first run downloads the AI background-removal model, so it takes a bit longer. After that, your browser cache makes future removals much faster.', 'textcraft-tools' ) . '</div>';
		echo '</div>';

		echo '<div class="tc-jc-progress" hidden>';
		echo '<div class="tc-jc-progress__row"><span class="tc-jc-progress-label">' . esc_html__( 'Preparing...', 'textcraft-tools' ) . '</span><span class="tc-jc-progress-pct">0%</span></div>';
		echo '<div class="tc-jc-progress__track"><div class="tc-jc-progress__bar"></div></div>';
		echo '</div>';

		$this->render_button_row(
			[
				[ 'id' => 'tc-rbg-remove',   'label' => '🖼️ ' . esc_html__( 'Remove Background', 'textcraft-tools' ), 'variant' => 'primary', 'disabled' => true ],
				[ 'id' => 'tc-rbg-download', 'label' => esc_html__( 'Download PNG', 'textcraft-tools' ),       'variant' => 'ghost' ],
				[ 'id' => 'tc-rbg-clear',    'label' => esc_html__( 'Clear', 'textcraft-tools' ),              'variant' => 'danger' ],
			]
		);

		$this->render_stat_bar(
			[
				[ 'id' => 'tc-rbg-stat-original', 'label' => esc_html__( 'Original Size', 'textcraft-tools' ) ],
				[ 'id' => 'tc-rbg-stat-output',   'label' => esc_html__( 'Output Size', 'textcraft-tools' ) ],
				[ 'id' => 'tc-rbg-stat-status',   'label' => esc_html__( 'Status', 'textcraft-tools' ) ],
			]
		);

		echo '<div class="tc-jc-results" hidden>';
		echo '<div class="tc-label-row"><span class="tc-label">' . esc_html__( 'Image Preview', 'textcraft-tools' ) . '</span></div>';
		echo '<div class="tc-jc-grid">';
		echo '<div class="tc-jc-card">';
		echo '<div class="tc-jc-card__preview">';
		echo '<div><span>' . esc_html__( 'Original', 'textcraft-tools' ) . '</span><img class="tc-rbg-original" alt=""></div>';
		echo '<div><span>' . esc_html__( 'Background Removed', 'textcraft-tools' ) . '</span><img class="tc-rbg-output tc-rbg-transparent-preview" alt=""></div>';
		echo '</div>';
		echo '<div class="tc-jc-card__body">';
		echo '<div class="tc-jc-card__name tc-rbg-file-name"></div>';
		echo '<div class="tc-jc-card__meta tc-rbg-file-meta">' . esc_html__( 'Ready', 'textcraft-tools' ) . '</div>';
		echo '</div></div></div>';
		echo '</div>';
		echo '</div>';

		$this->render_inline_script( <<<'JS'
(function () {
    var root = document.querySelector('[data-remove-background]');
    if (!root) return;

    var MODULE_URL = 'https://cdn.jsdelivr.net/npm/@imgly/background-removal@1.7.0/+esm';
    var drop = root.querySelector('.tc-jc-drop');
    var upload = root.querySelector('.tc-jc-upload');
    var smooth = root.querySelector('#tc-rbg-smooth');
    var smoothValue = root.querySelector('.tc-jc-quality-value');
    var progress = root.querySelector('.tc-jc-progress');
    var progressBar = root.querySelector('.tc-jc-progress__bar');
    var progressLabel = root.querySelector('.tc-jc-progress-label');
    var progressPct = root.querySelector('.tc-jc-progress-pct');
    var results = root.querySelector('.tc-jc-results');
    var originalImg = root.querySelector('.tc-rbg-original');
    var outputImg = root.querySelector('.tc-rbg-output');
    var fileName = root.querySelector('.tc-rbg-file-name');
    var fileMeta = root.querySelector('.tc-rbg-file-meta');
    var btnRemove = document.getElementById('tc-rbg-remove');
    var btnDownload = document.getElementById('tc-rbg-download');
    var btnClear = document.getElementById('tc-rbg-clear');
    var statOriginal = document.getElementById('tc-rbg-stat-original');
    var statOutput = document.getElementById('tc-rbg-stat-output');
    var statStatus = document.getElementById('tc-rbg-stat-status');
    var selectedFile = null;
    var originalUrl = '';
    var outputUrl = '';
    var outputBlob = null;
    var modulePromise = null;

    btnDownload.style.display = 'none';
    statOriginal.textContent = '-';
    statOutput.textContent = '-';
    statStatus.textContent = 'Ready';

    function formatSize(bytes) {
        return bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : (bytes / 1024).toFixed(1) + ' KB';
    }

    function outputName(name) {
        return name.replace(/\.(png|jpe?g|webp)$/i, '') + '-no-background.png';
    }

    function revoke(url) {
        if (url) URL.revokeObjectURL(url);
    }

    function setProgress(pct, label) {
        progress.hidden = false;
        var value = Math.max(0, Math.min(100, Math.round(pct || 0)));
        progressBar.style.width = value + '%';
        progressPct.textContent = value + '%';
        progressLabel.textContent = label || 'Processing...';
    }

    function loadBackgroundRemoval() {
        if (!modulePromise) {
            modulePromise = import(MODULE_URL).then(function (module) {
                return module.default || module.removeBackground;
            });
        }
        return modulePromise;
    }

    function imageFromBlob(blob) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(blob);
            var img = new Image();
            img.onload = function () {
                URL.revokeObjectURL(url);
                resolve(img);
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('Image decode failed.'));
            };
            img.src = url;
        });
    }

    function canvasToPngBlob(canvas) {
        return new Promise(function (resolve, reject) {
            canvas.toBlob(function (blob) {
                if (blob) {
                    resolve(blob);
                } else {
                    reject(new Error('PNG export failed.'));
                }
            }, 'image/png');
        });
    }

    async function smoothAlphaEdge(blob) {
        var radius = Math.max(0, Math.min(8, parseInt(smooth.value, 10) || 0));
        if (!radius) return blob;

        var img = await imageFromBlob(blob);
        var canvas = document.createElement('canvas');
        canvas.width = img.naturalWidth || img.width;
        canvas.height = img.naturalHeight || img.height;

        var ctx = canvas.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(img, 0, 0);

        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var data = imageData.data;
        var originalAlpha = new Uint8ClampedArray(canvas.width * canvas.height);

        for (var i = 0; i < originalAlpha.length; i++) {
            originalAlpha[i] = data[i * 4 + 3];
        }

        for (var y = 0; y < canvas.height; y++) {
            for (var x = 0; x < canvas.width; x++) {
                var index = y * canvas.width + x;
                var alpha = originalAlpha[index];
                if (alpha === 0 || alpha === 255) continue;

                var sum = 0;
                var count = 0;
                for (var oy = -radius; oy <= radius; oy++) {
                    var ny = y + oy;
                    if (ny < 0 || ny >= canvas.height) continue;
                    for (var ox = -radius; ox <= radius; ox++) {
                        var nx = x + ox;
                        if (nx < 0 || nx >= canvas.width) continue;
                        sum += originalAlpha[ny * canvas.width + nx];
                        count++;
                    }
                }
                data[index * 4 + 3] = Math.round(sum / count);
            }
        }

        ctx.putImageData(imageData, 0, 0);
        return canvasToPngBlob(canvas);
    }

    function isSupportedImage(file) {
        return file && (
            /image\/(png|jpeg|webp)/i.test(file.type) ||
            /\.(png|jpe?g|webp)$/i.test(file.name)
        );
    }

    function loadFile(file) {
        if (!isSupportedImage(file)) {
            statStatus.textContent = 'Unsupported';
            return;
        }

        selectedFile = file;
        outputBlob = null;
        revoke(originalUrl);
        revoke(outputUrl);
        originalUrl = URL.createObjectURL(file);
        outputUrl = '';
        originalImg.src = originalUrl;
        outputImg.removeAttribute('src');
        fileName.textContent = file.name;
        fileName.title = file.name;
        fileMeta.textContent = formatSize(file.size) + ' - ready';
        statOriginal.textContent = formatSize(file.size);
        statOutput.textContent = '-';
        statStatus.textContent = 'Loaded';
        btnRemove.disabled = false;
        btnDownload.style.display = 'none';
        results.hidden = false;
        progress.hidden = true;
        progressBar.style.width = '0%';
        progressPct.textContent = '0%';
    }

    drop.addEventListener('click', function () { upload.click(); });
    drop.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            upload.click();
        }
    });
    drop.addEventListener('dragover', function (event) {
        event.preventDefault();
        drop.classList.add('is-dragging');
    });
    drop.addEventListener('dragleave', function () { drop.classList.remove('is-dragging'); });
    drop.addEventListener('drop', function (event) {
        event.preventDefault();
        drop.classList.remove('is-dragging');
        if (event.dataTransfer.files[0]) loadFile(event.dataTransfer.files[0]);
    });
    upload.addEventListener('change', function () {
        if (upload.files[0]) loadFile(upload.files[0]);
        upload.value = '';
    });
    smooth.addEventListener('input', function () {
        smoothValue.textContent = smooth.value + 'px';
    });

    btnRemove.addEventListener('click', async function () {
        if (!selectedFile) return;

        btnRemove.disabled = true;
        btnRemove.textContent = 'Removing...';
        btnDownload.style.display = 'none';
        statStatus.textContent = 'Processing';
        setProgress(4, 'Loading removal engine...');

        try {
            var removeBackground = await loadBackgroundRemoval();
            setProgress(12, 'Preparing model...');
            outputBlob = await removeBackground(selectedFile, {
                progress: function (key, current, total) {
                    if (!total) return;
                    var pct = 12 + (current / total) * 78;
                    setProgress(pct, 'Downloading model assets...');
                }
            });
            setProgress(94, 'Smoothing transparent edge...');
            outputBlob = await smoothAlphaEdge(outputBlob);
            revoke(outputUrl);
            outputUrl = URL.createObjectURL(outputBlob);
            outputImg.src = outputUrl;
            statOutput.textContent = formatSize(outputBlob.size);
            statStatus.textContent = 'Done';
            fileMeta.textContent = formatSize(selectedFile.size) + ' -> ' + formatSize(outputBlob.size) + ' - transparent PNG';
            btnDownload.style.display = 'inline-flex';
            setProgress(100, 'Background removed!');
        } catch (error) {
            console.error(error);
            statStatus.textContent = 'Failed';
            setProgress(0, 'Removal failed. Please try another image.');
        } finally {
            btnRemove.disabled = false;
            btnRemove.textContent = 'Remove Background';
        }
    });

    btnDownload.addEventListener('click', function () {
        if (!outputBlob || !selectedFile) return;
        var link = document.createElement('a');
        link.href = outputUrl;
        link.download = outputName(selectedFile.name);
        link.click();
    });

    btnClear.addEventListener('click', function () {
        selectedFile = null;
        outputBlob = null;
        revoke(originalUrl);
        revoke(outputUrl);
        originalUrl = '';
        outputUrl = '';
        originalImg.removeAttribute('src');
        outputImg.removeAttribute('src');
        fileName.textContent = '';
        fileName.removeAttribute('title');
        fileMeta.textContent = 'Ready';
        results.hidden = true;
        progress.hidden = true;
        progressBar.style.width = '0%';
        progressPct.textContent = '0%';
        btnRemove.disabled = true;
        btnRemove.textContent = 'Remove Background';
        btnDownload.style.display = 'none';
        statOriginal.textContent = '-';
        statOutput.textContent = '-';
        statStatus.textContent = 'Ready';
    });
})();
JS
		);
	}
}
