<a href="report/sales/<?php echo ((isset($daterange)) ? '?daterange=' . urlencode($daterange) : '') ?>">
    <div class="widget-info" id="totalSalesAmount">
        <div class="info-box">
            <p>Total Sales<span class="right">₹</span>
                <span id="totalSalesAmountValue" class="prefix-rupee"><img
                        src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt=""></span>
                </span>
            </p>
        </div>
        <div class="chart-box">
            <canvas id="totalSalesAmountChart" class="barChart"></canvas>
        </div>
    </div>
</a>