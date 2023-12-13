@extends('layouts.app')

@section('content')
  <script src="{{ asset('tinymce/js/tinymce/tinymce.min.js') }}"></script>
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="row justify-content-center">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">Produit Amazon</div>

              <div class="card-body">
                <div class="mb-2 row align-items-center ">
                  <label for="url" class="col-form-label">Url</label>
                  <div class="col-sm-11">
                    <input type="url" class="form-control" id="url" name="url"
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
                  </div>
                  <div class="col-md-9">
                    <div class="mb-2 row">
                      <label for="product_title" class="col-form-label">Nom</label>
                      <div class="col-sm-12">
                        <input type="text" class="form-control" id="product_title" name="product_title">
                      </div>
                    </div>
                    <div class="mb-2 row">
                      <label for="product_regular_price" class="col-form-label">Prix</label>
                      <div class="col-sm-12">
                        <input type="number" class="form-control" id="product_regular_price" name="product_regular_price"
                          step="0.01">
                      </div>
                    </div>
                    <div class="mb-2 row">
                      <label for="product_sale_price" class="col-form-label">Prix Promo</label>
                      <div class="col-sm-12">
                        <input type="number" class="form-control" id="product_sale_price" name="product_sale_price"
                          step="0.01">
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
      </div>
    </div>
  </div>
  <script>
    let regularPriceInput = document.getElementById("product_regular_price");

    function scrapData() {
      let amazonCover = document.getElementById("amazonCover");
      // let imgDownload = document.getElementById("imgDownload");
      let titleInput = document.getElementById("product_title");
      let urlInput = document.getElementById("url");
      fetch("/amazon-product/?url=" + urlInput.value, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
        })
        .then((response) => response.json())
        .then((data) => {
          // 			console.log(data);
          amazonCover.src = data.data.image;
          // imgDownload.href = data.data.image;
          // imgDownload.download = "test.jpg";
          titleInput.value = data.data.title;
          regularPriceInput.value = data.data.price;
          tinymce.activeEditor.setContent(data.data.description);
          calculateSalePrice();
          // copyDownloadNameFromTitle()
        })
        .catch((error) => {
          console.error("Error:", error);
        });
    }

    function calculateSalePrice() {
      let salePriceInput = document.querySelector("input[name='product_sale_price']");
      let discountRate = 0.80;
      let calculatedSalePrice;
      calculatedSalePrice = parseInt(regularPriceInput.value * (1 - discountRate)) - 0.01;
      if (calculatedSalePrice < 19.99) {
        salePriceInput.value = 19.99
        return;
      }
      let numberWithoutDecimales = calculatedSalePrice.toString().split(".")[0];
      let lastNumber = parseInt(numberWithoutDecimales.substr(-1))
      // return;
      switch (true) {
        case (lastNumber === 0):
          salePriceInput.value = (parseInt(numberWithoutDecimales) * 100 - 1) / 100;
          break;
        case (lastNumber > 0 && lastNumber < 5):
          salePriceInput.value = ((parseInt(numberWithoutDecimales) - lastNumber) * 100 + 599) / 100;
          break;
        case (lastNumber > 5 && lastNumber < 7):
          salePriceInput.value = ((parseInt(numberWithoutDecimales) - lastNumber) * 100 + 799) / 100;
          break;
        case (lastNumber === 5):
        case (lastNumber === 7):
          salePriceInput.value = (parseInt(numberWithoutDecimales) * 100 + 99) / 100;
          break;
        default:
          salePriceInput.value = ((parseInt(numberWithoutDecimales) - lastNumber) * 100 + 999) / 100;
      }
    }


    // import tinymce from 'tinymce';

    document.addEventListener('DOMContentLoaded', function() {
      tinymce.init({
        selector: 'textarea', // cible tous les textareas
        plugins: 'autolink link image lists',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image',
        menubar: false
      });
    });
  </script>
@endsection
