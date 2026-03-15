<template>
    <div class="subscriptions">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Total Subscriptions</h6>
                                <h3 class="mb-0">{{ stats.total_subscriptions || 0 }}</h3>
                            </div>
                            <i class="fas fa-list fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Active</h6>
                                <h3 class="mb-0">{{ stats.active_subscriptions || 0 }}</h3>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Pending</h6>
                                <h3 class="mb-0">{{ stats.pending_subscriptions || 0 }}</h3>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Expired</h6>
                                <h3 class="mb-0">{{ stats.expired_subscriptions || 0 }}</h3>
                            </div>
                            <i class="fas fa-times-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="filters.search"
                            placeholder="Search by tenant, plan..."
                            @input="debounceSearch"
                        />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" v-model="filters.status" @change="fetchSubscriptions">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="pending_payment">Pending Payment</option>
                            <option value="expired">Expired</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tenant</label>
                        <select class="form-select" v-model="filters.tenant_id" @change="fetchSubscriptions">
                            <option value="">All Tenants</option>
                            <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                                {{ tenant.business_name || tenant.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="filters.date_from"
                            @change="fetchSubscriptions"
                        />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="filters.date_to"
                            @change="fetchSubscriptions"
                        />
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-secondary w-100" @click="resetFilters">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscriptions Table -->
        <div class="card">
            <div class="card-body">
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div v-else-if="subscriptions.length === 0" class="text-center py-5">
                    <p class="text-muted">No subscriptions found.</p>
                </div>

                <div v-else class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tenant</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days Remaining</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="subscription in subscriptions" :key="subscription.id">
                                <td>
                                    <div>
                                        <strong>{{ subscription.tenant?.business_name || subscription.tenant?.name || 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ subscription.tenant?.owner_email || 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ subscription.plan?.name || 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">${{ formatCurrency(subscription.plan?.price * 100 || 0) }}</small>
                                </td>
                                <td>
                                    <span class="badge" :class="getStatusBadgeClass(subscription.status)">
                                        {{ subscription.status }}
                                    </span>
                                </td>
                                <td>{{ formatDate(subscription.starts_at) }}</td>
                                <td>{{ formatDate(subscription.ends_at) }}</td>
                                <td>
                                    <span :class="getDaysRemainingClass(subscription.ends_at)">
                                        {{ getDaysRemaining(subscription.ends_at) }}
                                    </span>
                                </td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-info me-1"
                                        @click="viewSubscriptionDetails(subscription)"
                                        title="View Details"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav v-if="pagination.last_page > 1" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">Previous</a>
                        </li>
                        <li
                            v-for="page in pagination.last_page"
                            :key="page"
                            class="page-item"
                            :class="{ active: page === pagination.current_page }"
                        >
                            <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Subscription Details Modal -->
        <div v-if="selectedSubscription" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" @click.self="closeModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Subscription Details</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="selectedSubscription">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Subscription ID:</strong>
                                    <p class="text-muted">{{ selectedSubscription.id }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    <p>
                                        <span class="badge" :class="getStatusBadgeClass(selectedSubscription.status)">
                                            {{ selectedSubscription.status }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Tenant:</strong>
                                    <p class="text-muted" v-if="selectedSubscription.tenant">
                                        {{ selectedSubscription.tenant.business_name || selectedSubscription.tenant.name }}
                                    </p>
                                    <p class="text-muted" v-else>N/A</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Email:</strong>
                                    <p class="text-muted">{{ selectedSubscription.tenant?.owner_email || 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Plan:</strong>
                                    <p class="text-muted" v-if="selectedSubscription.plan">
                                        {{ selectedSubscription.plan.name }} - ${{ formatCurrency(selectedSubscription.plan.price) }}
                                    </p>
                                    <p class="text-muted" v-else>N/A</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Stripe Subscription ID:</strong>
                                    <p class="text-muted font-monospace small" v-if="selectedSubscription.stripe_subscription_id">
                                        {{ selectedSubscription.stripe_subscription_id }}
                                    </p>
                                    <p class="text-muted" v-else>N/A</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Start Date:</strong>
                                    <p class="text-muted">{{ formatDate(selectedSubscription.starts_at) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>End Date:</strong>
                                    <p class="text-muted">{{ formatDate(selectedSubscription.ends_at) }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Days Remaining:</strong>
                                    <p :class="getDaysRemainingClass(selectedSubscription.ends_at)">
                                        {{ getDaysRemaining(selectedSubscription.ends_at) }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Created At:</strong>
                                    <p class="text-muted">{{ formatDate(selectedSubscription.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    name: 'Subscriptions',
    data() {
        return {
            subscriptions: [],
            tenants: [],
            stats: {},
            loading: false,
            selectedSubscription: null,
            filters: {
                search: '',
                status: '',
                tenant_id: '',
                date_from: '',
                date_to: '',
            },
            pagination: {
                current_page: 1,
                last_page: 1,
                total: 0,
            },
            searchTimeout: null,
        };
    },
    mounted() {
        this.fetchSubscriptions();
        this.fetchStats();
        this.fetchTenants();
    },
    methods: {
        async fetchSubscriptions() {
            this.loading = true;
            try {
                const params = {
                    page: this.pagination.current_page,
                    per_page: 15,
                    ...this.filters,
                };

                const response = await axios.get('/dashboard/subscriptions', { params });
                
                if (response.data.success) {
                    this.subscriptions = response.data.data.data || [];
                    this.pagination = {
                        current_page: response.data.data.current_page || 1,
                        last_page: response.data.data.last_page || 1,
                        total: response.data.data.total || 0,
                    };
                }
            } catch (error) {
                console.error('Error fetching subscriptions:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load subscriptions'
                });
            } finally {
                this.loading = false;
            }
        },

        async fetchStats() {
            try {
                const response = await axios.get('/dashboard/subscriptions/stats');
                if (response.data.success) {
                    this.stats = response.data.data || {};
                }
            } catch (error) {
                console.error('Error fetching stats:', error);
            }
        },

        async fetchTenants() {
            try {
                const response = await axios.get('/dashboard/tenants');
                if (response.data.success) {
                    this.tenants = response.data.data || [];
                }
            } catch (error) {
                console.error('Error fetching tenants:', error);
            }
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.pagination.current_page = 1;
                this.fetchSubscriptions();
            }, 500);
        },

        resetFilters() {
            this.filters = {
                search: '',
                status: '',
                tenant_id: '',
                date_from: '',
                date_to: '',
            };
            this.pagination.current_page = 1;
            this.fetchSubscriptions();
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.fetchSubscriptions();
            }
        },

        viewSubscriptionDetails(subscription) {
            this.selectedSubscription = subscription;
        },

        closeModal() {
            this.selectedSubscription = null;
        },

        getStatusBadgeClass(status) {
            const classes = {
                active: 'bg-success',
                pending_payment: 'bg-warning',
                expired: 'bg-danger',
                cancelled: 'bg-secondary',
            };
            return classes[status] || 'bg-secondary';
        },

        getDaysRemaining(endDate) {
            if (!endDate) return 'N/A';
            const end = new Date(endDate);
            const today = new Date();
            const diff = Math.ceil((end - today) / (1000 * 60 * 60 * 24));
            
            if (diff < 0) return 'Expired';
            if (diff === 0) return 'Today';
            return `${diff} days`;
        },

        getDaysRemainingClass(endDate) {
            if (!endDate) return 'text-muted';
            const end = new Date(endDate);
            const today = new Date();
            const diff = Math.ceil((end - today) / (1000 * 60 * 60 * 24));
            
            if (diff < 0) return 'text-danger';
            if (diff <= 7) return 'text-warning';
            return 'text-success';
        },

        formatDate(date) {
            if (!date) return 'N/A';
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        },

        formatCurrency(amount) {
            return (amount / 100).toFixed(2);
        },
    }
}
</script>

<style scoped>
.subscriptions {
    padding: 0;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.modal.show {
    display: block !important;
}
</style>

