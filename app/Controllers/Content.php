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
        }else{
            $message = 'There is no data available';
        }
        $response = [
            'status'   => 200,
            'message' => $message,
            'data' => $getData,
           
        ];
        return $this->respond($response);
    }
    public function insertDictionary(){
        $word = $this->request->getVar('word');
        if(!empty($word)){
            $wordData= $this->dictionaryModel->where(['word'=>$word])->first();
            if(!empty($wordData)){
                $response = [
                    'status'   => 200,
                    'message' => 'Word is already exist in Database',
                    'data'=>[],
                ];
            }else{
                $meaning = $this->request->getVar('meaning');
                $example = $this->request->getVar('example');
                $createdDate = $this->request->getVar('createdDate');
                $userID = $this->request->getVar('userID');
                $newsletterDate = $this->request->getVar('newsletterDate');
                $data = array();
                if (!empty($meaning) && !empty($example) && !empty($createdDate) && !empty($userID) && !empty($newsletterDate)) {
                   $data = [
                        'word' => $word,
                        'meaning' => $meaning,
                        'example' => $example,
                        'userID' => $userID,
                        'newsletterDate' => $newsletterDate,
                        'createdDate' => $createdDate,
                   ];
                   $insetData = $this->dictionaryModel->insert($data);
                   if ($insetData) {
                        $response = [
                            'status'   => 200,
                            'message' => 'Dictionary Data is inserted sucessfully.',
                            'data'=> $data,
                        ];
                   }else{
                    $response = [
                        'status'   => 500,
                        'message' => 'Error Found',
                        'data'=>[],
                    ];
                   }
                }else{
                    $response = [
                        'status'   => 200,
                        'message' => 'Please fill the required field',
                        'data'=>[],
                    ];
                }
            }
        }else{
            $response = [
                'status'   => 200,
                'message' => 'Please fill the required field',
                'data'=>[],
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
                    'message' => 'Word is deleted successfully.',
                    'data' => [
                        'wordID'=> intval($wordId),
                    ],
                ];
            }else{
                $response = [
                    'status'   => 200,
                    'message' => 'Word id not found',
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
    public function updateDictionary(){
        $wordId = $this->request->getVar('wordID');
        $updateData = array();
        if(!empty($wordId)){
            $wordData= $this->dictionaryModel->where(['wordID'=>$wordId])->first();
            if(!empty($wordData)){
                $word = !empty($this->request->getVar('word')) ? $this->request->getVar('word') : $wordData['word'];
                $meaning = !empty($this->request->getVar('meaning')) ? $this->request->getVar('meaning') : $wordData['meaning'];
                $example = !empty($this->request->getVar('example')) ? $this->request->getVar('example') : $wordData['example'];
                $newsletterDate = !empty($this->request->getVar('newsletterDate')) ? $this->request->getVar('newsletterDate') : $wordData['newsletterDate'];
                $updateData = [
                    'word' => $word,
                    'meaning' => $meaning,
                    'example' => $example,
                    'newsletterDate' => $newsletterDate,
                ];
                $updata = $this->dictionaryModel->where('wordID', $wordId)->set($updateData)->update();
                if($updata){
                    $response = [
                        'status'  => 200,
                        'message' => 'Dictionary Data is updated sucessfully.',
                        'updated data'=> $updateData,
                    ];
                }else{
                    $response = [
                        'status'  => 500,
                        'message' => 'Error found.',
                    ];
                }
            }else{
                $response = [
                    'status'   => 200,
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
