<?php

Route::get('/flag', 'UserController@flag');
/*  About Users Routes   */
Route::get('/login','UserController@login');//login form

Route::post('/login','UserController@login');

Route::get('/register','UserController@register');//register form

Route::post('/register','UserController@register');

Route::get('/user', 'UserController@User_info')->middleware('user');
Route::get('/logout', 'UserController@User_logout')->middleware('user');

Route::get('/user/change','UserController@change_password')->middleware('user');//User Password change form

Route::post('/user/change','UserController@change_password')->middleware('user');//User Password change

Route::get('/pass/reset','UserController@reset_password');//User Password reset

Route::post('/pass/reset','UserController@reset_password');//User Password reset form


/* About commodity Routes*/
Route::get('/', 'ShopController@shop_index')->middleware('user');

Route::get('/shop','ShopController@shop_list');//List commodity

Route::get('/info/{id}','ShopController@shop_detail')->middleware('user');//Get commodity info

Route::post('/pay','ShopController@shop_pay')->middleware('user');


/*About second kill Routes*/
Route::get('/seckill','ShopController@seckill_get')->middleware('user');//second kill page or form? 没搞懂

Route::post('/seckill','ShopController@seckill_post')->middleware('user');

Route::get('/shopcar','ShopController@shopcar_get')->middleware('user');//List shopcar page

Route::post('/shopcar','ShopController@shopcar_post')->middleware('user');//pay shopcar //postdata={ "csrfname" : csrftoken , "price" : float_number}

Route::post('/shopcar/add','ShopController@shopcar_add')->middleware('user');//postdata={ "csrfname" : csrftoken , "commodity_id": id}

/*验证码*/
Route::get('/captcha','CaptchaController@captcha');
