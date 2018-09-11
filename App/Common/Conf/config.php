<?php

return array(
	//'配置项'=>'配置值'
    'SHOW_PAGE_TRACE' => env('SHOW_PAGE_TRACE',false),
    'SITE_URL'        => env('SITE_URL'),

    /* 应用设定 */
    'APP_USE_NAMESPACE'     =>  env('APP_USE_NAMESPACE',true),    // 应用类库是否使用命名空间
    'APP_SUB_DOMAIN_DEPLOY' =>  env('APP_SUB_DOMAIN_DEPLOY',false),   // 是否开启子域名部署
    'APP_SUB_DOMAIN_RULES'  =>  str2array( env('APP_SUB_DOMAIN_RULES','') ) , // 子域名部署规则
    'APP_DOMAIN_SUFFIX'     =>  env('APP_DOMAIN_SUFFIX',''), // 域名后缀 如果是com.cn net.cn 之类的后缀必须设置
    'ACTION_SUFFIX'         =>  env('ACTION_SUFFIX',''), // 操作方法后缀
    'MULTI_MODULE'          =>  env('MULTI_MODULE',false), // 是否允许多模块 如果为false 则必须设置 DEFAULT_MODULE
    'MODULE_DENY_LIST'      =>  ['Common','Runtime'],
    'CONTROLLER_LEVEL'      =>  env('CONTROLLER_LEVEL',1),
    'APP_AUTOLOAD_LAYER'    =>  'Controller,Model', // 自动加载的应用类库层 关闭APP_USE_NAMESPACE后有效
    'APP_AUTOLOAD_PATH'     =>  '', // 自动加载的路径 关闭APP_USE_NAMESPACE后有效

    /* Cookie设置 */
    'COOKIE_EXPIRE'         =>  env('COOKIE_EXPIRE',0),       // Cookie有效期
    'COOKIE_DOMAIN'         =>  env('COOKIE_DOMAIN',''),      // Cookie有效域名
    'COOKIE_PATH'           =>  env('COOKIE_PATH','/'),     // Cookie路径
    'COOKIE_PREFIX'         =>  env('COOKIE_PREFIX',''),      // Cookie前缀 避免冲突
    'COOKIE_SECURE'         =>  env('COOKIE_SECURE',false),   // Cookie安全传输
    'COOKIE_HTTPONLY'       =>  env('COOKIE_HTTPONLY',''),      // Cookie httponly设置

    /* 默认设定 */
    'DEFAULT_M_LAYER'       =>  'Model', // 默认的模型层名称
    'DEFAULT_C_LAYER'       =>  'Controller', // 默认的控制器层名称
    'DEFAULT_V_LAYER'       =>  'View', // 默认的视图层名称
    'DEFAULT_LANG'          =>  'zh-cn', // 默认语言
    'DEFAULT_THEME'         =>  '',	// 默认模板主题名称
    'DEFAULT_MODULE'        =>  env('DEFAULT_MODULE','Home'),  // 默认模块
    'DEFAULT_CONTROLLER'    =>  env('DEFAULT_CONTROLLER','Index'), // 默认控制器名称
    'DEFAULT_ACTION'        =>  env('DEFAULT_ACTION','index'), // 默认操作名称
    'DEFAULT_CHARSET'       =>  'utf-8', // 默认输出编码
    'DEFAULT_TIMEZONE'      =>  'PRC',	// 默认时区
    'DEFAULT_AJAX_RETURN'   =>  'JSON',  // 默认AJAX 数据返回格式,可选JSON XML ...
    'DEFAULT_JSONP_HANDLER' =>  'jsonpReturn', // 默认JSONP格式返回的处理方法
    'DEFAULT_FILTER'        =>  'htmlspecialchars', // 默认参数过滤方法 用于I函数...

    /* 错误设置 */
    'ERROR_MESSAGE'         =>  env('ERROR_MESSAGE','页面错误！请稍后再试～'),//错误显示信息,非调试模式有效
    'ERROR_PAGE'            =>  env('ERROR_PAGE',''),	// 错误定向页面
    'SHOW_ERROR_MSG'        =>  env('SHOW_ERROR_MSG',false),    // 显示错误信息
    'TRACE_MAX_RECORD'      =>  env('TRACE_MAX_RECORD',100),    // 每个级别的错误信息 最大记录数

    /* 日志设置 */
    'LOG_RECORD'            =>  env('LOG_RECORD',false),   // 默认不记录日志
    'LOG_TYPE'              =>  env('LOG_TYPE','File'), // 日志记录类型 默认为文件方式
    'LOG_LEVEL'             =>  env('LOG_LEVEL','EMERG,ALERT,CRIT,ERR'),// 允许记录的日志级别
    'LOG_FILE_SIZE'         =>  env('LOG_FILE_SIZE',2097152),	// 日志文件大小限制
    'LOG_EXCEPTION_RECORD'  =>  env('LOG_EXCEPTION_RECORD',false),    // 是否记录异常信息日志

    /* SESSION设置 */
    'SESSION_AUTO_START'    =>  env('SESSION_AUTO_START',true),    // 是否自动开启Session
    'SESSION_OPTIONS'       =>  array(), // session 配置数组 支持type name id path expire domain 等参数
    'SESSION_TYPE'          =>  env('SESSION_TYPE',''), // session hander类型 默认无需设置 除非扩展了session hander驱动
    'SESSION_PREFIX'        =>  env('SESSION_PREFIX',''), // session 前缀
    //'VAR_SESSION_ID'      =>  'session_id',     //sessionID的提交变量

    /* 模板引擎设置 */
    'TMPL_PATH'             =>  env('TMPL_PATH',null),
    'TMPL_CONTENT_TYPE'     =>  'text/html', // 默认模板输出类型
    'TMPL_ACTION_ERROR'     =>  env('TMPL_ACTION_ERROR',THINK_PATH.'Tpl/dispatch_jump.tpl'), // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  env('TMPL_ACTION_SUCCESS',THINK_PATH.'Tpl/dispatch_jump.tpl'), // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   =>  env('TMPL_EXCEPTION_FILE',THINK_PATH.'Tpl/think_exception.tpl'),// 异常页面的模板文件
    'TMPL_DETECT_THEME'     =>  false,       // 自动侦测模板主题
    'TMPL_TEMPLATE_SUFFIX'  =>  '.html',     // 默认模板文件后缀
    'TMPL_FILE_DEPR'        =>  env('TMPL_FILE_DEPR','/'), //模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符

    // 布局设置
    'TMPL_ENGINE_TYPE'      =>  'Think',     // 默认模板引擎 以下设置仅对使用Think模板引擎有效
    'TMPL_CACHFILE_SUFFIX'  =>  env('TMPL_CACHFILE_SUFFIX','.php'),      // 默认模板缓存后缀
    'TMPL_DENY_FUNC_LIST'   =>  env('TMPL_DENY_FUNC_LIST','echo,exit'),    // 模板引擎禁用函数
    'TMPL_DENY_PHP'         =>  env('TMPL_DENY_PHP',false), // 默认模板引擎是否禁用PHP原生代码
    'TMPL_L_DELIM'          =>  '{',            // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          =>  '}',            // 模板引擎普通标签结束标记
    'TMPL_VAR_IDENTIFY'     =>  'array',     // 模板变量识别。留空自动判断,参数为'obj'则表示对象
    'TMPL_STRIP_SPACE'      =>  env('TMPL_STRIP_SPACE',true),       // 是否去除模板文件里面的html空格与换行
    'TMPL_CACHE_ON'         =>  env('TMPL_CACHE_ON'),        // 是否开启模板编译缓存,设为false则每次都会重新编译
    'TMPL_CACHE_PREFIX'     =>  '',         // 模板缓存前缀标识，可以动态改变
    'TMPL_CACHE_TIME'       =>  env('TMPL_CACHE_TIME',0),         // 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
    'TMPL_LAYOUT_ITEM'      =>  '{__CONTENT__}', // 布局模板的内容替换标识
    'LAYOUT_ON'             =>  false, // 是否启用布局
    'LAYOUT_NAME'           =>  'layout', // 当前布局名称 默认为layout

    // Think模板引擎标签库相关设定
    'TAGLIB_BEGIN'          =>  '<',  // 标签库标签开始标记
    'TAGLIB_END'            =>  '>',  // 标签库标签结束标记
    'TAGLIB_LOAD'           =>  true, // 是否使用内置标签库之外的其它标签库，默认自动检测
    'TAGLIB_BUILD_IN'       =>  'cx', // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔 注意解析顺序
    'TAGLIB_PRE_LOAD'       =>  '',   // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔

    /* URL设置 */
    'URL_ROUTER_ON'         =>  env('URL_ROUTER_ON',false),   // 是否开启URL路由
    'URL_HTML_SUFFIX'       =>  env('URL_HTML_SUFFIX','html'),  // URL伪静态后缀设置
    'URL_CASE_INSENSITIVE'  =>  env('URL_CASE_INSENSITIVE',false),   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             =>  env('URL_MODEL',3),       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    'URL_PATHINFO_DEPR'     =>  '/',	// PATHINFO模式下，各参数之间的分割符号
    'URL_PATHINFO_FETCH'    =>  'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL', // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
    'URL_REQUEST_URI'       =>  'REQUEST_URI', // 获取当前页面地址的系统变量 默认为REQUEST_URI
    'URL_DENY_SUFFIX'       =>  'ico|png|gif|jpg', // URL禁止访问的后缀设置
    'URL_PARAMS_BIND'       =>  env('URL_PARAMS_BIND',false), // URL变量绑定到Action方法参数
    'URL_PARAMS_BIND_TYPE'  =>  env('URL_PARAMS_BIND_TYPE',0), // URL变量绑定的类型 0 按变量名绑定 1 按变量顺序绑定
    'URL_PARAMS_FILTER'     =>  env('URL_PARAMS_FILTER',false), // URL变量绑定过滤
    'URL_PARAMS_FILTER_TYPE'=>  env('URL_PARAMS_FILTER_TYPE',''), // URL变量绑定过滤方法 如果为空 调用DEFAULT_FILTER

    /* token配置 */
    'TOKEN_ON'      =>    env('TOKEN_ON',false),  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    env('TOKEN_NAME','__hash__'),    // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    env('TOKEN_TYPE','md5'),  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    env('TOKEN_RESET',false),  //令牌验证出错后是否重置令牌 默认为true

    /* 系统变量名称设置 */
    'VAR_MODULE'            =>  'm',     // 默认模块获取变量
    'VAR_ADDON'             =>  'addon',     // 默认的插件控制器命名空间变量
    'VAR_CONTROLLER'        =>  'c',    // 默认控制器获取变量
    'VAR_ACTION'            =>  'a',    // 默认操作获取变量
    'VAR_AJAX_SUBMIT'       =>  'ajax',  // 默认的AJAX提交变量
    'VAR_JSONP_HANDLER'     =>  'callback',
    'VAR_PATHINFO'          =>  's',    // 兼容模式PATHINFO获取变量例如 ?s=/module/action/id/1 后面的参数取决于URL_PATHINFO_DEPR
    'VAR_TEMPLATE'          =>  't',    // 默认模板切换变量
    'VAR_AUTO_STRING'		=>	false,	// 输入变量是否自动强制转换为字符串 如果开启则数组变量需要手动传入变量修饰符获取变量

    'HTTP_CACHE_CONTROL'    =>  'private',  // 网页缓存控制
    'CHECK_APP_DIR'         =>  true,       // 是否检查应用目录是否创建
    'FILE_UPLOAD_TYPE'      =>  'Local',    // 文件上传方式
    'DATA_CRYPT_TYPE'       =>  'Think',    // 数据加密方式



    // 扩展配置
    'LOAD_EXT_CONFIG'       =>  env('LOAD_EXT_CONFIG','route,database,mail'),




    // TODO 自定义参数
    'CRM_DOMAIN'            =>  env('CRM_DOMAIN','http://crm.xiaoying.net'),

    // 拓课云
    'TK_ROOM_URL'=>'http://global.talk-cloud.net/WebAPI/',
    'TK_ROOM_KEY'=>'PGxzTqaSNL0WEWTL',
    'TK_ROOM_DOMAIN'=>'yycgfw',


    //支付宝配置项
    'alipay'=> array (
        //应用ID,您的APPID。
        'app_id' => "2016091300504472",
        //商户私钥
        'merchant_private_key' => "MIIEpAIBAAKCAQEAtGKjMdlecVPpwHR+rRsLQw6Cr6AGzWmFDW1sAVDLU/RUKEcGG7J2tpLLK6fRIAcVR+PXQtm0hFJjDoSACv/6kkWUP8ZL+QdUHES72pGpydgjGvJqGGMgfeOhRO8FNTJz0s583Xu4av9NH6fURvMYek1Pc4PF9mFTYThwanAwzNkw/ty81rEC/mBe2LCL5BC2hMGGQxpgVLmPw7tTnuNMrxkvDaW7FT/pgGqMt0AqVlKuNnkv1tTWKHlnqNP5bUVXw3vHTgOxniNSt0QS7zvDjELKAjnzwx6XmOtgV8mJd/oeH+mGvxBozUD7rGoUpkDp0bFezqIey0NIFwsgevbGTwIDAQABAoIBAQCKfaBlyUllINO6WrZXGQhQd3eGdwpB8t2gLX5alDlHP7LNKZpa8W2LRrCN184LFOUVyKugO50Iamh64OedimJKovjq7ceubh35hhvj9ymwOizZp3wlNRXhET7juc75xL5//uCG+1HfdBs2Tpf/0OveGjUBrw3x1084uSg1z+TnoB6vtu+kIKz28eM5ToIV7v/JiXpajlQ+7RSSJZenxJwFdnMOPRKVnxj7YsRh+s2LwZ2I4KO5dVTLq4RiRLP8tjBWykFZVdXp1qdtaMzBzQTGTFnoFfNGkQGlNzyYNzRpeyivZ9o96fzwXYgay/QoLkMtYpIV1ym3dct0yOA/53nhAoGBANgZkCinWI+rLu0Dugk+Q1nriy59WIdAvJYXiZecFg2vVjf0ylsfHhs/Uts7XoOeA8V4CqtnVvmyHXpBqZKFWGz6OzgWQGzPVCOXkhgBjQkRyoup2xNe0qx3vQ1vQi0stovvJiQ2uWFXYEz24zbu54muKYQzpC4UcL3vN6BJ3E81AoGBANWw8ndaD7q9aa9RR9ufJEtG2APIPJKn6mWkAIP/PSjONriinWFfUdzFw7RPF5rsaXtC2NmoCvIhp7H8/Kk0HCW4AFmifVMtde/UqJZRfzxNpnXhw3TPz7u4Tkli++jgugaBg8dIhFEZofUFfLIaPpitPVRychZ+Se0m42kHKRvzAoGAJtA4l0SvYGaTsBhaQqy/NhhbUEdNLkXPWXnyyZtMTrruVnCOI92r76LpA91e9W7dKIuOBwkxkvmN19aRWXK/1hr/pexBOAnpB9eCcvNcXIOoiHtCkPEYD8gV2aLhFPIR+4JBrIFzvGsQLeKuHsC8L3trk4Ye9/PN6B9eM1jwy/0CgYBGWeL5eCUFjaZlmZTWsSX5wnELjbijb7SzkbK5wiYYcQuC7cZycDc7P8CWdBOJZZu0gOVzgudXn8zdxDRKYGWD5Xf4Co7H+4eAVbrLAjmpWMccuB7pLiFt49eiQXw9xv53ddw0l6M4MAAp8oyC9jD1gtNWIfd9gEZGVS85PKpEEQKBgQC2QbrdQjEIB5Jphw3W7Advv5saIhekpNH6Na9aCGmfQYNKMJtvv22B5iGmfW1rVFnqdjiVYZ+62TrDsefcBCR/Yo1Lee26Xe3Gs81h8hPAFFmR74/GNNfuxGLVnsNKu4V53pxgNYXasXjIetcXsTn3O9SxJYeHvNHMoXMQlv7o1Q==",

        //异步通知地址
        'notify_url' => "http://requestbin.leo108.com/1hssdqd1",

        //同步跳转
        'return_url' => "http://www.xiaoying.com/alipay/return",

        //编码格式
        'charset' => "UTF-8",

        //签名方式
        'sign_type'=>"RSA2",

        //支付宝网关
        'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA7QQzWTaE+9a3QmaNZAVAEdbNwiCh2nIBw+DBsukvJcB4EdvOI4LckaExjDX6O8Zt88J4UHjY0kDdqco8kqBA9P9LZeHzzy8PrbaT1fRx0r5684Go2FYSitzfHERdJNZ6LNUGhO/fTSIHk0VNaXMfk9Jj2iIYl47reCYa/PgVsi3H6i0pl2BC8u7ErWZPDhaXk1gyN+iQmqhB+bCXUqdTTgrRoBkRWG0i05P9lyYtAkx6PQU0SfvBOR3xnGyyKspgiW5Oh2G3Uk7Z72M8MVgN2MbV+3k8fterv2Um4OgXM9S9KJrxc2hFlCao14pIhrPKe6HWY+qeiS7tcOoseZacjQIDAQAB",
    ),
    //微信支付配置项
    'WEIXINPAY_CONFIG'       => array(
        'APPID'              => '', // 微信支付APPID
        'MCHID'              => '', // 微信支付MCHID 商户收款账号
        'KEY'                => '', // 微信支付KEY
        'APPSECRET'          => '', // 公众帐号secert (公众号支付专用)
        'NOTIFY_URL'         => 'http://baijunyao.com/Api/Weixinpay/notify', // 接收支付状态的连接
    ),
);