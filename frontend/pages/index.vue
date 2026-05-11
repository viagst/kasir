<template>
  <div class="login-container">
    <div class="login-box">
      <!-- Header -->
      <div class="header">
        <div class="logo">🛒</div>
        <h1>Kasir App</h1>
        <p class="subtitle">Sistem Point of Sale Modern</p>
      </div>

      <!-- Login Form -->
      <div class="form-container">
        <!-- Role Selection -->
        <div class="role-selection">
          <button 
            @click="selectRole('super_admin')" 
            :class="{ active: selectedRole === 'super_admin' }"
            class="role-btn"
          >
            <span class="role-icon">👑</span>
            <span class="role-text">Super Admin</span>
          </button>
          
          <button 
            @click="selectRole('kasir')" 
            :class="{ active: selectedRole === 'kasir' }"
            class="role-btn"
          >
            <span class="role-icon">💼</span>
            <span class="role-text">Kasir</span>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="login-form">
          <div class="input-group">
            <label for="email">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="Masukkan email"
              required
              class="input-field"
            >
          </div>

          <div class="input-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Masukkan password"
                required
                class="input-field"
              >
              <button 
                type="button" 
                @click="togglePassword"
                class="password-toggle"
              >
                {{ showPassword ? '🙈' : '👁️' }}
              </button>
            </div>
          </div>

          <!-- Login Button -->
          <button 
            type="submit" 
            :disabled="loading"
            class="submit-btn"
            :class="{ loading: loading }"
          >
            <span v-if="loading" class="loading-text">
              <span class="spinner"></span> Memproses...
            </span>
            <span v-else>🚀 Login Sekarang</span>
          </button>
        </form>

        <!-- Demo Credentials -->
        <div class="demo-credentials">
          <div class="demo-title">Akun Demo:</div>
          
          <div class="credential-card" @click="fillCredentials('super_admin')">
            <div class="credential-header">
              <span class="credential-icon">👑</span>
              <strong>Super Admin</strong>
            </div>
            <div class="credential-details">
              <div>Email: admin@kasir.com</div>
              <div>Password: password</div>
            </div>
            <button class="fill-btn" @click.stop="fillCredentials('super_admin')">
              Pakai Akun Ini
            </button>
          </div>

          <div class="credential-card" @click="fillCredentials('kasir')">
            <div class="credential-header">
              <span class="credential-icon">💼</span>
              <strong>Kasir</strong>
            </div>
            <div class="credential-details">
              <div>Email: kasir@kasir.com</div>
              <div>Password: password</div>
            </div>
            <button class="fill-btn" @click.stop="fillCredentials('kasir')">
              Pakai Akun Ini
            </button>
          </div>
        </div>

        <!-- Messages -->
        <div v-if="error" class="error-message">
          <div class="error-icon">⚠️</div>
          <div class="error-text">{{ error }}</div>
        </div>

        <div v-if="success" class="success-message">
          <div class="success-icon">✅</div>
          <div class="success-text">{{ success }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedRole: 'super_admin',
      form: {
        email: '',
        password: ''
      },
      showPassword: false,
      loading: false,
      error: '',
      success: '',
      
      // CREDENTIALS
      credentials: {
        super_admin: {
          email: 'admin@kasir.com',
          password: 'password',
          label: 'Super Admin'
        },
        kasir: {
          email: 'kasir@kasir.com',
          password: 'password',
          label: 'Kasir'
        }
      }
    }
  },

  mounted() {
    // Auto fill based on URL or localStorage
    const savedRole = localStorage.getItem('last_login_role') || 'super_admin'
    this.selectRole(savedRole)
    this.fillCredentials(savedRole)
    
    // Check if already logged in
    this.checkAuth()
  },

  methods: {
    selectRole(role) {
      this.selectedRole = role
      localStorage.setItem('last_login_role', role)
      this.fillCredentials(role)
    },

    fillCredentials(role) {
      this.selectedRole = role
      this.form.email = this.credentials[role].email
      this.form.password = this.credentials[role].password
      this.error = ''
      this.success = `Akun ${this.credentials[role].label} telah diisi`
      
      // Auto hide success message
      setTimeout(() => {
        this.success = ''
      }, 3000)
    },

    togglePassword() {
      this.showPassword = !this.showPassword
    },

    checkAuth() {
      const user = localStorage.getItem('user')
      if (user) {
        const userData = JSON.parse(user)
        if (userData.level === 'super_admin') {
          window.location.href = '/dashboard'
        } else {
          window.location.href = '/kasir'
        }
      }
    },

    async handleLogin() {
      // Reset messages
      this.error = ''
      this.success = ''
      this.loading = true

      // Validate
      if (!this.form.email || !this.form.password) {
        this.error = 'Email dan password harus diisi!'
        this.loading = false
        return
      }

      try {
        // API Call
        const response = await fetch('http://localhost/kasir1/kasir-backend/public/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(this.form)
        })

        const data = await response.json()
        console.log('Login Response:', data)

        if (data.success) {
          // Save user data
          localStorage.setItem('user', JSON.stringify(data.data.user))
          localStorage.setItem('token', data.data.token)
          localStorage.setItem('last_login_role', data.data.user.level)

          // Success message
          this.success = `Login berhasil! Selamat datang ${data.data.user.name}`

          // Redirect after 2 seconds
          setTimeout(() => {
            if (data.data.user.level === 'super_admin') {
              window.location.href = '/dashboard'
            } else {
              window.location.href = '/kasir'
            }
          }, 2000)

        } else {
          this.error = data.error || 'Login gagal. Coba lagi.'
        }

      } catch (err) {
        console.error('Login error:', err)
        this.error = 'Tidak dapat terhubung ke server. Pastikan:'
        this.error += '\n1. XAMPP Apache & MySQL jalan'
        this.error += '\n2. Backend Laravel di http://localhost/kasir1/kasir-backend/'
        this.error += '\n3. Cek console browser (F12) untuk detail error'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.login-box {
  background: white;
  border-radius: 20px;
  width: 100%;
  max-width: 500px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.header {
  background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
  color: white;
  padding: 30px;
  text-align: center;
}

.logo {
  font-size: 50px;
  margin-bottom: 10px;
}

h1 {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
}

.subtitle {
  margin: 5px 0 0;
  opacity: 0.9;
  font-size: 14px;
}

.form-container {
  padding: 30px;
}

.role-selection {
  display: flex;
  gap: 10px;
  margin-bottom: 25px;
}

.role-btn {
  flex: 1;
  padding: 15px;
  border: 2px solid #e0e0e0;
  background: white;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.3s; 
  font-size: 14px;
}

.role-btn:hover {
  border-color: #4CAF50;
  transform: translateY(-2px);
}

.role-btn.active {
  border-color: #4CAF50;
  background: #E8F5E9;
  color: #2E7D32;
}

.role-icon {
  font-size: 24px;
}

.role-text {
  font-weight: 600;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 25px;
}

.input-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #333;
}

.password-wrapper {
  position: relative;
}

.input-field {
  width: 100%;
  padding: 14px 50px 14px 15px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 16px;
  transition: all 0.3s;
  box-sizing: border-box;
}

.input-field:focus {
  outline: none;
  border-color: #4CAF50;
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.password-toggle {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #666;
}

.submit-btn {
  background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
  color: white;
  border: none;
  padding: 16px;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 10px;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(76, 175, 80, 0.3);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.submit-btn.loading {
  background: linear-gradient(135deg, #757575 0%, #424242 100%);
}

.loading-text {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255,255,255,0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.demo-credentials {
  background: #f8f9fa;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 20px;
}

.demo-title {
  font-weight: 600;
  margin-bottom: 15px;
  color: #333;
  font-size: 14px;
  text-align: center;
}

.credential-card {
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  padding: 15px;
  margin-bottom: 10px;
  cursor: pointer;
  transition: all 0.3s;
}

.credential-card:hover {
  border-color: #4CAF50;
  transform: translateX(5px);
}

.credential-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.credential-icon {
  font-size: 20px;
}

.credential-details {
  font-size: 12px;
  color: #666;
  margin-bottom: 10px;
}

.credential-details div {
  margin: 2px 0;
}

.fill-btn {
  width: 100%;
  padding: 8px;
  background: #E3F2FD;
  color: #1976D2;
  border: 1px solid #BBDEFB;
  border-radius: 5px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.fill-btn:hover {
  background: #BBDEFB;
}

.error-message {
  background: #FFEBEE;
  border: 1px solid #FFCDD2;
  color: #C62828;
  padding: 15px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 20px;
  font-size: 14px;
  white-space: pre-line;
}

.success-message {
  background: #E8F5E9;
  border: 1px solid #C8E6C9;
  color: #2E7D32;
  padding: 15px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 20px;
  font-size: 14px;
}

.error-icon, .success-icon {
  font-size: 20px;
}

.error-text, .success-text {
  flex: 1;
}
</style>