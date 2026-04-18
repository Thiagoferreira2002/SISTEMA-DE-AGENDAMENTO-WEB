@extends('admin.layouts.master')
@section('content')

<section class="section">
    <div class="section-header">
      <h1>Dados da empresa</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="">Painel</a></div>
        <div class="breadcrumb-item">Criar</div>
      </div>
    </div>

    <div class="section-body">

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4>Configurações</h4>

              <div class="card-header-action">
               <a href="" class="btn btn-primary" data-toggle="modal" data-target="#ajuda">Ajuda?</a>
              </div>

            </div>

            <div class="card-body">

                <form action="{{ route('dados.update') }}" method="post" enctype="multipart/form-data">
                 @csrf
                 @method('PUT')

                  <div class="form-group">
                    <img src="" style="width:190px;">
                  </div>
                  <div class="form-group">
                    <label for="">Logomarca(300x90px)</label>
                    <input type="file" name="logo" class="form-control" >
                  </div>

                  <div class="form-group">
                    <img src="" style="width:50px;">
                  </div>

                  <div class="form-group">
                    <label for="">Icone(50x50px)</label>
                    <input type="file" name="icone" class="form-control">
                  </div>

                  <div class="form-group">
                    <label for="">Nome</label>
                    <input type="text" name="nome" placeholder="Add nome da empresa" class="form-control" value="{{ old('nome', $dados->nome)}}">
                  </div>

                  <div class="form-group">
                    <label for="">CNPJ(Opcional)</label>
                    <input type="text" id="cnpj" name="cnpj" placeholder="Add CNPJ da empresa" class="form-control" value="{{ old('cnpj', $dados->cnpj)}}">
                  </div>


                 <div class="form-group row mb-4">

                    <div class="col-sm-6">
                    <label for="">Fone</label>
                    <input type="text" id="fone" name="fone" placeholder="Add Telefone fix" class="form-control" value="{{ old('fone', $dados->fone)}}">
                    </div>

                    <div class="col-sm-6">
                    <label for="">Whatsapp</label>
                    <input type="text" id="cel" name="whatsapp" placeholder="Add Whatsapp Válido" class="form-control" value="{{ old('whatsapp', $dados->whatsapp)}}">
                    </div>

                 </div>

                  <div class="form-group">
                    <label for="">E-mail</label>
                    <input type="text" name="email" placeholder="Add seu e-mail" class="form-control" value="{{ old('email', $dados->email)}}">
                  </div>

                  <div class="form-group row mb-4">
                  <div class="col-sm-4">
                    <label for="">Endereço:</label>
                    <input type="text" name="endereco" placeholder="Add endereço da empresa" class="form-control" value="{{ old('endereco', $dados->endereco)}}">
                  </div>

                  <div class="col-sm-4">
                    <label for="">Número:</label>
                    <input type="number" name="numero" placeholder="Add número" class="form-control" value="{{ old('numero', $dados->numero)}}">
                  </div>

                  <div class="col-sm-4">
                    <label for="">CEP:</label>
                    <input type="text" id="cepmj" name="cep" placeholder="Add CEP" class="form-control" value="{{ old('cep', $dados->cep)}}">
                  </div>
                </div>

                <div class="form-group row mb-4">

                    <div class="col-sm-6">
                    <label for="">Cidade</label>
                    <input type="text" name="cidade" placeholder="Add cidade" class="form-control" value="{{ old('cidade', $dados->cidade)}}">
                    </div>

                    <div class="col-sm-6">
                    <label for="">Estado</label>
                    <input type="text" name="estado" placeholder="Add estado" class="form-control" value="{{ old('estado', $dados->estado)}}">
                    </div>

                 </div>


                  <div class="form-group ">
                    <label>Descrição:</label>
                      <textarea class="summernote" name="descricao">{!! $dados->descricao !!}</textarea>
                  </div>


                  <button type="submit" class="btn btn-primary">Salvar</button>

                </form>

            </div>

          </div>
        </div>

      </div>

    </div>
</section>

@endsection

