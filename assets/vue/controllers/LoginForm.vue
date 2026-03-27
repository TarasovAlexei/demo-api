<script setup>
import { ref, onMounted } from 'vue';

const email = ref('');
const password = ref('');
const isPasswordVisible = ref(false);

const error = ref('');
const isLoading = ref(false);
const shouldShake = ref(false);
const isVisible = ref(false);

onMounted(() => {
    isVisible.value = true;
});

const triggerShake = () => {
    shouldShake.value = true;
    setTimeout(() => { shouldShake.value = false; }, 500);
};

const handleSubmit = async () => {
    isLoading.value = true;

    try {
        const response = await fetch('/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email.value, password: password.value })
        });

        const data = await response.json().catch(() => ({}));

        if (response.ok && data.redirect_url) {
            window.location.href = data.redirect_url;
            return;
        }

        error.value = data.error || 'Ошибка авторизации';
        triggerShake();
    } catch (e) {
        error.value = 'Ошибка связи';
        triggerShake();
    } finally {
        isLoading.value = false;
    }
}

</script>

<template>
  <div class="min-h-screen bg-[#edeef0] font-sans flex flex-col items-center justify-start pt-[10vh] p-4 overflow-hidden">
    
    <Transition name="fade-zoom">
      <div 
          v-if="isVisible"
          class="max-w-[360px] w-full bg-white rounded-xl shadow-sm border border-[#e7e8ec] overflow-hidden"
          :class="{ 'shake-animation': shouldShake }"
      >
        
        <div class="p-8 pb-0 flex flex-col items-center">
          <div class="w-16 h-16 bg-[#f0f2f5] rounded-full flex items-center justify-center mb-4 border border-gray-100">
             <svg class="w-10 h-10 text-[#2a5885]" fill="currentColor" viewBox="0 0 24 24">
               <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
             </svg>
          </div>
          <h1 class="text-[17px] font-medium text-black">Demo-API</h1>
          <p class="text-[13px] text-[#818c99] mt-2 text-center px-2 leading-snug">
            Данные заполнены автоматически для удобства тестирования
          </p>
        </div>
        <div v-if="error" class="mx-8 mt-4 p-3 text-[13px] text-[#e64646] bg-[#faebeb] rounded-lg border border-[#e64646]/20 text-center">
          {{ error }}
        </div>
        <form @submit.prevent="handleSubmit" class="p-8 space-y-4">
          <!-- Поле Email -->
          <div class="relative">
            <input 
              v-model="email"
              type="email"
              placeholder="Email"
              :disabled="isLoading"
              class="w-full h-9 bg-[#f0f2f5] border border-[#dce1e6] rounded-lg pl-3 pr-10 text-[13px] outline-none focus:border-[#c1cedb] focus:bg-white transition-all"
            />
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <svg class="w-4 h-4 text-[#adb5bd]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
          <div class="relative">
            <input 
              v-model="password"
              :type="isPasswordVisible ? 'text' : 'password'"
              placeholder="Пароль"
              :disabled="isLoading"
              class="w-full h-9 bg-[#f0f2f5] border border-[#dce1e6] rounded-lg pl-3 pr-10 text-[13px] outline-none focus:border-[#c1cedb] focus:bg-white transition-all"
            />
            <button 
              type="button"
              @click="isPasswordVisible = !isPasswordVisible"
              class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#adb5bd] hover:text-[#2a5885] transition-colors"
            >
              <svg v-if="isPasswordVisible" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
              </svg>
            </button>
          </div>
          <button 
            type="submit"
            :disabled="isLoading"
            class="w-full h-9 bg-[#2a5885] hover:bg-[#244b72] text-white text-[13px] font-medium rounded-lg transition-colors mt-2 disabled:opacity-60"
          >
            <span v-if="isLoading">Загрузка...</span>
            <span v-else>Войти</span>
          </button>
        </form>
        <div class="bg-[#f9fafb] border-t border-[#e7e8ec] py-4 flex justify-center">
          <a href="#" class="text-[13px] text-[#2a5885] hover:underline font-medium">Создать аккаунт</a>
        </div>
      </div>
    </Transition>
    <p class="mt-8 text-[12px] text-[#adb5bd] transition-opacity duration-1000" :class="isVisible ? 'opacity-100' : 'opacity-0'">
      © 2025-2026 Demo-API
    </p>
  </div>
</template>

<style scoped>
.fade-zoom-enter-active {
  transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.fade-zoom-enter-from {
  opacity: 0;
  transform: scale(0.9) translateY(20px);
}

.shake-animation {
  animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
}

@keyframes shake {
  10%, 90% { transform: translate3d(-1px, 0, 0); }
  20%, 80% { transform: translate3d(2px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
  40%, 60% { transform: translate3d(4px, 0, 0); }
}
</style>
