<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\WhatsNewModel;
use App\Models\OutoftheboxModel;
use CodeIgniter\API\ResponseTrait;

class WhatsNew extends BaseController
{
    use ResponseTrait;
    protected $whatsNewModel;

    public function __construct(){
        //helper(['form', 'file']);

        $this->db = \Config\Database::connect('default');

        $this->whatsNewModel = model('WhatsNewModel', true, $this->db);
        $this->outoftheboxModel = model('OutoftheboxModel', true, $this->db);
        
	}

    public function index()
    {
        $getData = array(); 
        $whatsnewData = $this->whatsNewModel->orderBy('createdDate', 'desc')->findAll();
        
        if(!empty($whatsnewData)){
            foreach ($whatsnewData as $key => $value) {
                $getData[] = $value;
               
            }
            $message = 'All whatsNew Data list';
            $suc_msg = 'true';
        }else{
            $message = 'There is no data available';
            $suc_msg = 'false';
        }
        $response = [
            'status'   => 200,
            'success'  => $suc_msg,
            'message' => $message,
            'data' => $getData,
           
        ];
        return $this->respond($response);
    }
    public function whatsnewById(){

        $whatsNewID = $this->request->getVar('whatsNewID');
        $whatsnewDataArr = array();
        if(!empty($whatsNewID)){
            $whatsnewData= $this->whatsNewModel->where(['whatsNewID'=>$whatsNewID])->first();
            if($whatsnewData){
                foreach ($whatsnewData as $key => $value) {
                    $whatsnewDataArr[$key] = $value;
                    
                }
                $response = [
                    'status'   => 200,
                    'success' => 'true',
                    'message' => 'Whatsnew Data list',
                    'data' => $whatsnewDataArr,
                   
                ];
            }else{
                $response = [
                    'status'   => 200,
                    'success' => 'false',
                    'message' => "whatsNew Id doesn't exist in database.",
                    'data' => [],
                   
                ];
            }
                
        }else{
            $response = [
                'status'   => 200,
                'success' => 'false',
                'message' => 'whatsNew Id is required',
                'data'=>[],
            ];
        }

        return $this->respond($response);
    }
    public function insertWhatsnew(){
        $title = $this->request->getVar('title');
        if(empty($title)){
            $response = [
                'status'   => 401,
                'message' => 'Please send title'
            ];
            return $this->respond($response);
        }
        $content = $this->request->getVar('content');
        if(empty($content)){
            $response = [
                'status'   => 401,
                'message' => 'Please send Content'
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
                'title' => $title,
                'content' => $content,
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
                        'message' => 'Whatsnew Data is inserted sucessfully.',
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
    public function updateWhatsnew(){
        $whatsNewID = $this->request->getVar('whatsNewID');
        $updateData = array();
        if(!empty($whatsNewID)){
            $whatsnewData= $this->whatsNewModel->where(['whatsNewID'=>$whatsNewID])->first();
            if(!empty($whatsnewData)){
                $title = !empty($this->request->getVar('title')) ? $this->request->getVar('title') : $whatsnewData['title'];
                $content = !empty($this->request->getVar('content')) ? $this->request->getVar('content') : $whatsnewData['content'];
                $status = !empty($this->request->getVar('status')) ? $this->request->getVar('status') : $whatsnewData['status'];
                $newsletterDate = !empty($this->request->getVar('newsletterDate')) ? $this->request->getVar('newsletterDate') : $whatsnewData['newsletterDate'];
                $updateData = [
                    'title' => $title,
                    'content' => $content,
                    'status' => $status,
                    'newsletterDate' => $newsletterDate,
                ];
                $updata = $this->whatsNewModel->where('whatsNewID', $whatsNewID)->set($updateData)->update();
                if($updata){
                    $response = [
                        'status'  => 200,
                        'success' => 'true',
                        'message' => 'Whatsnew Data is updated sucessfully.',
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
                    'message' => "whatsnew Id doesn't exit in Database.",
                    'data'=>[],
                ];
            }
        }else{
            $response = [
                'status'   => 200,
                'success' => 'false',
                'message' => 'Whatsnew id is required',
                'data'=>[],
            ];
        }
        return $this->respond($response);
    }
    public function deleteWhatsnew(){
        $whatsNewID = $this->request->getVar('whatsNewID');
        if(!empty($whatsNewID)){
            $whatsnewData= $this->whatsNewModel->where(['whatsNewID'=>$whatsNewID])->first();
            if(!empty($whatsnewData)){
                $del_whatsnewData = $this->whatsNewModel->where('whatsNewID', $whatsNewID)->delete();
                $response = [
                    'status'   => 200,
                    'success' => 'true',
                    'message' => 'Whatsnew is deleted successfully.',
                    'data' => [
                        'whatsNewID'=> intval($whatsNewID),
                    ],
                ];
            }else{
                $response = [
                    'status'   => 200,
                    'success' => 'false',
                    'message' => 'Whatsnew id not found',
                    'data'=>[],
                ];
            }
        }else{
            $response = [
                'status'   => 200,
                'success' => 'false',
                'message' => 'Whatsnew id is required',
                'data'=>[],
            ];
        }
        return $this->respond($response);
    }
    public function getOutofthebxData()
    {
        $getData = array(); 
        $outoftheboxData = $this->outoftheboxModel->orderBy('createdDate', 'desc')->findAll();
        
        if(!empty($outoftheboxData)){
            foreach ($outoftheboxData as $key => $value) {
                $getData[] = $value;
               
            }
            $message = 'All outofthebox Data list';
            $suc_msg = 'true';
        }else{
            $message = 'There is no data available';
            $suc_msg = 'false';
        }
        $response = [
            'status'   => 200,
            'success'  => $suc_msg,
            'message' => $message,
            'data' => $getData,
           
        ];
        return $this->respond($response);
    }
    public function insertoutofthebox(){
        $title = $this->request->getVar('title');
        if(empty($title)){
            $response = [
                'status'   => 401,
                'message' => 'Please send title'
            ];
            return $this->respond($response);
        }
        $content = $this->request->getVar('content');
        if(empty($content)){
            $response = [
                'status'   => 401,
                'message' => 'Please send Content'
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
                'title' => $title,
                'content' => $content,
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
                        'message' => 'Whatsnew Data is inserted sucessfully.',
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
}