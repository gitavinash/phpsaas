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
    <title>Transaction Scheduling</title>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="<?php echo $host."://".$_SERVER["HTTP_HOST"].'/php-checkoutjs/assets/css/bootstrap.min.css';?>">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <h2>Transaction Scheduling :</h2>
                <form method="POST" id="myform" action="transaction_scheduling_request.php">
                    <table class="table table-bordered table-hover">
                        <tr class="info">
                            <th width="40%">Field Name</th>
                            <th width="60%">Value</th>
                        </tr>         
                        <tr>
                            <td><label> Type of Transaction (eMandate/SI on Cards) <span style="color:red;">*</span></label></td>
                            <td>
                                <select class="form-control" name="transactionType" >
                                    <option value="002">eMandate</option>
                                    <option value="001">SI on Cards</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label> Mandate Registration Id <span style="color:red;">*</span></label></td>
                            <td><input class="form-control" type="text" name="mandateRegistrationId" value=""/></td>
                        </tr>
                        <tr>
                            <td><label> Amount <span style="color:red;">*</span></label></td>
                            <td><input class="form-control" type="text" name="amount" value=""/></td>
                        </tr>
                        <tr>
                            <td><label> Transaction Date <span style="color:red;">*</span></label></td>
                            <td><input class="form-control" type="date" name="transactionDate" value=""/></td>
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