<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>API Documentation</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            form input, form textarea {
                width: 100%;
            }

            form p {
                color: red;
            }
        </style>
    </head>
    <body>
	
	
	
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md">
                    Send Mail
                </div>
                <h4 style="color: green">
                    @if(session('success'))
                        <b>{{ session('success') }}</b>/<b>15</b>
                    @endif
                </h4>
				
                <h4 style="color: red">{{ $errors->first('exception') }}</h4>
                <form action="{{ route('apimail') }}" method="post">
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                     <div>
                        <label for="name">name:</label>
                        <input type="name" id="name" name="name" value="{{ old('name') }}">
                        <p>{{ $errors->first('name') }}</p>
                    </div>

                    <div>
                        <label for="email">Email address:</label>
                        <input type="test" id="email" name="email" value="{{ old('email') }}">
                        <p>{{ $errors->first('email') }}</p>
                    </div>
                    <div>
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}">
                        <p>{{ $errors->first('subject') }}</p>
                    </div>
					<div>
                        <label for="subject">IP Blacklist Check Script:</label>
                        <input type="text" id="ip" name="ip" value="{{ old('ip') }}">
                        <p>{{ $errors->first('subject') }}</p>
                    </div>
                    <div>
                        <label for="body">body:</label>
                        <textarea name="body" id="body" cols="30" rows="10">{{ old('body') }}</textarea>
                        <p>{{ $errors->first('body') }}</p>
                    </div>
                    <div>
                        <input type="submit">
                    </div>
                </form>

                <div class="links">
                    <a href="{{ url('/') }}">API Documentation</a>
                </div>
            </div>
        </div>
    </body>
</html>
