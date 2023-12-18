@extends('layouts.app')

@section('content')
  <script src="{{ asset('tinymce/js/tinymce/tinymce.min.js') }}"></script>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-8 mb-3">
        <form action="{{ route('add-product') }}" method="get">
          <div class="row justify-content-center">
            <div class="col-md-12">
              <div class="card" id="product_card">
                <div class="card-header d-flex justify-content-between align-items-center"><span>Article</span> <button
                    type="submit" class="btn btn-primary">Ajouter</button></div>
                <div class="card-body">
                  <div class="mb-2 row align-items-center ">
                    <label for="url" class="col-form-label">Url</label>
                    <div class="col-sm-11">
                      <input type="url" class="form-control" id="amzon_url" name="url"
                        placeholder="https://www.amazon.com/...">
                    </div>
                    <div class="col-sm-1">
                      <button type="button" class="btn btn-primary" id="scrapBtn" onclick="scrapData()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                          class="bi bi-box-arrow-in-down" viewBox="0 0 16 16">
                          <path fill-rule="evenodd"
                            d="M3.5 6a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-2a.5.5 0 0 1 0-1h2A1.5 1.5 0 0 1 14 6.5v8a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 14.5v-8A1.5 1.5 0 0 1 3.5 5h2a.5.5 0 0 1 0 1z" />
                          <path fill-rule="evenodd"
                            d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                        </svg>
                      </button>
                    </div>
                  </div>

                  <div class="row align-items-center">
                    <div class="col-md-3">
                      <img src="{{ asset('images/No-Image-Placeholder.jpg') }}" alt="" class="w-100 rounded"
                        id="amazonCover">
                      <input type='hidden' name='image' value="">
                    </div>
                    <div class="col-md-9">
                      <div class="mb-2 row">
                        <label for="product_title" class="col-form-label">Nom</label>
                        <div class="col-sm-12">
                          <input type="text" class="form-control" id="product_title" name="product_title"
                            onchange="copyDownloadNameFromTitle()" required>
                        </div>
                      </div>
                      <div class="row align-items-center">
                        <div class="col-md-8">
                          <div class="row">
                            <div class="col-md-12">
                              <label for="product_regular_price" class="col-form-label">Prix</label>
                              <div class="col-sm-12">
                                <input type="number" class="form-control" id="product_regular_price"
                                  name="product_regular_price" step="0.01" onchange="calculateSalePrice()">
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="mb-2 row">
                                <div class="col-md-8">
                                  <label for="product_sale_price" class="col-form-label">Prix Promo</label>
                                  <input type="number" class="form-control" id="product_sale_price"
                                    name="product_sale_price" step="0.01" required>
                                </div>
                                <div class="col-md-4">
                                  <label for="discount" class="col-form-label">Réduction</label>
                                  <select name="discount" class="form-select" id="discount"
                                    onchange="calculateSalePrice()">
                                    <option value="0.9">90%</option>
                                    <option value="0.85">85%</option>
                                    <option value="0.8">80%</option>
                                    <option value="0.75">75%</option>
                                    <option value="0.7" selected>70%</option>
                                    <option value="0.65">65%</option>
                                    <option value="0.6">60%</option>
                                    <option value="0.55">55%</option>
                                    <option value="0.5">50%</option>
                                    <option value="0.45">45%</option>
                                    <option value="0.4">40%</option>
                                    <option value="0.35">35%</option>
                                    <option value="0.3">30%</option>
                                    <option value="0.25">25%</option>
                                    <option value="0.2">20%</option>
                                    <option value="0.15">15%</option>
                                    <option value="0.1">10%</option>
                                  </select>
                                </div>
                              </div>
                            </div>

                          </div>
                          <div class="mb-2 row">
                            <label for="virtual_file_name" class="col-form-label">Nom du fichier</label>
                            <div class="col-sm-12">
                              <input type="text" class="form-control" id="virtual_file_name"
                                name="virtual_file_name" required>
                            </div>
                          </div>
                          <div class="mb-2 row">
                            <label for="virtual_file_url" class="col-form-label">Lien du fichier</label>
                            <div class="col-sm-12">
                              <input type="url" class="form-control" id="virtual_file_url" name="virtual_file_url"
                                placeholder="http://..." required>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="card">
                            <div class="card-header">
                              Catégories
                            </div>
                            <div class="card-body">
                              @foreach ($categories as $category)
                                <div class="form-check">
                                  <input type="checkbox" id="categoy-{{ $category->id }}" name="categories[]"
                                    value="{{ $category->id }}" class="form-check-input">
                                  <label for="categoy-{{ $category->id }}" class="form-check-label">
                                    {{ $category->name }}</label>
                                </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="mb-2 row">
                    <label for="product_description" class="col-form-label">Description</label>
                    <div class="col-sm-12">
                      <textarea type="number" class="form-control" id="product_description" name="product_description" step="0.01"></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="col-lg-4">
        <div class="card mb-3" id="file_card">
          <div class="card-header">Ajouter un fichier</div>

          <div class="card-body">
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
        <style>
          #addFile,
          .actionButtons,
          .actionHeader {
            display: none;
          }
        </style>
        @include('files.partials.files-list')
      </div>
    </div>
  </div>
  @include('products.script')
@endsection
