<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DictionaryModel;
use CodeIgniter\API\ResponseTrait;

class Content extends BaseController
{
    use ResponseTrait;
    protected $dictionaryModel;

    public function __construct(){
        //helper(['form', 'file']);

        $this->db = \Config\Database::connect('default');

        $this->dictionaryModel = model('DictionaryModel', true, $this->db);
        
	}
    public function index()
    {
        $getData = array(); 
        $dictionaryData = $this->dictionaryModel->orderBy('createdDate', 'desc')->findAll();
        
        if(!empty($dictionaryData)){
            foreach ($dictionaryData as $key => $value) {
                $getData[] = $value;
               
            }
            $message = 'All Dictionary Data list';
            $msg = 'true';
            
        }else{
            $message = 'There is no data available';
            $msg = 'false';
        }
        $response = [
            'status'   => 200,
            'success' => $msg,
            'message' => $message,
            'data' => $getData,
           
        ];
        return $this->respond($response);
    }
    public function wordById(){

        $wordId = $this->request->getVar('wordID');
        $wordDataArr = array();
        if(!empty($wordId)){
            $wordData= $this->dictionaryModel->where(['wordID'=>$wordId])->first();
            if($wordData){
                foreach ($wordData as $key => $value) {
                    $wordDataArr[] = $value;
                    
                }
                $response = [
                    'status'   => 200,
                    'success' => 'true',
                    'message' => 'Dictionary Data list',
                    'data' => $wordDataArr,
                   
                ];
            }else{
                $response = [
                    'status'   => 200,
                    'success' => 'false',
                    'message' => "Word Id doesn't exist in database.",
                    'data' => [],
                   
                ];
            }
                
        }else{
            $response = [
                'status'   => 200,
                'success' => 'false',
                'message' => 'word Id is required',
                'data'=>[],
            ];
        }

        return $this->respond($response);
    }
    public function insertDictionary(){
        $word = $this->request->getVar('word');
        if(empty($word)){
            $response = [
                'status'   => 401,
                'message' => 'Please send word'
            ];
            return $this->respond($response);
        }
        $meaning = $this->request->getVar('meaning');
        if(empty($meaning)){
            $response = [
                'status'   => 401,
                'message' => 'Please send meaning'
            ];
            return $this->respond($response);
        }  
        $example = $this->request->getVar('example');
        if(empty($example)){
            $response = [
                'status'   => 401,
                'message' => 'Please send example'
            ];
            return $this->respond($response);
        }  
        $userID = $this->request->getVar('userID');
        if(empty($userID)){
            $response = [
                'status'   => 401,
                'message' => 'Please send userID'
            ];
            return $this->respond($response);
        } 
        $status = $this->request->getVar('status');
        if(empty($userID)){
            $response = [
                'status'   => 401,
                'message' => 'Please send status'
            ];
            return $this->respond($response);
        }  
        $newsletterDate = $this->request->getVar('newsletterDate');
        if(empty($userID)){
            $response = [
                'status'   => 401,
                'message' => 'Please send newsletterDate'
            ];
            return $this->respond($response);
        } 

        $createdDate = date('Y-m-d h:i:s');
        $data = array();
        $data = [
                'word' => $word,
                'meaning' => $meaning,
                'example' => $example,
                'userID' => $userID,
                'status' => $status,
                'newsletterDate' => $newsletterDate,
                'createdDate' => $createdDate,
            ];
        try {
            $insetData = $this->whatsNewModel->insert($data);
            if ($insetData) {
                    $response = [
                        'status'   => 200,
                        'success' => 'true',
                        'message' => 'Dictionary Data is inserted sucessfully.',
                        'data'=> $data,
                    ];
            }else{
                $response = [
                    'status'   => 500,
                    'success' => 'false',
                    'message' => 'Error Found',
                    'data'=>[],
                ];
            }
        } catch (\Throwable $th) {
            $response = [
                'status'   => 500,
                'success' => 'false',
                'message' => 'Some Error Occurred!'. $th->message,
            ];
        }      
        return $this->respond($response);
    }

    public function deleteDictionary(){
        $wordId = $this->request->getVar('wordID');
        if(!empty($wordId)){
            $wordData= $this->dictionaryModel->where(['wordID'=>$wordId])->first();
            if(!empty($wordData)){
                $del_wordData = $this->dictionaryModel->where('wordID', $wordId)->delete();
                $response = [
                    'status'   => 200,
                    'success' => 'true',
                    'message' => 'Word is deleted successfully.',
                    'data' => [
                        'wordID'=> intval($wordId),
                    ],
                ];
            }else{
                $response = [
                    'status'   => 200,
                    'success' => 'false',
                    'message' => 'Word id not found',
                    'data'=>[],
                ];
            }
        }else{
            $response = [
                'status'   => 200,
                'success' => 'false',
                'message' => 'Word id is required',
                'data'=>[],
            ];
        }
        return $this->respond($response);
    }
   
    public function updateDictionary(){
        $wordId = $this->request->getVar('wordID');
        $updateData = array();
        if(!empty($wordId)){
            $wordData= $this->dictionaryModel->where(['wordID'=>$wordId])->first();
            if(!empty($wordData)){
                $word = !empty($this->request->getVar('word')) ? $this->request->getVar('word') : $wordData['word'];
                $meaning = !empty($this->request->getVar('meaning')) ? $this->request->getVar('meaning') : $wordData['meaning'];
                $example = !empty($this->request->getVar('example')) ? $this->request->getVar('example') : $wordData['example'];
                $status = !empty($this->request->getVar('status')) ? $this->request->getVar('status') : $wordData['status'];
                $newsletterDate = !empty($this->request->getVar('newsletterDate')) ? $this->request->getVar('newsletterDate') : $wordData['newsletterDate'];
                $updateData = [
                    'word' => $word,
                    'meaning' => $meaning,
                    'status' => $status,
                    'example' => $example,
                    'newsletterDate' => $newsletterDate,
                ];
                $updata = $this->dictionaryModel->where('wordID', $wordId)->set($updateData)->update();
                if($updata){
                    $response = [
                        'status'  => 200,
                        'success' => 'true',
                        'message' => 'Dictionary Data is updated sucessfully.',
                        'updated data'=> $updateData,
                    ];
                }else{
                    $response = [
                        'status'  => 500,
                        'success' => 'false',
                        'message' => 'Error found.',
                    ];
                }
            }else{
                $response = [
                    'status'   => 200,
                    'success' => 'false',
                    'message' => "Word Id doesn't exit in Database.",
                    'data'=>[],
                ];
            }
        }else{
            $response = [
                'status'   => 200,
                'message' => 'Word id is required',
                'data'=>[],
            ];
        }
        return $this->respond($response);
    }
    
}