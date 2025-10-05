<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
  <title>{{ strtoupper(config('app.name')) }}</title>
  <style>
    @media screen and (max-width: 776px) {
      #app {
        margin-inline-start: 0px !important;
        margin-block-start: 0px !important;
      }
    }
  </style>
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Symbols+Rounded">

  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

  @vite(['resources/scss/quasar.scss'])
</head>

<body>

  <div id="app" style=""></div>

  @vite(['resources/js/app.js'])

</body>

</html>