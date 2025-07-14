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
            <div class="nav-item notifications-dropdown">
                <a href="#" class="nav-link" id="notifications-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span id="notification-badge" class="notification-badge" style="display: none;">0</span>
                </a>
                <div class="dropdown-menu notifications-menu" id="notifications-dropdown">
                    <div class="dropdown-menu-header">
                        <span>الإشعارات</span>
                        <a href="#" class="mark-all-read">تحديد الكل كمقروء</a>
                    </div>
                    <div class="notifications-list" id="notifications-list">
                        <div class="no-notifications">ليس لديك إشعارات جديدة</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('profile') }}" class="nav-link">
                <img src="{{ Auth::user()->profile->avatar ?? asset('assets/images/defaultAvatar.jpeg') }}" alt="{{ Auth::user()->name }}" class="user-avatar" style="width: 36px; height: 36px;">
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


    <div id="chatWidgetsContainer" class="fixed bottom-0 right-0 flex flex-row-reverse gap-4 p-4 z-50"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // الإعدادات الأساسية
            const MAX_WIDGETS = 3; // الحد الأقصى لعدد ويدجات الشات المفتوحة
            const MESSAGES_PER_LOAD = 20; // عدد الرسائل التي يتم تحميلها في كل مرة
            const TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // متغيرات عامة
            const chatWidgetsContainer = document.getElementById('chatWidgetsContainer');
            const chatToggleBtns = document.querySelectorAll('.toggle-chat');
            const openedChats = []; // مصفوفة تخزن معرفات المستخدمين للشاتات المفتوحة

            // تهيئة أزرار فتح الشات
            chatToggleBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const userName = this.dataset.userName || 'مستخدم';
                    const userAvatar = this.dataset.userAvatar || 'https://via.placeholder.com/36';

                    // التحقق ما إذا كان الشات مفتوح بالفعل
                    const existingChatIndex = openedChats.findIndex(chat => chat.userId === userId);

                    if (existingChatIndex !== -1) {
                        // إذا كان مفتوح بالفعل، حركه للمقدمة
                        const chat = openedChats.splice(existingChatIndex, 1)[0];
                        openedChats.push(chat);
                        updateChatWidgetsPositions();
                        document.getElementById(`chatWidget_${userId}`).classList.add('chat-active');
                        return;
                    }

                    // التحقق من عدد الشاتات المفتوحة
                    if (openedChats.length >= MAX_WIDGETS) {
                        // إغلاق أقدم شات إذا وصلنا للحد الأقصى
                        const oldestChat = openedChats.shift();
                        removeWidget(oldestChat.userId);
                    }

                    // فتح الشات الجديد
                    startNewChat(userId, userName, userAvatar);
                });
            });

            // دالة لإنشاء شات جديد
            function startNewChat(userId, userName, userAvatar) {
                // إضافة معلومات الشات إلى المصفوفة
                const chatInfo = {
                    userId,
                    userName,
                    userAvatar,
                    conversationId: null,
                    lastMessageId: 0,
                    isLoading: false,
                    hasMoreMessages: true
                };

                openedChats.push(chatInfo);

                // إضافة ويدجت الشات للواجهة
                const widgetHtml = createChatWidgetHtml(userId, userName, userAvatar);
                chatWidgetsContainer.insertAdjacentHTML('beforeend', widgetHtml);

                // تحديث مواقع جميع الشاتات
                updateChatWidgetsPositions();

                const widget = document.getElementById(`chatWidget_${userId}`);
                const messagesArea = widget.querySelector('.chat-messages');
                const chatHeader = widget.querySelector('.chat-header');
                const chatForm = widget.querySelector('.chat-form');
                const messagesEndRef = widget.querySelector('.messages-end-ref');

                // إضافة مستمعات الأحداث للويدجت
                chatHeader.addEventListener('click', function() {
                    widget.classList.toggle('chat-expanded');
                    if (widget.classList.contains('chat-expanded')) {
                        messagesArea.scrollTop = messagesArea.scrollHeight;
                    }
                });

                widget.querySelector('.close-chat').addEventListener('click', function(e) {
                    e.stopPropagation();
                    removeWidget(userId);
                });

                chatForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const input = this.querySelector('input');
                    const message = input.value.trim();

                    if (message && chatInfo.conversationId) {
                        sendMessage(chatInfo.conversationId, message, messagesArea, input);
                    }
                });

                // تنفيذ التحميل أثناء التمرير
                messagesArea.addEventListener('scroll', function() {
                    // إذا وصلنا للأعلى وهناك رسائل أكثر للتحميل
                    if (messagesArea.scrollTop === 0 && chatInfo.hasMoreMessages && !chatInfo.isLoading) {
                        loadMoreMessages(chatInfo, messagesArea);
                    }
                });

                // جلب الرسائل من الخادم
                fetch(`/conversations/${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // تحديث معرف المحادثة
                            chatInfo.conversationId = data.conversation.id;

                            // عرض الرسائل
                            const messages = data.messages;
                            renderMessages(messagesArea, messages, userId);

                            // تحديث آخر معرف للرسائل للتحميل المتدرج
                            if (messages.length > 0) {
                                chatInfo.lastMessageId = messages[0].id;
                                chatInfo.hasMoreMessages = messages.length >= MESSAGES_PER_LOAD;
                            }

                            // تمرير للأسفل
                            messagesArea.scrollTop = messagesArea.scrollHeight;

                            // توسيع الشات بعد التحميل
                            widget.classList.add('chat-expanded');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching conversation:', error);
                        messagesArea.innerHTML += `<div class="message-error">حدث خطأ أثناء تحميل المحادثة</div>`;
                    });
            }

            // دالة لإزالة ويدجت
            function removeWidget(userId) {
                // إزالة من المصفوفة
                const chatIndex = openedChats.findIndex(chat => chat.userId === userId);
                if (chatIndex !== -1) {
                    openedChats.splice(chatIndex, 1);
                }

                // إزالة من واجهة المستخدم
                const widget = document.getElementById(`chatWidget_${userId}`);
                if (widget) {
                    widget.remove();
                }

                // تحديث المواقع
                updateChatWidgetsPositions();

                // إرسال طلب لإغلاق المحادثة في الخادم
                const chat = openedChats.find(chat => chat.userId === userId);
                if (chat && chat.conversationId) {
                    fetch(`/conversations/${chat.conversationId}/close`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': TOKEN
                        }
                    }).catch(error => console.error('Error closing chat:', error));
                }
            }

            // دالة لتحديث مواقع الويدجات
            function updateChatWidgetsPositions() {
                openedChats.forEach((chat, index) => {
                    const widget = document.getElementById(`chatWidget_${chat.userId}`);
                    if (widget) {
                        // حساب المسافة من اليمين (لتجنب التداخل)
                        const rightPosition = index * 320; // عرض الويدجت + المسافة بينهما
                        widget.style.right = `${rightPosition}px`;
                    }
                });
            }

            // دالة لإرسال رسالة جديدة
            function sendMessage(conversationId, message, messagesArea, inputElement) {
                // إضافة الرسالة محلياً (قبل الإرسال للخادم)
                const currentUserId = '{{ Auth::id() }}'; // افتراضياً، الرسالة من المستخدم الحالي
                const tempMessageId = `temp_${Date.now()}`;
                const tempMessageHtml = `
                    <div id="${tempMessageId}" class="message-bubble outgoing">
                        <div class="message-content">${escapeHtml(message)}</div>
                        <div class="message-time">الآن <i class="fas fa-clock text-xs ml-1"></i></div>
                    </div>
                `;

                messagesArea.insertAdjacentHTML('beforeend', tempMessageHtml);
                messagesArea.scrollTop = messagesArea.scrollHeight;

                // مسح محتوى الإدخال
                inputElement.value = '';

                // إرسال الرسالة للخادم
                fetch(`/conversations/${conversationId}/send`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': TOKEN
                        },
                        body: JSON.stringify({
                            message
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // تحديث الرسالة المؤقتة بالمعرف الفعلي والوقت الصحيح
                            const tempMessage = document.getElementById(tempMessageId);
                            if (tempMessage) {
                                tempMessage.id = `message_${data.message.id}`;
                                const timeElement = tempMessage.querySelector('.message-time');
                                if (timeElement) {
                                    const formattedTime = formatMessageTime(new Date(data.message.created_at));
                                    timeElement.innerHTML = `${formattedTime} <i class="fas fa-check text-xs ml-1"></i>`;
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error sending message:', error);
                        // تحديث الرسالة المؤقتة لتظهر خطأ في الإرسال
                        const tempMessage = document.getElementById(tempMessageId);
                        if (tempMessage) {
                            tempMessage.classList.add('message-error');
                            const timeElement = tempMessage.querySelector('.message-time');
                            if (timeElement) {
                                timeElement.innerHTML = `فشل الإرسال <i class="fas fa-exclamation-circle text-xs ml-1"></i>`;
                            }
                        }
                    });
            }

            // دالة لتحميل المزيد من الرسائل (التحميل المتدرج)
            function loadMoreMessages(chatInfo, messagesArea) {
                if (!chatInfo.hasMoreMessages || chatInfo.isLoading) return;

                chatInfo.isLoading = true;

                // إظهار مؤشر التحميل
                const loadingIndicator = document.createElement('div');
                loadingIndicator.className = 'loading-indicator';
                loadingIndicator.innerText = 'جاري التحميل...';
                messagesArea.prepend(loadingIndicator);

                // حفظ ارتفاع التمرير الحالي
                const scrollHeight = messagesArea.scrollHeight;

                // جلب الرسائل القديمة
                fetch(`/conversations/${chatInfo.conversationId}/load-more?last_message_id=${chatInfo.lastMessageId}`)
                    .then(response => response.json())
                    .then(data => {
                        // إزالة مؤشر التحميل
                        loadingIndicator.remove();

                        if (data.status === 'success') {
                            const messages = data.messages;

                            if (messages.length > 0) {
                                // إضافة الرسائل في بداية منطقة الرسائل
                                const tempDiv = document.createElement('div');
                                renderMessages(tempDiv, messages, chatInfo.userId);
                                messagesArea.prepend(...tempDiv.childNodes);

                                // تحديث آخر معرف رسالة تم تحميله
                                chatInfo.lastMessageId = messages[0].id;

                                // تحديث ما إذا كان هناك المزيد من الرسائل
                                chatInfo.hasMoreMessages = data.has_more;

                                // الحفاظ على موضع التمرير
                                messagesArea.scrollTop = messagesArea.scrollHeight - scrollHeight;
                            }
                        }

                        chatInfo.isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error loading more messages:', error);
                        loadingIndicator.remove();
                        chatInfo.isLoading = false;
                    });
            }

            // دالة لعرض الرسائل في منطقة الرسائل
            function renderMessages(container, messages, otherUserId) {
                const currentUserId = '{{ Auth::id() }}';
                let messagesHtml = '';

                messages.forEach(message => {
                    const isOutgoing = message.sender == currentUserId;
                    const messageClass = isOutgoing ? 'outgoing' : 'incoming';
                    const formattedTime = formatMessageTime(new Date(message.created_at));

                    messagesHtml += `
                        <div id="message_${message.id}" class="message-bubble ${messageClass}">
                            <div class="message-content">${escapeHtml(message.message)}</div>
                            <div class="message-time">
                                ${formattedTime}
                                ${isOutgoing ? (message.is_read ? '<i class="fas fa-check-double text-xs ml-1"></i>' : '<i class="fas fa-check text-xs ml-1"></i>') : ''}
                            </div>
                        </div>
                    `;
                });

                container.innerHTML += messagesHtml;
            }

            // دالة تنسيق وقت الرسالة
            function formatMessageTime(date) {
                return date.toLocaleTimeString('ar-SA', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // دالة لإنشاء HTML لويدجت الشات
            function createChatWidgetHtml(userId, userName, userAvatar) {
                return `
                    <div id="chatWidget_${userId}" class="chat-widget bg-white rounded-t-lg shadow-xl flex flex-col border border-gray-200 w-80 h-16 overflow-hidden transition-all duration-300" style="z-index: 1000;">
                        <div class="chat-header flex items-center justify-between p-4 bg-blue-600 text-white rounded-t-lg cursor-pointer">
                            <div class="flex items-center">
                                <img src="${userAvatar}" alt="${userName}" class="w-8 h-8 rounded-full mr-2">
                                <h2 class="text-lg font-semibold">${escapeHtml(userName)}</h2>
                            </div>
                            <div class="flex">
                                <button class="text-white hover:text-gray-200 focus:outline-none mx-2" aria-label="Toggle chat">
                                    <i class="fas fa-chevron-up toggle-icon"></i>
                                </button>
                                <button class="close-chat text-white hover:text-gray-200 focus:outline-none" aria-label="Close chat">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="chat-messages flex-1 p-4 overflow-y-auto hidden" style="direction: rtl; height: 250px;">
                            <div class="message-bubble system">جاري تحميل المحادثة...</div>
                            <div class="messages-end-ref"></div>
                        </div>

                        <form class="chat-form p-3 border-t border-gray-200 hidden">
                            <div class="flex items-center gap-2" style="direction: rtl;">
                                <input type="text" placeholder="اكتب رسالتك..."
                                    class="flex-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-right" style="direction: rtl;" />
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-200 ease-in-out">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                `;
            }

            // دالة لحماية النص من XSS
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
    </script>

    <style>
        /* تنسيقات CSS للشات */
        .chat-widget {
            position: fixed;
            bottom: 0;
            transition: height 0.3s ease;
        }

        .chat-widget.chat-expanded {
            height: 350px;
        }

        .chat-widget.chat-expanded .chat-messages,
        .chat-widget.chat-expanded .chat-form {
            display: block;
        }

        .chat-widget.chat-expanded .toggle-icon {
            transform: rotate(180deg);
        }

        .message-bubble {
            margin: 8px 0;
            padding: 8px 12px;
            border-radius: 12px;
            max-width: 70%;
            word-wrap: break-word;
            position: relative;
        }

        .message-bubble.outgoing {
            background-color: #e1f5fe;
            margin-left: auto;
            border-bottom-right-radius: 0;
        }

        .message-bubble.incoming {
            background-color: #f5f5f5;
            margin-right: auto;
            border-bottom-left-radius: 0;
        }

        .message-bubble.system {
            background-color: #fff3cd;
            margin: 4px auto;
            text-align: center;
            max-width: 90%;
            font-style: italic;
        }

        .message-bubble.message-error {
            background-color: #ffebee;
        }

        .message-content {
            font-size: 14px;
            line-height: 1.4;
            text-align: start;
            direction: inherit;
        }

        /* دعم RTL للرسائل بناءً على اللغة الحالية */
        html[dir="rtl"] .message-content {
            direction: rtl;
        }

        html[dir="ltr"] .message-content {
            direction: ltr;
        }

        .message-time {
            font-size: 10px;
            color: #757575;
            text-align: left;
            margin-top: 4px;
        }

        .loading-indicator {
            text-align: center;
            padding: 5px;
            color: #757575;
            font-style: italic;
        }

        /* أنماط الإشعارات */
        .notifications-dropdown {
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #f44336;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
        }

        .notifications-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 320px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            display: none;
            z-index: 1000;
            max-height: 400px;
            overflow-y: auto;
        }

        .dropdown-menu-header {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #eaeaea;
            font-weight: bold;
        }

        .mark-all-read {
            color: #1877f2;
            font-size: 0.8rem;
            cursor: pointer;
        }

        .notifications-list {
            padding: 10px 0;
        }

        .notification-item {
            padding: 10px 15px;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            align-items: flex-start;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f5f5f5;
        }

        .notification-content {
            flex: 1;
        }

        .notification-message {
            font-size: 0.9rem;
            margin-bottom: 3px;
        }

        .notification-time {
            font-size: 0.75rem;
            color: #757575;
        }

        .notification-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
            margin-right: 10px;
        }

        .no-notifications {
            padding: 20px;
            text-align: center;
            color: #757575;
            font-style: italic;
        }

        /* تحسين أنماط ويدجت الشات */
        .chat-widgets-container {
            position: fixed;
            bottom: 0;
            right: 10px;
            display: flex;
            gap: 10px;
            align-items: flex-end;
            direction: ltr;
            /* تحديد اتجاه للمنطقة بالكامل */
            z-index: 999;
        }
    </style>
    <div id="userId">{{ Auth::id() }}</div>
    <!-- منطقة الإشعارات -->
    <div id="notifications-area" class="notifications-container"></div>

    <!-- منطقة ويدجات الشات -->
    <div id="chat-widgets-container" class="chat-widgets-container"></div>

    @if(Auth::check())
    <!-- نصوص JavaScript للإشعارات والدردشة -->
    <script>
        // تعريف المتغيرات العامة
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
        // تعريف معرف المستخدم الحالي
        const currentUserId = document.getElementById('userId').textContent;

        // الحصول على الإشعارات
        function fetchNotifications() {
            fetch('{{ route("notifications.get") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications && data.notifications.length > 0) {
                        updateNotificationBadge(data.notifications.length);
                        renderNotifications(data.notifications);
                    } else {
                        document.getElementById('notifications-list').innerHTML = '<div class="no-notifications">ليس لديك إشعارات جديدة</div>';
                        updateNotificationBadge(0);
                    }
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                });
        }

        // عرض الإشعارات في القائمة
        function renderNotifications(notifications) {
            const notificationsList = document.getElementById('notifications-list');
            if (!notificationsList) return;

            if (notifications.length === 0) {
                notificationsList.innerHTML = '<div class="no-notifications">ليس لديك إشعارات جديدة</div>';
                return;
            }

            let html = '';
            notifications.forEach(notification => {
                const data = notification.data;
                html += `
                    <div class="notification-item" data-id="${notification.id}" data-conversation-id="${data.conversation_id}">
                        <img src="{{ asset('assets/images/defaultAvatar.jpeg') }}" class="notification-avatar">
                        <div class="notification-content">
                            <div class="notification-message">رسالة جديدة: ${data.message_preview}</div>
                            <div class="notification-time">${notification.created_at}</div>
                        </div>
                    </div>
                `;
            });

            notificationsList.innerHTML = html;

            // إضافة حدث النقر لفتح المحادثة
            document.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', function() {
                    const notificationId = this.dataset.id;
                    const conversationId = this.dataset.conversationId;

                    // تحديد الإشعار كمقروء
                    markNotificationAsRead(notificationId);

                    // فتح المحادثة
                    openConversation(conversationId);
                });
            });
        }

        // تحديث شارة الإشعارات
        function updateNotificationBadge(count) {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline' : 'none';
            }
        }

        // تحديد إشعار كمقروء
        function markNotificationAsRead(id) {
            fetch(`{{ url('notifications') }}/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // تحديث الإشعارات بعد تحديد إشعار كمقروء
                    fetchNotifications();
                })
                .catch(error => {
                    console.error('Error marking notification as read:', error);
                });
        }

        // تحديد جميع الإشعارات كمقروءة
        function markAllAsRead() {
            fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    fetchNotifications();
                })
                .catch(error => {
                    console.error('Error marking all notifications as read:', error);
                });
        }

        // فتح محادثة من الإشعار
        function openConversation(conversationId) {
            // التحقق من عدد المحادثات المفتوحة
            const openChats = document.querySelectorAll('.chat-widget');
            if (openChats.length >= 3) {
                // إغلاق المحادثة الأقدم
                closeOldestChat();
            }

            // التحقق مما إذا كانت المحادثة مفتوحة بالفعل
            const existingChat = document.querySelector(`.chat-widget[data-conversation-id="${conversationId}"]`);
            if (existingChat) {
                // فقط التركيز على المحادثة الموجودة
                existingChat.classList.add('chat-widget-focus');
                setTimeout(() => {
                    existingChat.classList.remove('chat-widget-focus');
                }, 1000);
                return;
            }

            // جلب بيانات المحادثة وفتحها
            fetch(`{{ url('conversations') }}/${conversationId}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        createChatWidget(data.conversation, data.messages, data.user);
                    }
                })
                .catch(error => {
                    console.error('Error opening conversation:', error);
                });
        }

        // إغلاق أقدم محادثة مفتوحة
        function closeOldestChat() {
            const widgets = document.querySelectorAll('.chat-widget');
            if (widgets.length > 0) {
                const oldestWidget = widgets[0]; // افتراض أن أول عنصر هو الأقدم
                const conversationId = oldestWidget.dataset.conversationId;

                // إغلاق المحادثة على الخادم
                fetch(`{{ url('conversations') }}/${conversationId}/close`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                // إزالة الويدجت من DOM
                oldestWidget.remove();
            }
        }

        // تهيئة أحداث النقر على زر الإشعارات
        document.getElementById('notifications-toggle').addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = document.getElementById('notifications-dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });

        // تهيئة حدث تحديد كل الإشعارات كمقروءة
        document.querySelector('.mark-all-read').addEventListener('click', function(e) {
            e.preventDefault();
            markAllAsRead();
        });

        // إغلاق قائمة الإشعارات عند النقر خارجها
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('notifications-dropdown');
            const toggle = document.getElementById('notifications-toggle');

            if (dropdown && toggle && dropdown.style.display === 'block' &&
                !dropdown.contains(e.target) && !toggle.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        // إذا كان المستخدم مسجل، ابدأ في جلب الإشعارات كل 30 ثانية
        setInterval(fetchNotifications, 30000);
        fetchNotifications(); // استدعاء فوري

        // إنشاء ويدجت شات جديد
        function createChatWidget(conversation, messages, user) {
            // التحقق من وجود المحادثة بالفعل
            const existingChat = document.querySelector(`.chat-widget[data-conversation-id="${conversation.id}"]`);
            if (existingChat) {
                // إذا كانت موجودة، قم بالتركيز عليها
                existingChat.classList.add('chat-widget-focus');
                setTimeout(() => {
                    existingChat.classList.remove('chat-widget-focus');
                }, 1000);
                return;
            }

            // إنشاء عنصر الويدجت
            const chatWidget = document.createElement('div');
            chatWidget.className = 'chat-widget';
            chatWidget.dataset.conversationId = conversation.id;
            chatWidget.dataset.userId = user.id;

            // تكوين HTML للويدجت
            chatWidget.innerHTML = `
                <div class="chat-header">
                    <img src="${user.avatar}" alt="${user.name}" class="chat-avatar">
                    <div class="chat-user-info">
                        <div class="chat-username">${user.name}</div>
                        <div class="chat-status">متصل</div>
                    </div>
                    <div class="chat-actions">
                        <button class="chat-minimize">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </button>
                        <button class="chat-close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="chat-body">
                    <div class="chat-messages" id="chat-messages-${conversation.id}">
                        <div class="load-more-messages" id="load-more-${conversation.id}">تحميل المزيد من الرسائل</div>
                        <div class="messages-container" id="messages-container-${conversation.id}">
                            <!-- الرسائل ستضاف هنا ديناميكيا -->
                        </div>
                    </div>
                </div>
                <div class="chat-footer">
                    <form class="chat-form" id="chat-form-${conversation.id}">
                        <input type="text" class="chat-input" placeholder="اكتب رسالتك هنا..." name="message" autocomplete="off">
                        <button type="submit" class="chat-send">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </form>
                </div>
            `;

            // إضافة الويدجت إلى الصفحة
            const container = document.getElementById('chat-widgets-container');
            container.appendChild(chatWidget);

            // تحميل الرسائل
            const messagesContainer = document.getElementById(`messages-container-${conversation.id}`);
            if (messages.length > 0) {
                renderMessages(messagesContainer, messages, conversation.id);
            } else {
                messagesContainer.innerHTML = '<div class="no-messages">لا توجد رسائل. ابدأ المحادثة الآن!</div>';
            }

            // إضافة معالجات الأحداث
            addChatEventListeners(chatWidget, conversation.id, user.id);

            // التمرير إلى أحدث رسالة
            const chatBody = chatWidget.querySelector('.chat-body');
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        // عرض الرسائل في نافذة المحادثة
        function renderMessages(container, messages, conversationId) {
            let html = '';

            messages.forEach(message => {
                const isMine = message.sender == currentUserId;
                const messageClass = isMine ? 'message-mine' : 'message-other';
                const readStatus = !isMine && message.is_read ? 'مقروءة' : !isMine ? 'غير مقروءة' : '';
                const readStatusClass = !isMine && message.is_read ? 'read' : !isMine ? 'unread' : '';

                // تحويل النص العادي إلى HTML آمن (لمنع XSS) مع احترام الاتجاه
                const messageText = message.message
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;')
                    .replace(/\n/g, '<br>');

                // تحديد اتجاه النص تلقائيًا بناء على محتواه
                const isRTL = detectRTL(message.message);

                // تحديد الاتجاه باستخدام طريقة تجنب مشاكل التحليل
                const dirAttr = isRTL ? 'dir="rtl"' : 'dir="ltr"';

                html += `
                    <div class="message ${messageClass}" data-message-id="${message.id}">
                        <div class="message-content" ${''+dirAttr}>${messageText}</div>
                        <div class="message-meta">
                            <span class="message-time">${formatTime(message.created_at)}</span>
                            ${isMine ? `<span class="message-status ${readStatusClass}">${readStatus}</span>` : ''}
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        // إضافة معالجات الأحداث لويدجت المحادثة
        function addChatEventListeners(widget, conversationId, userId) {
            // معالج إرسال الرسالة
            const form = widget.querySelector(`#chat-form-${conversationId}`);
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const input = this.querySelector('.chat-input');
                const message = input.value.trim();

                if (message) {
                    // إرسال الرسالة إلى الخادم
                    fetch(`{{ url('conversations') }}/${conversationId}/send`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                message: message,
                                receiver_id: userId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // إضافة الرسالة إلى واجهة المستخدم
                                const container = document.getElementById(`messages-container-${conversationId}`);
                                const currentMessages = container.innerHTML;

                                // تحديد اتجاه النص للرسالة الجديدة
                                const isRTL = detectRTL(message);

                                // تحديد الاتجاه للرسالة الجديدة بطريقة متوافقة
                                const newMsgDir = isRTL ? 'dir="rtl"' : 'dir="ltr"';

                                const messageHtml = `
                                <div class="message message-mine" data-message-id="${data.message.id}">
                                    <div class="message-content" ${''+newMsgDir}>${message}</div>
                                    <div class="message-meta">
                                        <span class="message-time">${formatTime(new Date())}</span>
                                        <span class="message-status unread">غير مقروءة</span>
                                    </div>
                                </div>
                            `;

                                // إذا كان هناك رسالة "لا توجد رسائل"، قم بإزالتها
                                if (container.querySelector('.no-messages')) {
                                    container.innerHTML = messageHtml;
                                } else {
                                    container.innerHTML = currentMessages + messageHtml;
                                }

                                // مسح حقل الإدخال وتمرير إلى أحدث رسالة
                                input.value = '';
                                const chatBody = widget.querySelector('.chat-body');
                                chatBody.scrollTop = chatBody.scrollHeight;
                            }
                        })
                        .catch(error => {
                            console.error('Error sending message:', error);
                        });
                }
            });

            // معالج النقر على زر التصغير
            const minimizeButton = widget.querySelector('.chat-minimize');
            minimizeButton.addEventListener('click', function() {
                widget.classList.toggle('chat-widget-minimized');
            });

            // معالج النقر على زر الإغلاق
            const closeButton = widget.querySelector('.chat-close');
            closeButton.addEventListener('click', function() {
                // إغلاق المحادثة على الخادم
                fetch(`{{ url('conversations') }}/${conversationId}/close`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                // إزالة الويدجت من الواجهة
                widget.remove();
            });

            // معالج تحميل المزيد من الرسائل
            const loadMoreButton = widget.querySelector(`#load-more-${conversationId}`);
            let page = 1; // الصفحة الحالية للتحميل المتدرج

            loadMoreButton.addEventListener('click', function() {
                // زيادة رقم الصفحة وتحميل المزيد من الرسائل
                page++;

                // إظهار مؤشر التحميل
                loadMoreButton.textContent = 'جاري التحميل...';
                loadMoreButton.disabled = true;

                fetch(`{{ url('conversations') }}/${conversationId}/load-more?page=${page}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const container = document.getElementById(`messages-container-${conversationId}`);
                            const currentMessages = container.innerHTML;

                            // عرض الرسائل القديمة في بداية المحتوى
                            let messagesHtml = '';

                            data.messages.forEach(message => {
                                const isMine = message.sender == currentUserId;
                                const messageClass = isMine ? 'message-mine' : 'message-other';
                                const readStatus = !isMine && message.is_read ? 'مقروءة' : !isMine ? 'غير مقروءة' : '';
                                const readStatusClass = !isMine && message.is_read ? 'read' : !isMine ? 'unread' : '';

                                // تحويل النص العادي إلى HTML آمن (لمنع XSS)
                                const messageText = message.message
                                    .replace(/&/g, '&amp;')
                                    .replace(/</g, '&lt;')
                                    .replace(/>/g, '&gt;')
                                    .replace(/"/g, '&quot;')
                                    .replace(/'/g, '&#039;')
                                    .replace(/\n/g, '<br>');

                                // تحديد اتجاه النص تلقائيًا
                                const isRTL = detectRTL(message.message);

                                // تحديد الاتجاه بطريقة تجنب مشاكل التحليل
                                const messageDir = isRTL ? 'dir="rtl"' : 'dir="ltr"';

                                messagesHtml += `
                                <div class="message ${messageClass}" data-message-id="${message.id}">
                                    <div class="message-content" ${''+messageDir}>${messageText}</div>
                                    <div class="message-meta">
                                        <span class="message-time">${formatTime(message.created_at)}</span>
                                        ${isMine ? `<span class="message-status ${readStatusClass}">${readStatus}</span>` : ''}
                                    </div>
                                </div>
                            `;
                            });

                            // إضافة الرسائل القديمة في بداية المحتوى الحالي
                            container.innerHTML = messagesHtml + currentMessages;

                            // إعادة تفعيل زر التحميل إذا كان هناك المزيد من الرسائل
                            if (data.has_more) {
                                loadMoreButton.textContent = 'تحميل المزيد من الرسائل';
                                loadMoreButton.disabled = false;
                            } else {
                                // إخفاء زر التحميل إذا لم تعد هناك رسائل للتحميل
                                loadMoreButton.style.display = 'none';
                            }

                            // الحفاظ على موضع التمرير بعد تحميل الرسائل الجديدة
                            const chatBody = widget.querySelector('.chat-body');
                            // احسب ارتفاع الرسائل الجديدة
                            const newMessagesHeight = messagesHtml.split('message').length * 60; // تقدير تقريبي
                            chatBody.scrollTop = newMessagesHeight;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading more messages:', error);
                        loadMoreButton.textContent = 'تحميل المزيد من الرسائل';
                        loadMoreButton.disabled = false;
                    });
            });
        }

        // الكشف عن اتجاه النص (RTL مثل العربية أو LTR مثل الإنجليزية)
        function detectRTL(text) {
            // قائمة نطاقات حروف RTL (العربية، العبرية، الفارسية، إلخ)
            const rtlChars = /[\u0591-\u07FF\uFB1D-\uFDFD\uFE70-\uFEFC]/;
            return rtlChars.test(text);
        }

        // تنسيق التوقيت إلى صيغة مناسبة بناء على اللغة الحالية
        function formatTime(timeStr) {
            const date = new Date(timeStr);
            if (isNaN(date.getTime())) {
                return timeStr; // إذا كان التاريخ غير صالح، أرجع النص الأصلي
            }

            // تحديد اللغة المناسبة بناءً على لغة الصفحة
            const locale = document.documentElement.lang || 'ar-SA';
            return date.toLocaleTimeString(locale, {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // إغلاق المحادثة الأقدم عند الوصول إلى الحد الأقصى للمحادثات المفتوحة
        function closeOldestChat() {
            const chatWidgets = document.querySelectorAll('.chat-widget');
            if (chatWidgets.length <= 0) return;

            // البحث عن المحادثة الأقدم (أول محادثة تمت إضافتها إلى DOM)
            let oldestChat = chatWidgets[0];

            // إرسال طلب إلى الخادم لإغلاق المحادثة
            const conversationId = oldestChat.dataset.conversationId;
            fetch(`{{ url('conversations') }}/${conversationId}/close`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            // إزالة المحادثة من الواجهة
            oldestChat.remove();
        }

        // إضافة معالجة النقر على زر "إرسال رسالة" عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            // التأكد من وجود حاوية ويدجات الدردشة
            if (!document.getElementById('chat-widgets-container')) {
                const chatContainer = document.createElement('div');
                chatContainer.id = 'chat-widgets-container';
                chatContainer.className = 'chat-widgets-container';
                document.body.appendChild(chatContainer);
            }

            // إضافة مستمعات الحدث لأزرار الدردشة
            document.querySelectorAll('.toggle-chat').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const userName = this.dataset.userName;
                    const userAvatar = this.dataset.userAvatar;

                    // التحقق من عدد المحادثات المفتوحة
                    const openChats = document.querySelectorAll('.chat-widget');
                    if (openChats.length >= 3) {
                        // إغلاق المحادثة الأقدم
                        closeOldestChat();
                    }

                    // بدء المحادثة مع المستخدم
                    fetch(`{{ url('conversations') }}/${userId}`, {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                createChatWidget(data.conversation, data.messages, {
                                    id: userId,
                                    name: userName,
                                    avatar: userAvatar
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error starting chat:', error);
                        });
                });
            });
        });
    </script>
    @endif
</body>

</html>