<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Livre simulé</title>
  <style>
    body {
      width: 1080px;
      height: 1080px;
    }

    .container {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 1080px;
      height: 1080px;
      margin: 0;
      background-color: #fff;
    }

    .book {
      position: relative;
      width: 775.5px;
      height: 990px;
      background-color: #fff;
      box-shadow: 0 0 40px rgba(0, 0, 0, 0.7);
      border-radius: 5px;
      transform: rotateY(30deg);
      transform: rotateZ(-0.125deg);
      overflow: hidden;
    }

    .book::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: calc(100% - 35.2px);
      height: 100%;
      background: url('{{ asset('images/image.jpg') }}') center/cover no-repeat;
      z-index: 9;
    }

    .side {
      position: absolute;
      z-index: 99;
      width: 35.2px;
      right: 0;
      height: 990px;
      background: url('{{ asset('images/book-side.jpg') }}') center/cover no-repeat;
      transform: rotateY(-30deg);
      transform: translateX(2px);
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="wrapper">
      <div class="book">
        <div class="side"></div>
      </div>
    </div>
  </div>
</body>

</html>
