<?php
    date_default_timezone_set('Asia/Calcutta');
    $strCurDate = date('d-m-Y');

    if( isset($_SERVER['HTTPS'] ) ) {
       $host ='https';
    }else{
        $host = 'http';
    }
?>
<html>
<head>
    <title>Stop Payment</title>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="<?php echo $host."://".$_SERVER["HTTP_HOST"].'/php-checkoutjs/assets/css/bootstrap.min.css';?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo $host."://".$_SERVER["HTTP_HOST"].'/php-checkoutjs/assets/js/bootstrap.min.js';?>"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Stop Payment :</h2>
                <form method="POST" id="myform" action="stop_payment_request.php">
                    <table class="table table-bordered table-hover">
                        <tr class="info">
                            <th width="40%">Field Name</th>
                            <th width="60%">Value</th>
                        </tr>
                        <tr>
                            <td><label>Worldline Merchant Transaction ID (From Transaction Scheduling) <span style="color:red;">*</span></label></td>
                            <td><input class="form-control" type="text" name="tpslTransactionID" value=""/></td>
                        </tr>
                        <tr>
                            <td colspan=2>
                                <input class="btn btn-info" type="submit" name="submit" value="Submit" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</body>
</html>