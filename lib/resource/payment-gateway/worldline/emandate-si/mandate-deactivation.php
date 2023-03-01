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
    <title>Mandate Deactivation</title>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="<?php echo $host."://".$_SERVER["HTTP_HOST"].'/php-checkoutjs/assets/css/bootstrap.min.css';?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo $host."://".$_SERVER["HTTP_HOST"].'/php-checkoutjs/assets/js/bootstrap.min.js';?>"></script>
</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <h2>Mandate Deactivation :</h2>

                <form method="POST" id="myform" action="mandate_deactivation_request.php">
                    <table class="table table-bordered table-hover">
                        <tr class="info">
                            <th width="40%">Field Name</th>
                            <th width="60%">Value</th>
                        </tr>
                        <tr class="hidden">
                            <td><label>Type of Transaction (eMandate/SI on Cards) <span style="color:red;">*</span></label></td>
                            <td>
                                <select class="form-control" name="type" >
                                    <option value="002" >eMandate</option>
                                    <option value="001" selected="selected">SI on Cards</option>
                                </select>
                            </td>
                        </tr>        
                        <tr>
                            <td><label>Mandate Registration ID <span style="color:red;">*</span></label></td>
                            <td><input class="form-control" type="text" name="mandateRegistrationID" value=""/></td>
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