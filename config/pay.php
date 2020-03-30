<?php

return [
    'alipay' => [
        'app_id'         => '2016102300743607',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA8105GEko4Fe7S/gIaiC3mnt9tX4qmZwouRk1N6oBL+vad4Bl1RzDJHUq/Bqlg7P4JpMx3VQY/YP1eL7khB8p70O1OHno2Cs6NQmxzXYU0BX6gf3mjYpU6LEVHntH8NEjAh4i4eBtFN3fi+8M7R9UJlffGQ6dvZWPdT+PpgJLSVvdBqiA5bSrl2gFVicdN0V5QU1DeYyipeHU50jCkpE3xvW25UvJSvu20oV/4EdMXeyHOYeM74IH+eZVEduFUhymGQ+b9QPvFPJPpYLmkOkmYt2oEcY3lqP/kf37z/XtaxaaWzrVh97ugjAmu+BFH6MZbAtpHZZ3n/auzOAWil44FwIDAQAB',
        'private_key'    => 'MIIEowIBAAKCAQEAmIAbaexC+vKnfeHuPwfQD0+AJxk+yuyXSSbKLynd52NVQmIRjbdC0XgX801PSXkc2Bn2v53rbxOZfwNCKV+lAtRWh7kz9DXXNcyNiOXvjhn/dlhEDy4TTHBXAcAXjAsPZzJEM6vPqdrRo6ky+hu7cq0bqP1Mevr2DcUgPjRxjYpXZOygtV30f0htM73hkuszFhEGcZpHmfPQH2YemtjxKW6wYiwDaBGcVHhA20zAxpLtbTGUwdJtlKm9TZgMubfc3j/s7DXxL3BDfi/aoSjQWRkJbHICMFnRwgBj+2KLJMbKp6dLpexeawBXrHurstmUrodjhU5UuQg4KuC1ScRRdQIDAQABAoIBAF3k6nwSMr9N7iBMniMCzXDP0yi/m3DXsOIiVvQpA+62s0T3GrW1sxdDqQNtgzbKsh7ABbO/KLkwSR7xw6ezsuaGFGzmc/2VtNb0BeTXJeLJUQftmqH6DXnP5VI3kofwO2Cfi2yBCZAcIV1yDOf/cS9PNCudIZzKrfqWd561LYEmSYd9tWNOraZMriE6WAiJILDom+1U+X/q6qU1eW8s2Ykoenxr28Z981MPNhrObY4ZDXYZiDy2teW2nxy/9Ac6e0sA6OOeHgwled3L8muP2iEr3XGv1VDzR65/wIvNDzxidMALkRMad3kyU74+7Im74Q+vGK9JkS8QAuvFEOdAmFUCgYEA5DeoyR7JOHUH1Z7dFQRKIkuzaq0Xen5cLRljSzYdAV4vpmije4AQkB7zi6eMERditSush07ofifXouxJWt6tbnQW4r6KI0NuUnjQSCDXvFw3nS14m0NLGGdp7UzL7CwR3QwAM5PhO3voeM1XX87s4RHudmrN1wa1eleFc/WhOjsCgYEAqxC+6QJ3Q/EqLMTetcnB3USZlRtAUE8maY/l3byqKwZHtsNennbnzgWaRWo8AUnAWl5REkGeY7OExExDSf9w9wymZEVXfYmfbTqAfdqDJkQDYU+rtZp4OElHFUSFISKhzJUl09u9LHccTj/BXUalZIUNdNtmThJx5GtnnBNvOA8CgYEA229lPJT8d+uXb+DDxRyNx7IwPqdWRvLO4JJjtgK/GvycJo7AetRlmJ4aITMl650nzPnEd4n45Kycm8xEsDoiWm0HQFhjbIq+vk/qPSBPL48f7mK1b9zhjQKKiKBqB5lMR9XtL6rGS7LVkaonlDjb7YGXX4dMiq+puYwEBr6smtcCgYBYBC22RgaHdrZ0gnb0ofKVno5HIdZde9wPxHJJPKFxsbGEX9F3R0bNwiQ2QwrMvt2xoYWlFw4fzmYpefPFRyEge+nA/cyeUwksckVae+uu7J+wmgWHUws4KrvgPXkiK2eEk9j6wLz8++wcdsFO9OJ0beEWlyx0Txk6peiRbY50EQKBgBLOCIukgTc0VTR0OjTrnRYQRwEU6iwD8oERNcWOLpvD0KJAesN7UB0eYJ2vZwRS7Z9S3ZSbk3h0K5fxFIDw94LYV2+SQIJqLOw9cvZHkga9iKRSmEfZtksfpCZK52lV3uGBs1da5n6jFviR2au9r8CQ1e7JYVO0wlE0feNBj1Js',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],



    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
