<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-napps-primary">NAPPS Conference</h1>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <button
                            @click="logout"
                            class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-napps-primary hover:bg-napps-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-napps-primary"
                        >
                            Sign out
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-10">
            <main>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <router-view></router-view>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'

export default {
    name: 'Dashboard',
    setup() {
        const store = useStore()
        const router = useRouter()

        const logout = async () => {
            await store.dispatch('auth/logout')
            router.push('/login')
        }

        return {
            logout
        }
    }
}
</script>
