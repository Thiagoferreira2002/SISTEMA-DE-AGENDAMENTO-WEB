<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Dados;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Http\Request;

class DadosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dados = Dados::first();

        return view('admin.dados.index', compact('dados'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // dd($request->all());

        $request->validate([
            // 'logo' => ['required', 'max:3000'],
            // 'icone' => ['required', 'max:3000'],
            'nome' => ['required', 'max:255'],
            'cnpj' => ['required', 'max:255'],
            'whatsapp' => ['required', 'max:255'],
            'email' => ['required', 'max:255'],
            'descricao' => ['required'],
        ]);

        $dados = Dados::first();

        if (! $dados) {
            $dados = new Dados;
        }
        $dados->nome = $request->nome;
        $dados->cnpj = $request->cnpj;
        $dados->whatsapp = $request->whatsapp;
        $dados->email = $request->email;
        $dados->descricao = $request->descricao;
        $dados->fone = $request->fone;
        $dados->endereco = $request->endereco;
        $dados->numero = $request->numero;
        $dados->cep = $request->cep;
        $dados->cidade = $request->cidade;
        $dados->estado = $request->estado;
        $dados->save();

        Flasher::addSuccess('Atualizado com sucesso!');

        return redirect()->back();

    }
}
