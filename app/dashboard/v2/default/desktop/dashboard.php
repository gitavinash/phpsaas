<div class="box-1 dashboard-datepicker">
    <div class="form-group right">
        <label>Reporting period</label>
        <input type="text" class="dateRangePicker" id="dashboardReportingDateRangePicker"
            placeholder="Select date range">
    </div>
</div>
<div class="widget box-3">
    <div class="widget-info" id="totalOrder">
        <div class="info-box">
            <p>Total Orders
                <span id="totalOrderValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt=""></span>
            </p>
        </div>
        <div class="chart-box">
            <canvas id="totalOrderChart"></canvas>
        </div>
    </div>
</div>
<div class="widget box-3">
    <div class="widget-info" id="totalOrderAmount">
        <div class="info-box">
            <p>Total Orders<span class="right">₹</span>
                <span id="totalOrderAmountValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif"
                        alt=""></span>
                </span>
            </p>
        </div>
        <div class="chart-box">
            <canvas id="totalOrderAmountChart"></canvas>
        </div>
    </div>
</div>
<div class="widget box-3">
    <div class="widget-info">
        <div class="info-box" id="recentOrderAmount">
            <p>Recent Orders<span class="right">Last 7 Days</span>
                <span id="recentOrderAmountValue"><img src="<?php echo IMG_LIBRARY ?>icons8/dots-loader.gif" alt="">
                </span>
                </span>
            </p>
        </div>
        <div class="chart-box">
            <canvas id="recentOrderAmountChart"></canvas>
        </div>
    </div>
</div>

<div class="widget box-3-1">
    <div class="widget-info">
        <div class="info-box">
            <p>Daily Visitors<span class="right">Last 30 Days</span></p>
        </div>
    </div>
</div>

<div class="widget box-3-2">
    <div class="widget-info">
        <div class="info-box">
            <p>Recent added products</p>
        </div>
    </div>
</div>


<div class="widget box-2">
    <div class="widget-info">
        <div class="info-box">
            <p>Recent Orders</p>
        </div>
    </div>
</div>

<div class="widget box-2">
    <div class="widget-info">
        <div class="info-box">
            <p>Recent added products</p>
        </div>
    </div>
</div>