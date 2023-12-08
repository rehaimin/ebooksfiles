@extends('layouts.app')

@section('content')

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Ajouter un fichier</div>

          <div class="card-body">
            <a href="{{ route('files.index') }}" class="btn btn-primary my-3">Liste des fichiers</a>
            @include('files.partials.form')
            @if ($errors->any())
              <div class="alert alert-danger my-3">
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
