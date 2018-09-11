<?php

return [
    /* 邮箱设置 */
    "MAIL_HOST"       => env('MAIL_HOST',''),     //设置126邮箱服务
    "MAIL_SMTPAUTH"   => env('MAIL_SMTPAUTH',true),               //设置需要验证
    "MAIL_USERNAME"   => env('MAIL_USERNAME',''), //发件人使用邮箱
    "MAIL_PASSWORD"   => env('MAIL_PASSWORD',''),          //设置发件人密码
    "MAIL_FROM"       => env('MAIL_FROM',''), // 发件人邮箱
    "MAIL_FROM_NAME"  => env('MAIL_FROM_NAME',''),            //发送者名称
];