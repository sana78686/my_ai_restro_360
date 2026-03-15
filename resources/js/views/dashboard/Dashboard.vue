<template>
    <div class="dashboard">
        <!-- Stats Overview -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ stats.totalTenants }}</h3>
                        <p>{{ $t("main_dashboard.stats.totalTenants") }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ stats.activeTenants }}</h3>
                        <p>{{ $t("main_dashboard.stats.activeTenants") }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ stats.pendingTenants }}</h3>
                        <p>{{ $t("main_dashboard.stats.pendingTenants") }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-danger">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ stats.suspendedTenants }}</h3>
                        <p>{{ $t("main_dashboard.stats.suspendedTenants") }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Tenants -->
            <div class="col-md-6">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="card-title mb-0">
                            {{ $t("dashboard.recentTenants") }}
                        </h5>
                        <router-link
                            to="/dashboard/tenants"
                            class="btn btn-sm btn-primary"
                        >
                            {{ $t("common.viewAll") }}
                        </router-link>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ $t("tenants.list.name") }}</th>
                                        <th>{{ $t("tenants.list.owner") }}</th>
                                        <th>{{ $t("tenants.list.status") }}</th>
                                        <th>{{ $t("common.actions") }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="tenant in recentTenants"
                                        :key="tenant.id"
                                    >
                                        <td>
                                            <div
                                                class="d-flex align-items-center"
                                            >
                                                <img
                                                    :src="
                                                        tenant.logo_url ||
                                                        tenant.logo ||
                                                        '/images/default-restaurant.png'
                                                    "
                                                    class="tenant-logo me-2"
                                                    :alt="tenant.name"
                                                />
                                                {{ tenant.name }}
                                            </div>
                                        </td>
                                        <td>{{ tenant.owner_name }}</td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="
                                                    getStatusBadgeClass(
                                                        tenant.status
                                                    )
                                                "
                                            >
                                                {{ tenant.status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button
                                                    v-if="
                                                        tenant.status ===
                                                        'pending'
                                                    "
                                                    class="btn btn-sm btn-success"
                                                    @click="
                                                        approveTenant(tenant)
                                                    "
                                                >
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button
                                                    v-if="
                                                        tenant.status ===
                                                        'active'
                                                    "
                                                    class="btn btn-sm btn-warning"
                                                    @click="
                                                        suspendTenant(tenant)
                                                    "
                                                >
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                                <button
                                                    v-if="
                                                        tenant.status ===
                                                        'suspended'
                                                    "
                                                    class="btn btn-sm btn-success"
                                                    @click="
                                                        activateTenant(tenant)
                                                    "
                                                >
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Stats -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            {{ $t("dashboard.systemStats") }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <div
                                class="list-group-item d-flex justify-content-between align-items-center"
                            >
                                {{ $t("main_dashboard.stats.totalUsers") }}
                                <span class="badge bg-primary rounded-pill">{{
                                    stats.totalUsers
                                }}</span>
                            </div>
                            <div
                                class="list-group-item d-flex justify-content-between align-items-center"
                            >
                                {{ $t("main_dashboard.stats.totalRoles") }}
                                <span class="badge bg-primary rounded-pill">{{
                                    stats.totalRoles
                                }}</span>
                            </div>

                            <div
                                class="list-group-item d-flex justify-content-between align-items-center"
                            >
                                {{ $t("main_dashboard.stats.activeUsers") }}
                                <span class="badge bg-success rounded-pill">{{
                                    stats.activeUsersToday
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            {{ $t("main_dashboard.quickActions.title") }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <router-link
                                    to="/dashboard/tenants"
                                    class="quick-action-card"
                                >
                                    <i class="fas fa-store"></i>
                                    <span>{{
                                        $t(
                                            "main_dashboard.quickActions.manageTenants"
                                        )
                                    }}</span>
                                </router-link>
                            </div>
                            <div class="col-6">
                                <router-link
                                    to="/dashboard/users"
                                    class="quick-action-card"
                                >
                                    <i class="fas fa-users"></i>
                                    <span>{{
                                        $t(
                                            "main_dashboard.quickActions.manageUsers"
                                        )
                                    }}</span>
                                </router-link>
                            </div>
                            <div class="col-6">
                                <router-link
                                    to="/dashboard/roles"
                                    class="quick-action-card"
                                >
                                    <i class="fas fa-user-shield"></i>
                                    <span>{{
                                        $t(
                                            "main_dashboard.quickActions.manageRoles"
                                        )
                                    }}</span>
                                </router-link>
                            </div>
                            <div class="col-6">
                                <a
                                    href="#"
                                    class="quick-action-card"
                                    @click.prevent="clearCache"
                                >
                                    <i class="fas fa-sync"></i>
                                    <span>{{
                                        $t(
                                            "main_dashboard.quickActions.clearCache"
                                        )
                                    }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

export default {
    name: "Dashboard",
    setup() {
        const stats = ref({
            totalTenants: 0,
            activeTenants: 0,
            pendingTenants: 0,
            suspendedTenants: 0,
            totalUsers: 0,
            totalRoles: 0,
            totalPermissions: 0,
            activeUsersToday: 0,
        });
        const recentTenants = ref([]);
        const loading = ref(true);
        const error = ref(null);

        const fetchDashboardData = async () => {
            loading.value = true;
            error.value = null;
            try {
                const statsRes = await axios.get("/dashboard/stats");
                if (statsRes.data && statsRes.data.data) {
                    stats.value = statsRes.data.data;
                }
                const tenantsRes = await axios.get("/dashboard/recent-tenants");
                if (tenantsRes.data && tenantsRes.data.data) {
                    // Only show tenants created today
                    const today = new Date().toISOString().slice(0, 10);
                    recentTenants.value = tenantsRes.data.data.filter(
                        (t) =>
                            t.created_at && t.created_at.slice(0, 10) === today
                    );
                }
            } catch (err) {
                error.value = "Failed to load dashboard data.";
                console.error("Error fetching dashboard data:", err);
            } finally {
                loading.value = false;
            }
        };

        const getStatusBadgeClass = (status) => {
            const classes = {
                active: "bg-success",
                pending: "bg-warning",
                suspended: "bg-danger",
                trial: "bg-secondary",
                inactive: "bg-secondary",
            };
            return classes[status] || "bg-secondary";
        };

        const approveTenant = async (tenant) => {
            const result = await Swal.fire({
                title: "Are you sure?",
                text: "Do you want to approve this tenant?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, approve",
                cancelButtonText: "Cancel",
            });
            if (result.isConfirmed) {
                try {
                    await axios.patch(
                        `/dashboard/tenants/${tenant.id}/status`,
                        { status: "active" }
                    );
                    await fetchDashboardData();
                    Swal.fire({
                        icon: "success",
                        title: "Approved",
                        text: "Tenant has been approved.",
                    });
                } catch (error) {
                    console.error("Error approving tenant:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to approve tenant.",
                    });
                }
            }
        };

        const suspendTenant = async (tenant) => {
            const result = await Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to suspend this tenant?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, suspend",
                cancelButtonText: "Cancel",
            });
            if (result.isConfirmed) {
                try {
                    await axios.patch(
                        `/dashboard/tenants/${tenant.id}/status`,
                        { status: "suspended" }
                    );
                    await fetchDashboardData();
                    Swal.fire({
                        icon: "success",
                        title: "Suspended",
                        text: "Tenant has been suspended.",
                    });
                } catch (error) {
                    console.error("Error suspending tenant:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to suspend tenant.",
                    });
                }
            }
        };

        const activateTenant = async (tenant) => {
            const result = await Swal.fire({
                title: "Are you sure?",
                text: "Do you want to activate this tenant?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, activate",
                cancelButtonText: "Cancel",
            });
            if (result.isConfirmed) {
                try {
                    await axios.patch(
                        `/dashboard/tenants/${tenant.id}/status`,
                        { status: "active" }
                    );
                    await fetchDashboardData();
                    Swal.fire({
                        icon: "success",
                        title: "Activated",
                        text: "Tenant has been activated.",
                    });
                } catch (error) {
                    console.error("Error activating tenant:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to activate tenant.",
                    });
                }
            }
        };

        const clearCache = async () => {
            try {
                await axios.post("/clear-cache");
                alert("Cache cleared successfully");
            } catch (error) {
                console.error("Error clearing cache:", error);
            }
        };

        onMounted(() => {
            fetchDashboardData();

            // Watch for error and show SweetAlert if error occurs
            watch(
                () => error.value,
                (val) => {
                    if (val) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: val,
                        });
                    }
                }
            );
        });

        return {
            stats,
            recentTenants,
            loading,
            error,
            getStatusBadgeClass,
            approveTenant,
            suspendTenant,
            activateTenant,
            clearCache,
        };
    },
};
</script>

<style scoped>
.dashboard {
    padding: 20px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.stat-icon i {
    color: white;
    font-size: 1.5rem;
}

.stat-info h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.stat-info p {
    margin: 0;
    color: #6c757d;
}

.tenant-logo {
    width: 30px;
    height: 30px;
    object-fit: cover;
    border-radius: 4px;
}

.quick-action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    text-decoration: none;
    color: #343a40;
    transition: all 0.3s;
}

.quick-action-card:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.quick-action-card i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5em 0.75em;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.btn-group .btn i {
    font-size: 0.875rem;
}
</style>
