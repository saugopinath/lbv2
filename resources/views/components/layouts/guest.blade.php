<!DOCTYPE html>
<html lang="en">


<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Premium Tailwind CSS Admin & Dashboard Template" />
  <meta name="author" content="Webonzer" />

  <!-- Site Tiltle -->
    <title>Jai Bangla | Government of West Bengal</title>


  <!-- Favicon Icon -->
  <link rel="shortcut icon" href="{{asset('images/biswofab.ico')}}">
  <!-- Style Css -->
  @livewireStyles
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-cover bg-center bg-no-repeat bg-white bg-login-cover">
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white/80 rounded-2xl shadow-xl w-full max-w-6xl overflow-hidden flex flex-col lg:flex-row">

      <!-- Left Image Section -->
      <div class="lg:flex lg:w-1/2 flex-col justify-center items-center p-4 relative rounded-l-2xl">
        <div class="absolute top-4 left-4 bg-[#003974] p-3 rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-5 h-5 fill-white text-white">
            <path d="M341.8 72.6C329.5 61.2 310.5 61.2 298.3 72.6L74.3 280.6C64.7 289.6 61.5 303.5 66.3 315.7C71.1 327.9 82.8 336 96 336L112 336L112 512C112 547.3 140.7 576 176 576L464 576C499.3 576 528 547.3 528 512L528 336L544 336C557.2 336 569 327.9 573.8 315.7C578.6 303.5 575.4 289.5 565.8 280.6L341.8 72.6zM304 384L336 384C362.5 384 384 405.5 384 432L384 528L256 528L256 432C256 405.5 277.5 384 304 384z" />
          </svg>
        </div>
        <!-- <img class="w-64 mb-6" src="https://c.animaapp.com/mdn4r47eB5hzlO/img/biswo-1.png" alt="Logo"> -->
        <div class="flex justify-center items-center ">
          <img class="w-48 sm:w-64 mb-4" src="images/biswo-1.png" alt="Logo">
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
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Jai Bangla Portal</h1>
        </div>
        {{ $slot }}
      </div>
    </div>
  </div>
  @livewireScripts
</body>

</html>