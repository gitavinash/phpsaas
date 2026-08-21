<a href="report/visits/<?php echo ((isset($daterange)) ? '?daterange=' . urlencode($daterange) : '') ?>">
    <div class="widget-info" id="totalVisits">
        <div class="info-box">
            <p>Total Visits<span class="right"><i class="fi fi-ss-entrance"></i></span>
                <span id="totalVisitsValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt=""></span>
                </span>
            </p>
        </div>
        <div class="chart-box">
            <canvas id="totalVisitsChart" class="barChart"></canvas>
        </div>
    </div>
</a>