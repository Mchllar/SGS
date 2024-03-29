
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
                            laravel: "blue",
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
                ><img class="w-38" src="{{asset('images/School-of-Graduate-Studies-logo.png') }}" alt="" class="logo"
            /></a>
            <ul class="flex space-x-6 mr-6 text-lg">
                @auth
                <li>
                   <span class="font-bold"> Welcome, {{auth()->user()->name}}</span>
                </li>               
                <li>
                    <form class="inline" method="POST" action="/logout">
                      @csrf
                      <button type="logout">
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
        <footer id="footer" class="fixed bottom-0 left-0 w-full flex items-center justify-start font-bold bg-laravel text-white h-16 p-4 md:justify-center transition-opacity duration-500 opacity-100">
            <p class="ml-2">&copy; 2023, All Rights Reserved</p>
        </footer>

        <script>
            let lastScrollTop = 0;
            const footer = document.getElementById('footer');

            window.addEventListener('scroll', function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                if (scrollTop > lastScrollTop) {
                    // Scrolling down, hide the footer
                    footer.classList.remove('opacity-100');
                    footer.classList.add('opacity-0');
                } else {
                    // Scrolling up, show the footer
                    footer.classList.remove('opacity-0');
                    footer.classList.add('opacity-100');
                }

                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
            });
        </script>


        <x-flash-message></x-flash-message>
    </body>
</html>