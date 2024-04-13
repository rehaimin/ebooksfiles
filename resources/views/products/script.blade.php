<script>
  let titleInput = document.getElementById("product_title");
  let regularPriceInput = document.getElementById("product_regular_price");
  let salePriceInput = document.querySelector("input[name='product_sale_price']");
  let imageInput = document.querySelector("input[name='image']");
  let amazonCover = document.getElementById("amazonCover");

  function showLoader(loaderParentSelector) {
    let loaderParent = document.querySelector(loaderParentSelector);
    let loaderWrapper = document.createElement('div');
    loaderWrapper.classList = "loader-wrapper"
    let loader = document.createElement('div');
    loader.classList = "loader";
    loaderParent.appendChild(loaderWrapper);
    loaderWrapper.appendChild(loader);
    // document.body.classList.add('overflow-hidden')
  }

  function hideLoader(loaderParentSelector) {
    let loaderParent = document.querySelector(loaderParentSelector);
    let loaderWrapper = document.querySelector('.loader-wrapper');
    loaderParent.removeChild(loaderWrapper);
    // document.body.classList.remove('overflow-hidden')
  }



  function scrapData() {
    // let imgDownload = document.getElementById("imgDownload");
    let urlInput = document.getElementById("amzon_url");
    showLoader('#product_card');
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
        // copyDownloadNameFromTitle();
        hideLoader('#product_card');
      })
      .catch((error) => {
        console.error("Error:", error);
        hideLoader('#product_card')
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
          imageInput.value = data.image_path;
        };
      }).catch(error => {
        // Gérer l'erreur
        console.error('Erreur fetch :', error);
      });
  }

  function calculateSalePrice() {

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

  let downloadNameInput = document.querySelector("input[name='virtual_file_name']");
  let downloadUrlInput = document.querySelector("input[name='virtual_file_url']");
  downloadNameInput.value = "DOWNLOAD";
  
  function copyDownloadNameFromTitle() {
    let uplaodFileName = document.getElementById('name');
    downloadNameInput.value = titleInput.value;
    uplaodFileName.value = titleInput.value;
  }

  let fileForm = document.getElementById('fileForm');
  fileForm.addEventListener('submit', function(e) {
    e.preventDefault();
    showLoader('#file_card');
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
          downloadUrlInput.value = data.link;
          updateFilesList();
          fileForm.reset();
        };
        hideLoader('#file_card')
      }).catch(error => {
        // Gérer l'erreur
        console.error('Erreur fetch :', error);
        hideLoader('#file_card')
      });
  });

  function createWooProduct() {
    let checkedCategory = document.querySelector("input[type='checkbox']:checked").value;
    fetch(
        "{{ route('add-product') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            title: titleInput.value,
            regular_price: regularPriceInput.value,
            sale_price: salePriceInput.value,
            description: tinymce.activeEditor.getContent(),
            image: amazonCover.src,
            donwload_name: downloadNameInput.value,
            donwload_link: downloadUrlInput.value,
            category_id: checkedCategory,
          })
        }).then((response) => (response.json()))
      .then((data) => {
        console.log(data);
      }).catch(error => {
        console.error('Erreur fetch :', error);
      });
  }



  let searchForm = document.querySelector('#searchForm');
  searchForm.addEventListener('submit', function(e) {
    e.preventDefault();
    showLoader('#filesListCard');
    updateFilesList();
    hideLoader('#filesListCard');
  })

  function listenToFilesCardEvents() {
    let pageLinks = [];
    pageLinks = document.querySelectorAll("a.page-link");

    pageLinks.forEach(pageLink => {
      if (!pageLink.dataset.event) {
        pageLink.addEventListener('click', function(e) {
          e.preventDefault();
          showLoader('#filesListCard');
          let url = new URL(e.target.href);
          pageNumber = url.searchParams.get('page');
          updateFilesList(`page=${pageNumber}`);
          hideLoader('#filesListCard');
          pageLink.dataset.event = true;
        })
      }
    })
  }

  listenToFilesCardEvents();

  function updateFilesList(paramsText = '') {
    let parser = new DOMParser();
    let searchTerm = document.querySelector('#searchTerm').value
    fetch("/files?search=" + searchTerm + '&' + paramsText, {
        method: "GET",
      })
      .then((response) => response.text())
      .then((html) => {
        let doc = parser.parseFromString(html, "text/html");
        document.querySelector(".table-wrapper").innerHTML =
          doc.querySelector(".table-wrapper").innerHTML;
        listenToFilesCardEvents();
      })
      .catch(error => {
        console.error('Erreur liste de fichiers :', error);
      });
  }

  function getUrlParam(param) {
    let url = new URL(window.location.href);
    return url.searchParams.get(param);
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
