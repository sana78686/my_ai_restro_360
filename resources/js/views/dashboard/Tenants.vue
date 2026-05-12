<template>
    <div class="tenants-page">
        <div class="card tenants-card shadow-sm border-0">
            <div class="card-body">
                <div
                    class="row g-2 mb-3 align-items-stretch align-items-md-center tenants-toolbar"
                >
                    <div class="col-12 col-md-6 col-lg-5">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                v-model="searchQuery"
                                :placeholder="
                                    $t('tenants.filters.searchPlaceholder')
                                "
                            />
                            <span class="input-group-text bg-white">
                                <i
                                    class="fas fa-search text-muted"
                                    :title="$t('common.search')"
                                ></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-3 ms-md-auto">
                        <select
                            class="form-select w-100"
                            v-model="statusFilter"
                        >
                            <option value="">
                                {{ $t("tenants.filters.allStatus") }}
                            </option>
                            <option value="pending">
                                {{ $t("tenants.status.pending") }}
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

                <div class="tenants-table-responsive">
                    <table class="table table-hover tenants-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">
                                    {{ $t("tenants.list.name") }}
                                </th>
                                <th scope="col">{{ $t("tenants.list.owner") }}</th>
                                <th scope="col">{{ $t("tenants.list.domain") }}</th>
                                <th scope="col">
                                    {{ $t("tenants.list.status") }}
                                </th>
                                <th scope="col">
                                    {{ $t("tenants.list.verification") }}
                                </th>
                                <th scope="col" class="text-end">
                                    {{ $t("common.actions") }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="initialLoad && !tenants.length">
                                <td colspan="6" class="text-center py-4 text-muted">
                                    {{ $t("common.loading") }}
                                </td>
                            </tr>
                            <tr
                                v-for="tenant in filteredTenants"
                                :key="tenant.id"
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
                                        <div class="min-w-0">
                                            <div class="fw-semibold text-truncate">
                                                {{ tenant.name }}
                                            </div>
                                            <small class="text-muted d-block">
                                                {{ $t("created") }}:
                                                {{
                                                    formatDate(
                                                        tenant.created_at
                                                    )
                                                }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">
                                        {{ tenant.owner_name }}
                                    </div>
                                    <small class="text-muted text-break d-block">{{
                                        tenant.owner_email
                                    }}</small>
                                </td>
                                <td class="text-break">
                                    <span class="domain-cell">{{
                                        tenantHost(tenant)
                                    }}</span>
                                </td>
                                <td>
                                    <span
                                        :class="[
                                            'badge',
                                            getStatusBadgeClass(
                                                displayStatusKey(tenant)
                                            ),
                                        ]"
                                    >
                                        {{ displayStatusLabel(tenant) }}
                                    </span>
                                </td>
                                <td>
                                    <button
                                        v-if="tenant.owner_account_approved"
                                        type="button"
                                        class="btn btn-sm dash-verify dash-verify--ok"
                                        disabled
                                    >
                                        {{ $t("tenants.list.ownerVerified") }}
                                    </button>
                                    <button
                                        v-else
                                        type="button"
                                        class="btn btn-sm dash-verify dash-verify--pending"
                                        :disabled="
                                            verifyingTenantId === tenant.id ||
                                            loading
                                        "
                                        @click="approveOwnerAccount(tenant)"
                                    >
                                        {{
                                            verifyingTenantId === tenant.id
                                                ? $t("common.verifying")
                                                : $t(
                                                      "tenants.list.needVerification"
                                                  )
                                        }}
                                    </button>
                                </td>
                                <td class="text-end text-md-end">
                                    <select
                                        class="form-select form-select-sm tenants-action-select ms-md-auto"
                                        :disabled="loading"
                                        :value="tenant.status"
                                        @change="
                                            changeTenantStatus(
                                                tenant,
                                                $event.target.value
                                            )
                                        "
                                    >
                                        <option value="pending">
                                            {{
                                                $t("tenants.status.pending")
                                            }}
                                        </option>
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
                                            {{
                                                $t("tenants.status.suspended")
                                            }}
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr
                                v-if="
                                    !initialLoad &&
                                    !filteredTenants.length &&
                                    !loading
                                "
                            >
                                <td colspan="6" class="p-0">
                                    <div class="tenants-empty text-center py-5">
                                        <i
                                            class="fas fa-store fa-3x text-muted mb-3 opacity-50"
                                        ></i>
                                        <h4 class="h5 mb-2">
                                            {{ $t("tenants.empty.title") }}
                                        </h4>
                                        <p class="text-muted mb-0 px-3">
                                            {{
                                                searchQuery || statusFilter
                                                    ? $t("tenants.empty.filtered")
                                                    : $t("tenants.empty.noData")
                                            }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import axios from "axios";
import Swal from "sweetalert2";

export default {
    name: "Tenants",
    setup() {
        const { t } = useI18n();
        const tenants = ref([]);
        const searchQuery = ref("");
        const statusFilter = ref("");
        const loading = ref(false);
        const initialLoad = ref(true);
        const verifyingTenantId = ref(null);

        const tenantHost = (tenant) => {
            const sub = tenant.subdomain || "";
            let base = "localhost";
            if (typeof window !== "undefined") {
                if (window.TENANT_DOMAIN_BASE) {
                    base = String(window.TENANT_DOMAIN_BASE).replace(
                        /^\./,
                        ""
                    );
                } else if (window.MAIN_DOMAIN) {
                    base = String(window.MAIN_DOMAIN);
                }
            }
            return sub ? `${sub}.${base}` : base;
        };

        const displayStatusKey = (tenant) => {
            if (tenant.is_paid == 1 || tenant.is_paid === "1") {
                return "subscribed";
            }
            return tenant.status || "pending";
        };

        const displayStatusLabel = (tenant) => {
            if (tenant.is_paid == 1 || tenant.is_paid === "1") {
                return "subscribed";
            }
            return tenant.status || "—";
        };

        const filteredTenants = computed(() => {
            return tenants.value.filter((tenant) => {
                const q = (searchQuery.value || "").toLowerCase();
                const name = (tenant.name || "").toLowerCase();
                const ownerName = (tenant.owner_name || "").toLowerCase();
                const ownerEmail = (tenant.owner_email || "").toLowerCase();

                const matchesSearch =
                    !q ||
                    name.includes(q) ||
                    ownerName.includes(q) ||
                    ownerEmail.includes(q);

                const matchesStatus =
                    !statusFilter.value ||
                    tenant.status === statusFilter.value;

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
                if (error.response?.status !== 404) {
                    Swal.fire({
                        icon: "error",
                        title: t("common.error"),
                        text:
                            error.response?.data?.message ||
                            "Failed to load restaurants. Please try again.",
                    });
                } else {
                    tenants.value = [];
                }
            } finally {
                loading.value = false;
                initialLoad.value = false;
            }
        };

        const approveOwnerAccount = async (tenant) => {
            const result = await Swal.fire({
                title: t("tenants.approveOwnerDialog.title"),
                text: t("tenants.approveOwnerDialog.text"),
                icon: "question",
                showCancelButton: true,
                confirmButtonText: t("tenants.approveOwnerDialog.confirm"),
                cancelButtonText: t("tenants.approveOwnerDialog.cancel"),
            });
            if (!result.isConfirmed) return;

            verifyingTenantId.value = tenant.id;
            try {
                await axios.post(
                    `/dashboard/tenants/${tenant.id}/approve-account`
                );
                await fetchTenants();
                Swal.fire({
                    icon: "success",
                    title: t("common.success"),
                    text: t("tenants.approveOwnerSuccess"),
                });
            } catch (error) {
                console.error("Error approving owner:", error);
                Swal.fire({
                    icon: "error",
                    title: t("common.error"),
                    text:
                        error.response?.data?.message ||
                        "Failed to approve owner account",
                });
            } finally {
                verifyingTenantId.value = null;
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
                    title: t("common.success"),
                    text: `Tenant status changed to ${status}`,
                });
            } catch (error) {
                console.error("Error changing tenant status:", error);
                Swal.fire({
                    icon: "error",
                    title: t("common.error"),
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
                pending: "bg-warning text-dark",
                suspended: "bg-danger",
                trial: "bg-secondary",
                inactive: "bg-secondary",
                subscribed: "bg-info text-dark",
            };
            return classes[status] || "bg-secondary";
        };

        const formatDate = (date) => {
            if (!date) return "—";
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
            initialLoad,
            verifyingTenantId,
            filteredTenants,
            approveOwnerAccount,
            changeTenantStatus,
            getStatusBadgeClass,
            formatDate,
            tenantHost,
            displayStatusKey,
            displayStatusLabel,
        };
    },
};
</script>

<style scoped>
.tenants-page {
    padding: 0;
    max-width: 100%;
}

.tenants-card {
    border-radius: 14px;
    overflow: hidden;
}

.tenant-logo {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.tenants-table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}

.tenants-table {
    min-width: 920px;
    font-size: 0.9375rem;
}

.tenants-table thead th {
    background: #f9fafb;
    color: #374151;
    font-weight: 600;
    font-size: 0.8125rem;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
}

.tenants-table tbody td {
    vertical-align: middle;
    border-color: #f3f4f6;
}

.domain-cell {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas,
        "Liberation Mono", "Courier New", monospace;
    font-size: 0.8125rem;
}

.tenants-action-select {
    max-width: 11rem;
    min-width: 8.5rem;
}

.dash-verify {
    font-weight: 600;
    white-space: nowrap;
    border-radius: 999px;
    padding: 0.28rem 0.85rem;
    min-width: 8.25rem;
}

.dash-verify--ok {
    background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%);
    border: 1px solid #15803d;
    color: #fff !important;
    opacity: 1;
    cursor: default;
}

.dash-verify--ok:disabled {
    opacity: 1;
}

.dash-verify--pending {
    border: 1px solid #d97706;
    color: #b45309;
    background: #fffbeb;
}

.dash-verify--pending:hover:not(:disabled) {
    background: #fef3c7;
    color: #92400e;
}

.tenants-empty h4 {
    color: #4b5563;
}

@media (max-width: 767.98px) {
    .tenants-toolbar .form-select,
    .tenants-toolbar .input-group {
        font-size: 16px;
    }

    .tenants-action-select {
        max-width: 100%;
        width: 100%;
    }

    .tenants-table tbody td:last-child {
        text-align: left !important;
    }
}
</style>
