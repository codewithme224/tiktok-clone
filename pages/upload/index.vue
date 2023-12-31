<template>

    <UploadError :errorType="errorType"/>
    
    <div 
        v-if="isUploading"
        class="fixed top-0 left-0 z-50 flex items-center justify-center w-full h-screen bg-black bg-opacity-50"
    >
        <Icon class="ml-1 animate-spin" name="mingcute:loading-line" size="100" color="#FFFFFF"/>
    </div>
    
  <UploadLayout>
    <div
      class="w-full mt-[80px] mb-[40px] bg-white shadow-lg rounded-md py-6 md:px-10 px-4"
    >
      <div class="text-[23px] font-semibold">Upload Video</div>
      <div class="mt-1 text-gray-400">Post a video to your account</div>
      <div class="gap-6 mt-8 md:flex">
        <label
          v-if="!fileDisplay"
          for="fileInput"
          @drop.prevent="onDrop"
          @dragover.prevent=""
          class="flex flex-col items-center justify-center w-full mx-auto mt-4 mb-6 md:mx-0 max-w-[260px] h-[470px] text-center p-3 border-2 border-dashed border-gray-300 rounded-lg hover:bg-gray-100 cursor-pointer"
        >
          <Icon name="majesticons:cloud-upload" size="40" color="#b3b3b1" />
          <div class="mt-4 text-[17px]">Select video to upload</div>
          <div class="mt-1.5 text-gray-500 text-[13px]">
            Or drag and drop a file
          </div>
          <div class="mt-12 text-sm text-gray-400">MP4</div>
          <div class="mt-2 text-gray-400 text-[13px]">Up to 30 minutes</div>
          <div class="mt-2 text-gray-400 text-[13px]">Less than 2 GB</div>
          <div
            class="px-2 py-1.5 mt-8 text-white text-[15px] w-[80%] bg-[#F02c56] rounded-sm"
          >
            Select file
          </div>
          <input 
          type="file" 
          id="fileInput" 
          ref="file" 
          @input="onChange"
          hidden 
          name="video"
          accept=".mp4" />
        </label>

        <div
          v-else
          class="flex items-center justify-center w-full mx-auto mt-4 md:mx-0 md:mb-12 md-16 max-w-[260px] h-[540px] p-3 rounded-2xl cursor-pointer relative"
        >
          <div class="w-full h-full bg-black" />

          <img
            class="absolute z-20 pointer-events-none"
            src="~/assets/images/mobile-case.png"
          />
          <img
            class="absolute z-20 right-4 bottom-6"
            width="90"
            src="~/assets/images/tiktok-logo-white.png"
          />
          <video
            autoplay
            loop
            muted
            class="absolute z-10 object-cover rounded-xl p-[13px] w-full h-full"
            :src="fileDisplay"
          />

          <div
            class="absolute z-50 flex items-center justify-between w-full p-2 border rounded -bottom-12 xl-gray-300"
          >
            <div class="flex items-center truncate">
              <Icon
                name="clarity:success-standard-line"
                size="16"
                class="min-w-[16px]"
              />
              <div class="text-[11px] pl-1 truncate text-ellipsis">
                {{ fileData.name  }}
              </div>
            </div>
            <button 
            @click="$event => clearVideo()"
            class="text-[11px] ml-2 font-semibold">Change</button>
          </div>
        </div>

        <div class="mt-4 mb-6">
          <div class="flex bg-[#F8F8F8] py-4 px-6">
            <div>
                <Icon class="mr-4" size="20" name="mdi:box-cutter-off" />
            </div>
            <div>
              <div class="text-semibold text-[15px] md-1.5">
                Divide videos and edit
              </div>
              <div class="text-semibold text-[13px] text-gray-400">
                You can quickly divide videos into multiple parts, remove
                redundant parts, and turn landscape videos into portrait videos.
              </div>
            </div>
            <div
              class="flex justify-end max-w-[130px] w-full h-full text-center my-auto"
            >
              <button
                class="px-8 text-white py-1.5 text-[15px] rounded-sm bg-[#F02C56] font-semibold"
              >
                Edit
              </button>
            </div>
          </div>
          
          <div class="mt-5">
            <div class="flex items-center justify-between">
                <div class="mb-1 text-[15px]">Caption</div>
                <div class="text-gray-400 text-[12px]">{{ caption.length  }}/150</div>
            </div>
            
            <input 
            v-model="caption"
            type="text" 
            maxlength="150" 
            class="w-full border p-2.5 rounded-md focus:outline-none">
          </div>

          <div class="flex gap-3"> 
            <button
                @click="$event => discard()"
             class="px-10 py-2.5 mt-8 border text-[16px] hover:bg-gray-100 rounded-sm">
                Discard
            </button>
            <button 
            @click="$event => createPost()"
            class="px-10 py-2.5 mt-8 border text-[16px] text-white bg-[#F02C56] rounded-sm">
                Post
            </button>
          </div>
          <div v-if="errors" class="mt-4">
              <div class="text-red-600" v-if="errors && errors.video">
                {{ errors.video[0] }}
            </div>
            <div class="text-red-600" v-if="errors && errors.text">
                {{ errors.text[0] }}
            </div>
        </div>
        </div>
      </div>
    </div>
  </UploadLayout>
</template>

<script setup>
import UploadLayout from "~/layouts/UploadLayout.vue";
const {$userStore } = useNuxtApp();
const router = useRouter();

definePageMeta({ middleware: 'auth' })


let file = ref(null);
let fileDisplay = ref(null)
let errorType = ref(null)
let caption = ref('')
let fileData = ref(null)
let errors = ref(null)
let isUploading = ref(false)

watch(() => caption.value, (caption) => {
    if (caption.length >= 150) {
        errorType.value = 'caption'
        return
    }
    errorType.value = null
})

const onChange = () => {
    fileDisplay.value = URL.createObjectURL(file.value.files[0]);
    fileData.value = file.value.files[0]
}

const onDrop = (e) => {
    errorType.value = ''
    file.value = e.dataTransfer.files[0];
    fileDisplay.value = e.dataTransfer.files[0];

    let extension = file.value.name.substring(file.value.name.lastIndexOf('.') + 1)

    if (extension !== 'mp4') {
        errorType.value = 'file'
        return
    }

    fileDisplay.value = URL.createObjectURL(e.dataTransfer.files[0]);
}

const discard = () => {
    file.value = null
    fileDisplay.value = null
    fileData.value = null
    caption.value = ''
}


const createPost = async () => {
    errors.value = null

    let data = new FormData();

    data.append('video', fileData.value || '')
    data.append('text', caption.value || '')

    if (fileData.value && caption.value) {
        isUploading.value = true
    }

    try {
        let res = await $userStore.createPost(data)
        if (res.status === 200) {
            setTimeout(() => {
                router.push('/profile/' + $userStore.id)
                isUploading.value = false
            }, 1000)
        }
    } catch (error) {
        errors.value = error.response.data.errors
        isUploading.value = false
    }
}

const clearVideo = () => {
    file.value = null
    fileDisplay.value = null
    fileData.value = null
   
}
</script>
