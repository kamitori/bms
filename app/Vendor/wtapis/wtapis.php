<?php
class wtapis{
    // jobtraq api key
    private $apiKey = '546c57c300806a5c7ee67751';
    // host url
//    private $host = 'http://parkland.local/api';
    private $host = 'http://parkland.anvyonline.com/index.php?a=APIS';
    const WT_APIS_VERSION = '2.1';


    const API_CONNECT_TIMEOUT = 1200;
    const API_PROCESS_TIMEOUT = 2400;



    public function __construct($p_api_key ='', $p_use_https = ''){
        if($p_api_key) $this->apiKey = $p_api_key;
        if($p_use_https) $this->host = $p_use_https;
    }


    protected function get_curl_handler($p_uri, array $arr_post_data = NULL, $p_auto_refresh_token = TRUE){
        $c_url = curl_init($p_uri);

        curl_setopt($c_url, CURLOPT_RETURNTRANSFER, true);

        if ($arr_post_data !== NULL) {
            curl_setopt($c_url, CURLOPT_POST, TRUE);
            curl_setopt($c_url, CURLOPT_POSTFIELDS, http_build_query($arr_post_data));
        }
        return $c_url;
    }


    private function is_post_method($p_method){
        switch ($p_method) {
            case 'rfq':
                $p_is_post_method = TRUE;
                break;

            default:
                $p_is_post_method = FALSE;
                break;
        }

        return $p_is_post_method;
    }

    protected function api($p_method, array $arr_args = array(), $p_key_index = NULL, $p_auto_refresh_token = TRUE){
        static $cnt = 1;
        if (!$this->is_post_method($p_method)) {
            $arr_query = $arr_args;
            $arr_post_data = NULL;
        } else {
            $arr_query = NULL;
            $arr_post_data = $arr_args;
        }

        $v_uri = $this->host;
        $c_url = $this->get_curl_handler($v_uri, $arr_post_data, $p_auto_refresh_token);
        $v_time_start = microtime(TRUE);
        $v_response = curl_exec($c_url);
        $v_http_code = curl_getinfo($c_url, CURLINFO_HTTP_CODE);

        if ($v_response === FALSE) {
            throw new WT_Api_Exception('Failed to reach URL "' . $v_uri . '": ' . curl_error($c_url));
        }

        curl_close($c_url);
        $arr_res = json_decode($v_response, true);

        if (isset($arr_res['error']) || $v_http_code != 200) {
            $v_error_code = 0;

            if (isset($arr_res['error'])) {
                $v_error_msg = $arr_res['error'];

                if (!empty($arr_res['code'])) {
                    $v_error_code = (int) $arr_res['code'];
                }
            } else {
                $v_error_msg = 'Invalid response HTTP code: ' . $v_http_code;
            }

            throw new WT_Api_Exception($v_error_msg, $v_error_code);
        }

        $v_time_end = microtime(TRUE);

        if (!headers_sent()) {
            header('X-ImageStylor-API-Call-Method-' . $cnt . ': ' . $p_method);
            header('X-ImageStylor-API-Call-Time-' . $cnt . ': ' . ($v_time_end - $v_time_start));
        }
        $cnt++;
        if($p_key_index){
            if(isset($arr_res[$p_key_index])){
                return $arr_res[$p_key_index];
            }
            else{
                return $arr_res;
            }
        }else{
            return $arr_res;
        }
    }
    /*
     * @param :
        * id:primary key
        * type: method action
            * data: list data
            * update: update data
        * module: module name
     * @return: json result
     */
    public function functionality_method($id = '',$type = 'data',$module='rfq',$arr_data_update = array()){
//        if(is_string($id) || is_numeric($id)) $id = new MongoId($id);
        return $this->api($module,
            array(
                'key' => $this->apiKey,
                '_id'=>$id
                ,'module'=>$module
                ,'moduleAction'=>$type
                ,'qt_update'=>json_encode($arr_data_update)
            ),$type
        );
    }
    public function functionalities_method(array $arr_where = array(),$type = 'data',$module='rfq',$arr_data_update = array()){
        return $this->api($module,
            array(
                'key' => $this->apiKey,
                'where'=>json_encode($arr_where)
                ,'module'=>$module
                ,'moduleAction'=>$type
                ,'qt_update'=>json_encode($arr_data_update)
            ),$type
        );
    }
}

/**
 * API Exception
 */
class WT_Api_Exception extends Exception {
    public function __construct($p_message, $p_code = 0, Exception $p_previous = null){
        parent::__construct($p_message, $p_code, $p_previous);
    }
    public function to_string(){
        return __CLASS__.": [{$this->code}]: {$this->message}";
    }
}

?>