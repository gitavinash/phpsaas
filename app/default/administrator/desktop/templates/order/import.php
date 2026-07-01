<h2>Import Orders</h2>

<style>
    #orderImportFlow {
        --oi-bg: #ffffff;
        --oi-bg-soft: #f8fafc;
        --oi-border: rgba(15, 23, 42, 0.08);
        --oi-text: #0f172a;
        --oi-muted: #64748b;
        --oi-primary: #2563eb;
        --oi-primary-soft: #eff6ff;
        --oi-success: #047857;
        --oi-danger: #b91c1c;
        --oi-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    }

    #orderImportFlow {
        background: var(--oi-bg);
        border: 1px solid var(--oi-border);
        border-radius: 14px;
        box-shadow: var(--oi-shadow);
        padding: 20px;
        margin-top: 12px;
    }

    #orderImportFlow .flow-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    #orderImportFlow .flow-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--oi-text);
    }

    #orderImportFlow .flow-sub {
        margin: 5px 0 0;
        font-size: 12px;
        color: var(--oi-muted);
    }

    #orderImportFlow .step-track {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    #orderImportFlow .step-chip {
        border: 1px solid #dbeafe;
        background: #f8fbff;
        color: #1e40af;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    #orderImportFlow .step-chip.active {
        border-color: #bfdbfe;
        background: var(--oi-primary-soft);
        color: var(--oi-primary);
    }

    #orderImportFlow .step-title {
        margin: 0 0 12px;
        font-size: 15px;
        font-weight: 700;
        color: var(--oi-text);
    }

    #orderImportFlow .step-block {
        border: 1px solid var(--oi-border);
        border-radius: 12px;
        background: var(--oi-bg-soft);
        padding: 14px;
    }

    #orderImportFlow .upload-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    #orderImportFlow input[type='file'] {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 6px;
        background: #fff;
    }

    #orderImportFlow .btn {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #1f2937;
        border-radius: 8px;
        padding: 8px 13px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }

    #orderImportFlow .btn:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    #orderImportFlow .btn.primary {
        background: var(--oi-primary);
        border-color: var(--oi-primary);
        color: #fff;
    }

    #orderImportFlow .btn.primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }

    #orderImportFlow .btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    #orderImportFlow .help {
        margin: 10px 0 0;
        font-size: 12px;
        color: var(--oi-muted);
    }

    #orderImportFlow .mapping-wrap {
        margin-top: 18px;
        display: none;
    }

    #orderImportFlow .mapping-toolbar {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    #orderImportFlow .mapping-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 16px;
    }

    #orderImportFlow .mapping-progress {
        margin: 0 0 12px;
        border: 1px solid var(--oi-border);
        border-radius: 10px;
        background: #fff;
        padding: 10px 12px;
    }

    #orderImportFlow .mapping-progress-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: 12px;
        color: var(--oi-muted);
        font-weight: 600;
        margin-bottom: 8px;
    }

    #orderImportFlow .progress-track {
        width: 100%;
        height: 8px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
    }

    #orderImportFlow .progress-fill {
        width: 0%;
        height: 100%;
        background: linear-gradient(90deg, #60a5fa 0%, #2563eb 100%);
        transition: width .2s ease;
    }

    #orderImportFlow .panel {
        border: 1px solid var(--oi-border);
        border-radius: 10px;
        padding: 12px;
        background: #fff;
    }

    #orderImportFlow .panel h4 {
        margin: 0 0 10px;
        font-size: 13px;
        color: var(--oi-text);
    }

    #orderImportFlow .source-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    #orderImportFlow .source-col {
        padding: 7px 10px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 12px;
        color: #334155;
        cursor: grab;
        user-select: none;
        transition: all .15s;
    }

    #orderImportFlow .source-col:hover {
        border-color: #93c5fd;
        background: #eff6ff;
        color: #1d4ed8;
    }

    #orderImportFlow .target-row {
        display: grid;
        grid-template-columns: 160px 1fr;
        gap: 8px;
        align-items: center;
        margin-bottom: 8px;
    }

    #orderImportFlow .target-label {
        font-size: 12px;
        font-weight: 700;
        color: #374151;
    }

    #orderImportFlow .drop-zone {
        min-height: 36px;
        border: 1px dashed #94a3b8;
        border-radius: 8px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 8px;
        gap: 8px;
    }

    #orderImportFlow .drop-zone.drag-over {
        border-color: var(--oi-primary);
        background: var(--oi-primary-soft);
    }

    #orderImportFlow .mapped-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #dbeafe;
        color: #1e40af;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
    }

    #orderImportFlow .mapped-pill button {
        border: 0;
        background: transparent;
        color: #1e40af;
        font-weight: 700;
        cursor: pointer;
        line-height: 1;
    }

    #orderImportFlow .empty-drop {
        color: #94a3b8;
        font-size: 12px;
    }

    #orderImportFlow .sample-wrap {
        margin-top: 14px;
        overflow: auto;
        border: 1px solid var(--oi-border);
        border-radius: 8px;
        background: #fff;
    }

    #orderImportFlow .sample-wrap table {
        width: 100%;
        border-collapse: collapse;
        min-width: 560px;
    }

    #orderImportFlow .sample-wrap th,
    #orderImportFlow .sample-wrap td {
        border-bottom: 1px solid #f3f4f6;
        padding: 8px 10px;
        font-size: 12px;
        text-align: left;
    }

    #orderImportFlow .sample-wrap th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
    }

    #orderImportFlow .status {
        margin-top: 10px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 8px;
        padding: 8px 10px;
        display: none;
    }

    #orderImportFlow .status.ok {
        display: block;
        color: var(--oi-success);
        border: 1px solid #a7f3d0;
        background: #ecfdf5;
    }

    #orderImportFlow .status.err {
        display: block;
        color: var(--oi-danger);
        border: 1px solid #fecaca;
        background: #fef2f2;
    }

    #orderImportFlow .result-box {
        margin-top: 12px;
        border: 1px solid var(--oi-border);
        border-radius: 8px;
        padding: 12px;
        background: #fff;
        display: none;
    }

    #orderImportFlow .result-box ul {
        margin: 8px 0 0;
        padding-left: 18px;
    }

    @media (max-width: 900px) {
        #orderImportFlow .flow-head {
            flex-direction: column;
            align-items: stretch;
        }

        #orderImportFlow .mapping-grid {
            grid-template-columns: 1fr;
        }

        #orderImportFlow .target-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="orderImportFlow">
    <div class="flow-head">
        <div>
            <h3 class="flow-title">Smart Order Import</h3>
            <p class="flow-sub">Upload file, map columns with drag and drop, then run validated import.</p>
        </div>
        <div class="step-track">
            <span class="step-chip active" id="uploadStepChip">Step 1: Upload</span>
            <span class="step-chip" id="mappingStepChip">Step 2: Map &amp; Import</span>
        </div>
    </div>

    <div class="step-block">
        <h3 class="step-title">Step 1: Upload and Read File</h3>
        <div class="upload-row">
            <input type="file" id="orderImportFile" accept=".csv,.xls,.xlsx">
            <button type="button" class="btn" id="loadColumnsBtn">Load Columns</button>
        </div>
        <p class="help">Supported files: CSV, XLS, XLSX. After upload, drag source columns to required fields.</p>
    </div>

    <div id="importStatus" class="status"></div>

    <div class="mapping-wrap" id="mappingWrap">
        <h3 class="step-title">Step 2: Map Columns and Import</h3>

        <div class="mapping-progress">
            <div class="mapping-progress-head">
                <span>Required fields mapped</span>
                <span id="mappingCountText">0 / 6</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="mappingProgressFill"></div>
            </div>
        </div>

        <div class="mapping-toolbar">
            <button type="button" class="btn" id="autoMapBtn">Auto Map</button>
            <button type="button" class="btn" id="resetMapBtn">Reset Mapping</button>
            <button type="button" class="btn primary" id="startImportBtn" disabled>Start Import</button>
        </div>

        <div class="mapping-grid">
            <div class="panel">
                <h4>CSV Columns (Drag these)</h4>
                <div class="source-list" id="sourceColumns"></div>
            </div>
            <div class="panel">
                <h4>System Fields (Drop here)</h4>
                <div id="targetFields"></div>
            </div>
        </div>

        <div class="sample-wrap" id="sampleWrap"></div>
        <div class="result-box" id="importResult"></div>
    </div>
