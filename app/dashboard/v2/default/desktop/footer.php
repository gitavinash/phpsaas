				</div>

				<?php

				if (SITE_VERSION != 'mobile') { ?>
				<footer>
				    <div class="credit">
				        <p><a href="<?php echo CREDIT_URL ?>" title="Developed by <?php echo CREDIT ?>" target="_blank">Powered by
				                <a href='https://aleya.in?utm_source=<?php echo strtolower(str_replace(' ', '_', SITE_NAME)) ?>'
				                    target="_blank"><?php echo ucwords(str_replace('_', ' ', SITE_APP_NAME)) ?><sup
				                        class="trademark">TM</sup></a> | Made
				                with <i class="fi-rr-heart"></i> in
				                India by <a href="https://blumox.com?utm_source=aleya&utm_medium=dashboard"><strong>Blumox
				                        Technologies</strong></a></p>
				    </div>
				</footer>
				<?php
				}

				?>
				</main>

				<?php require_once NOTIFICATION . 'index.php' ?>
				<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>sweetalert/sweetalert.min.js" type="text/javascript"></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>moment/moment.min.js' type='text/javascript'></script>
				<script src='<?php echo PLUGIN_LIBRARY ?>daterangepicker/daterangepicker.min.js' type='text/javascript'></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/datatables.min.js" type="text/javascript"></script>
				<script src="https://cdn.datatables.net/fixedheader/3.1.9/js/dataTables.fixedHeader.min.js"></script>
				<script src="https://cdn.datatables.net/fixedcolumns/4.0.0/js/dataTables.fixedColumns.min.js"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>datatable/js/responsive.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>chartjs/chart.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>select2/js/select2.min.js" type="text/javascript"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>image-uploader/js/image-uploader.min.js" type="text/javascript"></script>
				<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
				<script src="<?php echo PLUGIN_LIBRARY ?>drag_and_drop/drag_and_drop.min.js"></script>
				<script src="<?php echo JS_LIBRARY ?>tinymce/tinymce.min.js"></script>
				<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
				<script src="<?php echo ADMIN_JS_LIBRARY ?>script.js"></script>
				<?php
				if (SITE_VERSION == 'mobile') { ?>

				<!-- Mobile JS -->
				<script>
(function() {
    var openBtn = document.getElementById('moreOpen');
    var panel = document.getElementById('morePanel');
    var closeBtn = document.getElementById('moreClose');

    if (!openBtn || !panel || !closeBtn) return;

    function openMore() {
        panel.setAttribute('aria-hidden', 'false');
        document.body.classList.add('more-open');
        try {
            closeBtn.focus();
        } catch (e) {}
    }

    function closeMore() {
        panel.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('more-open');
        try {
            openBtn.focus();
        } catch (e) {}
    }

    // Open/Close controls
    openBtn.addEventListener('click', openMore);
    closeBtn.addEventListener('click', closeMore);

    // Close on Esc
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeMore();
    });

    // Close panel after selecting a menu item (nice on mobile)
    panel.querySelectorAll('.more-item, .logout-btn').forEach(function(el) {
        el.addEventListener('click', closeMore);
    });

    // Optional: close when clicking empty space beside content (only if target is the outer panel)
    panel.addEventListener('click', function(e) {
        if (e.target === panel) closeMore();
    });
})();
				</script>


				<?php
				}
				?>
				<script>
if ($(".editor").length) {
    const editors = document.querySelectorAll('.editor');
    const quillInstances = [];
    editors.forEach((editor, index) => {
        const quill = new Quill(editor, {
            theme: 'snow'
        });

        if ($(`#editor-${index}`).length) {
            const hiddenTextarea = document.getElementById(`editor-${index}`);
            quill.root.innerHTML = hiddenTextarea.value;
            quill.on('text-change', function() {
                hiddenTextarea.value = quill.root.innerHTML;
            });
            quillInstances.push(quill);
        }
    });
}

if ($(".itineraryEditor").length) {

    // Helper: sanitize and return clean plain text
    function sanitizeToPlainText(input) {
        var s = String(input || '');

        // Convert common HTML breaks to line breaks
        s = s.replace(/<\s*br\s*\/?>/gi, '\n')
            .replace(/<\/\s*p\s*>/gi, '\n\n');

        // Decode HTML entities & strip HTML tags
        var div = document.createElement('div');
        div.innerHTML = s;
        var text = div.textContent || '';

        // Normalize whitespace and remove non-printable chars
        text = text.replace(/\r\n?/g, '\n'); // CRLF -> LF
        text = text.replace(/[\u00A0]/g, ' '); // nbsp -> space
        text = text.replace(/[\u200B-\u200D\uFEFF]/g, ''); // zero-width chars
        text = text.replace(/[ \t]+\n/g, '\n') // trim line-end spaces
            .replace(/\n[ \t]+/g, '\n'); // trim line-start spaces
        text = text.replace(/\n{3,}/g, '\n\n'); // collapse blank lines
        text = text.replace(/[ \t]{2,}/g, ' '); // collapse spaces
        text = text.replace(/[^\x09\x0A\x0D\x20-\x7E\u00A0-\uFFFF]/g, ''); // remove control chars

        return text.trim();
    }

    const editors = document.querySelectorAll('.editor');
    const quillInstances = [];

    editors.forEach(function(editor) {
        // find matching textarea for this editor
        var hiddenTextarea = editor.parentElement.querySelector('textarea[name="dayContent[]"]');

        // initialize Quill editor
        var quill = new Quill(editor, {
            theme: 'snow',
            modules: {
                toolbar: false
            } // no toolbar for plain text
        });

        // set initial clean text into Quill
        if (hiddenTextarea) {
            var initialValue = sanitizeToPlainText(hiddenTextarea.value);
            quill.setText(initialValue);
            hiddenTextarea.value = initialValue;

            // keep synced on changes
            quill.on('text-change', function() {
                var cleanValue = sanitizeToPlainText(quill.getText());
                hiddenTextarea.value = cleanValue;
            });
        }

        quillInstances.push(quill);
    });
}
				</script>
				<?php

				if ($site_access_type == 'agent') { ?>
				<script>
const tempId = '<?php echo base64_encode(USER_ID) ?>';
				</script>
				<?php
				}

				?>
				<script src="<?php echo ADMIN_JS ?>app.js"></script>
				<script src="<?php echo ADMIN_JS . SITE_APP ?>.js"></script>
				</body>

				</html>