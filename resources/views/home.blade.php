@extends('layouts.front')

@section('sidebar-left')
<h3>القائمة</h3>
<div class="sidebar-item">
    <div class="sidebar-item-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
    </div>
    <span>الصفحة الرئيسية</span>
</div>
<div class="sidebar-item">
    <div class="sidebar-item-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
    </div>
    <span>الأصدقاء</span>
</div>
<div class="sidebar-item">
    <div class="sidebar-item-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
        </svg>
    </div>
    <span>الصور</span>
</div>
<div class="sidebar-item">
    <div class="sidebar-item-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
    </div>
    <span>المساعدة</span>
</div>
@endsection

@section('content')
<div class="post-card">
    <div class="post-header">
        <img src="https://via.placeholder.com/40" alt="User" class="user-avatar">
        <div>
            <strong>أحمد محمد</strong>
            <div style="font-size: 0.8rem; color: #65676b;">منذ ساعتين</div>
        </div>
    </div>
    <div class="post-content">
        <p>مرحبًا بكم في الصفحة الرئيسية الجديدة! يمكنك استخدام دوال الألوان الجديدة:</p>
        <p>
            اللون الأحمر: <br>
            اللون الأخضر: <br>
            اللون الأزرق:
        </p>
    </div>
    <div class="post-actions">
        <div class="post-action">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
            </svg>
            إعجاب
        </div>
        <div class="post-action">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path
                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                </path>
            </svg>
            تعليق
        </div>
        <div class="post-action">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
            </svg>
            مشاركة
        </div>
    </div>
</div>

<div class="post-card">
    <div class="post-header">
        <img src="https://via.placeholder.com/40" alt="User" class="user-avatar">
        <div>
            <strong>سارة أحمد</strong>
            <div style="font-size: 0.8rem; color: #65676b;">منذ 5 ساعات</div>
        </div>
    </div>
    <div class="post-content">
        <p>هذا مثال على منشور في الصفحة الرئيسية. يمكنك تخصيص هذا المحتوى كما تريد!</p>
    </div>
    <div class="post-actions">
        <div class="post-action">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
            </svg>
            إعجاب
        </div>
        <div class="post-action">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path
                    d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                </path>
            </svg>
            تعليق
        </div>
        <div class="post-action">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
            </svg>
            مشاركة
        </div>
    </div>
</div>
@endsection

@section('sidebar-right')
<h3>{{__('Registered Users')}}</h3>


@forelse ($users as $user)
<div id="users">
    <div class="user-card">
        <img src="{{ $user->profile->avatar ?? asset('assets/images/defaultAvatar.jpeg') }}" alt="User Avatar" class="user-avatar">
        <div>
            <strong>{{ $user->name }}</strong>
            <div style="font-size: 0.8rem; color: #65676b;">متصل الآن</div>
        </div>
        <div class="dropdown">
            <button class="dropdown-toggle" type="button" id="userCardDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i> </button>
            <ul class="dropdown-menu" aria-labelledby="userCardDropdown">
                <li><a class="dropdown-item" href="{{ route('users-show-profile', $user->id) }}">عرض البروفايل</a></li>
                <li><a class="dropdown-item" href="{{ route('users-send-friend-request', $user->id) }}">إرسال طلب صداقة</a></li>
                <li><a class="dropdown-item toggle-chat" data-user-id="{{ $user->id }}">إرسال رسالة</a></li>
            </ul>
        </div>
    </div>
    @empty
    <p>{{__('No youhave not any friends')}}</p>
    @endforelse
</div>
@endsection