# phpkit-support
  轻量级PHP 基础功能函数库与老版本PHP兼容性支持库
 
# 调用说明
## 功能函数
1. pk_request_uri
   获取请求完整URL
2. pk_raw
   基于CURL实现网络请求访问;
##老版本PHP函数支持
1. pk_strpos
   strpos在7.0后强制提示第二个参数不能为空
2. pk_each
   在7.0以上提示过时