                        <style>
                            /* body {
                                background: url( '{{ asset( 'admin/images/login_bg.jpg' ) }}' );
                                background-position: center;
                            } */
                            .login .title {
                                color: #fff;
                            }
                            .login .lead {
                                color: #fff;
                            }
                            /* .login-language-switcher > label {
                                color: #fff;
                            } */
                        </style>
                        
                        <div class="text-center mt-4">
                            <!-- admin/img/icons/default.png -->
                            <img style="width: 50%;"src="{{ asset( 'admin/img/placeholder/fff.jpg' ) }}" />
                            <br>
                            <br>
                            <h1 class="h2">{{ __( 'auth.welcome' ) }}</h1>
                            <p class="lead">
                                {!! __( 'auth.sign_in_continue', [ 'type' => __( 'auth.admin' ) ] ) !!}
                            </p>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-4">
                                    <form method="POST" action="{{ route('admin.login') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('auth.email') }}</label>
                                            <input class="form-control form-control-sm @error('username') is-invalid @enderror" type="text" name="username" placeholder="{{ __( 'auth.enter_your_x', [ 'type' => strtolower( __( 'auth.email' ) ) ] ) }}" value="{{ old( 'username' ) ? old( 'username' ) : '' }}" />
                                            @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('auth.password_') }}</label>
                                            <div class="password-wrapper">
                                                <input id="password" class="form-control form-control-sm @error('password') is-invalid @enderror" type="password" name="password" placeholder="{{ __( 'auth.enter_your_x', [ 'type' => strtolower( __( 'auth.password_' ) ) ] ) }}" />
                                                <i id="showPassword" onclick="showPassword(true)" icon-name="eye" stroke-width="1.5"></i>
                                                <i id="hidePassword" onclick="showPassword(false)" icon-name="eye-off" stroke-width="1.5" class="hidden"></i>
                                            </div>
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                            <!-- <small><a href="pages-reset-password.html">{{ __('Forgot Password') }}?</a></small> -->
                                        </div>
                                        @if ( 1 == 2 )
                                        <div>
                                            <label class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <span class="form-check-label">{{ __( 'auth.remember_me' ) }}</span>
                                            </label>
                                        </div>
                                        @endif
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-sm btn-primary w-100">{{ __( 'auth.sign_in' ) }}</button>
                                        </div>
                                        @if( 1 == 2 )
                                        <div class="mt-5"><small>{{ __( 'auth.not_type', [ 'website' => Helper::websiteName(), 'type' => ucfirst( __( 'auth.admin' ) ) ] ) }} <a href="{{ Helper::baseBranchUrl() }}/login">{{ __( 'auth.go_to_dashboard', [ 'type' => ucfirst( __( 'auth.branch' ) ) ] ) }}</a></small></div>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 login-language-switcher d-flex align-items-center">
                            <label for="select_language" class="">{{ __( 'auth.choose_language' ) }}</label>
                            <div>
                                <select class="form-select form-select-sm" id="select_language" onchange="switchLanguage()">
                                    @foreach( Config::get( 'languages' ) as $lang => $language )
                                    @if( $lang != App::getLocale() )
                                    <option value="{{ $lang }}">{{ $language }}</option>
                                    @else
                                    <option selected value="{{ $lang }}">{{ $language }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <script src="{{ asset( 'admin/js/lucide.min.js' ) . Helper::assetVersion() }}"></script>

                        <script>
                            lucide.createIcons();
                            
                            function showPassword( state ) {
                                if( state ) {
                                    document.getElementById( 'showPassword' ).classList.add( 'hidden' );
                                    document.getElementById( 'hidePassword' ).classList.remove( 'hidden' );
                                    document.getElementById( 'password' ).type = 'text';
                                } else {
                                    document.getElementById( 'showPassword' ).classList.remove( 'hidden' );
                                    document.getElementById( 'hidePassword' ).classList.add( 'hidden' );
                                    document.getElementById( 'password' ).type = 'password';
                                }
                            }

                            function switchLanguage() {
                                window.location.href = '{{ route( 'admin.lang' ) }}/' + document.getElementById( 'select_language' ).value
                            }
                        </script>