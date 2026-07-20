<h2>Import Orders</h2>

<div id="orderImportFlow">
    <div class="flow-head">
        <div>
            <h3 class="flow-title">Smart Order Import</h3>
            <p class="flow-sub">A guided 4-step import flow to upload, map, validate, and import order data with confidence.</p>
        </div>
        <div class="step-track">
            <span class="step-chip active" id="uploadStepChip"><span class="chip-index">1</span>Upload CSV</span>
            <span class="step-chip" id="mappingStepChip"><span class="chip-index">2</span>Match Columns</span>
            <span class="step-chip" id="previewStepChip"><span class="chip-index">3</span>Preview</span>
            <span class="step-chip" id="resultStepChip"><span class="chip-index">4</span>Result</span>
        </div>
    </div>

    <div class="step-block wizard-step active" id="wizardStep1">
        <h3 class="step-title">Step 1: Select Company &amp; Upload CSV</h3>
        <div class="upload-row" style="margin-bottom: 12px; display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label for="companyId" style="display: block; font-size: 12px; font-weight: 700; color: var(--oi-text); margin-bottom: 4px;">Select Company (Optional)</label>
                <select id="companyId" name="companyId">
                    <option value=""> -- No Company -- </option>
                    <?php
                    if ($checkCompany = mysqli_query($connection, "SELECT company_id, company_title FROM " . TABLE_PREFIX . "company WHERE company_status=1 AND company_site_id=" . SITE_ID)) {
                        while ($companyInfo = mysqli_fetch_assoc($checkCompany)) {
                            echo "<option value='{$companyInfo['company_id']}'>" . htmlspecialchars($companyInfo['company_title'], ENT_QUOTES, 'UTF-8') . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="orderImportFile" style="display: block; font-size: 12px; font-weight: 700; color: var(--oi-text); margin-bottom: 4px;">CSV File</label>
                <input type="file" id="orderImportFile" accept=".csv">
            </div>
        </div>
        <p class="help">Select a company (optional) and upload a CSV file with a header row. In the next step, map CSV headers to required table columns.</p>
        <div class="step-actions align-right">
            <button type="button" class="btn primary" id="loadColumnsBtn">Upload File &amp; Continue</button>
        </div>
    </div>

    <div id="importStatus" class="status"></div>

    <div class="mapping-wrap wizard-step" id="wizardStep2">
        <h3 class="step-title">Step 2: Match Columns</h3>

        <div class="mapping-progress">
            <div class="mapping-progress-head">
                <span>Required fields mapped</span>
                <span id="mappingCountText">0 / 0</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" id="mappingProgressFill"></div>
            </div>
        </div>

        <div class="mapping-toolbar">
            <button type="button" class="btn" id="autoMapBtn">Auto Map</button>
            <button type="button" class="btn" id="resetMapBtn">Reset Mapping</button>
        </div>

        <p class="help">Tip: Select a CSV column, then click a target field to map. Keyboard: select source with Enter/Space, then map on target with Enter/Space.</p>

        <div class="mapping-grid">
            <div class="panel">
                <h4>CSV Columns</h4>
                <div class="source-list" id="sourceColumns"></div>
            </div>
            <div class="panel">
                <h4>Table Columns</h4>
                <div id="targetFields"></div>
            </div>
        </div>

        <div class="step-actions">
            <button type="button" class="btn" id="backToUploadBtn">Back</button>
            <button type="button" class="btn primary" id="goPreviewBtn" disabled>Next Step</button>
        </div>

    </div>

    <div class="wizard-step" id="wizardStep3">
        <h3 class="step-title">Step 3: CSV Preview (Matched Table Values)</h3>
        <div class="sample-wrap" id="sampleWrap"></div>
        <div class="step-actions">
            <button type="button" class="btn" id="backToMatchBtn">Back</button>
            <button type="button" class="btn primary" id="startImportBtn" disabled>Start Import</button>
        </div>
    </div>

    <div class="wizard-step" id="wizardStep4">
        <h3 class="step-title">Step 4: Import Result</h3>
        <div class="result-box" id="importResult"></div>
        <div class="step-actions">
            <!-- <button type="button" class="btn" id="downloadFailedCsvBtn" style="display:none;">Download Failed Rows CSV</button> -->
            <button type="button" class="btn primary" id="importNewProductBtn">Import New Orders</button>
        </div>
    </div>
</div>

<script>
    (function() {
        var fileInput = document.getElementById('orderImportFile');
        var loadBtn = document.getElementById('loadColumnsBtn');
        var autoMapBtn = document.getElementById('autoMapBtn');
        var resetMapBtn = document.getElementById('resetMapBtn');
        var backToUploadBtn = document.getElementById('backToUploadBtn');
        var backToMatchBtn = document.getElementById('backToMatchBtn');
        var downloadFailedCsvBtn = document.getElementById('downloadFailedCsvBtn');
        var importNewProductBtn = document.getElementById('importNewProductBtn');
        var goPreviewBtn = document.getElementById('goPreviewBtn');
        var startImportBtn = document.getElementById('startImportBtn');
        var statusBox = document.getElementById('importStatus');
        var wizardStep1 = document.getElementById('wizardStep1');
        var wizardStep2 = document.getElementById('wizardStep2');
        var wizardStep3 = document.getElementById('wizardStep3');
        var wizardStep4 = document.getElementById('wizardStep4');
        var sourceColumnsBox = document.getElementById('sourceColumns');
        var targetFieldsBox = document.getElementById('targetFields');
        var sampleWrap = document.getElementById('sampleWrap');
        var importResult = document.getElementById('importResult');
        var actionBars = document.querySelectorAll('#orderImportFlow .step-actions');
        var uploadStepChip = document.getElementById('uploadStepChip');
        var mappingStepChip = document.getElementById('mappingStepChip');
        var previewStepChip = document.getElementById('previewStepChip');
        var resultStepChip = document.getElementById('resultStepChip');
        var mappingCountText = document.getElementById('mappingCountText');
        var mappingProgressFill = document.getElementById('mappingProgressFill');

        var targetFields = [];

        var headers = [];
        var sampleRows = [];
        var mapping = {};
        var selectedSourceHeader = '';
        var latestFailedDetails = [];

        function setCurrentStep(stepNumber) {
            var steps = [wizardStep1, wizardStep2, wizardStep3, wizardStep4];
            var chips = [uploadStepChip, mappingStepChip, previewStepChip, resultStepChip];

            for (var i = 0; i < steps.length; i++) {
                if (!steps[i]) {
                    continue;
                }
                steps[i].classList.remove('active');
            }

            for (var j = 0; j < chips.length; j++) {
                if (!chips[j]) {
                    continue;
                }
                chips[j].classList.remove('active');
                chips[j].classList.remove('done');
                if (j + 1 < stepNumber) {
                    chips[j].classList.add('done');
                }
                if (j + 1 === stepNumber) {
                    chips[j].classList.add('active');
                }
            }

            if (steps[stepNumber - 1]) {
                steps[stepNumber - 1].classList.add('active');
            }

            syncActionBars();
        }

        function syncActionBars() {
            var rect = null;
            if (wizardStep1 && typeof wizardStep1.getBoundingClientRect === 'function') {
                rect = wizardStep1.getBoundingClientRect();
            }

            if (!rect || rect.width <= 0) {
                return;
            }

            var left = Math.max(12, Math.round(rect.left));
            var width = Math.max(280, Math.round(rect.width));

            for (var i = 0; i < actionBars.length; i++) {
                actionBars[i].style.left = left + 'px';
                actionBars[i].style.width = width + 'px';
            }
        }

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

        function getTargetKeywordMap() {
            return {
                name: ['name', 'customer', 'fullname', 'full', 'client', 'buyer'],
                user_mobile: ['mobile', 'phone', 'contact', 'cell', 'whatsapp', 'number', 'mobileno', 'phoneno'],
                address: ['address', 'addr', 'street', 'location', 'area', 'house', 'flat'],
                city: ['city', 'town'],
                pin_code: ['pin', 'pincode', 'postal', 'zipcode', 'zip', 'post'],
                state: ['state', 'province', 'region'],
                product: ['product', 'item', 'sku', 'medicine', 'service'],
                delivery_date: ['deliverydate', 'delivery', 'date', 'shipdate', 'shippingdate', 'scheduledate'],
                order_status: ['status', 'orderstatus', 'state']
            };
        }

        function getHeaderScoreForTarget(targetKey, headerName, keywordMap) {
            var normHeader = normalize(headerName);
            var targetNorm = normalize(targetKey);
            var keywords = keywordMap[targetKey] || [];
            var score = 0;

            if (normHeader === targetNorm) {
                score += 120;
            }

            for (var i = 0; i < keywords.length; i++) {
                var kw = normalize(keywords[i]);
                if (!kw) {
                    continue;
                }
                if (normHeader === kw) {
                    score += 100;
                    continue;
                }
                if (normHeader.indexOf(kw) !== -1 || kw.indexOf(normHeader) !== -1) {
                    score += 40;
                }
            }

            if (normHeader.indexOf(targetNorm) !== -1 || targetNorm.indexOf(normHeader) !== -1) {
                score += 20;
            }

            return score;
        }

        function getValuePatternScore(targetKey, value) {
            var text = String(value == null ? '' : value).trim();
            if (!text) {
                return 0;
            }

            var lower = text.toLowerCase();
            var digits = lower.replace(/\D+/g, '');

            if (targetKey === 'user_mobile') {
                return (digits.length >= 10 && digits.length <= 13) ? 7 : 0;
            }

            if (targetKey === 'pin_code') {
                return /^\d{5,6}$/.test(digits) ? 6 : 0;
            }

            if (targetKey === 'delivery_date') {
                var dateLike = /^\d{1,2}[\/\.\-]\d{1,2}([\/\.\-]\d{2,4})?$/.test(lower) ||
                    /^\d{4}[\/\.\-]\d{1,2}[\/\.\-]\d{1,2}$/.test(lower) ||
                    /\b(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)\b/.test(lower);
                return dateLike ? 7 : 0;
            }

            if (targetKey === 'order_status') {
                return /^(pending|completed|cancelled|processing|confirmed|rejected|waiting|rto|delivered|\d+)$/.test(lower) ? 5 : 0;
            }

            if (targetKey === 'city' || targetKey === 'state') {
                return /^[a-z\s\.]{2,}$/i.test(text) ? 2 : 0;
            }

            if (targetKey === 'name') {
                return /^[a-z\s\.]{2,}$/i.test(text) ? 2 : 0;
            }

            return 0;
        }

        function getSampleScoreForTarget(targetKey, headerName) {
            if (!sampleRows || !sampleRows.length) {
                return 0;
            }

            var maxRows = Math.min(sampleRows.length, 5);
            var score = 0;
            for (var i = 0; i < maxRows; i++) {
                var row = sampleRows[i] || {};
                score += getValuePatternScore(targetKey, row[headerName]);
            }
            return score;
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
            var ready = isRequiredMapped();
            startImportBtn.disabled = !ready;
            if (goPreviewBtn) {
                goPreviewBtn.disabled = !ready;
            }
            updateMappingProgress();
        }

        function setSelectedSourceHeader(headerName) {
            selectedSourceHeader = String(headerName || '');
            var sourceItems = sourceColumnsBox.querySelectorAll('.source-col');
            for (var i = 0; i < sourceItems.length; i++) {
                var item = sourceItems[i];
                if (item.getAttribute('data-header') === selectedSourceHeader) {
                    item.classList.add('active-source');
                } else {
                    item.classList.remove('active-source');
                }
            }
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
            renderMappedPreview();
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
                item.setAttribute('tabindex', '0');
                item.setAttribute('role', 'button');
                item.setAttribute('aria-label', 'Select source column ' + h);
                item.addEventListener('dragstart', function(e) {
                    e.dataTransfer.setData('text/plain', this.getAttribute('data-header'));
                });
                item.addEventListener('click', function() {
                    setSelectedSourceHeader(this.getAttribute('data-header'));
                });
                item.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        setSelectedSourceHeader(this.getAttribute('data-header'));
                    }
                });
                sourceColumnsBox.appendChild(item);
            }
        }

        function createDropZone(target) {
            var wrap = document.createElement('div');
            wrap.className = 'drop-zone';
            wrap.setAttribute('data-target', target.key);
            wrap.setAttribute('tabindex', '0');
            wrap.setAttribute('role', 'button');
            wrap.setAttribute('aria-label', 'Map selected source column to ' + target.label);

            var mapped = mapping[target.key];
            if (mapped) {
                var pill = document.createElement('span');
                pill.className = 'mapped-pill';
                pill.innerHTML = '<span>' + mapped + '</span>';

                var clearBtn = document.createElement('button');
                clearBtn.type = 'button';
                clearBtn.textContent = 'x';
                clearBtn.title = 'Clear mapping';
                clearBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    clearMappingKey(target.key);
                });

                pill.appendChild(clearBtn);
                wrap.appendChild(pill);
            } else {
                var empty = document.createElement('span');
                empty.className = 'empty-drop';
                empty.textContent = 'Drop file column here';
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
                    setSelectedSourceHeader(headerName);
                }
            });

            wrap.addEventListener('click', function() {
                if (selectedSourceHeader) {
                    assignMapping(target.key, selectedSourceHeader);
                }
            });

            wrap.addEventListener('keydown', function(e) {
                if ((e.key === 'Enter' || e.key === ' ') && selectedSourceHeader) {
                    e.preventDefault();
                    assignMapping(target.key, selectedSourceHeader);
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

                var title = document.createElement('span');
                title.textContent = target.label + (target.required ? ' *' : '');
                label.appendChild(title);

                var mappedBadge = document.createElement('span');
                mappedBadge.className = 'field-badge ' + (mapping[target.key] ? 'matched' : (target.required ? 'required' : 'optional'));
                mappedBadge.textContent = mapping[target.key] ? 'Matched' : (target.required ? 'Required' : 'Optional');
                label.appendChild(mappedBadge);

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

        function renderMappedPreview() {
            if (!sampleRows.length || !targetFields.length) {
                sampleWrap.innerHTML = '';
                return;
            }

            var previewRows = sampleRows.slice(0, 5);
            var html = '<table><thead><tr>';

            for (var i = 0; i < targetFields.length; i++) {
                html += '<th>' + targetFields[i].label + (targetFields[i].required ? ' *' : '') + '</th>';
            }

            html += '</tr></thead><tbody>';

            for (var r = 0; r < previewRows.length; r++) {
                html += '<tr>';
                for (var c = 0; c < targetFields.length; c++) {
                    var tf = targetFields[c];
                    var mappedHeader = mapping[tf.key] || '';
                    var value = mappedHeader && previewRows[r][mappedHeader] != null ? previewRows[r][mappedHeader] : '';
                    var cellContent = escapeHtml(value);
                    if (tf.key === 'product' && value) {
                        cellContent = '<a href="' + escapeHtml(value) + '" target="_blank">' + escapeHtml(value) + '</a>';
                    }
                    html += '<td>' + cellContent + '</td>';
                }
                html += '</tr>';
            }

            html += '</tbody></table>';
            sampleWrap.innerHTML = html;
        }

        function autoMap() {
            mapping = {};
            var keywordMap = getTargetKeywordMap();
            var usedHeaders = {};
            var orderedTargets = targetFields.slice().sort(function(a, b) {
                if (!!a.required === !!b.required) {
                    return 0;
                }
                return a.required ? -1 : 1;
            });

            for (var i = 0; i < orderedTargets.length; i++) {
                var target = orderedTargets[i];
                var bestHeader = '';
                var bestScore = 0;

                for (var h = 0; h < headers.length; h++) {
                    var headerName = headers[h];
                    if (usedHeaders[headerName]) {
                        continue;
                    }

                    var score = getHeaderScoreForTarget(target.key, headerName, keywordMap) + getSampleScoreForTarget(target.key, headerName);
                    if (score > bestScore) {
                        bestScore = score;
                        bestHeader = headerName;
                    }
                }

                if (bestHeader && bestScore >= 25) {
                    mapping[target.key] = bestHeader;
                    usedHeaders[bestHeader] = true;
                }
            }

            renderTargetFields();
            renderMappedPreview();
            updateImportButtonState();
        }

        function parseJsonResponse(res) {
            return res.text().then(function(text) {
                var payload = String(text || '').trim();
                var jsonStart = payload.indexOf('{');
                var jsonEnd = payload.lastIndexOf('}');

                if (jsonStart !== -1 && jsonEnd !== -1 && jsonEnd > jsonStart) {
                    payload = payload.slice(jsonStart, jsonEnd + 1);
                }

                try {
                    return JSON.parse(payload);
                } catch (e) {
                    throw new Error('Invalid API response: ' + String(text || '').slice(0, 220));
                }
            });
        }

        function escapeHtml(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function csvEscape(value) {
            var text = String(value == null ? '' : value);
            if (text.indexOf('"') !== -1) {
                text = text.replace(/"/g, '""');
            }
            if (/[",\n]/.test(text)) {
                text = '"' + text + '"';
            }
            return text;
        }

        function buildFailedRowsCsv(details) {
            var rows = ['row_number,error'];
            for (var i = 0; i < details.length; i++) {
                var message = String(details[i] || '');
                var rowNumber = '';
                var errorText = message;
                var match = message.match(/^Row\s+(\d+)\s*:\s*(.*)$/i);
                if (match) {
                    rowNumber = match[1];
                    errorText = match[2];
                }
                rows.push(csvEscape(rowNumber) + ',' + csvEscape(errorText));
            }
            return rows.join('\n');
        }

        function downloadFailedRowsCsv(details) {
            if (!details || !details.length) {
                return;
            }

            var csvContent = buildFailedRowsCsv(details);
            var blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'order_import_failed_rows_' + new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-') + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        loadBtn.addEventListener('click', function() {
            importResult.style.display = 'none';
            importResult.innerHTML = '';

            if (!fileInput.files || !fileInput.files[0]) {
                setStatus('Please select a file first.', true);
                return;
            }

            var selectedFile = fileInput.files[0];
            var selectedName = String(selectedFile.name || '').toLowerCase();
            if (selectedName.slice(-4) !== '.csv') {
                setStatus('Please upload CSV file only.', true);
                return;
            }

            setStatus('Reading file and extracting columns...', false);
            loadBtn.disabled = true;

            var formData = new FormData();
            formData.append('action', 'preview');
            formData.append('file', selectedFile);

            fetch('/api/order/import/', {
                    method: 'POST',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    body: formData
                })
                .then(function(res) {
                    return parseJsonResponse(res);
                })
                .then(function(resp) {
                    if (!resp.success) {
                        throw new Error(resp.message || 'Failed to preview file');
                    }

                    headers = resp.headers || [];
                    sampleRows = resp.sampleRows || [];
                    targetFields = Array.isArray(resp.tableColumns) ? resp.tableColumns : [];
                    mapping = {};

                    if (!headers.length) {
                        throw new Error('No header row found in uploaded file.');
                    }

                    if (!targetFields.length) {
                        throw new Error('Order table columns not found for mapping.');
                    }

                    setCurrentStep(2);
                    renderSourceColumns();
                    autoMap();
                    renderMappedPreview();
                    setStatus('Match required columns, then click Next Step.', false);
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

        goPreviewBtn.addEventListener('click', function() {
            if (!isRequiredMapped()) {
                setStatus('Please map all required fields before continuing.', true);
                return;
            }

            renderMappedPreview();
            setCurrentStep(3);
            setStatus('Preview is ready. Click Start Import to continue.', false);
        });

        backToUploadBtn.addEventListener('click', function() {
            setCurrentStep(1);
            setStatus('You are back on Step 1.', false);
        });

        backToMatchBtn.addEventListener('click', function() {
            setCurrentStep(2);
            setStatus('You are back on Step 2.', false);
        });

        importNewProductBtn.addEventListener('click', function() {
            mapping = {};
            headers = [];
            sampleRows = [];
            selectedSourceHeader = '';
            latestFailedDetails = [];

            sourceColumnsBox.innerHTML = '';
            targetFieldsBox.innerHTML = '';
            sampleWrap.innerHTML = '';
            importResult.innerHTML = '';
            importResult.style.display = 'none';
            if (downloadFailedCsvBtn) {
                downloadFailedCsvBtn.style.display = 'none';
            }

            if (fileInput) {
                fileInput.value = '';
            }

            var companySelect = document.getElementById('companyId');
            if (companySelect) {
                companySelect.value = '';
            }

            setCurrentStep(1);
            setStatus('Ready for a new import.', false);
        });

        if (downloadFailedCsvBtn) {
            downloadFailedCsvBtn.addEventListener('click', function() {
                downloadFailedRowsCsv(latestFailedDetails);
            });
        }

        resetMapBtn.addEventListener('click', function() {
            mapping = {};
            renderTargetFields();
            renderMappedPreview();
            updateImportButtonState();
            setCurrentStep(2);
        });

        startImportBtn.addEventListener('click', function() {
            if (!fileInput.files || !fileInput.files[0]) {
                setStatus('File missing. Please upload file again.', true);
                return;
            }

            var selectedFile = fileInput.files[0];
            var selectedName = String(selectedFile.name || '').toLowerCase();
            if (selectedName.slice(-4) !== '.csv') {
                setStatus('Please upload CSV file only.', true);
                return;
            }

            if (!isRequiredMapped()) {
                setStatus('Please map all required fields before import.', true);
                return;
            }

            setCurrentStep(3);

            startImportBtn.disabled = true;
            setStatus('Import in progress. Please wait...', false);

            var formData = new FormData();
            formData.append('action', 'import');
            formData.append('file', selectedFile);
            formData.append('mapping', JSON.stringify(mapping));

            var companySelect = document.getElementById('companyId');
            if (companySelect && companySelect.value) {
                formData.append('companyId', companySelect.value);
            }

            fetch('/api/order/import/', {
                    method: 'POST',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    body: formData
                })
                .then(function(res) {
                    return parseJsonResponse(res);
                })
                .then(function(resp) {
                    if (!resp.success) {
                        throw new Error(resp.message || 'Import failed');
                    }

                    var importedCount = Number(resp.importedCount || 0);
                    var failedCount = Number(resp.failedCount || 0);
                    var totalCount = importedCount + failedCount;
                    var hasFailures = failedCount > 0;
                    var details = Array.isArray(resp.details) ? resp.details : [];
                    latestFailedDetails = details;

                    var html = '';
                    html += '<div class="result-summary">';
                    html += '  <div class="result-head">';
                    html += '    <div>';
                    html += '      <h4 class="result-title">Import Completed</h4>';
                    html += '      <p class="result-subtitle">Processed ' + totalCount + ' records from this file.</p>';
                    html += '    </div>';
                    html += '    <span class="result-badge ' + (hasFailures ? 'warn' : 'ok') + '">' + (hasFailures ? 'Completed with Issues' : 'Completed Successfully') + '</span>';
                    html += '  </div>';
                    html += '  <div class="result-kpis">';
                    html += '    <div class="result-kpi">';
                    html += '      <p class="result-kpi-label">Successful Imports</p>';
                    html += '      <p class="result-kpi-value success">' + importedCount + '</p>';
                    html += '    </div>';
                    html += '    <div class="result-kpi">';
                    html += '      <p class="result-kpi-label">Failed Imports</p>';
                    html += '      <p class="result-kpi-value fail">' + failedCount + '</p>';
                    html += '    </div>';
                    html += '  </div>';

                    if (details.length) {
                        html += '  <div class="result-errors">';
                        html += '    <p class="result-errors-title">Failure Details</p>';
                        html += '    <ul>';
                        for (var i = 0; i < details.length; i++) {
                            html += '<li>' + escapeHtml(details[i]) + '</li>';
                        }
                        html += '    </ul>';
                        html += '  </div>';
                    }

                    html += '</div>';

                    importResult.innerHTML = html;
                    importResult.style.display = 'block';
                    if (downloadFailedCsvBtn) {
                        downloadFailedCsvBtn.style.display = details.length ? 'inline-flex' : 'none';
                    }
                    setCurrentStep(4);
                    setStatus('Import finished.', false);
                })
                .catch(function(err) {
                    setStatus(err.message || 'Import failed.', true);
                })
                .finally(function() {
                    updateImportButtonState();
                });
        });

        setCurrentStep(1);
        syncActionBars();

        window.addEventListener('resize', syncActionBars);
        window.addEventListener('orientationchange', syncActionBars);
    }());
</script>