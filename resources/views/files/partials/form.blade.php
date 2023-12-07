<form action="{{ isset($file) ? route('files.update', $file->token) : route('files.store') }}" method="post"
  enctype="multipart/form-data">
  @csrf
  <div class="mb-3 row">
    <label for="name" class="col-sm-2 col-form-label">Nom</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="name" name="name"
        value="{{ isset($file) ? $file->name : '' }}">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="file" class="col-sm-2 col-form-label">Fichier</label>
    <div class="col-sm-10">
      <input class="form-control" type="file" id="file" name="file">
    </div>
  </div>
  <div class="mb-3 row">
    <label for="url" class="col-sm-2 col-form-label">Ou une url</label>
    <div class="col-sm-10">
      <input type="url" class="form-control" id="url" name="url">
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Valider</button>
</form>
