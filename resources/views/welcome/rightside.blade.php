

<div class="w-full max-w-[400px]">
    <div class="bg-white dark:bg-slate-800 shadow-xl rounded-xl p-4 lg:p-6 flex flex-col gap-4">
        <!-- Login Form -->
        <div class="flex flex-col gap-4">
            <h1 class="text-2xl text-center">
                {{ __('welcome.Log_account') }}
            </h1>
            <a class="w-full h-12 bg-primary hover:bg-primary/90 text-white font-bold text-xl rounded-lg transition-colors inline-flex items-center justify-center" href="{{ route('login') }}">
                <span class="material-icons px-2">login</span>
                {{ __('welcome.Log_In') }}
            </a>
        </div>

        <div class="text-center">
            <a class="text-primary hover:underline text-sm font-medium" href="{{ route('password.request') }}">
                {{ __('welcome.Forgotten_password?') }}
            </a>
        </div>

        <hr class="border-slate-200 dark:border-slate-700 my-2"/>

        <div class="flex justify-center py-2 flex-col gap-4">
            <h1 class="text-xl text-center">{{ __('welcome.No_account?') }}</h1>
            <a class="px-6 h-12 bg-[#42b72a] hover:bg-[#36a420] text-white font-bold text-base rounded-lg transition-colors inline-flex items-center justify-center" href="{{ route('register') }}">
                <span class="material-icons px-2">person_add</span>
                {{ __('welcome.New_Account') }}
            </a>
        </div>
    </div>

    <div class="text-center lg:text-left mx-10 my-4">
        <p class="text-sm text-slate-600 dark:text-slate-400">
            {{ __('welcome.Note') }}
        </p>
    </div>
</div>