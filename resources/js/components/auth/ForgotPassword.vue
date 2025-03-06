<template>
    <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Reset your password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your email address and we'll send you a link to reset your password.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" @submit.prevent="handleSubmit">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email address
                        </label>
                        <div class="mt-1">
                            <input
                                id="email"
                                v-model="email"
                                type="email"
                                required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-napps-primary focus:border-napps-primary sm:text-sm"
                            />
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-napps-primary hover:bg-napps-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-napps-primary"
                        >
                            {{ loading ? 'Sending reset link...' : 'Send reset link' }}
                        </button>
                    </div>

                    <div v-if="error" class="mt-4 text-red-600 text-sm text-center">
                        {{ error }}
                    </div>

                    <div v-if="success" class="mt-4 text-green-600 text-sm text-center">
                        {{ success }}
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">
                                Remember your password?
                                <router-link
                                    :to="{ name: 'login' }"
                                    class="font-medium text-napps-primary hover:text-napps-primary"
                                >
                                    Sign in
                                </router-link>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useStore } from 'vuex'

export default {
    name: 'ForgotPassword',
    setup() {
        const store = useStore()
        const email = ref('')
        const success = ref('')

        const loading = computed(() => store.state.auth.loading)
        const error = computed(() => store.state.auth.error)

        const handleSubmit = async () => {
            const { error } = await store.dispatch('auth/resetPassword', email.value)
            
            if (!error) {
                success.value = 'Password reset link has been sent to your email'
                email.value = ''
            }
        }

        return {
            email,
            loading,
            error,
            success,
            handleSubmit
        }
    }
}
</script>
