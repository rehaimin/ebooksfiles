<script>
  let regularPriceInput = document.getElementById("product_regular_price");

  function showLoader() {
    let loaderWrapper = document.createElement('div');
    loaderWrapper.classList = "loader-wrapper"
    let loader = document.createElement('div');
    loader.classList = "loader";
    document.body.appendChild(loaderWrapper);
    loaderWrapper.appendChild(loader);
    document.body.classList.add('overflow-hidden')
  }

  function hideLoader() {
    let loaderWrapper = document.querySelector('.loader-wrapper');
    document.body.removeChild(loaderWrapper);
    document.body.classList.remove('overflow-hidden')
  }

  let titleInput = document.getElementById("product_title");

  function scrapData() {
    let amazonCover = document.getElementById("amazonCover");
    // let imgDownload = document.getElementById("imgDownload");
    let urlInput = document.getElementById("amzon_url");
    showLoader();
    fetch("/amazon-product/?url=" + urlInput.value, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then((response) => response.json())
      .then((data) => {
        // 			console.log(data);
        if (data.data.largeimage) {
          amazonCover.src = data.data.largeimage;
          transformImage(data.data.largeimage);

        } else {
          amazonCover.src = data.data.image;
          transformImage(data.data.image);
        }
        // imgDownload.href = data.data.image;
        // imgDownload.download = "test.jpg";
        titleInput.value = data.data.title;
        regularPriceInput.value = data.data.price;
        tinymce.activeEditor.setContent(data.data.description);
        calculateSalePrice();
        copyDownloadNameFromTitle()
        hideLoader()
      })
      .catch((error) => {
        console.error("Error:", error);
        hideLoader()
      });
  }

  function transformImage(imageUrl) {
    fetch("/cover?url=" + imageUrl, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then((response) => response.json())
      .then((data) => {
        if (data.message = 'success') {
          amazonCover.src = data.image_path;
        };
      }).catch(error => {
        // Gérer l'erreur
        console.error('Erreur fetch :', error);
      });
  }

  function calculateSalePrice() {
    let salePriceInput = document.querySelector("input[name='product_sale_price']");
    let discountRate = document.getElementById('discount').value;
    let calculatedSalePrice;
    let minmumPrice = 25.99;
    calculatedSalePrice = parseInt(regularPriceInput.value * (1 - discountRate)) - 0.01;
    if (calculatedSalePrice < minmumPrice) {
      salePriceInput.value = minmumPrice
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

  function copyDownloadNameFromTitle() {
    let downloadNameInput = document.querySelector("input[name='virtual_file_name']");
    let uplaodFileName = document.getElementById('name');
    downloadNameInput.value = titleInput.value;
    uplaodFileName.value = titleInput.value;
  }

  let fileForm = document.getElementById('fileForm');
  fileForm.addEventListener('submit', function(e) {
    e.preventDefault();
    showLoader();
    fetch("{{ route('files.store') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          url: document.getElementById('url').value,
          file: document.querySelector('[name="file"]').value,
          name: document.getElementById('name').value,
          api: true
        })
      }).then((response) => (response.json()))
      .then((data) => {
        if (data.message = 'success') {
          document.getElementById('virtual_file_url').value = data.link;
        };
        hideLoader()
      }).catch(error => {
        // Gérer l'erreur
        console.error('Erreur fetch :', error);
        hideLoader()
      });
  })
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
