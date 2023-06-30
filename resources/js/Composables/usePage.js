import { getCurrentInstance } from 'vue'

export default function usePage() {
    return getCurrentInstance().appContext.config.globalProperties.$page;
}
