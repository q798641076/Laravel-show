<?php

return [
    //节流处理
    'rate_limits'=>[
        //访问相关一分钟60次
        'access'=>env('RATE_LIMITS_ACCESS','60,1'),
        //登录相关一分钟10次
        'sign'=>env('RATE_LIMITS_SIGH','10,1'),
    ]
];
