import { supabase } from '../supabase'
import store from '../store'

export const requireAuth = async (to, from, next) => {
    const { data: { user } } = await supabase.auth.getUser()
    
    if (!user) {
        next({ name: 'login', query: { redirect: to.fullPath } })
        return
    }

    // Check for role-based routes
    if (to.meta.roles && !to.meta.roles.includes(user.user_metadata.role)) {
        next({ name: 'dashboard' })
        return
    }

    next()
}

export const requireGuest = async (to, from, next) => {
    const { data: { user } } = await supabase.auth.getUser()
    
    if (user) {
        next({ name: 'dashboard' })
        return
    }

    next()
}

// Initialize auth state
export const initializeAuth = async () => {
    const { data: { user } } = await supabase.auth.getUser()
    store.commit('auth/SET_USER', user)

    // Listen for auth state changes
    supabase.auth.onAuthStateChange((event, session) => {
        store.commit('auth/SET_USER', session?.user || null)
    })
}
