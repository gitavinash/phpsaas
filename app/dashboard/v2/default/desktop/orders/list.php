<h2 class="heading">Orders List<span>All orders list will be displayed here.</span></h2>
<div class="main-box">
    <div class="filters">
        <div class="form-group">
            <label>Order Status</label>
            <select id="orderStatus">
                <option value="">Order Status</option>
                <option value="1">Confirm</option>
                <option value="2">Pending</option>
                <option value="3">Cancel</option>
            </select>
        </div>
        <div class="form-group right">
            <label>Date</label>
            <input type="text" id="daterangepicker" placeholder="Select date range">
        </div>
    </div>
    <div class="list-box">
        <table id="orderList">
            <tr></tr>
        </table>
    </div>
</div>