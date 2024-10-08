<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\MembresiaController;
use App\Http\Controllers\MovimientosController;

use App\Http\Controllers\MiembrosController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashBoardController;
use App\Models\Movimientos;
use App\Models\Producto;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



*/


Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::get('/dashboard', [DashBoardController::class, 'index'])->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::get('/about', fn () => Inertia::render('About'))->name('about');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    
    Route::resource('/productos', ProductosController::class);
    Route::post('/updateprod', [ProductosController::class, 'updateprod'])->name('updateprod');

    Route::resource('/categorias', CategoriasController::class);
    Route::resource('/ventas', VentasController::class);
    Route::get('/ventas/imprimir/{id}', [VentasController::class, 'imprimir'])->name('ventas.imprimir');

    Route::resource('/membresias', MembresiaController::class);

    Route::resource('/miembros', MiembrosController::class);

    
    Route::resource('/movimientos', MovimientosController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/storage-link',function () {
    $targetFolder = storage_path('app/public');
    $linkFolder = $_SERVER['DOCUMENT_ROOT'].'/storage';
    symlink( $targetFolder,$linkFolder);
});


require __DIR__.'/auth.php';


