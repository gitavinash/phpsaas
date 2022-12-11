<?php

$productStatus = mysqli_real_escape_string($connection, $_GET['status']);
$startFrom = (isset($product) ? $product : 0);

$productStatus = (($productStatus == 'all') ? '' : "AND product_status='{$productStatus}'");

?>
<h2>Add new url redirection</h2>

<div class="form-box">

    <form method="POST" id="redirectionForm">
        <div class="form-group">
            <label>From</label>
            <input type="text" name="fromURL" class="form-control inside-shadow" required>
        </div>
        <div class="form-group">
            <label>To</label>
            <input type="text" name="toURL" class="form-control inside-shadow" required>
        </div>
        <div class="form-group">
            <label>HTTP Status ( Learn more about redirect status : <a
                    href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Redirections" target="_blank">Click
                    here</a>
                )</label>
            <select name="httpCode" class="form-control inside-shadow">
                <option value="301">300 ( Multiple Choice ) </option>
                <option value="301" selected>301 ( Moved Permanently ) </option>
                <option value="301">302 ( Found ) </option>
                <option value="301">303 ( See Other ) </option>
                <option value="301">304 ( Not Modified ) </option>
                <option value="301">307 ( Temporary Redirect ) </option>
                <option value="302">308 ( Permanent Redirect ) </option>
            </select>
        </div>
        <input type="hidden" name="postType" id="type" value="redirection">
        <input type="hidden" name="action" value="publish">
        <input type="submit" class="btn btn-green" value="Add Redirection">
    </form>
</div>

<h2>All redirects</h2>
<div class="options-bar">
    <div class="left-box">
        <form method="GET">
            <input type="hidden" name="type" value="<?php echo $_GET['type']; ?>">
            <select class="form-control" name="status" id="changeStatus">
                <option value="#">Select HTTP Status</option>
                <option value="301">300 ( Multiple Choice ) </option>
                <option value="301">301 ( Moved Permanently ) </option>
                <option value="301">302 ( Found ) </option>
                <option value="301">303 ( See Other ) </option>
                <option value="301">304 ( Not Modified ) </option>
                <option value="301">307 ( Temporary Redirect ) </option>
                <option value="302">308 ( Permanent Redirect ) </option>
            </select>
            <input type="submit" class="btn" value="Filter">
        </form>
    </div>
    <div class="right-box">

    </div>
</div>
<table id="table">
    <thead>
        <tr>
            <!-- <th style="width: 50px"></th> -->
            <th style="width: 400px;">From</th>
            <th style="width: 400px;">To</th>
            <th>HTTP Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php

        if ($dataList = mysqli_query($connection, "SELECT * FROM ".TABLE_PREFIX."redirection WHERE redirection_site_id=".SITE_ID)) {
            if (mysqli_num_rows($dataList) != 0) {
                while ($data = mysqli_fetch_assoc($dataList)) {
                    extract($data) ?>
        <tr>
            <td><?php echo $redirection_from ?></td>
            <td><?php echo $redirection_to ?></td>
            <td><?php echo $redirection_http_status ?></td>
            <td>
                <a href="<?php echo $redirection_from ?>" class="btn btn-sm btn-blue" target="_blank"><i
                        class="fas fa-eye"></i>
                    View</a>
                <a href="#" class="btn btn-sm btn-blue"><i class="fas fa-trash"></i>
                    Delete</a>
            </td>
        </tr>
        <?php
                }
            }
        }

        ?>
    </tbody>
</table>