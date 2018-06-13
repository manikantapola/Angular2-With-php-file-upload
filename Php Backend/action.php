<?php
 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type ");
include('db.php');
 

//  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//   echo json_encode(array('status' => "failure", 'message' => 'Request method should be post'));
//   exit;
// }

if(isset($_REQUEST['request']) && $_REQUEST['request'] == 'getList'){
  // echo "test";exit;
    $stmt = $conn->prepare("SELECT * FROM documents WHERE Status = 'Active' ");
    $stmt->execute();

    $results = $stmt->fetchAll();

    $list = array();

    foreach($results as $result){
      
      $list[] = array(
        'DocumentID' => $result['DocumentID'],
        'DocumentName' => $result['DocumentName'],
        'DocumentType' => $result['DocumentType'],
        'DocumentSize' => formatSizeUnits($result['DocumentSize']),
        'CreatedOn' => $result['CreatedOn']
      );
    }

    $response = array("status" => "success", "data"=> $list);

    echo json_encode($response);
    exit;

}


if(isset($_REQUEST['request']) && $_REQUEST['request'] == 'delete'){

  $postdata = file_get_contents("php://input");
  $request = json_decode($postdata);

    $document_id = $request->DocumentID;

    $stmt = $conn->prepare("UPDATE documents SET Status = 'Deleted' WHERE DocumentID = ? ");
    $stmt->execute(array($document_id));

    
    $response = array("status" => "success", "message"=> 'Deleted successfully');

    echo json_encode($response);
    exit;
}

if(isset($_REQUEST['request']) && $_REQUEST['request'] == 'info'){

  $postdata = file_get_contents("php://input");
  $request = json_decode($postdata);

    $document_id = $request->DocumentID;

    $stmt = $conn->prepare("SELECT * FROM documents WHERE DocumentID = ? ");
    $stmt->execute(array($document_id));

    $results = $stmt->fetch();
    $doc_path = $results['DocumentPath'];

    $full_path = "http://localhost/angularTest/".$doc_path;
    $file_info = file_get_contents($full_path);

    
    $response = array("status" => "success", "info"=> $file_info);

    echo json_encode($response);
    exit;
}




 function formatSizeUnits($bytes){
        if ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }elseif ($bytes >= 1048576){
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }elseif ($bytes >= 1024){
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }elseif ($bytes > 1){
            $bytes = $bytes . ' bytes';
        }elseif ($bytes == 1){
            $bytes = $bytes . ' byte';
        }else{
            $bytes = '0 bytes';
        }

        return $bytes;
}


?> 