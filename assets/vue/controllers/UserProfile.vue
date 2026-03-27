<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    user: Object,
    currentUser: Object
});

const isMenuOpen = ref(false);
const BASE_URL = window.location.origin; 
const posts = ref([]);
const isLoading = ref(false);

const followers = ref([]);
const following = ref([]);
const isLoadingRelations = ref(false);

const isFollowingUser = ref(props.user?.isSubscribed || false); 

const currentUserData = ref(null);

const fetchRelationships = async () => {
    if (!props.user?.id) return;
    
    isLoadingRelations.value = true;
    try {
        const [followersRes, followingRes] = await Promise.all([
            fetch(`${BASE_URL}/api/users/${props.user.id}/followers?itemsPerPage=6`),
            fetch(`${BASE_URL}/api/users/${props.user.id}/following?itemsPerPage=6`)
        ]);

        const followersData = await followersRes.json();
        const followingData = await followingRes.json();

        followers.value = followersData['hydra:member'] || followersData.member || [];
        following.value = followingData['hydra:member'] || followingData.member || [];
    } catch (e) {
        console.error("Ошибка при загрузке связей:", e);
    } finally {
        isLoadingRelations.value = false;
    }
};

const displayUser = computed(() => {
    if (!props.user) return null;
    
    const multiplyArray = (arr) => {
        if (!arr || !Array.isArray(arr)) return [];
        return Array(9).fill(arr).flat();
    };

    const userId = props.user?.id || '0';
    const seed = String(userId).split('').reduce((acc, char, i) => acc + (char.charCodeAt(0) * (i + 1)), 0);
    const hash = Math.abs(Math.sin(seed) * 10000);
    const fakePostsCount = Math.floor((hash % 900) + 100);

    return {
        ...props.user,
        fakePostsCount,
        followersCount: (props.user.followersCount || 0) * 9,
        followingCount: (props.user.followingCount || 0) * 9,
        followersPreview: multiplyArray(followers.value),
        followingPreview: multiplyArray(following.value)
    };
});

const toggleFollow = async () => {
    if (!props.user?.id) return;
    isFollowingUser.value = !isFollowingUser.value;
};
const getAvatar = (avatar, firstName, size = 'small') => {
  const isSmall = size === 'small' || size === true;

  if (avatar) {
    if (isSmall && avatar.thumbUrl) {
      return avatar.thumbUrl;
    }
    
    if (avatar.contentUrl) {
      return avatar.contentUrl;
    }
  }

  const initial = firstName ? firstName.charAt(0).toUpperCase() : '?';
  const totalDefaults = 6; 
  const charCode = initial.charCodeAt(0) || 0;
  const avatarIndex = (charCode % totalDefaults) + 1;

  return `/media/avatars/default${avatarIndex}.png`;
};

const fetchPosts = async () => {
    if (!props.user?.id) return;
    isLoading.value = true;
    try {
        const response = await fetch(`${BASE_URL}/api/posts?author=${props.user.id}`);
        const data = await response.json();
        posts.value = (data['hydra:member'] || data.member || []).map(post => ({
            ...post,
            liked: false,
            likesCount: (Math.floor(Math.random() * 10) + 2) * 9
        }));
    } catch (e) {
        console.error("Ошибка при fetch постов:", e);
    } finally {
        isLoading.value = false;
    }
};

watch(() => props.user, (newVal) => {
    if (newVal?.id) {
        isFollowingUser.value = newVal.isSubscribed || false;
        fetchPosts();
        fetchRelationships();
    }
}, { immediate: true });

const getProfileLink = (id) => {
    const numericId = typeof id === 'object' ? id.id : (typeof id === 'string' ? id.split('/').pop() : id);
    return `/user/${numericId}`;
};

const toggleLike = (post) => {
    post.liked = !post.liked;
    post.liked ? post.likesCount++ : post.likesCount--;
};
</script>

