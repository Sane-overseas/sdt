<style type="text/css">
    .save-btn {
        border-radius: 5px;
        padding: 1px 17px;
        color: #fff;
        background-color: #000000;
        border: 2px solid #000000 !important;
    }

     @media only screen and (max-width: 600px) {
       .auth-btn {
            font-size: 14px;
            border-radius: 5px;
            padding: 0px 15px;
        }
        .dropdown {
            position: absolute !important;
        }
        .d-image {
            position: relative !important;
        }
        img {
            width: 40% !important;
        }
    } 
</style>
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')
         <div class="form-outline form-white mb-4">
        <label class="form-label" for="typeEmailX">Current Password</label>
        <input type="password" id="current_password" name="current_password" class="form-control form-control login-input" />
        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
      </div>
      <div class="form-outline form-white mb-4">
         <label class="form-label" for="typePasswordX">New Password</label>
        <input type="password" id="password" name="password" class="form-control form-control login-input" />
         <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>
       <div class="form-outline form-white mb-4">
         <label class="form-label" for="typePasswordX">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control login-input" />
         <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
      </div>
       <div class="flex items-center gap-4">
        <button class="save-btn" type="submit">SAVE</button>
          @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
