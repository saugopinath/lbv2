<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Premium Tailwind CSS Admin & Dashboard Template" />
  <meta name="author" content="Webonzer" />

  <!-- Site Tiltle -->
  <title>Lakshmir Bhandar | Government of West Bengal</title>

  <!-- Favicon Icon -->
  <link rel="shortcut icon" href="{{asset('images/biswofab.ico')}}">
  <!-- Style Css -->
  @livewireStyles
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-cover bg-center bg-no-repeat bg-white"
  style="background-image: url('https://c.animaapp.com/mdn4r47eB5hzlO/img/testimonial-bg1-1.png')">
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white/80 rounded-2xl shadow-xl w-full max-w-6xl overflow-hidden flex flex-col lg:flex-row">

      <!-- Left Image Section -->
      <div class="lg:flex lg:w-1/2 flex-col justify-center items-center p-4 relative rounded-l-2xl">
        <div class="absolute top-4 left-4 bg-[#003974] p-3 rounded-full">
          <img src="https://c.animaapp.com/mdn4r47eB5hzlO/img/home.svg" alt="Home" class="w-5 h-5">
        </div>
        <!-- <img class="w-64 mb-6" src="https://c.animaapp.com/mdn4r47eB5hzlO/img/biswo-1.png" alt="Logo"> -->
        <div class="flex justify-center items-center ">
          <img class="w-48 sm:w-64 mb-4" src="https://c.animaapp.com/mdn4r47eB5hzlO/img/biswo-1.png" alt="Logo">
        </div>
        <div class="flex justify-center items-center ">
          <!-- <div class="w-48 sm:w-64 mb-4 flex items-center justify-center text-center text-2xl sm:text-3xl font-bold text-green-800">
          পশ্চিমবঙ্গ সরকার
        </div> -->
          <div class="text-2xl sm:text-3xl font-bold text-green-800 text-center">
            পশ্চিমবঙ্গ সরকার
          </div>
        </div>
        <div class="flex justify-center items-center ">
          <div class="text-2xl sm:text-3xl font-bold text-purple-800 text-center">
            Government of West Bengal
          </div>
        </div>

      </div>
      <!-- Form Section -->
      <div class="w-full lg:w-1/2 p-18 relative bg-white" x-data="{ showPassword: false }">
        <!-- Home Icon -->
        <!-- <div class="absolute top-4 left-4 bg-[#003974] p-3 rounded-full">
            <img src="https://c.animaapp.com/mdn4r47eB5hzlO/img/home.svg" alt="Home" class="w-5 h-5">
          </div> -->
        <!-- Top Right Logo -->
        <div class="w-full flex justify-center mb-2">
          <img src="images/biswo_bangla.png" alt="Centered Logo" class="w-24 h-auto sm:block">
          <!-- <h2 class="text-2xl lg:text-3xl text-center text-[#003974] font-bold">Lakshmir Bhandar Portal</h2> -->
        </div>
        <div class="text-center mb-2">
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Lakshmir Bhandar Portal</h1>
        </div>
        {{ $slot }}
      </div>
    </div>
  </div>
  @livewireScripts
</body>
</html>