</div>

<script>
    (function() {
        var fileInput = document.getElementById('orderImportFile');
        var loadBtn = document.getElementById('loadColumnsBtn');
        var autoMapBtn = document.getElementById('autoMapBtn');
        var resetMapBtn = document.getElementById('resetMapBtn');
        var startImportBtn = document.getElementById('startImportBtn');
        var statusBox = document.getElementById('importStatus');
        var mappingWrap = document.getElementById('mappingWrap');
        var sourceColumnsBox = document.getElementById('sourceColumns');
        var targetFieldsBox = document.getElementById('targetFields');
        var sampleWrap = document.getElementById('sampleWrap');
        var importResult = document.getElementById('importResult');
        var uploadStepChip = document.getElementById('uploadStepChip');
        var mappingStepChip = document.getElementById('mappingStepChip');
        var mappingCountText = document.getElementById('mappingCountText');
        var mappingProgressFill = document.getElementById('mappingProgressFill');

        var targetFields = [{
                key: 'order_id',
                label: 'Order ID',
                required: true
            },
            {
                key: 'customer_name',
                label: 'Customer Name',
                required: true
            },
            {
                key: 'customer_email',
                label: 'Customer Email',
                required: true
            },
            {
                key: 'order_date',
                label: 'Order Date (YYYY-MM-DD)',
                required: true
            },
            {
                key: 'total_amount',
                label: 'Total Amount',
                required: true
            },
            {
                key: 'status',
                label: 'Status',
                required: true
            }
        ];

        var headers = [];
        var sampleRows = [];
        var mapping = {};

        function setStatus(message, isError) {
            if (!message) {
                statusBox.className = 'status';
                statusBox.textContent = '';
                return;
            }

            statusBox.className = isError ? 'status err' : 'status ok';
            statusBox.textContent = message;
        }

        function normalize(value) {
            return String(value || '').toLowerCase().replace(/[^a-z0-9]+/g, '');
        }

        function isRequiredMapped() {
            for (var i = 0; i < targetFields.length; i++) {
                var f = targetFields[i];
                if (f.required && !mapping[f.key]) {
                    return false;
                }
            }
            return true;
        }

        function updateMappingProgress() {
            var required = 0;
            var mapped = 0;

            for (var i = 0; i < targetFields.length; i++) {
                if (targetFields[i].required) {
                    required++;
                    if (mapping[targetFields[i].key]) {
                        mapped++;
                    }
                }
            }

            var pct = required > 0 ? Math.round((mapped / required) * 100) : 0;
            mappingCountText.textContent = mapped + ' / ' + required;
            mappingProgressFill.style.width = pct + '%';
        }

        function updateImportButtonState() {
            startImportBtn.disabled = !isRequiredMapped();
            updateMappingProgress();
        }

        function clearMappingKey(targetKey) {
            delete mapping[targetKey];
            renderTargetFields();
            updateImportButtonState();
        }

        function assignMapping(targetKey, headerName) {
            for (var key in mapping) {
                if (Object.prototype.hasOwnProperty.call(mapping, key) && mapping[key] === headerName) {
                    delete mapping[key];
                }
            }
            mapping[targetKey] = headerName;
            renderTargetFields();
            updateImportButtonState();
        }

        function renderSourceColumns() {
            sourceColumnsBox.innerHTML = '';
            for (var i = 0; i < headers.length; i++) {
                var h = headers[i];
                var item = document.createElement('div');
                item.className = 'source-col';
                item.draggable = true;
                item.textContent = h;
                item.setAttribute('data-header', h);
                item.addEventListener('dragstart', function(e) {
                    e.dataTransfer.setData('text/plain', this.getAttribute('data-header'));
                });
                sourceColumnsBox.appendChild(item);
            }
        }

        function createDropZone(target) {
            var wrap = document.createElement('div');
            wrap.className = 'drop-zone';
            wrap.setAttribute('data-target', target.key);

            var mapped = mapping[target.key];
            if (mapped) {
                var pill = document.createElement('span');
                pill.className = 'mapped-pill';
                pill.innerHTML = '<span>' + mapped + '</span>';

                var clearBtn = document.createElement('button');
                clearBtn.type = 'button';
                clearBtn.textContent = 'x';
                clearBtn.title = 'Clear mapping';
                clearBtn.addEventListener('click', function() {
                    clearMappingKey(target.key);
                });

                pill.appendChild(clearBtn);
                wrap.appendChild(pill);
            } else {
                var empty = document.createElement('span');
                empty.className = 'empty-drop';
                empty.textContent = 'Drop CSV column here';
                wrap.appendChild(empty);
            }

            wrap.addEventListener('dragover', function(e) {
                e.preventDefault();
                wrap.classList.add('drag-over');
            });

            wrap.addEventListener('dragleave', function() {
                wrap.classList.remove('drag-over');
            });

            wrap.addEventListener('drop', function(e) {
                e.preventDefault();
                wrap.classList.remove('drag-over');
                var headerName = e.dataTransfer.getData('text/plain');
                if (headerName) {
                    assignMapping(target.key, headerName);
                }
            });

            return wrap;
        }

        function renderTargetFields() {
            targetFieldsBox.innerHTML = '';
            for (var i = 0; i < targetFields.length; i++) {
                var target = targetFields[i];
                var row = document.createElement('div');
                row.className = 'target-row';

                var label = document.createElement('div');
                label.className = 'target-label';
                label.textContent = target.label + (target.required ? ' *' : '');

                row.appendChild(label);
                row.appendChild(createDropZone(target));
                targetFieldsBox.appendChild(row);
            }
        }

        function renderSampleTable() {
            if (!sampleRows.length) {
                sampleWrap.innerHTML = '';
                return;
            }

            var html = '<table><thead><tr>';
            for (var i = 0; i < headers.length; i++) {
                html += '<th>' + headers[i] + '</th>';
            }
            html += '</tr></thead><tbody>';

            for (var r = 0; r < sampleRows.length; r++) {
                html += '<tr>';
                for (var c = 0; c < headers.length; c++) {
                    var key = headers[c];
                    var val = sampleRows[r][key] == null ? '' : sampleRows[r][key];
                    html += '<td>' + String(val) + '</td>';
                }
                html += '</tr>';
            }
            html += '</tbody></table>';
            sampleWrap.innerHTML = html;
        }

        function autoMap() {
            mapping = {};
            var normalizedHeaders = {};

            for (var i = 0; i < headers.length; i++) {
                normalizedHeaders[normalize(headers[i])] = headers[i];
            }

            for (var j = 0; j < targetFields.length; j++) {
                var target = targetFields[j];
                var targetNorm = normalize(target.key);
                if (normalizedHeaders[targetNorm]) {
                    mapping[target.key] = normalizedHeaders[targetNorm];
                    continue;
                }

                for (var h = 0; h < headers.length; h++) {
                    var hn = normalize(headers[h]);
                    if (hn.indexOf(targetNorm) !== -1 || targetNorm.indexOf(hn) !== -1) {
                        mapping[target.key] = headers[h];
                        break;
                    }
                }
            }

            renderTargetFields();
            updateImportButtonState();
        }

        loadBtn.addEventListener('click', function() {
            importResult.style.display = 'none';
            importResult.innerHTML = '';

            if (!fileInput.files || !fileInput.files[0]) {
                setStatus('Please select a file first.', true);
                return;
            }

            setStatus('Reading file and extracting columns...', false);
            loadBtn.disabled = true;

            var formData = new FormData();
            formData.append('action', 'preview');
            formData.append('file', fileInput.files[0]);

            fetch('/api/order/import.php', {
                    method: 'POST',
                    body: formData
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(resp) {
                    if (!resp.success) {
                        throw new Error(resp.message || 'Failed to preview file');
                    }

                    headers = resp.headers || [];
                    sampleRows = resp.sampleRows || [];
                    mapping = {};

                    if (!headers.length) {
                        throw new Error('No header row found in uploaded file.');
                    }

                    mappingWrap.style.display = 'block';
                    uploadStepChip.classList.remove('active');
                    mappingStepChip.classList.add('active');
                    renderSourceColumns();
                    autoMap();
                    renderSampleTable();
                    setStatus('Columns loaded. Drag CSV columns to system fields, then start import.', false);
                })
                .catch(function(err) {
                    setStatus(err.message || 'Failed to read file.', true);
                })
                .finally(function() {
                    loadBtn.disabled = false;
                });
        });

        autoMapBtn.addEventListener('click', function() {
            autoMap();
        });

        resetMapBtn.addEventListener('click', function() {
            mapping = {};
            renderTargetFields();
            updateImportButtonState();
        });

        startImportBtn.addEventListener('click', function() {
            if (!fileInput.files || !fileInput.files[0]) {
                setStatus('File missing. Please upload file again.', true);
                return;
            }

            if (!isRequiredMapped()) {
                setStatus('Please map all required fields before import.', true);
                return;
            }

            startImportBtn.disabled = true;
            setStatus('Import in progress. Please wait...', false);

            var formData = new FormData();
            formData.append('action', 'import');
            formData.append('file', fileInput.files[0]);
            formData.append('mapping', JSON.stringify(mapping));

            fetch('/api/order/import.php', {
                    method: 'POST',
                    body: formData
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(resp) {
                    if (!resp.success) {
                        throw new Error(resp.message || 'Import failed');
                    }

                    var html = '<strong>Import completed.</strong><br>' +
                        'Imported: ' + (resp.importedCount || 0) + ' | Failed: ' + (resp.failedCount || 0);

                    if (resp.details && resp.details.length) {
                        html += '<ul>';
                        for (var i = 0; i < resp.details.length; i++) {
                            html += '<li>' + resp.details[i] + '</li>';
                        }
                        html += '</ul>';
                    }

                    importResult.innerHTML = html;
                    importResult.style.display = 'block';
                    setStatus('Import finished.', false);
                })
                .catch(function(err) {
                    setStatus(err.message || 'Import failed.', true);
                })
                .finally(function() {
                    updateImportButtonState();
                });
        });
    }());
</script>