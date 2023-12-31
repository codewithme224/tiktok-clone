import axios from "axios"

// export default defineNuxtPlugin((NuxtApp) => {

//     axios.defaults.withCredentials = true;
//     axios.defaults.baseURL = 'http://localhost:3000'import axios from "axios"

export default defineNuxtPlugin((NuxtApp) => {
    axios.defaults.withCredentials = true;
    axios.defaults.baseURL = process.env.BASE_URL || 'http://localhost:3000'
    return {
        provide: {
            axios: axios
        }
    }
})
//     return {
//         provide: {
//             axios: axios
//         }
//     }
// })