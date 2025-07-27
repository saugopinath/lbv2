<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Premium Tailwind CSS Admin & Dashboard Template" />
  <meta name="author" content="Webonzer" />

  <title>Lakshmir Bhandar | Government of West Bengal</title>

  <link rel="shortcut icon" href="{{asset('images/biswofab.ico')}}">
  @livewireStyles
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="main"
  class="font-inter text-base antialiased font-medium relative min-h-screen flex flex-col items-center justify-center
  bg-gradient-to-b from-sky-100 via-sky-300 to-sky-500 dark:from-sky-900 dark:via-sky-800 dark:to-sky-700
  text-dark dark:text-white p-8">

  <!-- Header Row: Centered Heading + Toggle Right -->
  <div class="flex flex-col items-center w-full max-w-6xl relative mb-8">

   <h1 class="text-3xl font-bold text-center mb-2 px-4 py-2 rounded-lg bg-white/70 dark:bg-sky-900/70">
  Lakshmir Bhandar Portal | Government of West Bengal
</h1>


    <!-- Toggle: absolute top-right -->
    <div class="absolute right-0 top-0">
      <a href="javascript:;" x-show="$store.app.mode === 'light'" @click="$store.app.toggleMode('dark')"
        class="text-gray-700 hover:text-primary dark:text-gray-300">
        <!-- Moon Icon -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd"
            d="M22 12C22 17.5228 17.5228 22 12 22C10.8358 22 9.71801 21.801 8.67887 21.4352C8.24138 20.3767 8 19.2165 8 18C8 15.7787 8.80467 13.7454 10.1384 12.1757C11.31 13.8813 13.2744 15 15.5 15C17.8615 15 19.9289 13.7405 21.0672 11.8568C21.3065 11.4607 22 11.5372 22 12Z"
            fill="currentColor" />
          <path
            d="M2 12C2 16.3586 4.78852 20.0659 8.67887 21.4353C8.24138 20.3768 8 19.2166 8 18C8 15.7788 8.80467 13.7455 10.1384 12.1758C9.42027 11.1303 9 9.86422 9 8.5C9 6.13845 10.2594 4.07105 12.1432 2.93276C12.5392 2.69347 12.4627 2 12 2C6.47715 2 2 6.47715 2 12Z"
            fill="currentColor" />
        </svg>
      </a>
      <a href="javascript:;" x-show="$store.app.mode === 'dark'" @click="$store.app.toggleMode('light')"
        class="text-yellow-400 hover:text-yellow-300">
        <!-- Sun Icon -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path
            d="M18 12C18 15.3137 15.3137 18 12 18C8.68629 18 6 15.3137 6 12C6 8.68629 8.68629 6 12 6C15.3137 6 18 8.68629 18 12Z"
            fill="currentColor" />
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M12 1.25C12.4142 1.25 12.75 1.58579 12.75 2V3C12.75 3.41421 12.4142 3.75 12 3.75C11.5858 3.75 11.25 3.41421 11.25 3V2C11.25 1.58579 11.5858 1.25 12 1.25ZM1.25 12C1.25 11.5858 1.58579 11.25 2 11.25H3C3.41421 11.25 3.75 11.5858 3.75 12C3.75 12.4142 3.41421 12.75 3 12.75H2C1.58579 12.75 1.25 12.4142 1.25 12ZM20.25 12C20.25 11.5858 20.5858 11.25 21 11.25H22C22.4142 11.25 22.75 11.5858 22.75 12C22.75 12.4142 22.4142 12.75 22 12.75H21C20.5858 12.75 20.25 12.4142 20.25 12ZM12 20.25C12.4142 20.25 12.75 20.5858 12.75 21V22C12.75 22.4142 12.4142 22.75 12 22.75C11.5858 22.75 11.25 22.4142 11.25 22V21C11.25 20.5858 11.5858 20.25 12 20.25Z"
            fill="currentColor" />
        </svg>
      </a>
    </div>

  </div>

  <!-- Bordered Box: Logo Left + Slot Right -->
  <div
    class="flex flex-col md:flex-row items-center justify-center gap-12 p-8 border-4 border-gray-200 dark:border-gray-600 rounded-2xl backdrop-blur-xl bg-white/80 dark:bg-black/40 shadow-2xl max-w-6xl w-full mx-4">

    <!-- Logo Left -->
    <div class="text-center md:text-left">
      <a href="#" class="block">
        <x-logos.lb-logo width="200" height="200" />
      </a>
    </div>

    <!-- Slot Right -->
    <div class="w-full max-w-md">
      {{$slot}}
    </div>

  </div>

  <footer class="py-6 text-center text-gray-700 dark:text-gray-300">
    &copy; <script>document.write(new Date().getFullYear());</script> Lakshmir Bhandar
  </footer>

  @livewireScriptConfig
</body>

</html>
