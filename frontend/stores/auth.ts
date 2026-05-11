import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any,
    token: null as string | null,
    isAuthenticated: false
  }),

  actions: {
    async login(credentials: { email: string; password: string }) {
      try {
        console.log('Sending login request...', credentials)
        
        const response = await fetch('http://localhost:8000/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(credentials)
        })

        console.log('Response status:', response.status)
        
        const data = await response.json()
        console.log('Response data:', data)

        if (data.success) {
          this.user = data.data.user
          this.token = data.data.token
          this.isAuthenticated = true
          
          localStorage.setItem('token', data.data.token)
          localStorage.setItem('user', JSON.stringify(data.data.user))
          
          return { success: true, data: data.data }
        } else {
          return { success: false, error: data.error }
        }
      } catch (error: any) {
        console.error('Login error:', error)
        return { 
          success: false, 
          error: 'Terjadi kesalahan saat login: ' + error.message 
        }
      }
    },

    checkAuth() {
      const token = localStorage.getItem('token')
      const userData = localStorage.getItem('user')
      
      if (token && userData) {
        this.token = token
        this.user = JSON.parse(userData)
        this.isAuthenticated = true
        return true
      }
      return false
    },

    logout() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
  }
})