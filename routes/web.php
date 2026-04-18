<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? (auth()->user()->nivel === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('cliente.dashboard'))
        : view('auth.login');
});

//rotas organizada da pasta web
foreach(File::allFiles(__DIR__ . '/web') as $rotaArquivo){
  require $rotaArquivo->getPathname();
}

//rota do painel de admin
Route::get('admin/login', [AdminController::class, 'login'])->name('admin.login');

require __DIR__.'/auth.php';