<template>
  <div v-if="displayUser" class="min-h-screen bg-[#edeef0] font-sans text-[#000000] pb-10" @click="isMenuOpen = false">
    <div class="max-w-[960px] mx-auto flex gap-[15px] pt-[63px] px-4">
      <main class="flex-grow min-w-0 space-y-[15px]">
        <div class="bg-white rounded-xl shadow-sm border border-[#e7e8ec] overflow-hidden">
          <div class="p-5 flex items-center gap-5">
            <a :href="getProfileLink(displayUser.id)" class="block flex-shrink-0">
              <img :src="getAvatar(displayUser.avatar, displayUser.firstName)" class="w-20 h-20 rounded-full object-cover border border-black/5 shadow-sm bg-[#f0f2f5] hover-scale">
            </a>
            <div>
              <a :href="getProfileLink(displayUser.id)" class="hover:underline">
                <h1 class="text-[19px] font-semibold leading-tight text-[#000000]">{{ displayUser.firstName }} {{ displayUser.lastName }}</h1>
              </a>
              <p class="text-[12.5px] text-[#818c99] mt-1">{{ displayUser.email }}</p>
            </div>
          </div>
          <div class="relative">
            <div class="absolute inset-x-5 top-0 h-[1px] bg-[#f0f2f5]"></div>
            <div class="flex items-center justify-around text-center py-2 px-1">
              <div v-for="stat in [
                {l: 'подписчиков', v: displayUser.followersCount}, 
                {l: 'подписок', v: displayUser.followingCount}, 
                {l: 'записей', v: displayUser.fakePostsCount}
              ]" 
              :key="stat.l" 
              class="flex-1 text-center cursor-pointer group/stat hover:bg-[#f0f2f5] transition-colors duration-200 py-1.5 rounded-lg"
              >
                <div class="text-[13px] font-medium text-[#2a5885] leading-none group-hover/stat:underline mt-[10px]">
                  {{ stat.v }}
                </div>
                <div class="text-[11px] text-[#818c99] mt-1 group-hover/stat:underline">
                  {{ stat.l }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-[#e7e8ec] overflow-hidden min-h-[200px]">
          <div v-if="isLoading" class="divide-y divide-[#f0f2f5]">
            <div v-for="i in 10" :key="'post-sk-' + i" class="p-4 flex gap-4">
              <div class="flex-shrink-0">
                <div class="skeleton-item rounded-full !block ring-1 ring-black/5" style="width: 50px; height: 50px;"></div>
              </div>
              <div class="flex-grow min-w-0 pt-0.5">
                <div style="height: 19px;" class="flex items-center">
                  <div class="skeleton-item" style="width: 110px; height: 12px; border-radius: 3px;"></div>
                </div>
                <div class="mt-0.5" style="height: 20px; display: flex; align-items: center;"> 
                  <div class="skeleton-item" style="width: 60%; height: 11px; border-radius: 2px;"></div>
                </div>
                <div class="flex items-center justify-between" style="height: 18px;">
                  <div class="skeleton-item" style="width: 120px; height: 9px; border-radius: 2px; opacity: 0.6;"></div> 
                    <div class="skeleton-item" style="width: 32px; height: 12px; border-radius: 3px; opacity: 0.5;"></div>
                </div>
              </div>
            </div>
          </div>
          <div v-else-if="!posts.length" class="p-16 text-center text-[13px] text-[#818c99]">
            На стене пока нет записей
          </div>
          <TransitionGroup 
            v-else 
            tag="div" 
            class="divide-y divide-[#f0f2f5]"
            enter-active-class="transition duration-200"
            enter-from-class="opacity-0"
          >
            <div v-for="post in posts" :key="post.id" class="relative group">
              <div class="p-4 flex gap-4 hover:bg-[#f9f9fa] transition-colors">
                <div class="flex-shrink-0">
                  <a :href="getProfileLink(displayUser.id)">
                    <img :src="getAvatar(displayUser.avatar, displayUser.firstName)" 
                         class="w-[50px] h-[50px] rounded-full object-cover ring-1 ring-black/5 bg-[#f0f2f5] hover-scale">
                  </a>
                </div>
                <div class="flex-grow min-w-0 pt-0.5">
                  <a :href="getProfileLink(displayUser.id)" 
                     class="text-[13px] font-bold hover:underline cursor-pointer block mb-0.5"
                     style="color: #2a5885;">
                    {{ displayUser.firstName }} {{ displayUser.lastName }}
                  </a>
                  <div class="text-[13px] leading-[1.5] text-[#000] break-words whitespace-pre-wrap">
                    {{ post.content }}
                  </div>
                  <div class=" flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-[12px] text-[#818c99]">
                      <span>{{ post.createdAtAgo || '23 окт в 0:34' }}</span>
                      <span class="opacity-50">|</span>
                      <button class="hover:underline text-[#2a5885]">Ответить</button>
                    </div>
                    <button @click="toggleLike(post)" 
                            class="flex items-center gap-1 group cursor-pointer select-none hover:opacity-80 transition-opacity">
                      <svg width="14" height="14" viewBox="0 0 24 24" 
                           :fill="post.liked ? '#ff3347' : '#bac4ce'" 
                           class="transition-all duration-300 transform" 
                           :class="{ 'scale-125': post.liked }">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                      </svg>
                      <span class="text-[12px] font-medium text-[#2a5885]">
                        {{ post.likesCount }}
                      </span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </TransitionGroup>
        </div>
      </main>
     <aside class="w-[280px] space-y-[15px] flex-shrink-0">
        <div class="bg-white rounded-xl shadow-sm p-3 border border-[#e7e8ec]">
          <a class="block w-full aspect-square mb-3 rounded-lg overflow-hidden border border-black/5 bg-[#f0f2f5]">
            <img :src="getAvatar(displayUser.avatar, displayUser.firstName, false)" class="w-full h-full object-cover hover-scale">
          </a>
          <button v-if="Number(displayUser.id) === Number(currentUser?.id)" 
              class="w-full bg-[#f0f2f5] text-[#2a5885] py-2 rounded-lg text-[13px] font-medium hover:bg-[#e5ebf1] active:scale-[0.97] transition-all duration-100">
            Редактировать профиль
          </button>
          <button v-else @click="toggleFollow" 
              class="w-full py-2 rounded-lg text-[13px] font-medium transition-all duration-100 active:scale-[0.97]" 
              :class="isFollowingUser ? 'bg-[#f0f2f5] text-[#55677d] hover:bg-[#e5ebf1]' : 'bg-[#2a5885] text-white hover:bg-[#244b72]'">
            {{ isFollowingUser ? 'Отписаться' : 'Подписаться' }}
          </button>
        </div>
        <div class="bg-white rounded-xl p-4 border border-[#e7e8ec]">
          <h3 class="text-[13px] font-normal mb-3 text-black">
            <a href="#" class="hover:underline">Подписчики</a>
            <span class="text-[#818c99] ml-1">{{ displayUser.followersCount }}</span>
          </h3>
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px 0; min-height: 184px; align-content: start;">
            <template v-if="isLoadingRelations">
              <div v-for="i in 6" :key="'sk-fol-' + i" class="flex flex-col items-center py-2">
                <div class="skeleton-item w-[50px] h-[50px] rounded-full"></div>
                <div class="skeleton-item mt-1.5 rounded-full" style="width: 40px; height: 12px;"></div>
              </div>
            </template>
            <template v-else>
              <a v-for="(f, idx) in displayUser.followersPreview?.slice(0, 6)" 
                :key="'follower-' + idx" 
                :href="getProfileLink(f.id)" 
                class="flex flex-col items-center py-2 rounded-lg hover:bg-[#f0f2f5] transition-colors group">
                <img :src="getAvatar(f.avatar, f.firstName)" class="w-[50px] h-[50px] rounded-full object-cover ring-1 ring-black/5 bg-[#f0f2f5] transition-transform duration-200 group-hover:scale-105">
                <p class="text-[12px] text-[#2a5885] mt-1.5 leading-tight truncate w-full text-center px-1">
                  {{ f.firstName }}
                </p>
              </a>
            </template>
          </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-[#e7e8ec]">
          <h3 class="text-[13px] font-normal mb-3 text-black">
            <a href="#" class="hover:underline">Подписки</a>
            <span class="text-[#818c99] ml-1">{{ displayUser.followingCount }}</span>
          </h3>
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px 0; min-height: 184px; align-content: start;">
            <template v-if="isLoadingRelations">
              <div v-for="i in 6" :key="'sk-sub-' + i" class="flex flex-col items-center py-2">
                <div class="skeleton-item w-[50px] h-[50px] rounded-full"></div>
                <div class="skeleton-item mt-1.5 rounded-full" style="width: 40px; height: 12px;"></div>
              </div>
            </template>
            <template v-else>
              <a v-for="(f, idx) in displayUser.followingPreview?.slice(0, 6)" 
                :key="'following-' + idx" 
                :href="getProfileLink(f.id)" 
                class="flex flex-col items-center py-2 rounded-lg hover:bg-[#f0f2f5] transition-colors group">
                <img :src="getAvatar(f.avatar, f.firstName)" class="w-[50px] h-[50px] rounded-full object-cover ring-1 ring-black/5 bg-[#f0f2f5] transition-transform duration-200 group-hover:scale-105">
                <p class="text-[12px] text-[#2a5885] mt-1.5 leading-tight truncate w-full text-center px-1">
                  {{ f.firstName }}
                </p>
              </a>
            </template>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<style scoped>
@keyframes shimmer {
  0% { background-position: 150% 0; }
  100% { background-position: -150% 0; }
}

.skeleton-item {
  background: linear-gradient(90deg, #f0f2f5 25%, #e7e8ec 37%, #f0f2f5 63%);
  background-size: 400% 100%;
  animation: shimmer 1.4s ease-in-out infinite;
}

.hover-scale {
  transition: transform 0.2s ease-in-out;
}
.hover-scale:hover {
  transform: scale(1.05);
}

a {
  text-decoration: none;
  color: inherit;
}
</style>
