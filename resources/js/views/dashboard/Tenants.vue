<template>
    <div class="tenants">
        <div class="card">
            <div class="card-body">
                <!-- Search and Filter -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                v-model="searchQuery"
                                :placeholder="
                                    $t('tenants.filters.searchPlaceholder')
                                "
                            />
                            <button class="btn btn-outline-secondary">
                                <i
                                    class="fas fa-search"
                                    :title="$t('common.search')"
                                ></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" v-model="statusFilter">
                            <option value="">
                                {{ $t("tenants.filters.allStatus") }}
                            </option>
                            <option value="trial">
                                {{ $t("tenants.status.trial") }}
                            </option>
                            <option value="active">
                                {{ $t("tenants.status.active") }}
                            </option>
                            <option value="inactive">
                                {{
                                    $t("tenants.status.inactive") || "Inactive"
                                }}
                            </option>
                            <option value="suspended">
                                {{ $t("tenants.status.suspended") }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Tenants Table -->
                <div class="table-responsive" v-if="filteredTenants.length > 0">
                    <table class="table table-hover table-sm align-middle">
                        <thead>
                            <tr>
                                <th>{{ $t("tenants.list.name") }}</th>
                                <th class="d-none d-md-table-cell">
                                    {{ $t("tenants.list.owner") }}
                                </th>
                                <th class="d-none d-md-table-cell">
                                    {{ $t("tenants.list.domain") }}
                                </th>
                                <th class="d-none d-md-table-cell">
                                    {{ $t("tenants.list.status") }}
                                </th>
                                <th class="d-none d-md-table-cell">
                                    {{ $t("common.actions") }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="tenant in filteredTenants"
                                :key="tenant.id"
                                class="d-block d-md-table-row w-100 mb-2 mb-md-0"
                            >
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img
                                            :src="
                                                tenant.logo_url ||
                                                tenant.logo ||
                                                '/images/default-restaurant.png'
                                            "
                                            :alt="tenant.name"
                                            class="tenant-logo me-2"
                                        />
                                        <div>
                                            <div class="fw-bold">
                                                {{ tenant.name }}
                                            </div>
                                            <small class="text-muted"
                                                >Created:
                                                {{
                                                    formatDate(
                                                        tenant.created_at
                                                    )
                                                }}</small
                                            >
                                            <div class="d-md-none small mt-1">
                                                <div>
                                                    {{
                                                        $t(
                                                            "tenants.list.owner"
                                                        )
                                                    }}:
                                                    {{ tenant.owner_name }} ({{
                                                        tenant.owner_email
                                                    }})
                                                </div>
                                                <div>
                                                    {{
                                                        $t(
                                                            "tenants.list.domain"
                                                        )
                                                    }}:
                                                    {{
                                                        tenant.subdomain
                                                    }}.yourdomain.com
                                                </div>
                                                <div>
                                                    {{
                                                        $t(
                                                            "tenants.list.status"
                                                        )
                                                    }}:
                                                    <span
                                                        :class="[
                                                            'badge',
                                                            getStatusBadgeClass(
                                                                tenant.status
                                                            ),
                                                        ]"
                                                    >
                                                        <!-- {{ tenant.status }} -->
                                                        {{
                                                            tenant.is_paid ==
                                                                1 ||
                                                            tenant.is_paid ===
                                                                "1"
                                                                ? "subscribed"
                                                                : tenant.status
                                                        }}
                                                    </span>
                                                </div>
                                                <div class="mt-1">
                                                    <select
                                                        class="form-select form-select-sm"
                                                        :disabled="loading"
                                                        :value="tenant.status"
                                                        @change="
                                                            changeTenantStatus(
                                                                tenant,
                                                                $event.target
                                                                    .value
                                                            )
                                                        "
                                                    >
                                                        <option value="trial">
                                                            {{
                                                                $t(
                                                                    "tenants.status.trial"
                                                                )
                                                            }}
                                                        </option>
                                                        <option value="active">
                                                            {{
                                                                $t(
                                                                    "tenants.status.active"
                                                                )
                                                            }}
                                                        </option>
                                                        <option
                                                            value="inactive"
                                                        >
                                                            {{
                                                                $t(
                                                                    "tenants.status.inactive"
                                                                ) || "Inactive"
                                                            }}
                                                        </option>
                                                        <option
                                                            value="suspended"
                                                        >
                                                            {{
                                                                $t(
                                                                    "tenants.status.suspended"
                                                                )
                                                            }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <div>{{ tenant.owner_name }}</div>
                                    <small class="text-muted">{{
                                        tenant.owner_email
                                    }}</small>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    {{ tenant.subdomain }}.yourdomain.com
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <!-- <span :class="['badge', getStatusBadgeClass(tenant.status)]">
                    {{ tenant.status }}
                  </span> -->
                                    <span
                                        :class="[
                                            'badge',
                                            getStatusBadgeClass(
                                                tenant.is_paid == 1 ||
                                                    tenant.is_paid === '1'
                                                    ? 'subscribed'
                                                    : tenant.status
                                            ),
                                        ]"
                                    >
                                        {{
                                            tenant.is_paid == 1 ||
                                            tenant.is_paid === "1"
                                                ? "subscribed"
                                                : tenant.status
                                        }}
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <select
                                        class="form-select form-select-sm"
                                        :disabled="loading"
                                        :value="tenant.status"
                                        @change="
                                            changeTenantStatus(
                                                tenant,
                                                $event.target.value
                                            )
                                        "
                                    >
                                        <option value="trial">
                                            {{ $t("tenants.status.trial") }}
                                        </option>
                                        <option value="active">
                                            {{ $t("tenants.status.active") }}
                                        </option>
                                        <option value="inactive">
                                            {{
                                                $t("tenants.status.inactive") ||
                                                "Inactive"
                                            }}
                                        </option>
                                        <option value="suspended">
                                            {{ $t("tenants.status.suspended") }}
                                        </option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-else class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-store fa-3x text-muted mb-3"></i>
                        <h4>{{ $t("tenants.empty.title") }}</h4>
                        <p class="text-muted">
                            {{
                                searchQuery || statusFilter
                                    ? $t("tenants.empty.filtered")
                                    : $t("tenants.empty.noData")
                            }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

export default {
    name: "Tenants",
    setup() {
        const tenants = ref([]);
        const searchQuery = ref("");
        const statusFilter = ref("");
        const loading = ref(false);

        const filteredTenants = computed(() => {
            return tenants.value.filter((tenant) => {
                const matchesSearch =
                    !searchQuery.value ||
                    tenant.name
                        .toLowerCase()
                        .includes(searchQuery.value.toLowerCase()) ||
                    tenant.owner_name
                        .toLowerCase()
                        .includes(searchQuery.value.toLowerCase()) ||
                    tenant.owner_email
                        .toLowerCase()
                        .includes(searchQuery.value.toLowerCase());

                const matchesStatus =
                    !statusFilter.value || tenant.status === statusFilter.value;

                return matchesSearch && matchesStatus;
            });
        });

        const fetchTenants = async () => {
            try {
                loading.value = true;
                const response = await axios.get("/dashboard/tenants");
                tenants.value = response.data.data || [];
            } catch (error) {
                console.error("Error fetching tenants:", error);
                // Only show error if it's not a 404 (no tenants found)
                if (error.response?.status !== 404) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text:
                            error.response?.data?.message ||
                            "Failed to load restaurants. Please try again.",
                    });
                } else {
                    tenants.value = [];
                }
            } finally {
                loading.value = false;
            }
        };

        const approveTenant = async (tenant) => {
            try {
                loading.value = true;
                await axios.post(`/dashboard/tenants/${tenant.id}/approve`);
                await fetchTenants();
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Restaurant approved successfully",
                });
            } catch (error) {
                console.error("Error approving tenant:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text:
                        error.response?.data?.message ||
                        "Failed to approve restaurant",
                });
            } finally {
                loading.value = false;
            }
        };

        const suspendTenant = async (tenant) => {
            try {
                loading.value = true;
                await axios.post(`/dashboard/tenants/${tenant.id}/suspend`);
                await fetchTenants();
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Restaurant suspended successfully",
                });
            } catch (error) {
                console.error("Error suspending tenant:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text:
                        error.response?.data?.message ||
                        "Failed to suspend restaurant",
                });
            } finally {
                loading.value = false;
            }
        };

        const activateTenant = async (tenant) => {
            try {
                loading.value = true;
                await axios.post(`/dashboard/tenants/${tenant.id}/activate`);
                await fetchTenants();
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Restaurant activated successfully",
                });
            } catch (error) {
                console.error("Error activating tenant:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text:
                        error.response?.data?.message ||
                        "Failed to activate restaurant",
                });
            } finally {
                loading.value = false;
            }
        };

        const changeTenantStatus = async (tenant, status) => {
            try {
                loading.value = true;
                await axios.patch(`/dashboard/tenants/${tenant.id}/status`, {
                    status,
                });
                await fetchTenants();
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: `Tenant status changed to ${status}`,
                });
            } catch (error) {
                console.error("Error changing tenant status:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text:
                        error.response?.data?.message ||
                        "Failed to change tenant status",
                });
            } finally {
                loading.value = false;
            }
        };

        const getStatusBadgeClass = (status) => {
            const classes = {
                active: "bg-success",
                pending: "bg-warning",
                suspended: "bg-danger",
            };
            return classes[status] || "bg-secondary";
        };

        const formatDate = (date) => {
            return new Date(date).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        };

        onMounted(() => {
            fetchTenants();
        });

        return {
            tenants,
            searchQuery,
            statusFilter,
            loading,
            filteredTenants,
            approveTenant,
            suspendTenant,
            activateTenant,
            changeTenantStatus,
            getStatusBadgeClass,
            formatDate,
        };
    },
};
</script>

<style scoped>
.tenants {
    padding: 1rem;
}

.tenant-logo {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
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

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #6c757d;
}

.empty-state i {
    opacity: 0.5;
}

.empty-state h4 {
    margin: 1rem 0 0.5rem;
}

.empty-state p {
    max-width: 300px;
    margin: 0 auto;
}
</style>
