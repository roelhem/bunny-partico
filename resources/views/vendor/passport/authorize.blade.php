<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <!-- Header -->
        <div class="mb-4 text-lg">
            <h1>Authorization Request</h1>
        </div>

        <div class="mb-4 text-sm text-gray-600">
            <strong>{{ $client->name }}</strong> {{ __('is requesting permission to access your account.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        @if (count($scopes) > 0)
            <div class="mb-4 font-medium text-sm text-gray-600">
                <p>This application will be able to:</p>

                <ul class="list-inside list-disc">
                    @foreach ($scopes as $scope)
                        <li>{{ $scope->description }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Authorize Button -->
        <div class="flex items-center justify-end mt-4">
            <form method="post" action="{{ route('passport.authorizations.approve') }}">
                @csrf

                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <x-jet-button class="ml-4">Authorize</x-jet-button>
            </form>

            <form method="post" action="{{ route('passport.authorizations.deny') }}">
                @csrf
                @method('DELETE')

                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <x-jet-danger-button class="ml-4">Cancel</x-jet-danger-button>
            </form>
        </div>
    </x-jet-authentication-card>
</x-guest-layout>


{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="utf-8">--}}
{{--    <meta http-equiv="X-UA-Compatible" content="IE=edge">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1">--}}

{{--    <title>{{ config('app.name') }} - Authorization</title>--}}

{{--    <!-- Styles -->--}}
{{--    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">--}}

{{--    <style>--}}
{{--        .passport-authorize .container {--}}
{{--            margin-top: 30px;--}}
{{--        }--}}

{{--        .passport-authorize .scopes {--}}
{{--            margin-top: 20px;--}}
{{--        }--}}

{{--        .passport-authorize .buttons {--}}
{{--            margin-top: 25px;--}}
{{--            text-align: center;--}}
{{--        }--}}

{{--        .passport-authorize .btn {--}}
{{--            width: 125px;--}}
{{--        }--}}

{{--        .passport-authorize .btn-approve {--}}
{{--            margin-right: 15px;--}}
{{--        }--}}

{{--        .passport-authorize form {--}}
{{--            display: inline;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body class="passport-authorize">--}}
{{--    <div class="container">--}}
{{--        <div class="row justify-content-center">--}}
{{--            <div class="col-md-6">--}}
{{--                <div class="card card-default">--}}
{{--                    <div class="card-header">--}}
{{--                        Authorization Request--}}
{{--                    </div>--}}
{{--                    <div class="card-body">--}}
{{--                        <!-- Introduction -->--}}
{{--                        <p><strong>{{ $client->name }}</strong> is requesting permission to access your account.</p>--}}

{{--                        <!-- Scope List -->--}}
{{--                        @if (count($scopes) > 0)--}}
{{--                            <div class="scopes">--}}
{{--                                    <p><strong>This application will be able to:</strong></p>--}}

{{--                                    <ul>--}}
{{--                                        @foreach ($scopes as $scope)--}}
{{--                                            <li>{{ $scope->description }}</li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        <div class="buttons">--}}
{{--                            <!-- Authorize Button -->--}}
{{--                            <form method="post" action="{{ route('passport.authorizations.approve') }}">--}}
{{--                                @csrf--}}

{{--                                <input type="hidden" name="state" value="{{ $request->state }}">--}}
{{--                                <input type="hidden" name="client_id" value="{{ $client->id }}">--}}
{{--                                <input type="hidden" name="auth_token" value="{{ $authToken }}">--}}
{{--                                <button type="submit" class="btn btn-success btn-approve">Authorize</button>--}}
{{--                            </form>--}}

{{--                            <!-- Cancel Button -->--}}
{{--                            <form method="post" action="{{ route('passport.authorizations.deny') }}">--}}
{{--                                @csrf--}}
{{--                                @method('DELETE')--}}

{{--                                <input type="hidden" name="state" value="{{ $request->state }}">--}}
{{--                                <input type="hidden" name="client_id" value="{{ $client->id }}">--}}
{{--                                <input type="hidden" name="auth_token" value="{{ $authToken }}">--}}
{{--                                <button class="btn btn-danger">Cancel</button>--}}
{{--                            </form>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</body>--}}
{{--</html>--}}
