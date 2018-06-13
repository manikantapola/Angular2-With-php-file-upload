<?php
 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type ");
include('db.php');
 
 if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(array('status' => false));
  exit;
}
 
$path = 'uploads/';
 
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
    $stmt = $conn->prepare("INSERT INTO documents (DocumentName, DocumentPath, DocumentType, DocumentSize, CreatedOn, CreatedBy) VALUES(?, ?, ?, ?, ?, ? )");

    $stmt->execute(array($document_name, $document_path, $document_type, $document_size, $created_time, $created_by));

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
 
?> 