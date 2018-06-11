<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Commoditys;
use App\Services\Application;

class ShopController extends Controller
{
    protected $application;
    protected $user;

    public function __construct(Application $application, User $user)
    {
        $this->application = $application;
        $this->user = $user;
    }

    public function shop_index()
    {
        return redirect('/shop');
    }

    public function shop_list(Request $request)
    {
        $page = $request->input(['page']);
        if ((int)$page) {
            $page = (int)$page;
        } else {
            $page = 1;
        }

        $commoditys = Commoditys::where('amount', '>', 0)
            ->orderBy('price', 'desc')
            ->limit(config('shop.limit'))
            ->offset(($page - 1) * config('shop.limit'))
            ->get();

        return view('index', ['commoditys' => $commoditys,
            'preview' => $page - 1,
            'next' => $page + 1,
            'limit' => config('shop.limit')
        ]);
    }

    public function shop_detail(Request $request, $id)
    {
        if (!$id) {
            $id = 1;
        }
        $commodity = Commoditys::where('id', $id)->firstOrFail();
        if (!$commodity) {
            return redirect('/');
        } else {
            return view('info', ['commodity' => $commodity]);
        }
    }

    public function shop_pay(Request $request)
    {
        $price = $request->input(['price']);
        $integral = $this->user->pay(session('username'), (float)$price);
        if ($integral) {
            User::where('username', session('username'))->update(['integral' => $integral]);
            return view('pay', ['success' => 1]);
        } else {
            return view('pay', ['danger' => 1]);
        }
    }

    public function shopcar_get()
    {
        $id = session('commmodity_id');
        if ($id) {
            $commodity = Commoditys::where('id', $id)->firstOrFail();
            return view('shopcar', ['commodity' => $commodity]);
        } else {
            return view('shopcar');
        }
    }

    public function shopcar_post(Request $request)
    {
        $price = $request->input(['price']);
        $res = $this->user->pay(session('username'), (float)$price);
        if ($res) {
            $integral = $res;
            User::where('username', session('username'))->update(['integral' => $integral]);
            session()->forget('commmodity_id');
            return view('shopcar', ['success' => 1]);
        } else {
            return redirect('/shopcar');
        }
    }

    public function shopcar_add(Request $request)
    {
        $id = $request->input(['id']);
        session(['commmodity_id' => $id]);
        return redirect('/shopcar');
    }

    public function seckill_get()
    {
        return view('seckill');
    }

    public function seckill_post(Request $request)
    {
        $id = $request->input(['id']);
        $commodity = Commoditys::where('id', $id)->firstOrFail();
        if ($commodity['amount'] -= 1 > 0) {
            Commoditys::where('id', $id)->update(['amount' => $commodity['amount'] - 1]);
            return view('seckill', ['success' => 1]);
        } else {
            return view('seckill', ['danger' => 1]);
        }
    }
}
