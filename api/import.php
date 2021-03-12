<?php

    require_once LOGIN_CHECK;

    function import($connection, $data) {

        extract($data);

        $i=0;
        $success=0;
        $failed=0;
        $dublicate=0;
        
        if($categoryID!='') {

            if($_FILES['importFile']['error']==0) {
                
                $extension=explode('.', $_FILES['importFile']['name']);
                $extension=end($extension);
    
                $validCSV=array(
                    'text/csv',
                    'text/plain',
                    'application/csv',
                    'text/comma-separated-values',
                    'application/excel',
                    'application/vnd.ms-excel',
                    'application/vnd.msexcel',
                    'text/anytext',
                    'application/octet-stream',
                    'application/txt'
                );

                /** Check file extension is csv or not */

                if($extension=='csv' && (in_array($_FILES['importFile']['type'], $validCSV))) {

                    $openFile=fopen($_FILES['importFile']['tmp_name'], 'r');

                    $importAppModuleFile=MODULES.'import/'.$action.'/'.SITE_APP.'/'.$importFrom.'.php';
                    

                    if(file_exists($importAppModuleFile)) {

                        require_once $importAppModuleFile;

                    } else {

                        $importModuleFile=MODULES.'import/'.$action.'/'.$importFrom.'.php';

                        if(file_exists($importModuleFile)) {

                            require_once $importModuleFile;

                        } else {

                            $status='warning';
                            $msg='Please select a valid import method';

                        }

                    }
                    
                } else {

                    $msg='Please upload a valid csv file';

                }

            } else {

                $msg='Something is wrong while uploading CSV';

            }

        } else {

            $msg='Please select category';

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'data' => array(
                'success' => ((isset($success)) ? $success : 0 ),
                'failed' => ((isset($failed)) ? $failed : 0 ),
                'dublicate' => ((isset($dublicate)) ? $dublicate : 0 ),
            )
        );
    
        echo json_encode($result, TRUE);

    }