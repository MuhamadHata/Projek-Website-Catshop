<?php

class Home extends Controller
{   
    //dapat diakses tanpa login
    public function index()
    {   
        $data['produk_populer'] = $this->model('Home_model')->getPopularProduk(3);
        $data['page_title'] = 'home';
        $this->view('templates/header', $data);
        $this->view('home/index', $data);
        $this->view('templates/footer', $data);
    }
}