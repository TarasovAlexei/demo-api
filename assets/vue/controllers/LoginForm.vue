<template>
    <div class="bg-gray-50 font-[sans-serif]">
    <div class="min-h-screen flex flex-col items-center justify-center py-6 px-4">
        <div class="max-w-md w-full -mt-64">
            <div class=" flex flex-col items-center justify-center">
                <svg id="Layer_1" class="w-16 h-16 mb-2" fill="#1e2328" viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m89.349 107.879a39 39 0 0 0 13.734-29.712v-4.172a34.429 34.429 0 0 0 5.188-18.08 35.623 35.623 0 0 0 -10.993-25.422c7.408-7.3 7.511-17.857 7.511-18a1.772 1.772 0 0 0 -1.581-1.768 1.725 1.725 0 0 0 -1.876 1.387c-.015.057-1.518 5.213-15.046 10.509a49.923 49.923 0 0 0 -44.572 0c-13.529-5.3-15.03-10.447-15.035-10.453a1.75 1.75 0 0 0 -3.469.324c0 .138.1 10.692 7.51 18a35.625 35.625 0 0 0 -10.991 25.423 34.429 34.429 0 0 0 5.188 18.08v4.172a39 39 0 0 0 13.734 29.712 1.725 1.725 0 0 0 .231.2 39.168 39.168 0 0 0 8.983 5.671h-10.865a1.75 1.75 0 0 0 0 3.5h54a1.75 1.75 0 0 0 0-3.5h-10.866a39.168 39.168 0 0 0 8.983-5.671 1.725 1.725 0 0 0 .232-.2zm-1.29-3.523c-4.689-4.239-5.727-10.678-5.956-13.282a43.959 43.959 0 0 0 17.463-12.238 35.491 35.491 0 0 1 -11.507 25.519zm12.1-85.4c-1.49 4.822-4.95 11.006-12.979 13.594a92.036 92.036 0 0 0 -20.818 9.523c1.263-4.125 5.1-10.741 16.724-14.582 8.732-2.881 13.955-5.947 17.075-8.531zm-59.808 17.135a85 85 0 0 1 21.374 10.255 15.581 15.581 0 1 1 -21.374-10.255zm23.649 22.177a19.176 19.176 0 0 0 5.454 6.974l-5.454 6.93-5.456-6.926a19.176 19.176 0 0 0 5.456-6.978zm1.75-7.935a15.618 15.618 0 0 1 .525-3.987 85 85 0 0 1 21.37-10.253 15.581 15.581 0 1 1 -21.895 14.24zm15.828-26.016c-10.628 3.621-15.424 9.411-17.578 14.091-2.154-4.68-6.95-10.47-17.578-14.091a46.611 46.611 0 0 1 35.156 0zm-53.8-5.406c3.114 2.594 8.347 5.681 17.135 8.584 11.717 3.872 15.524 10.481 16.756 14.605a92.057 92.057 0 0 0 -20.852-9.544 19.652 19.652 0 0 1 -13.04-13.646zm5.643 13.844a22.267 22.267 0 0 0 2.748 1.651 19.074 19.074 0 0 0 19.441 32.774l7.014 8.9a1.75 1.75 0 0 0 2.75 0l7.01-8.9a19.078 19.078 0 0 0 19.454-32.78 22.268 22.268 0 0 0 2.741-1.647 32.219 32.219 0 0 1 10.193 23.16c0 19.321-18.29 35.041-40.771 35.041s-40.772-15.718-40.772-35.039a32.221 32.221 0 0 1 10.192-23.161zm-4.987 46.081a43.959 43.959 0 0 0 17.466 12.237c-.229 2.607-1.268 9.044-5.956 13.283a35.491 35.491 0 0 1 -11.51-25.52zm14.218 27.779c4.848-4.615 6.248-10.953 6.648-14.352a50.365 50.365 0 0 0 29.4 0c.4 3.4 1.8 9.738 6.649 14.352a35.5 35.5 0 0 1 -42.7 0z"/><path d="m46.667 55.379a5.046 5.046 0 1 0 -5.046-5.046 5.052 5.052 0 0 0 5.046 5.046zm0-6.592a1.546 1.546 0 1 1 -1.546 1.546 1.547 1.547 0 0 1 1.546-1.546z"/><path d="m86.379 50.333a5.046 5.046 0 1 0 -5.046 5.046 5.052 5.052 0 0 0 5.046-5.046zm-6.592 0a1.546 1.546 0 1 1 1.546 1.546 1.547 1.547 0 0 1 -1.546-1.546z"/></svg>
                <h2 class="text-[#1e2328] text-2xl  font-serif mb-6">Войти в Demo</h2>
            </div>
            <div class="p-8 rounded-2xl bg-white shadow">
                <div v-if="error" class="mt-3 p-3 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                    {{ error }}
                </div>
                <form v-on:submit.prevent="handleSubmit" class="mt-8 space-y-4">
                <div>
                    <label class="text-gray-800 text-sm mb-2 block" for="email">Email</label>
                    <div class="relative flex items-center">
                    <input 
                        class="w-full text-gray-800 text-sm border border-gray-300 px-4 py-3 rounded-md outline-none" 
                        :class="{ 'border-red-600': error }"
                        id="email"
                        v-model="email"
                        name="username" 
                        type="email"  
                        placeholder="Введите email" />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#bbb" stroke="#bbb" class="w-4 h-4 absolute right-4" viewBox="0 0 24 24">
                        <circle cx="10" cy="7" r="6" data-original="#000000"></circle>
                        <path d="M14 15H6a5 5 0 0 0-5 5 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 5 5 0 0 0-5-5zm8-4h-2.59l.3-.29a1 1 0 0 0-1.42-1.42l-2 2a1 1 0 0 0 0 1.42l2 2a1 1 0 0 0 1.42 0 1 1 0 0 0 0-1.42l-.3-.29H22a1 1 0 0 0 0-2z" data-original="#000000"></path>
                    </svg>
                    </div>
                </div>

                <div>
                    <label class="text-gray-800 text-sm mb-2 block" for="password">Пароль</label>
                    <div class="relative flex items-center">
                    <input
                        class="w-full text-gray-800 text-sm border border-gray-300 px-4 py-3 rounded-md outline-none" 
                        :class="{ 'border-red-600': error }"
                        id="password"
                        v-model="password"
                        type="password"
                        name="password"  
                        placeholder="Введите пароль" />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#bbb" stroke="#bbb" class="w-4 h-4 absolute right-4 cursor-pointer" viewBox="0 0 128 128">
                        <path d="M64 104C22.127 104 1.367 67.496.504 65.943a4 4 0 0 1 0-3.887C1.367 60.504 22.127 24 64 24s62.633 36.504 63.496 38.057a4 4 0 0 1 0 3.887C126.633 67.496 105.873 104 64 104zM8.707 63.994C13.465 71.205 32.146 96 64 96c31.955 0 50.553-24.775 55.293-31.994C114.535 56.795 95.854 32 64 32 32.045 32 13.447 56.775 8.707 63.994zM64 88c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z" data-original="#000000"></path>
                    </svg>
                    </div>
                </div>

            
                <div class="!mt-8">
                    <button
                        class="w-full py-3 px-4 text-sm tracking-wide rounded-lg text-white bg-green-800 hover:bg-green-900 focus:outline-none"
                        type="submit"
                        :disabled="isLoading"
                        :class="{ 'bg-green-700': isLoading, 'hover:bg-green-700': isLoading }"
                        >
                            Войти
                    </button>
                </div>
                <p class="text-gray-800 text-sm !mt-8 text-center">У вас нет учетной записи?  <a href="javascript:void(0);" class="text-blue-600 hover:underline ml-1 whitespace-nowrap font-normal">Регистрация</a></p>
                </form>
            </div>
        </div>
    </div>
    </div>

</template>

<script setup>
    import { createApp, ref } from 'vue';

    const email = ref('');
    const password = ref('');
    const error = ref('');
    const isLoading = ref(false);
    const emit = defineEmits(['user-authenticated']);


    const handleSubmit = async () => {
        isLoading.value = true;
        error.value = '';
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email.value,
                password: password.value
            })

            
        });

        isLoading.value = false;

        if (!response.ok) {
            const data = await response.json();
            error.value = data.error;
            return;
        } 

        location.reload();
    
        email.value = '';
        password.value = '';

    }

</script>