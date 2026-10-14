@extends('layout.principal')

@section('title', 'Cadastro de Usuário')

@section('content')

    <div class="row justify-content-center d-flex mt-4">
        <div class= "col-8 rounded shadow-lg p-3 mb-5 bg-body-tertiary">
            <img src="e.jpg" alt="Descrição da imagem" style="width:850px; height:150px; border-radius:5%;">
            <h2 class="text-center mt-4">Cadastro de Usuário</h2>
            <div class="row justify-content-center d-flex mt-2">
                <div class="col-lg-6 col-md-6 col-sm-12 mt-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite seu nome">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mt-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu email">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mt-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mt-3">
                    <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" placeholder="Digite sua senha novamente">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mt-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" class="form-control"  maxlength="11"id="cpf" name="cpf" placeholder="Digite seu CPF">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 mt-3">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control form-control-sm" id="data_nascimento" name="data_nascimento" placeholder="Digite sua data de nascimento">
                </div>
                <div class="col-4 mt-2 justify-content-center d-flex">
                <button type="button" class="btn btn-primary margin-auto mt-4">Cadastrar Usuário</button>


            </div>

         </div>
    </div>




@endsection
