<?php 

if(isset($_GET['msg']))
{
    $msg = trim($_GET['msg']);
    $msg_arr = explode("|", $msg);

    $count = count($msg_arr);
    $hash = $msg_arr[$count-1]; //Last hash value in pipe generated response

    $admin_data = file_get_contents("./worldline_AdminData.json");
    $mer_array = json_decode($admin_data, true);

    $updated_array = array_slice($msg_arr,0,$count-1,false);
    $new_array = array_push($updated_array,$mer_array['salt']);
    $updated_msg = implode("|", $updated_array);

    $hashed = hash('sha512',$updated_msg); //Hash value of pipe generated response except last value

    if($hash == $hashed)
    {
        echo $msg_arr[3].'|'.$msg_arr[5].'|1';
    }
    else
    {
        echo $msg_arr[3].'|'.$msg_arr[5].'|0';
    }
}
else
{
    echo "ERROR !!! Invalid Input.";
}

?>