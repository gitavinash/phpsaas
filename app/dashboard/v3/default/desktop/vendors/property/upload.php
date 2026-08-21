<h2 class="heading"><?php echo 'Import property' ?><span></span>
</h2>
<div class="main-box">
    <section>
        <div class="steps-box">
            <form action="#" method="POST" id="importData">
                <section class="form">
                    <div class="step-open-box step_1_box">
                        <div class="uploadBox form-group">
                            <p><strong>Select .CSV file to import property<sup>*</sup></strong></p>
                            <div id="dropZone">
                                <span class="defaultText">Drag & Drop .CSV files here or click to upload</span>
                            </div>
                            <input type="file" id="fileInput" name="propertyFile" data-limit="1" accept=".csv"
                                style="display: none;" />
                        </div>

                        <!-- Loader -->
                        <div id="loader" style="display:none; margin: 1rem 0;">
                            <img src="<?php echo IMG_LIBRARY ?>loader_aleya.gif" alt="Loading..." width="40">
                            <span>Importing data, please wait...</span>
                        </div>

                        <!-- Status Summary -->
                        <div id="csvResult" class="import-summary" style="display:none;">
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

                        <div class="box-1">
                            <div class="form-group">
                                <a href="#" class="btn popUpBtn" id="startImportProperty"><i class="fi-rr-check"></i>
                                    <span id="btnText">Start import</span></a>
                            </div>
                        </div>
                    </div>
                </section>
            </form>
        </div>
    </section>
</div>