<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-slate-100">

    <div class="min-h-screen flex items-center justify-center px-4 py-10">

        <div class="w-full max-w-md">

            <!-- Logo / Brand -->
            <div class="text-center mb-8">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 shadow-lg shadow-blue-600/30">
                    <span class="text-2xl font-bold text-white">S</span>
                </div>

                <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                    Welcome back
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Sign in to your account to continue
                </p>
            </div>

            <!-- Login Card -->
            <div class="rounded-2xl bg-white p-8 shadow-xl shadow-slate-200/60">

                <form method="POST" action="#">

                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email"
                               class="mb-2 block text-sm font-medium text-slate-700">
                            Email address
                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            placeholder="you@example.com"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                        >
                    </div>

                    <!-- Password -->
                    <div class="mt-5">
                        <div class="mb-2 flex items-center justify-between">
                            <label for="password"
                                   class="block text-sm font-medium text-slate-700">
                                Password
                            </label>

                            <a href="#"
                               class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                Forgot password?
                            </a>
                        </div>

                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >

                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-2 text-slate-400 hover:text-slate-600"
                                aria-label="Show password"
                            >
                                👁
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="mt-5 flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        >

                        <label for="remember"
                               class="ml-2 text-sm text-slate-600">
                            Remember me
                        </label>
                    </div>

                
                    <!-- Login Button -->
                    <button
                        type="submit"
                        class="mt-6 w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20"
                    >
                        Sign in
                    </button>

                </form>

                <!-- Register -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-slate-500">
                        Don't have an account?
                        <a href="#"
                           class="font-semibold text-blue-600 hover:text-blue-700">
                            Create an account
                        </a>
                    </p>
                </div>

            </div>

            <!-- Footer -->
            <p class="mt-6 text-center text-xs text-slate-400">
                © {{ date('Y') }} Your Company. All rights reserved.
            </p>

        </div>

    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');

            password.type =
                password.type === 'password'
                    ? 'text'
                    : 'password';
        }
    </script>

</body>
</html>