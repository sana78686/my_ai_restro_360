<template>
  <div class="tenant-dashboard-layout">
    <!-- Top Bar -->
    <header class="td-header d-flex align-items-center justify-content-end px-4 py-2">
      <button class="btn btn-link text-white me-3"><i class="fas fa-bell"></i></button>
      <button class="btn btn-link text-white"><i class="fas fa-sign-out-alt"></i></button>
    </header>
    <div class="td-main d-flex justify-content-center align-items-start bg-light py-4">
      <!-- Sidebar -->
      <aside class="td-sidebar bg-white shadow rounded-3 me-4">
        <div class="td-user-info text-center py-4 border-bottom">
          <div class="td-avatar mx-auto mb-2">
            <i class="fas fa-user-circle fa-3x text-muted"></i>
          </div>
          <div class="td-user-name fw-bold">Emilio Verdines</div>
        </div>
        <nav class="td-nav flex-column mt-2">
          <router-link to="/dashboard" class="td-nav-link" active-class="active"><i class="fas fa-th-large"></i> <span>Dashboard</span></router-link>
          <router-link to="/dashboard/profile" class="td-nav-link" active-class="active"><i class="fas fa-user"></i> <span>Profile</span></router-link>
          <router-link to="/dashboard/password" class="td-nav-link" active-class="active"><i class="fas fa-key"></i> <span>Password Change</span></router-link>
          <router-link to="/dashboard/addresses" class="td-nav-link" active-class="active"><i class="fas fa-map-marker-alt"></i> <span>Addresses</span></router-link>
          <router-link to="/dashboard/favorites" class="td-nav-link" active-class="active"><i class="fas fa-heart"></i> <span>Favorites</span></router-link>
          <router-link to="/dashboard/reservations" class="td-nav-link" active-class="active"><i class="fas fa-calendar-alt"></i> <span>Reservations</span></router-link>
          <router-link to="/dashboard/orders" class="td-nav-link" active-class="active"><i class="fas fa-clipboard-list"></i> <span>Orders</span></router-link>
          <router-link to="/dashboard/logout" class="td-nav-link" active-class="active"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></router-link>
        </nav>
      </aside>
      <!-- Main Content -->
      <main class="td-content bg-white rounded-3 shadow p-4 flex-grow-1">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
// import { ref, onMounted } from 'vue'
// import axios from 'axios'

// const users = ref([])

// onMounted(() => {
//   // This runs when the component loads
//   navigator.geolocation.getCurrentPosition(
//     async (pos) => {
//       try {
//         const token = localStorage.getItem('token')

//         const res = await axios.get('http://pathan-hotel-multan-road.localhost:8000/api/tenant/check/location', {
//           params: {
//             latitude: pos.coords.latitude,
//             longitude: pos.coords.longitude,
//           },
//           headers: { Authorization: `Bearer ${token}` },
//         })

//         users.value = res.data
//         console.log('Users:', users.value) // like dd()
//         debugger // pause in dev tools
//       } catch (err) {
//         console.error('API error:', err)
//       }
//     },
//     (err) => {
//       console.error('Geolocation error:', err)
//     }
//   )
// })


</script>

<style scoped>
.tenant-dashboard-layout {
  min-height: 100vh;
  background: #f5f6fa;
  display: flex;
  flex-direction: column;
}
.td-header {
  background: #d32f2f;
  color: #fff;
  height: 54px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  position: sticky;
  top: 0;
  z-index: 100;
}
.td-main {
  flex: 1 1 0%;
  min-height: 0;
  padding-top: 2rem;
}
.td-sidebar {
  width: 250px;
  min-height: 500px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.07);
  display: flex;
  flex-direction: column;
  align-items: stretch;
}
.td-user-info {
  background: #fafafa;
  border-radius: 12px 12px 0 0;
}
.td-avatar {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.5rem;
}
.td-user-name {
  font-size: 1.1rem;
  color: #444;
}
.td-nav {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
  padding: 1rem 0.5rem;
}
.td-nav-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  color: #444;
  padding: 0.7rem 1.2rem;
  border-radius: 8px;
  font-size: 1.05rem;
  font-weight: 500;
  text-decoration: none;
  transition: background 0.15s, color 0.15s;
}
.td-nav-link i {
  font-size: 1.2rem;
  color: #b71c1c;
}
.td-nav-link.active, .td-nav-link:hover {
  background: #f5f5f5;
  color: #d32f2f;
}
.td-content {
  min-width: 0;
  min-height: 500px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.07);
  margin-left: 0;
  flex: 1 1 0%;
}
@media (max-width: 991.98px) {
  .td-main {
    flex-direction: column;
    align-items: stretch;
    padding: 1rem 0.5rem;
  }
  .td-sidebar {
    width: 100%;
    margin-bottom: 1.5rem;
  }
  .td-content {
    margin-left: 0;
    padding: 1rem;
  }
}
</style> 