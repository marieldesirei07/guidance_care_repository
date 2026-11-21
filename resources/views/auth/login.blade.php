<x-guest-layout>
    <h2>Log in</h2>
    <p class="gc-subtitle">Use your school email and password.</p>

    {{-- Session status (e.g. after a password reset) --}}
    @if (session('status'))
        <div class="gc-flash gc-flash-success" style="margin-bottom: 16px;">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate
          onsubmit="var b=this.querySelector('button[type=submit]');b.disabled=true;b.textContent='Logging in…';">
        @csrf

        <div class="gc-field">
            <label for="email">Email</label>
            <input id="email" class="gc-input @error('email') is-invalid @enderror" type="email" name="email"
                   value="{{ old('email') }}" placeholder="name@globalcity.sti.edu.ph"
                   required autofocus autocomplete="username"
                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email')
                <p class="gc-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="gc-field">
            <label for="password">Password</label>
            <input id="password" class="gc-input @error('password') is-invalid @enderror" type="password" name="password"
                   required autocomplete="current-password"
                   @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            @error('password')
                <p class="gc-error" id="password-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="gc-auth-row">
            <label for="remember_me" class="gc-check">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="gc-btn gc-btn-primary gc-btn-block">
            Log in
        </button>
    </form>
</x-guest-layout>
