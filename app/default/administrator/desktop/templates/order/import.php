<h2>Import Orders</h2>

<style>
    #orderImportFlow {
        --oi-bg: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
        --oi-bg-soft: #f7faff;
        --oi-panel: #ffffff;
        --oi-border: rgba(30, 64, 175, 0.12);
        --oi-text: #0b1220;
        --oi-muted: #56667f;
        --oi-primary: #1d4ed8;
        --oi-primary-strong: #1e40af;
        --oi-primary-soft: #eef4ff;
        --oi-success: #047857;
        --oi-danger: #b91c1c;
        --oi-shadow: 0 16px 30px rgba(15, 23, 42, 0.07);
        --oi-radius: 16px;
    }

    #orderImportFlow {
        background: var(--oi-bg);
        border: 1px solid var(--oi-border);
        border-radius: var(--oi-radius);
        box-shadow: var(--oi-shadow);
        padding: 22px;
        padding-bottom: 104px;
        margin-top: 12px;
        position: relative;
        overflow: visible;
    }

    #orderImportFlow::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: radial-gradient(circle at 90% -20%, rgba(37, 99, 235, 0.14), transparent 36%);
    }

    #orderImportFlow .flow-head {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(15, 23, 42, 0.07);
    }

    #orderImportFlow .flow-title {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.015em;
        color: var(--oi-text);
    }

    #orderImportFlow .flow-sub {
        margin: 6px 0 0;
        font-size: 13px;
        color: var(--oi-muted);
        max-width: 560px;
    }

    #orderImportFlow .step-track {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    #orderImportFlow .step-chip {
        border: 1px solid rgba(148, 163, 184, 0.45);
        background: #ffffff;
        color: #334155;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        transition: all .18s ease;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    #orderImportFlow .step-chip .chip-index {
        width: 18px;
        height: 18px;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.6);
        background: #f8fafc;
        color: #475569;
        font-size: 10px;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
    }

    #orderImportFlow .step-chip.active {
        border-color: rgba(37, 99, 235, 0.4);
        background: var(--oi-primary-soft);
        color: var(--oi-primary);
        box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.12);
    }

    #orderImportFlow .step-chip.active .chip-index {
        border-color: rgba(37, 99, 235, 0.45);
        background: #dbeafe;
        color: #1d4ed8;
    }

    #orderImportFlow .step-chip.done {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #047857;
    }

    #orderImportFlow .step-chip.done .chip-index {
        border-color: #86efac;
        background: #dcfce7;
        color: #047857;
    }

    #orderImportFlow .step-title {
        margin: 0 0 12px;
        font-size: 16px;
        font-weight: 800;
        letter-spacing: -0.01em;
        color: var(--oi-text);
    }

    #orderImportFlow .step-block {
        border: 1px solid var(--oi-border);
        border-radius: 14px;
        background: var(--oi-bg-soft);
        padding: 16px;
    }

    #orderImportFlow .wizard-step {
        display: none;
    }

    #orderImportFlow .wizard-step.active {
        display: block;
        animation: oiFade .2s ease;
    }

    @keyframes oiFade {
        from {
            opacity: 0;
            transform: translateY(4px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #orderImportFlow .upload-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    #orderImportFlow input[type='file'] {
        border: 1px solid #c8d5eb;
        border-radius: 10px;
        padding: 8px;
        min-width: 280px;
        background: #fff;
    }

    #orderImportFlow .btn {
        border: 1px solid #bfd0e9;
        background: #fff;
        color: #1f2937;
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s;
    }

    #orderImportFlow .btn:hover {
        background: #f4f8ff;
        border-color: #8fb0df;
    }

    #orderImportFlow .btn.primary {
        background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
        border-color: var(--oi-primary-strong);
        color: #fff;
    }

    #orderImportFlow .btn.primary:hover {
        filter: brightness(0.98);
    }

    #orderImportFlow .btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    #orderImportFlow .help {
        margin: 10px 0 0;
        font-size: 12px;
        color: var(--oi-muted);
        line-height: 1.5;
    }

    #orderImportFlow .mapping-wrap {
        margin-top: 14px;
        display: none;
    }

    #orderImportFlow .mapping-toolbar {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    #orderImportFlow .step-actions {
        position: fixed;
        bottom: 12px;
        left: 0;
        transform: none;
        z-index: 60;
        margin-top: 14px;
        padding: 12px 14px;
        width: 320px;
        box-sizing: border-box;
        border: 1px solid var(--oi-border);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(6px);
        box-shadow: 0 -4px 14px rgba(15, 23, 42, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    #orderImportFlow .step-actions.align-right {
        justify-content: flex-end;
    }

    #orderImportFlow .mapping-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    #orderImportFlow .mapping-progress {
        margin: 0 0 12px;
        border: 1px solid var(--oi-border);
        border-radius: 12px;
        background: var(--oi-panel);
        padding: 12px;
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
        border-radius: 12px;
        padding: 12px;
        background: var(--oi-panel);
    }

    #orderImportFlow .panel h4 {
        margin: 0 0 10px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--oi-text);
    }

    #orderImportFlow .source-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
    }

    #orderImportFlow .source-col {
        width: 100%;
        box-sizing: border-box;
        padding: 8px 11px;
        background: #f3f7ff;
        border: 1px solid #c9d9f0;
        border-radius: 10px;
        font-size: 12px;
        color: #334155;
        cursor: grab;
        user-select: none;
        transition: all .15s;
    }

    #orderImportFlow .source-col:focus {
        outline: 2px solid rgba(37, 99, 235, 0.35);
        outline-offset: 1px;
    }

    #orderImportFlow .source-col.active-source {
        border-color: #2563eb;
        background: #dbeafe;
        color: #1e3a8a;
        box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.2);
    }

    #orderImportFlow .source-col:hover {
        border-color: #8fb4e5;
        background: #eaf2ff;
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
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    #orderImportFlow .field-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 2px 7px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    #orderImportFlow .field-badge.required {
        color: #9a3412;
        background: #ffedd5;
        border: 1px solid #fed7aa;
    }

    #orderImportFlow .field-badge.optional {
        color: #334155;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
    }

    #orderImportFlow .field-badge.matched {
        color: #065f46;
        background: #d1fae5;
        border: 1px solid #a7f3d0;
    }

    #orderImportFlow .drop-zone {
        min-height: 38px;
        border: 1px dashed #9cb3d6;
        border-radius: 10px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 8px;
        gap: 8px;
    }

    #orderImportFlow .drop-zone:focus {
        outline: 2px solid rgba(37, 99, 235, 0.35);
        outline-offset: 1px;
    }

    #orderImportFlow .drop-zone.drag-over {
        border-color: var(--oi-primary);
        background: var(--oi-primary-soft);
    }

    #orderImportFlow .mapped-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #e7efff;
        color: #1e3a8a;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
    }

    #orderImportFlow .mapped-pill button {
        border: 0;
        background: transparent;
        color: #1e3a8a;
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
        border-radius: 12px;
        background: var(--oi-panel);
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
        background: #f3f7ff;
        color: #334155;
        font-weight: 700;
    }

    #orderImportFlow .status {
        margin-top: 10px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 10px;
        padding: 10px 12px;
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
        border-radius: 12px;
        padding: 12px;
        background: var(--oi-panel);
        display: none;
    }

    #orderImportFlow .result-box ul {
        margin: 8px 0 0;
        padding-left: 18px;
    }

    #orderImportFlow .result-summary {
        display: grid;
        gap: 12px;
    }

    #orderImportFlow .result-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    #orderImportFlow .result-title {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.01em;
        color: #0f172a;
    }

    #orderImportFlow .result-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--oi-muted);
    }

    #orderImportFlow .result-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border: 1px solid;
    }

    #orderImportFlow .result-badge.ok {
        color: #065f46;
        background: #dcfce7;
        border-color: #86efac;
    }

    #orderImportFlow .result-badge.warn {
        color: #9a3412;
        background: #ffedd5;
        border-color: #fdba74;
    }

    #orderImportFlow .result-kpis {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    #orderImportFlow .result-kpi {
        border: 1px solid var(--oi-border);
        border-radius: 12px;
        padding: 12px;
        background: #f8fbff;
    }

    #orderImportFlow .result-kpi-label {
        margin: 0;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #64748b;
    }

    #orderImportFlow .result-kpi-value {
        margin: 6px 0 0;
        font-size: 26px;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
    }

    #orderImportFlow .result-kpi-value.success {
        color: #047857;
    }

    #orderImportFlow .result-kpi-value.fail {
        color: #b91c1c;
    }

    #orderImportFlow .result-errors {
        border: 1px solid #fecaca;
        border-radius: 12px;
        background: #fff7f7;
        padding: 10px 12px;
    }

    #orderImportFlow .result-errors-title {
        margin: 0 0 8px;
        font-size: 12px;
        font-weight: 800;
        color: #991b1b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    #orderImportFlow .result-errors ul {
        margin: 0;
        padding-left: 18px;
    }

    #orderImportFlow .result-errors li {
        font-size: 12px;
        line-height: 1.45;
        color: #7f1d1d;
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

        #orderImportFlow .result-kpis {
            grid-template-columns: 1fr;
        }
    }
</style>

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
        <h3 class="step-title">Step 1: Upload CSV</h3>
        <div class="upload-row">
            <input type="file" id="orderImportFile" accept=".csv">
        </div>
        <p class="help">Upload a CSV file with header row. In the next step, map CSV headers to required table columns.</p>
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
                clearBtn.addEventListener('click', function() {
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
                    html += '<td>' + String(value) + '</td>';
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