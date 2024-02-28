
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="icon" href="images/favicon.ico" />
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
            integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
        />
        <script src="//unpkg.com/alpinejs" defer></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            laravel: "green",
                        },
                    },
                },
            };
        </script>
        <title>Strathmore University | SGS</title>
    </head>
    <body class="mb-48">
        <nav class="flex justify-between items-center mb-4">
            <a href="/"
                ><img class="w-24" src="{{asset('images/savify.jpg') }}" alt="" class="logo"
            /></a>
            <ul class="flex space-x-6 mr-6 text-lg">
                @auth
                <li>
                   <span class="font-bold"> Hey {{auth()->user()->name}}</span>
                </li>
                @if(auth()->user()->role == "merchant" | auth()->user()->role == "admin" )
                <li>
                    <a href="/listings/manage " class="hover:text-laravel"
                        ><i class="fa-solid fa-gear"></i>
                        Manage Listings</a>
                </li>
                @endif
                @if(auth()->user()->role == "admin" )
                <li>
                    <a href="/admin/list " class="hover:text-laravel"
                        ><i class="fa-solid fa-person"></i>
                        Manage Users</a>
                </li>
                @endif
                @if(auth()->user()->role == "user" )
                <li>
                    <a href="/wallet " class="hover:text-laravel">
                        <i class="fa-solid fa-wallet"></i>
                        Your Wallet</a>
                </li>
                @endif
               
                <li>
                    <form class="inline" method="POST" action="/logout">
                      @csrf
                      <button type="submit">
                        <i class="fa-solid fa-door-closed"></i> Logout
                      </button>
                    </form>
                </li>
                @else
                <li>
                    <a href="/register" class="hover:text-laravel"
                        ><i class="fa-solid fa-user-plus"></i> Register</a
                    >
                </li>
                <li>
                    <a href="/login " class="hover:text-laravel"
                        ><i class="fa-solid fa-arrow-right-to-bracket"></i>
                        Login</a
                    >
                </li>
                @endauth
            </ul>
        </nav>
        <main>
        {{$slot}}
        </main>
        <footer
            class="fixed bottom-0 left-0 w-full flex items-center justify-start font-bold bg-laravel text-white h-24 mt-24 opacity-90 md:justify-center">
            <p class="ml-2">Copyright &copy; 2023, All Rights reserved</p>
        </footer>
        <x-flash-message></x-flash-message>
    </body>
</html>