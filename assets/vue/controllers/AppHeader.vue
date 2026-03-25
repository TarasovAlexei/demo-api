<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    currentUser: Object 
});

const isMenuOpen = ref(false);
const BASE_URL = window.location.origin;

const sharedUserData = ref(null);

const fetchMe = async () => {
    if (sharedUserData.value) return;
    try {
        const response = await fetch(`${BASE_URL}/api/me`);
        if (response.ok) {
            sharedUserData.value = await response.json();
        }
    } catch (e) {
        console.error("Header Fetch Error:", e);
    }
};

const closeMenu = (e) => {
    if (!e.target.closest('.user-menu-container')) {
        isMenuOpen.value = false;
    }
};

onMounted(() => {
    fetchMe();
    document.addEventListener("turbo:before-visit", () => {
        isMenuOpen.value = false;
    });
    window.addEventListener('click', closeMenu);
});

onUnmounted(() => {
    window.removeEventListener('click', closeMenu);
});

const getAvatar = () => {
    const user = sharedUserData.value || props.currentUser;
    const avatar = user?.avatar;
    const firstName = user?.firstName;

    if (avatar?.thumbUrl) return avatar.thumbUrl;
    if (avatar?.contentUrl) return avatar.contentUrl;
    
    const initial = firstName ? firstName.charAt(0).toUpperCase() : '?';
    const index = (initial.charCodeAt(0) % 6) + 1;
    return `/media/avatars/default${index}.png`;
};

const getUserName = () => {
    return sharedUserData.value?.firstName || props.currentUser?.firstName || '';
};
</script>

<template>
  <header class="fixed top-0 left-0 right-0 h-[48px] bg-white border-b border-[#dce1e6] z-50 shadow-[0_1px_2px_rgba(0,0,0,0.03)]">
    <div class="max-w-[960px] mx-auto h-full flex items-center justify-between px-4">
      <div class="flex items-center flex-1">
        <!-- ЛОГО API -->
        <div style="flex-shrink: 0; margin-right: 12px;">
          <a href="/api" style="color: #2a5885; font-weight: bold; font-size: 15px; text-decoration: none; text-transform: uppercase; letter-spacing: -0.5px;">SWAGGER UI</a>
        </div>
        <div style="position: relative; width: 230px; flex-shrink: 0;"> 
          <div style="position: absolute; top: 0; bottom: 0; left: 10px; display: flex; align-items: center; pointer-events: none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#818c99" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" x2="16.65" y1="21" y2="16.65"></line></svg>
          </div>
          <input type="text" placeholder="Поиск" style="background-color: #ebedf0; border: none; border-radius: 8px; height: 32px; width: 100%; padding-left: 34px !important; padding-right: 12px; font-size: 13px; outline: none;" class="placeholder-[#818c99] focus:bg-white focus:ring-1 focus:ring-[#dce1e6] transition-all">
        </div>
        <div class="h-4 w-[1px] bg-[#dce1e6] mx-2"></div>
        <nav class="flex items-center"></nav>
      </div>
      <div class="flex items-center gap-3">
        <div class="shrink-0 relative user-menu-container">
          <button @click.stop="isMenuOpen = !isMenuOpen" class="flex items-center gap-2 group hover:bg-[#f0f2f5] p-1 px-2 rounded-lg transition-all outline-none">
            <template v-if="!sharedUserData && !props.currentUser">
              <div class="w-20 h-3.5 rounded-full bg-[#ebedf0] animate-pulse"></div>
              <div class="w-9 h-9 rounded-full bg-[#ebedf0] animate-pulse"></div>
            </template>
            <template v-else>
              <span class="text-[13px] font-semibold text-[#2a5885]">
                {{ getUserName() }}
              </span>
              <div class="w-9 h-9 rounded-full overflow-hidden border border-black/5 shadow-sm bg-[#f0f2f5]">
                <img :src="getAvatar()" class="w-full h-full object-cover">
              </div>
            </template>
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#818c99" stroke-width="3" class="transition-transform duration-200" :class="{'rotate-180': isMenuOpen}">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
          <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
          >
            <div v-if="isMenuOpen" class="absolute right-0 mt-1 w-44 bg-white border border-[#dce1e6] rounded-xl shadow-xl py-1.5 z-50">
              <a href="/" class="flex items-center justify-between px-4 py-2 text-[13px] text-[#2a5885] hover:bg-[#f0f2f5] transition-colors group">
                Моя страница
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a5885" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </a>
              <a href="" class="flex items-center justify-between px-4 py-2 text-[13px] text-[#2a5885] hover:bg-[#f0f2f5] transition-colors group">
                Новости
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a5885" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                  <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
              </a>
              <a href="" class="flex items-center justify-between px-4 py-2 text-[13px] text-[#2a5885] hover:bg-[#f0f2f5] transition-colors group">
                Мессенджер
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a5885" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
              </a>
              <a href="" class="flex items-center justify-between px-4 py-2 text-[13px] text-[#2a5885] hover:bg-[#f0f2f5] transition-colors group">
                Друзья
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a5885" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </a>
              <a href="" class="flex items-center justify-between px-4 py-2 text-[13px] text-[#2a5885] hover:bg-[#f0f2f5] transition-colors group">
                Сообщества
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a5885" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="7" height="7"></rect>
                  <rect x="14" y="3" width="7" height="7"></rect>
                  <rect x="14" y="14" width="7" height="7"></rect>
                  <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
              </a>
              <a href="" class="flex items-center justify-between px-4 py-2 text-[13px] text-[#2a5885] hover:bg-[#f0f2f5] transition-colors group">
                Фотографии
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a5885" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                  <circle cx="8.5" cy="8.5" r="1.5"></circle>
                  <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
              </a>
              <a href="" class="flex items-center justify-between px-4 py-2 text-[13px] text-[#2a5885] hover:bg-[#f0f2f5] transition-colors group">
                Музыка
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a5885" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 18V5l12-2v13"></path>
                  <circle cx="6" cy="18" r="3"></circle>
                  <circle cx="18" cy="16" r="3"></circle>
                </svg>
              </a>
              <div class="h-[1px] bg-[#f0f2f5] my-1"></div>
              <!-- Настройки -->
              <a href="" class="flex items-center justify-between px-4 py-2 text-[13px] text-[#2a5885] hover:bg-[#f0f2f5] transition-colors group">
                Настройки
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a5885" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="3"></circle>
                  <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
              </a>
              <!-- Выход -->
              <form action="/logout" method="POST" class="m-0">
                <button type="submit" class="w-full flex items-center justify-between px-4 py-2 text-[13px] text-[#2a5885] hover:bg-[#f0f2f5] transition-colors group">
                  <span>Выйти</span>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2a5885" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                  </svg>
                </button>
              </form>
            </div>
          </Transition>
        </div>
      </div>
    </div>
  </header>
</template>



