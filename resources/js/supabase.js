import { createClient } from '@supabase/supabase-js'

const supabaseUrl = import.meta.env.VITE_SUPABASE_URL
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON_KEY

export const supabase = createClient(supabaseUrl, supabaseAnonKey)

// Auth helper functions
export const auth = {
    signUp: async ({ email, password, ...data }) => {
        const { data: user, error } = await supabase.auth.signUp({
            email,
            password,
            options: {
                data: { ...data }
            }
        })
        return { user, error }
    },

    signIn: async ({ email, password }) => {
        const { data: { user }, error } = await supabase.auth.signInWithPassword({
            email,
            password
        })
        return { user, error }
    },

    signOut: async () => {
        const { error } = await supabase.auth.signOut()
        return { error }
    },

    getUser: async () => {
        const { data: { user }, error } = await supabase.auth.getUser()
        return { user, error }
    },

    resetPassword: async (email) => {
        const { data, error } = await supabase.auth.resetPasswordForEmail(email)
        return { data, error }
    },

    updatePassword: async (newPassword) => {
        const { data, error } = await supabase.auth.updateUser({
            password: newPassword
        })
        return { data, error }
    }
}

// Database helper functions
export const db = {
    // Participants
    getParticipants: async () => {
        const { data, error } = await supabase
            .from('participants')
            .select('*')
        return { data, error }
    },

    createParticipant: async (participantData) => {
        const { data, error } = await supabase
            .from('participants')
            .insert([participantData])
            .select()
        return { data, error }
    },

    // Validations
    createValidation: async (validationData) => {
        const { data, error } = await supabase
            .from('validations')
            .insert([validationData])
            .select()
        return { data, error }
    },

    getValidations: async (participantId) => {
        const { data, error } = await supabase
            .from('validations')
            .select('*')
            .eq('participant_id', participantId)
        return { data, error }
    },

    // Resources
    getResources: async () => {
        const { data, error } = await supabase
            .from('resources')
            .select('*')
            .order('created_at', { ascending: false })
        return { data, error }
    },

    createResource: async (resourceData) => {
        const { data, error } = await supabase
            .from('resources')
            .insert([resourceData])
            .select()
        return { data, error }
    },

    // Notifications
    getNotifications: async () => {
        const { data, error } = await supabase
            .from('notifications')
            .select('*')
            .order('created_at', { ascending: false })
        return { data, error }
    },

    createNotification: async (notificationData) => {
        const { data, error } = await supabase
            .from('notifications')
            .insert([notificationData])
            .select()
        return { data, error }
    }
}
