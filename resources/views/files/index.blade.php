@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Liste des fichiers</div>

          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <a href="{{ route('files.create') }}" class="btn btn-primary my-3">Ajouter un Fichier</a>
              <form method="GET" action="{{ route('files.index') }}" accept-charset="UTF-8"
                class="form-inline my-2 my-lg-0 float-right" role="search">
                @csrf
                <div class="input-group">
                  <input type="search" class="form-control" name="search" placeholder="Recherche..."
                    value="{{ request('search') }}">
                  <button class="btn btn-secondary" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                      class="bi bi-search" viewBox="0 0 16 16">
                      <path
                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                  </button>
                </div>
              </form>
            </div>
            {{ $files->links() }}
            <table class="table-responsive table table-striped table-light">
              <thead>
                <tr>
                  <th style="width: 5%;">#</th>
                  <th style="width: 55%;">Nom</th>
                  <th style="width: 5%;">Lien</th>
                  <th style="width: 5%;">Taille/Mb</th>
                  <th style="width: 15%;">Date ajout</th>
                  <th style="width: 15%;">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($files as $file)
                  <tr>
                    <td style="width: 5%;">{{ count($files) - $loop->index }}</td>
                    <td style="width: 55%;">{{ $file->name }}</td>
                    <td style="width: 5%; text-align:center;">
                      <a href="{{ asset('download/' . $file->token) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512" style="width: 32px">
                          <path
                            d="M320 336h76c55 0 100-21.21 100-75.6s-53-73.47-96-75.6C391.11 99.74 329 48 256 48c-69 0-113.44 45.79-128 91.2-60 5.7-112 35.88-112 98.4S70 336 136 336h56M192 400.1l64 63.9 64-63.9M256 224v224.03"
                            fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="32" />
                        </svg>
                      </a>
                    </td>
                    <td style="text-align: end;width: 5%;">{{ $file->size }}</td>
                    <td style="width: 15%; text-align:center;">{{ date('d/m/Y H:i:s', strtotime($file->created_at)) }}
                    </td>
                    <td style="width: 15%; text-align:center;">
                      <button type="button" class="btn" data-bs-toggle="modal"
                        data-bs-target="#modal{{ $file->id }}" style="border:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"
                          style="width: 32px;color:red;">
                          <path d="M112 112l20 320c.95 18.49 14.4 32 32 32h184c17.67 0 30.87-13.51 32-32l20-320"
                            fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="32" />
                          <path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32"
                            d="M80 112h352" />
                          <path
                            d="M192 112V72h0a23.93 23.93 0 0124-24h80a23.93 23.93 0 0124 24h0v40M256 176v224M184 176l8 224M328 176l-8 224"
                            fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="32" />
                        </svg>
                      </button>

                      <a href="{{ route('files.edit', $file->token) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"
                          style="width: 32px;color:green">
                          <path d="M384 224v184a40 40 0 01-40 40H104a40 40 0 01-40-40V168a40 40 0 0140-40h167.48"
                            fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="32" />
                          <path
                            d="M459.94 53.25a16.06 16.06 0 00-23.22-.56L424.35 65a8 8 0 000 11.31l11.34 11.32a8 8 0 0011.34 0l12.06-12c6.1-6.09 6.67-16.01.85-22.38zM399.34 90L218.82 270.2a9 9 0 00-2.31 3.93L208.16 299a3.91 3.91 0 004.86 4.86l24.85-8.35a9 9 0 003.93-2.31L422 112.66a9 9 0 000-12.66l-9.95-10a9 9 0 00-12.71 0z" />
                        </svg>
                      </a>
                    </td>
                    <!-- Modal -->
                    <div class="modal fade" id="modal{{ $file->id }}" tabindex="-1"
                      aria-labelledby="modalLabel{{ $file->id }}" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalLabel{{ $file->id }}">Confirmer la suppression
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            Etes vous sû de vouloir supprimer : <span style="font-weight: 700">{{ $file->name }}</span>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Annuler</button>
                            <form action="{{ route('files.destroy', $file->token) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </tr>
                @endforeach
              </tbody>
            </table>
            {{ $files->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
