<?php 
/**
 * PHPKit Support
 *
 * An open source application library for PHP 5.5 or newer
 *
 * @package		PHPKit.Support
 * @author		Eric-XI 
 * @license		http://phpkit-support.eric-xi.com/license.html
 * @link		http://phpkit-support.eric-xi.com
 * @since		Version 1.0
 * @filesource
 */

 /**#基础安全设置 */
 /**
  *  #获取请求URL;
  *  不建议使用;
  */
function pk_detect_uri()
{
    if ( ! isset($_SERVER['REQUEST_URI']) OR ! isset($_SERVER['SCRIPT_NAME']))
    {
        return '';
    } 
    $uri = $_SERVER['REQUEST_URI']; 
    if (pk_strpos($uri , $_SERVER['SCRIPT_NAME']==""?"index":$_SERVER['SCRIPT_NAME']) === 0)
    {
        $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
    }
    elseif (pk_strpos($uri, dirname($_SERVER['SCRIPT_NAME']==""?"index":$_SERVER['SCRIPT_NAME'])) === 0)
    {
        $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
    } 
    if (strncmp($uri, '?/', 2) === 0)
    {
        $uri = substr($uri, 2);
    }
    $parts = preg_split('#\?#i', $uri, 2);
    $uri = $parts[0];
    if (isset($parts[1]))
    {
        $_SERVER['QUERY_STRING'] = $parts[1];
        parse_str($_SERVER['QUERY_STRING'], $_GET);
    }
    else
    {
        $_SERVER['QUERY_STRING'] = '';
        $_GET = array();
    }

    if ($uri == '/' || empty($uri))
    {
        return '/';
    }
    $uri = parse_url($uri, PHP_URL_PATH);  
     return str_replace(array('//', '../'), '/', trim($uri, '/'));
 } 
 function pk_env($name){
     return  getenv($name);
 }
 /**
  *  #获取请求URL;
  *  推荐;
  */
 function pk_request_uri(){
    $uri=pk_detect_uri();
    if(!$uri ){ 
           $uri = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
           if (trim($uri, '/') == '' || $uri == "/". SELF)
           {
               exit("并接请求路径失败!");
           } 
    }  
    return $_SERVER['QUERY_STRING']?$uri."?".$_SERVER['QUERY_STRING']:$uri; 
 }
 /**#异常信息拦截器 */
 function pk_error_render($rCode=false){
         error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
        error_reporting(-1);
        if ( ! @ini_get('display_errors'))
        {
            @ini_set('display_errors', 1);
        }  
 }
  /**
  *  #数据请求;
  *  基于CURL实现网络请求访问;
  */
function pk_raw($url, $data_string,$contentType="json" ) { 
            $data_string=json_encode($data_string); 
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch , CURLOPT_TIMEOUT_MS , 5000); 
            curl_setopt($ch , CURLOPT_FOLLOWLOCATION , true); 
            curl_setopt($ch , CURLOPT_AUTOREFERER , true);
            curl_setopt($ch , CURLOPT_CONNECTTIMEOUT ,120);
            curl_setopt($ch , CURLOPT_TIMEOUT , 120);
            curl_setopt($ch , CURLOPT_MAXREDIRS , 10); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否

            $headers=array('Content-Length: ' . strlen($data_string));
            if($contentType=="json"){
                $headers[]='Content-Type: application/json; charset=utf-8';
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER,   $headers );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);  
            curl_close($ch);
            if($httpCode==200){
                if($contentType=="json"){ 
                    return json_decode($data,true);
                }else{
                    return $data;
                }
            }else{
                return false;
            } 
}
 
/**老版本PHP函数支持
 * ******************************/
/**
 * #strpos函数支持;
* 在7.0后强制提示第二个参数不能为空
*/
function pk_strpos($arg0,$agr1=""){

    if($agr1==""||$agr1==null)$agr1="pk_strpost_default";
    return strpos($arg0,$agr1);
}
/**
* #each函数支持
* 在7.0以上提示过时
*/
function pk_each(&$array){
    $res = array();
    $key = key($array);
    if($key !== null){
        next($array);
        $res[1] = $res['value'] = $array[$key];
        $res[0] = $res['key'] = $key;
    }else{
         $res = false;
    }
    return $res;
}
 

function pk_cookie($name,$value=false,$arg0=false,$arg1=false,$arg2=false){ 
       if($arg0==false||$arg0<1){
           $arg0=time()+3600*12;
       }  
       //pk_env("XSERVER_ROOT")
       setcookie($name,$value,$arg0,"/",pk_env("XSERVER_ROOT"),defined("PK_DATA_HTTPS")?1:0,1);     
}
/**#mysql函数支持 */
if(!function_exists('mysql_pconnect')){  
    function mysql_free_result($cont=false){
       
    }
    
    define("MYSQL_NUM","MYSQL_NUM");
    define("MYSQL_ASSOC","MYSQL_ASSOC");
    function mysql_num_fields($rs){  
       global $mysqli;  
       return mysqli_num_fields($rs);  
       }  
    function mysql_error($con=false){
            global $mysqli;  
       return mysqli_error($con);  
       }
       function mysql_errno(){
           
       }
   function mysql_pconnect($dbhost, $dbuser, $dbpass){   
       global $mysqli;   
       $mysqli = mysqli_connect($dbhost, $dbuser, $dbpass);  
       return $mysqli;  
       }  
    function mysql_connect($dbhost, $dbuser, $dbpass){   
       global $mysqli;   
       $mysqli = mysqli_connect($dbhost, $dbuser, $dbpass);   
       return $mysqli;  
       }  
   function mysql_get_server_info(){
       return "5.7";
   }
   function mysql_select_db($dbname){  
       global $mysqli;  
       return mysqli_select_db($mysqli,$dbname);  
       }  
   function mysql_fetch_array($result){  
       return mysqli_fetch_array($result);  
       }  
   function mysql_fetch_assoc($result){  
       return mysqli_fetch_assoc($result);  
       }  
   function mysql_fetch_row($result){  
       return mysqli_fetch_row($result);  
       }  
   function mysql_query($cxn){  
       global $mysqli;   
       return mysqli_query($mysqli,$cxn);  
       }  
    function mysql_num_rows($result){  
         $row_cnt = mysqli_num_rows($result);  return $row_cnt ;} 
    function mysql_escape_string($data){  
         global $mysqli;    
         return mysqli_real_escape_string($mysqli, $data);    }  
    function mysql_real_escape_string($data){    return mysql_real_escape_string($data);    }  
   function mysql_close(){     global $mysqli;   return mysqli_close($mysqli);    }  
}  

function pk_error($code,$msg=""){
    header('HTTP/1.1 404 Not Found'); 
    header("status: 404 Not Found");
    exit(); 
}
/***#安全校验 */ 
function pk_check_sec($type="http"){
    
    @ini_set("session.cookie_httponly", true);  
    if($type=="https"){
       @ini_set("session.cookie_secure", true);  
       define("PK_DATA_HTTPS","https");
    }
    include_once 'lib_sec.php'; 
    $requestRoot=pk_request_uri();
    /**#URL地址禁止存在空格 */
    if (pk_has_space($requestRoot)) {
       
        //pk_error(404); 
    }else{
       $fullRequest=pk_request_body(); 
  
         
       if(pk_has_risk($fullRequest)|| $fullRequest!=tp_remove_xss($fullRequest)){ 
            pk_error(404);
       } 
    }  
}

 
?>