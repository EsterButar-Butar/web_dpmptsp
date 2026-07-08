<x-guest-layout>

    <div class="w-full max-w-[480px] mx-auto">

        {{-- TITLE --}}
        <div class="text-center mb-10 md:mb-12">

            <h1
                class="
                    text-[28px]
                    md:text-[32px]
                    font-bold
                    text-blue-600
                "
            >
                Daftar Akun
            </h1>

        </div>


        <form
            method="POST"
            action="{{ route('register') }}"
            class="space-y-4"
        >
            @csrf


            {{-- NAME --}}
            <div>

                <div
                    class="
                        relative
                        flex
                        items-center
                        min-h-[54px]
                        px-4
                        rounded-xl
                        bg-gray-100
                        border
                        border-transparent
                        focus-within:border-blue-500
                        focus-within:ring-2
                        focus-within:ring-blue-100
                        transition
                    "
                >

                    <div
                        class="
                            w-10
                            flex
                            items-center
                            justify-center
                            flex-shrink-0
                            text-black
                        "
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-6 h-6"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.75 20.105a8.25 8.25 0 0116.5 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5a18.683 18.683 0 01-7.813-1.7.75.75 0 01-.437-.695z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>


                    <div class="flex-1 ml-3">

                        <label
                            for="name"
                            class="
                                block
                                text-[10px]
                                text-gray-500
                                mb-0.5
                            "
                        >
                            Nama
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Nama lengkap"
                            required
                            autofocus
                            autocomplete="name"
                            class="
                                block
                                w-full
                                p-0
                                border-0
                                bg-transparent
                                text-sm
                                text-gray-900
                                placeholder:text-gray-500
                                focus:ring-0
                                focus:outline-none
                            "
                        >

                    </div>

                </div>


                @error('name')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- EMAIL --}}
            <div>

                <div
                    class="
                        relative
                        flex
                        items-center
                        min-h-[54px]
                        px-4
                        rounded-xl
                        bg-gray-100
                        border
                        border-transparent
                        focus-within:border-blue-500
                        focus-within:ring-2
                        focus-within:ring-blue-100
                        transition
                    "
                >

                    <div
                        class="
                            w-10
                            flex
                            items-center
                            justify-center
                            flex-shrink-0
                            text-black
                        "
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-6 h-6"
                        >
                            <path
                                d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"
                            />

                            <path
                                d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"
                            />
                        </svg>
                    </div>


                    <div class="flex-1 ml-3">

                        <label
                            for="email"
                            class="
                                block
                                text-[10px]
                                text-gray-500
                                mb-0.5
                            "
                        >
                            Email
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="example@gmail.com"
                            required
                            autocomplete="username"
                            class="
                                block
                                w-full
                                p-0
                                border-0
                                bg-transparent
                                text-sm
                                text-gray-900
                                placeholder:text-gray-500
                                focus:ring-0
                                focus:outline-none
                            "
                        >

                    </div>

                </div>


                @error('email')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- PASSWORD --}}
            <div>

                <div
                    class="
                        relative
                        flex
                        items-center
                        min-h-[54px]
                        px-4
                        rounded-xl
                        bg-gray-100
                        border
                        border-transparent
                        focus-within:border-blue-500
                        focus-within:ring-2
                        focus-within:ring-blue-100
                        transition
                    "
                >

                    <div
                        class="
                            w-10
                            flex
                            items-center
                            justify-center
                            flex-shrink-0
                            text-black
                        "
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-6 h-6"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M15.75 1.5a6.75 6.75 0 00-6.593 8.2L1.72 17.137a.75.75 0 00-.22.53v3.583c0 .414.336.75.75.75h3.583a.75.75 0 00.53-.22l.97-.97h1.917a.75.75 0 00.75-.75v-1.917h1.917a.75.75 0 00.53-.22l1.903-1.903A6.75 6.75 0 1015.75 1.5z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>


                    <div class="flex-1 ml-3">

                        <label
                            for="password"
                            class="
                                block
                                text-[10px]
                                text-gray-500
                                mb-0.5
                            "
                        >
                            Password
                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="••••••••••••"
                            required
                            autocomplete="new-password"
                            class="
                                block
                                w-full
                                p-0
                                pr-10
                                border-0
                                bg-transparent
                                text-sm
                                text-gray-900
                                focus:ring-0
                                focus:outline-none
                            "
                        >

                    </div>


                    <button
                        type="button"
                        onclick="togglePassword('password')"
                        class="
                            absolute
                            right-4
                            text-gray-600
                            hover:text-blue-600
                        "
                        aria-label="Tampilkan password"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-5 h-5"
                        >
                            <path
                                d="M12 4.5c-4.8 0-8.7 3-10.5 7.5C3.3 16.5 7.2 19.5 12 19.5s8.7-3 10.5-7.5C20.7 7.5 16.8 4.5 12 4.5zm0 12a4.5 4.5 0 110-9 4.5 4.5 0 010 9z"
                            />
                        </svg>
                    </button>

                </div>


                @error('password')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- PASSWORD CONFIRMATION --}}
            <div>

                <div
                    class="
                        relative
                        flex
                        items-center
                        min-h-[54px]
                        px-4
                        rounded-xl
                        bg-gray-100
                        border
                        border-transparent
                        focus-within:border-blue-500
                        focus-within:ring-2
                        focus-within:ring-blue-100
                        transition
                    "
                >

                    <div
                        class="
                            w-10
                            flex
                            items-center
                            justify-center
                            flex-shrink-0
                            text-black
                        "
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-6 h-6"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M12.516 2.17a.75.75 0 00-1.032 0 11.2 11.2 0 01-7.877 3.08.75.75 0 00-.722.515A12.74 12.74 0 002.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.75.75 0 00.374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 00-.722-.516 11.2 11.2 0 01-7.877-3.08z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>


                    <div class="flex-1 ml-3">

                        <label
                            for="password_confirmation"
                            class="
                                block
                                text-[10px]
                                text-gray-500
                                mb-0.5
                            "
                        >
                            Konfirmasi Password
                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="••••••••••••"
                            required
                            autocomplete="new-password"
                            class="
                                block
                                w-full
                                p-0
                                pr-10
                                border-0
                                bg-transparent
                                text-sm
                                text-gray-900
                                focus:ring-0
                                focus:outline-none
                            "
                        >

                    </div>


                    <button
                        type="button"
                        onclick="togglePassword('password_confirmation')"
                        class="
                            absolute
                            right-4
                            text-gray-600
                            hover:text-blue-600
                        "
                        aria-label="Tampilkan konfirmasi password"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="w-5 h-5"
                        >
                            <path
                                d="M12 4.5c-4.8 0-8.7 3-10.5 7.5C3.3 16.5 7.2 19.5 12 19.5s8.7-3 10.5-7.5C20.7 7.5 16.8 4.5 12 4.5zm0 12a4.5 4.5 0 110-9 4.5 4.5 0 010 9z"
                            />
                        </svg>
                    </button>

                </div>

            </div>


            {{-- REGISTER BUTTON --}}
            <button
                type="submit"
                class="
                    w-full
                    h-[54px]
                    mt-4
                    rounded-xl
                    bg-blue-600
                    text-white
                    text-sm
                    font-semibold
                    hover:bg-blue-700
                    focus:ring-4
                    focus:ring-blue-200
                    transition
                "
            >
                Daftar
            </button>


            {{-- LOGIN LINK --}}
            <p class="text-center text-sm text-gray-800 pt-1">

                Sudah memiliki akun?

                <a
                    href="{{ route('login') }}"
                    class="
                        text-blue-600
                        hover:underline
                    "
                >
                    Masuk
                </a>

            </p>


            {{-- GOOGLE --}}
            @if (Route::has('google.redirect'))

                <a
                    href="{{ route('google.redirect') }}"
                    class="
                        flex
                        items-center
                        justify-center
                        gap-4
                        w-full
                        h-[54px]
                        mt-8
                        rounded-xl
                        bg-white
                        border
                        border-gray-100
                        shadow-md
                        text-sm
                        text-gray-700
                        hover:bg-gray-50
                        hover:shadow-lg
                        transition
                    "
                >

                    <svg
                        class="w-6 h-6"
                        viewBox="0 0 48 48"
                    >
                        <path
                            fill="#FFC107"
                            d="M43.6 20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 3l5.7-5.7C34 6 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20c11.6 0 19.3-8.2 19.3-19.7 0-1.3-.1-2.4-.3-3.3z"
                        />

                        <path
                            fill="#FF3D00"
                            d="M6.3 14.7l6.6 4.8C14.7 15.1 18.9 12 24 12c3 0 5.7 1.1 7.8 3l5.7-5.7C34 6 29.3 4 24 4c-7.7 0-14.4 4.4-17.7 10.7z"
                        />

                        <path
                            fill="#4CAF50"
                            d="M24 44c5.1 0 9.8-1.9 13.3-5.1l-6.1-5.2C29.2 35.2 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.5 5C9.5 39.5 16.2 44 24 44z"
                        />

                        <path
                            fill="#1976D2"
                            d="M43.6 20H24v8h11.3c-.8 2.3-2.3 4.3-4.2 5.7l6.1 5.2c-.4.4 6.8-5 6.8-14.9 0-1.3-.1-2.7-.4-4z"
                        />
                    </svg>

                    <span>
                        Login dengan Google
                    </span>

                </a>

            @endif

        </form>

    </div>


    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);

            if (!input) {
                return;
            }

            input.type =
                input.type === 'password'
                    ? 'text'
                    : 'password';
        }
    </script>

</x-guest-layout>