<template>
  <div class="admin-layout">
    <nav class="sidebar">
      <div class="logo">
        <h2>Admin Dashboard</h2>
      </div>
      <ul class="nav-links">
        <li>
          <router-link to="/admin/dashboard">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
          </router-link>
        </li>
        <li>
          <router-link to="/admin/orders">
            <i class="fas fa-shopping-cart"></i>
            Orders
          </router-link>
        </li>
        <li>
          <router-link to="/admin/products">
            <i class="fas fa-utensils"></i>
            Products
          </router-link>
        </li>
        <li>
          <router-link to="/admin/categories">
            <i class="fas fa-list"></i>
            Categories
          </router-link>
        </li>
        <li>
          <router-link to="/admin/customers">
            <i class="fas fa-users"></i>
            Customers
          </router-link>
        </li>
        <li>
          <router-link to="/admin/settings">
            <i class="fas fa-cog"></i>
            Settings
          </router-link>
        </li>
      </ul>
    </nav>

    <div class="main-content">
      <header class="topbar">
        <div class="search-bar">
          <input type="text" placeholder="Search...">
        </div>
        <div class="user-info">
          <span class="username">{{ user.name }}</span>
          <button @click="logout" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Logout
          </button>
        </div>
      </header>

      <main class="content">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: {
        name: 'Admin User'
      }
    }
  },
  methods: {
    async logout() {
      console.log('loging out');
       try {
        const token = localStorage.getItem('token');
        if (token) {
          await axios.post('/api/logout', {}, {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
        }
        // Clear storage and headers
        localStorage.clear();
        delete axios.defaults.headers.common['Authorization'];
        // Show success message with better styling
        await Swal.fire({
          icon: 'success',
          title: 'Logged Out Successfully',
          text: 'You have been logged out of your account.',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        });
        // Redirect to login after toast
        setTimeout(() => {
          router.push('/login');
        }, 200);
      } catch (error) {
        console.error('Logout error:', error);
        // Still clear storage and redirect even if API call fails
        localStorage.clear();
        delete axios.defaults.headers.common['Authorization'];
        router.push('/login');
      }
    }
  }
}
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 250px;
  background-color: #2c3e50;
  color: white;
  padding: 20px 0;
}

.logo {
  padding: 0 20px;
  margin-bottom: 30px;
}

.logo h2 {
  margin: 0;
  font-size: 1.5em;
}

.nav-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-links li {
  margin-bottom: 10px;
}

.nav-links a {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: white;
  text-decoration: none;
  transition: background-color 0.3s;
}

.nav-links a:hover {
  background-color: #34495e;
}

.nav-links a.router-link-active {
  background-color: #34495e;
}

.nav-links i {
  margin-right: 10px;
  width: 20px;
  text-align: center;
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.topbar {
  height: 60px;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;
}

.search-bar input {
  padding: 8px 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  width: 300px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 15px;
}

.username {
  font-weight: bold;
}

.logout-btn {
  padding: 8px 15px;
  background-color: #e74c3c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 5px;
}

.content {
  flex: 1;
  padding: 20px;
  background-color: #f8f9fa;
}
</style> 