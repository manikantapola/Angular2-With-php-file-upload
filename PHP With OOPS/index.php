<?php 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type ");

/*Including the Rest and Database Files.
 * 
 * REST will help to process the request and returns the reponse.
 * 
 * Database will help to connect the Database and will perform actions like (create, update, delete).
 * 
 */

include('Rest.php');
include('Database.php');

//Creating Rest object here.
$rest = new REST();
$post_data = $rest->getJSONData();

//intializing the class it self.
$ind = new index($post_data);

class index{
    
    /**
     * this varible will be object of Database
     * by using this object will perform Database actions
     */
    private $db;
    
    /**
     * this varible will holds the post data.
     */
    private $post;
    
    public function __construct($post){
        $this->db = new Database();
        $this->post = $post;
        if( isset($this->post['request'])  && $this->post['request'] == 'getList'){
            $this->getList();
        }else if(isset($this->post['request'])  && $this->post['request'] == 'delete'){
            $this->deleteDocument();
        }else if(isset($this->post['request'])  && $this->post['request'] == 'info'){
            $this->getDocumentData();
        }else{
            $response = array("status" => "failure", "message" => $this->post['request']);
            echo json_encode($response);
            exit;
        }
    }
    
    /**
     * This function will returns the list of documents
     */
    public function getList(){
        try {
            $query = "SELECT * FROM documents WHERE Status = 'Active' ";
            $results = $this->db->getRecords($query);
            
            $list = array();
            foreach($results as $result){
                $list[] = array(
                    'DocumentID' => $result->DocumentID,
                    'DocumentName' => $result->DocumentName,
                    'DocumentType' => $result->DocumentType,
                    'DocumentSize' => $this->formatSizeUnits($result->DocumentSize),
                    'CreatedOn' => $result->CreatedOn
                );
            }
            $response = array("status" => "success", "data"=> $list);
            echo json_encode($response);
            exit;
        } catch (Exception $e) {
            echo json_encode(array("status" => "failure", "message"=> "DB Error"));
            exit;
        }
            
    }
    
    
    /**
     * This function will update the status of document to 'Deleted'
     */
    public function deleteDocument(){
        try{
            $document_id = $this->post['DocumentID'];
            
            $update_params = array('Status' => 'Deleted');
            $where_params = array('DocumentID' => $document_id);
            $this->db->updateRecord("documents", $update_params, $where_params);
            
            $response = array("status" => "success", "message"=> 'Deleted successfully');
            echo json_encode($response);
            exit;
        }catch (Exception $e) {
            echo json_encode(array("status" => "failure", "message"=> "DB Error"));
            exit;
        }
    }
    
    /**
     * This function will returns the document data.
     */
    public function getDocumentData(){
        try{
            $document_id = $this->post['DocumentID'];
            $doc_path = $this->db->getOne("SELECT DocumentPath FROM documents WHERE DocumentID = ? ", array($document_id));
            
            $root = dirname(__FILE__);
            $config = parse_ini_file($root . '/config.ini', true);
            $uploads_dir_path = $config['database settings']['upload_folder_dir_path'];
            
            $full_path = $uploads_dir_path.''.$doc_path;
            $file_info = file_get_contents($full_path);
            
            $response = array("status" => "success", "info"=> $file_info);
            echo json_encode($response);
            exit;
        }catch (Exception $e) {
            echo json_encode(array("status" => "failure", "message"=> "DB Error"));
            exit;
        }
        
    }
    
    /**
     * This function will convert the size and returns in respective size formats.
     */
    public function formatSizeUnits($bytes){
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



}



?>