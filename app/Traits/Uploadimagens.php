<?php

namespace App\Traits;

use Illuminate\Http\Request;
use File;

trait UploadImagensTrait
{
   //enviar uma imagm única
   public function enviaImagemUnica(Request $request, $nomeDoCampo, $pasta)
   {

     if($request->hasFile($nomeDoCampo)){

        $imagem = $request->{$nomeDoCampo};
        $ext = $imagem->getClientOriginalExtension();
        $dia = date('d');
        $mes = date('m');
        $ano = date('Y');
        $urlDaImagem = 'media_' . uniqid() . '-msflix-' . $dia . '-' . $mes . '-' . $ano . '-.'.$ext;
        $imagem->move(public_path($pasta), $urlDaImagem);

        //caminho da pasta de imagens
        return $pasta . '/' . $urlDaImagem;

     }

   }
}
