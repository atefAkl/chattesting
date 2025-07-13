<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    </head>

    <body>
        <nav class="navbar">
            <a href="{{ route('home') }}" class="navbar-brand">{{ config('app.name', 'Laravel') }}</a>
            <div class="navbar-nav">
                @guest
                <a href="{{ route('login') }}" class="nav-link">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="nav-link">التسجيل</a>
                @else
                <a href="{{ route('profile') }}" class="nav-link">
                    <img src="https://via.placeholder.com/36" alt="{{ Auth::user()->name }}" class="user-avatar" style="width: 36px; height: 36px;">
                </a>
                <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
                @endguest
            </div>
        </nav>

        <div class="main-container">
            <div class="sidebar sidebar-left">
                @yield('sidebar-left')
            </div>

            <div class="content">
                @yield('content')
            </div>

            <div class="sidebar sidebar-right">
                @yield('sidebar-right')
            </div>
        </div>


        <script>
            function chatWidget(options) {
                return `
                    <div id="chatWidget_${options.user_id}" class="chat-widget fixed bottom-4 right-4 w-full max-w-sm h-16 bg-white rounded-lg shadow-xl flex flex-col border border-gray-200 sm:w-80"
                        style="z-index: 1000;">
                        <div class="flex items-center justify-between p-4 bg-blue-600 text-white rounded-t-lg cursor-pointer" id="chatHeader">
                            <h2 class="text-lg font-semibold">الرسائل</h2>
                            <button class="text-white hover:text-gray-200 focus:outline-none" aria-label="Toggle chat">
                                <i class="fas fa-comment-dots" id="chatHeaderIcon"></i> {{-- Font Awesome icon --}}
                            </button>
                        </div>

                        <div id="chatMessagesArea" class="flex-1 p-4 overflow-y-auto chat-messages hidden" style="direction: rtl;">
                            <div class="message-bubble system">مرحباً بك في خدمة الرسائل!</div>
                            <div class="message-bubble system">كيف يمكنني مساعدتك اليوم؟</div>
                            <div id="messagesEndRef"></div>
                        </div>

                        <form id="chatInputForm" class="p-4 border-t border-gray-200 hidden items-center gap-2" style="direction: rtl;">
                            <input type="text" id="newMessageInput" placeholder="اكتب رسالتك..."
                                class="flex-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-right" style="direction: rtl;" />
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-200 ease-in-out">
                                إرسال
                            </button>
                        </form>
                    </div>`
                }
                document.addEventListener('DOMContentLoaded', function() {
                    
                const chatToggleBtns = document.querySelectorAll('.toggle-chat');
                const chatHeader = document.getElementById('chatHeader');
                const chatHeaderIcon = document.getElementById('chatHeaderIcon');
                const chatMessagesArea = document.getElementById('chatMessagesArea');
                const chatInputForm = document.getElementById('chatInputForm');
                const newMessageInput = document.getElementById('newMessageInput');
                const messagesEndRef = document.getElementById('messagesEndRef');
                
                
                
                // Function to toggle chat widget visibility
                function openChat() {
                    
                    
                }
        
                // Function to scroll to the bottom of messages
                function scrollToBottom() {
                    messagesEndRef.scrollIntoView({ behavior: 'smooth' });
                }
                chatToggleBtns.forEach(function (openChatBtn) {
                    openChatBtn.addEventListener('click', function() {
                        getConversation(openChatBtn.dataset.userId);
                        chatWidget({
                            user_id: openChatBtn.dataset.userId,
                        });
                    });
                });
        
                // get conversation between two users
                function getConversation(user_id) {
                    fetch('/users/conversations/'+user_id)
                    .then(function (response) {
                        console.log(response.data);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
                }
            });
        </script>
    </body>

</html>