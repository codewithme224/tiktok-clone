<template>
    <div
        id="AuthOverlay"
        class="fixed top-0 left-0 z-50 flex items-center justify-center w-full h-full bg-black bg-opacity-50"
    >
        <div class="relative w-full bg-white max-w-[470px] h-[70%] p-4 rounded-lg">
            <div class="flex justify-end w-full">
                <button 
                @click="$event => $generalStore.isLoginOpen = false"
                class="p-1.5 rounded-full bg-gray-100">
                    <Icon name="mdi:close" size="26"/>
                </button>
            </div>
            
            <Login v-if="isRegister"/>
            <Register v-else />
            
            <div class="absolute bottom-0 left-0 flex items-center justify-center w-full py-5 border-t">
                <span class="text-[14px] text-gray-600">Don't have an account?</span>
                <button
                    @click="$event => isRegister = !isRegister"
                    class="text-[14px] text-[#F02C56] font-semibold pl-1"
                >
                    <span v-if="isRegister">Sign up</span>
                    <span v-else>Log in</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
const { $generalStore } = useNuxtApp()
let isRegister = ref(true)
</script>