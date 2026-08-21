<a href="report/expenses/<?php echo ((isset($daterange)) ? '?daterange=' . urlencode($daterange) : '') ?>">
    <div class="widget-info" id="totalExpenses">
        <div class="info-box">
            <p>Total Expenses<span class="right"><i class="fi fi-ss-expense"></i></span>
                <span id="totalExpensesValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt=""></span>
                </span>
            </p>
        </div>
        <div class="chart-box">
            <canvas id="totalExpensesChart" class="barChart"></canvas>
        </div>
    </div>
</a>