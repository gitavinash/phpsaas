<div class="package-page-wrapper property-import-wrapper" id="propertyImportApp">
    <div class="package-header-bar">
        <div class="package-header-left">
            <div class="package-header-icon teal">
                <i class="fi fi-rr-cloud-upload"></i>
            </div>
            <div class="package-header-titles">
                <h2>Import Properties</h2>
                <p>Upload a CSV file to add or update properties in your directory.</p>
            </div>
        </div>
        <div class="package-header-actions">
            <a href="/dashboard/vendors/property/list/" class="btn-v3-action"><i class="fi fi-rr-arrow-left"></i> Properties</a>
        </div>
    </div>

    <div class="property-import-card">
        <div class="property-import-intro">
            <span class="property-import-step">01</span>
            <div>
                <h3>Upload property data</h3>
                <p>Use a CSV file with your property details. Existing matching records will be updated.</p>
            </div>
        </div>
        <div class="steps-box">
            <form action="#" method="POST" id="importData">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="uploadBox form-group">
                            <p class="property-import-label"><strong>Select CSV file <sup>*</sup></strong><span>One file per import</span></p>
                            <label id="dropZone" for="fileInput">
                                <span class="defaultText"><i class="fi fi-rr-cloud-upload upload-icon"></i><strong>Drop your CSV file here</strong><small>or click to browse from your computer</small><em>CSV files only</em></span>
                            </label>
                            <input type="file" id="fileInput" name="propertyFile" data-limit="1" accept=".csv"
                                class="d-none" />
                            <div class="property-selected-file" id="propertySelectedFile" aria-live="polite"><i class="fi fi-rr-document"></i><span id="propertySelectedFileName">No file selected</span><button type="button" id="clearPropertyFile" title="Remove selected file">&times;</button></div>
                        </div>

                        <!-- Loader -->
                        <div id="loader" class="property-import-loader is-hidden">
                            <div class="table-loading-spinner"></div>
                            <div><strong>Importing properties</strong><span>Please keep this page open while the CSV is processed.</span></div>
                        </div>

                        <!-- Status Summary -->
                        <div id="csvResult" class="import-summary is-hidden">
                            <div class="summary-header">
                                <h3 class="summary-title">Import Summary</h3>
                                <span class="summary-time" id="summaryTime"></span>
                            </div>

                            <ul class="summary-list">
                                <li><span class="label success">Imported</span><span class="value"
                                        id="importedCount">0</span></li>
                                <li><span class="label warning">Duplicates</span><span class="value"
                                        id="duplicateCount">0</span></li>
                                <li><span class="label danger">Failed</span><span class="value"
                                        id="failedCount">0</span></li>
                            </ul>

                            <div class="summary-actions">
                                <a href="/dashboard/vendors/property/upload/" class="btn btn-primary"
                                    id="btnImportAnother">
                                    <i class="fi-rr-upload"></i> Import another file
                                </a>
                                <a href="/dashboard/vendors/property/list/" class="btn btn-ghost" id="btnRefreshPage">
                                    <i class="fi-rr-rotate-right"></i> Show all hotels
                                </a>
                            </div>
                        </div>

                        <div class="property-import-actions">
                            <a href="#" class="btn btn-primary" id="startImportProperty"><i class="fi fi-rr-upload"></i><span id="btnText">Start import</span></a>
                            <span class="property-import-note"><i class="fi fi-rr-info"></i> Your original file will not be changed.</span>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </div>
</div>