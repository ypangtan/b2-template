                        <div class="text-center mt-4">
                            <img src="{{ asset( 'admin/img/icons/128px.png' ) }}" />
                            <br>
                            <br>
                            <h1 class="h2">Welcome back</h1>
                            <p class="lead">
                                Sign in to your account to continue
                            </p>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-4">
                                    <form method="POST" action="{{ route('admin.login') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Email') }}</label>
                                            <input class="form-control form-control-lg @error('email') is-invalid @enderror" type="text" name="email" placeholder="Enter your email" />
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('Password') }}</label>
                                            <input class="form-control form-control-lg @error('password') is-invalid @enderror" type="password" name="password" placeholder="Enter your password" />
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                            <!-- <small><a href="pages-reset-password.html">{{ __('Forgot Password') }}?</a></small> -->
                                        </div>
                                        <div>
                                            <label class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <span class="form-check-label">{{ __('Remember me next time') }}</span>
                                            </label>
                                        </div>
                                        <div class="text-center mt-3">
                                            <button type="submit" class="btn btn-lg btn-primary">{{ __('Sign in') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>