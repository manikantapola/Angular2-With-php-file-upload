<?php
 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type ");

include('Database.php');
$db = new  Database();
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(array('status' => false));
  exit;
}

$path = 'uploads/';

try {
    if (isset($_FILES['file'])) {
        $originalName = $_FILES['file']['name'];
        $ext = '.'.pathinfo($originalName, PATHINFO_EXTENSION);
        $generatedName = md5($_FILES['file']['tmp_name']).$ext;
        $filePath = $path.$generatedName;
    
        if (!is_writable($path)) {
            echo json_encode(array(
                'status' => false,
                'msg'    => 'Destination directory not writable.'
            ));
            exit;
        }
    
        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
    
            $document_name = $originalName;
            $document_path = 'uploads/'.$generatedName;
            $document_size = $_FILES['file']['size'];
            $document_type = pathinfo($document_name, PATHINFO_EXTENSION);
            $created_time = date('y-m-d H:i:s');
            $created_by = 1;
    
            $document_params = array(
                'DocumentName' => $document_name,
                'DocumentPath' => $document_path,
                'DocumentType' => $document_type,
                'DocumentSize' => $document_size,
                'CreatedOn'    => $created_time,
                'CreatedBy'    => $created_by
            );
            $db->insertRecord('documents', $document_params);
            echo json_encode(array(
                'status'        => 'true',
                'originalName'  => $originalName,
                'generatedName' => $generatedName
            ));
            exit;
        }
    }
    else {
        echo json_encode(
            array('status' => false, 'msg' => 'No file uploaded.')
            );
        exit;
    }
} catch (Exception $e) {
    echo json_encode(array(
        'status' => false,
        'msg'    => "Please try again later"
    ));
    exit;
}
 

 
?> 