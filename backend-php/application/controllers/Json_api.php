<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'libraries/JSON_Auth.php';

use Stringy\StaticStringy as S;

class Json_api extends CI_Controller
{
    public $modelPrefix = 'api/';
    public $modelName;
    public $mainModel;

    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, GET, POST, PATCH, DELETE');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');
        $this->load->helper('inflector');
       // $this->load->model($this->getModelName(), 'mainModel');
    }

    public function getModelName()
    {

        return $this->modelPrefix . $this->modelName;
    }

    public function index()
    {

        header('Access-Control-Allow-Origin: *');
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $data = $this->GET_items($_GET);
                break;
            case 'POST':
                $post_data = file_get_contents("php://input");
                $raw_data = json_decode($post_data);
                $data = $this->POST_items($raw_data->data);
                break;
            default:
                return;
                exit();
                break;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));
    }

    public function item($id)
    {
        header('Access-Control-Allow-Origin: *');
        $data = [];

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $data = $this->GET_item($id);
                break;
            case 'PATCH':
                $post_data = file_get_contents("php://input");
                $raw_data = json_decode($post_data);
                $data = $this->PATCH_item($raw_data->data, $id);
                break;
            case 'POST':
                $data = $this->POST_item($id);
                break;
            case 'DELETE':
                $data = $this->DELETE_item($id);
                break;
            default:
                return;
                exit();
                break;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));
    }

    private function loadModel($modelName)
    {
        $model = S::underscored(singular($modelName));
        try {
            $this->load->model("api/{$model}", 'mainModel');
            $this->modelName = $model;
        } catch (\Throwable $th) {
            $this->output->set_status_header(404);
            exit();
        }
    }

    //items

    public function GET_items($params = [])
    {
        //header('Access-Control-Allow-Origin: *');

        //$this->checkAuthorization();
        $this->loadModel($this->modelName);

        $result = $this->mainModel->getMany($_GET);
        $result['data'] = $this->mainModel->getRelationships($result['data']);
        $result['included'] = $this->mainModel->getIncludes($result['data']);

        foreach ($result['data'] as &$value) {
            $value = $this->mainModel->serialize($value);
        }

        return $result;
    }

    public function GET_item($id)
    {
        //$this->checkAuthorization();
        $this->loadModel($this->modelName);

        $result = $this->mainModel->getOne($id);
        if ($result['data']) {
            $result['data'] = $this->mainModel->getRelationships($result['data']);
            $result['data'] = $this->mainModel->serialize($result['data']);
        }
        return $result;
    }

    public function POST_items($data)
    {
        $this->checkAuthorization();
        $data2 = $this->mainModel->deserialize($data);
        $id = $this->mainModel->create($data2);

        if ($id) {
            $this->mainModel->saveRelationShips($data, $id);
            return $this->GET_item($id);
        }

        return null;
    }


    public function POST_item($id)
    {
        $this->checkAuthorization();
        $this->output->set_status_header(405);
        exit();
    }

    public function PATCH_item($data, $id)
    {
        //$this->checkAuthorization();
        $this->loadModel($this->modelName);
        $data2 = $this->mainModel->deserialize($data);
        $this->mainModel->update($data2, $id);

        return $this->GET_item($id);
    }

    public function DELETE_item($id)
    {
        $this->checkAuthorization();
        $this->output->set_status_header(405);
        exit();
    }

    public function checkAuthorization()
    {
        if (!JSON_Auth::create($this)->checkAuthorization()) {
            $this->output->set_status_header(401);
            exit();
        }
    }

}
