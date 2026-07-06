<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label>Name: </label>
            <span>{{ Auth::user()->instructor_name }}  </span>
        </div>
        <div>
            <label>Email: </label>
            <span>{{ Auth::user()->email }}  </span>
        </div>
        <div>
            <label>Phone Number: </label>
            <span>{{ Auth::user()->instructor_number }}  </span>
        </div>
        <div>
            <label>Trainer Code: </label>
            <span>{{ Auth::user()->instructor_code }}  </span>
        </div>

      <!--   <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div> -->
    </form>
</section>
