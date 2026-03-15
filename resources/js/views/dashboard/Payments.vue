<template>
    <div class="payments">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Total Revenue</h6>
                                <h3 class="mb-0">${{ formatCurrency(stats.total_revenue || 0) }}</h3>
                            </div>
                            <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">This Month</h6>
                                <h3 class="mb-0">${{ formatCurrency(stats.this_month_revenue || 0) }}</h3>
                            </div>
                            <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
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
                                <h3 class="mb-0">{{ stats.pending_payments || 0 }}</h3>
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
                                <h6 class="card-title mb-0">Failed</h6>
                                <h3 class="mb-0">{{ stats.failed_payments || 0 }}</h3>
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
                            placeholder="Search by tenant, email, or payment ID..."
                            @input="debounceSearch"
                        />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" v-model="filters.status">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="succeeded">Succeeded</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Payment Type</label>
                        <select class="form-select" v-model="filters.payment_type">
                            <option value="">All Types</option>
                            <option value="one_time">One Time</option>
                            <option value="recurring">Recurring</option>
                            <option value="upgrade">Upgrade</option>
                            <option value="downgrade">Downgrade</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tenant</label>
                        <select class="form-select" v-model="filters.tenant_id">
                            <option value="">All Tenants</option>
                            <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                                {{ tenant.business_name || tenant.name }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date Range</label>
                        <div class="input-group">
                            <input
                                type="date"
                                class="form-control"
                                v-model="filters.date_from"
                                placeholder="From"
                            />
                            <input
                                type="date"
                                class="form-control"
                                v-model="filters.date_to"
                                placeholder="To"
                            />
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button class="btn btn-primary me-2" @click="fetchPayments">
                            <i class="fas fa-search me-1"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary me-2" @click="resetFilters">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                        <button class="btn btn-success" @click="exportToCSV" :disabled="loading">
                            <i class="fas fa-download me-1"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Payments</h5>
                <span class="badge bg-secondary">{{ pagination.total }} total</span>
            </div>
            <div class="card-body">
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div v-else-if="payments.length === 0" class="text-center py-5">
                    <p class="text-muted">No payments found.</p>
                </div>

                <div v-else class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Tenant</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Payment ID</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="payment in payments" :key="payment.id">
                                <td>{{ formatDate(payment.created_at) }}</td>
                                <td>
                                    <div v-if="payment.tenant">
                                        <div class="fw-bold">{{ payment.tenant.business_name || payment.tenant.name }}</div>
                                        <small class="text-muted">{{ payment.email }}</small>
                                    </div>
                                    <span v-else class="text-muted">N/A</span>
                                </td>
                                <td>
                                    <span v-if="payment.plan">{{ payment.plan.name }}</span>
                                    <span v-else class="text-muted">N/A</span>
                                </td>
                                <td>
                                    <strong>${{ formatCurrency(payment.amount) }}</strong>
                                    <small class="text-muted d-block">{{ payment.currency?.toUpperCase() }}</small>
                                </td>
                                <td>
                                    <span class="badge" :class="getStatusBadgeClass(payment.status)">
                                        {{ payment.status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ formatPaymentType(payment.payment_type) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted font-monospace">
                                        {{ payment.stripe_payment_intent_id?.substring(0, 20) }}...
                                    </small>
                                </td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-outline-primary"
                                        @click="viewPaymentDetails(payment)"
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

        <!-- Payment Details Modal -->
        <div v-if="selectedPayment" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" @click.self="closeModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payment Details</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="selectedPayment">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Payment ID:</strong>
                                    <p class="text-muted">{{ selectedPayment.id }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    <p>
                                        <span class="badge" :class="getStatusBadgeClass(selectedPayment.status)">
                                            {{ selectedPayment.status }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Tenant:</strong>
                                    <p class="text-muted" v-if="selectedPayment.tenant">
                                        {{ selectedPayment.tenant.business_name || selectedPayment.tenant.name }}
                                    </p>
                                    <p class="text-muted" v-else>N/A</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Email:</strong>
                                    <p class="text-muted">{{ selectedPayment.email }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Plan:</strong>
                                    <p class="text-muted" v-if="selectedPayment.plan">{{ selectedPayment.plan.name }}</p>
                                    <p class="text-muted" v-else>N/A</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Amount:</strong>
                                    <p class="text-muted">${{ formatCurrency(selectedPayment.amount) }} {{ selectedPayment.currency?.toUpperCase() }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Payment Type:</strong>
                                    <p class="text-muted">{{ formatPaymentType(selectedPayment.payment_type) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Date:</strong>
                                    <p class="text-muted">{{ formatDate(selectedPayment.created_at) }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Stripe Payment Intent ID:</strong>
                                    <p class="text-muted font-monospace small">{{ selectedPayment.stripe_payment_intent_id }}</p>
                                </div>
                                <div class="col-md-6" v-if="selectedPayment.stripe_invoice_id">
                                    <strong>Stripe Invoice ID:</strong>
                                    <p class="text-muted font-monospace small">{{ selectedPayment.stripe_invoice_id }}</p>
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
    name: 'Payments',
    data() {
        return {
            payments: [],
            tenants: [],
            stats: {},
            loading: false,
            selectedPayment: null,
            filters: {
                search: '',
                status: '',
                payment_type: '',
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
        this.fetchPayments();
        this.fetchStats();
        this.fetchTenants();
    },
    methods: {
        async fetchPayments() {
            this.loading = true;
            try {
                const params = {
                    page: this.pagination.current_page,
                    per_page: 15,
                    ...this.filters,
                };
                
                // Remove empty filters
                Object.keys(params).forEach(key => {
                    if (params[key] === '' || params[key] === null) {
                        delete params[key];
                    }
                });

                const response = await axios.get('/dashboard/payments', { params });
                
                if (response.data.success) {
                    this.payments = response.data.data.data || [];
                    this.pagination = {
                        current_page: response.data.data.current_page || 1,
                        last_page: response.data.data.last_page || 1,
                        total: response.data.data.total || 0,
                    };
                }
            } catch (error) {
                console.error('Error fetching payments:', error);
                Swal.fire('Error', 'Failed to load payments', 'error');
            } finally {
                this.loading = false;
            }
        },
        async fetchStats() {
            try {
                const response = await axios.get('/dashboard/payments/stats');
                if (response.data.success) {
                    this.stats = response.data.data;
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
        async viewPaymentDetails(payment) {
            try {
                const response = await axios.get(`/dashboard/payments/${payment.id}`);
                if (response.data.success) {
                    this.selectedPayment = response.data.data;
                }
            } catch (error) {
                console.error('Error fetching payment details:', error);
                Swal.fire('Error', 'Failed to load payment details', 'error');
            }
        },
        closeModal() {
            this.selectedPayment = null;
        },
        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.fetchPayments();
            }
        },
        resetFilters() {
            this.filters = {
                search: '',
                status: '',
                payment_type: '',
                tenant_id: '',
                date_from: '',
                date_to: '',
            };
            this.pagination.current_page = 1;
            this.fetchPayments();
        },
        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.pagination.current_page = 1;
                this.fetchPayments();
            }, 500);
        },
        exportToCSV() {
            // Simple CSV export
            const headers = ['Date', 'Tenant', 'Plan', 'Amount', 'Currency', 'Status', 'Type', 'Payment ID'];
            const rows = this.payments.map(p => [
                this.formatDate(p.created_at),
                p.tenant?.business_name || p.tenant?.name || 'N/A',
                p.plan?.name || 'N/A',
                p.amount,
                p.currency?.toUpperCase() || 'USD',
                p.status,
                this.formatPaymentType(p.payment_type),
                p.stripe_payment_intent_id || 'N/A',
            ]);

            const csvContent = [
                headers.join(','),
                ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
            ].join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `payments_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
        },
        formatCurrency(amount) {
            return parseFloat(amount).toFixed(2);
        },
        formatDate(date) {
            if (!date) return 'N/A';
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        },
        formatPaymentType(type) {
            const types = {
                one_time: 'One Time',
                recurring: 'Recurring',
                upgrade: 'Upgrade',
                downgrade: 'Downgrade',
            };
            return types[type] || type || 'N/A';
        },
        getStatusBadgeClass(status) {
            const classes = {
                succeeded: 'bg-success',
                pending: 'bg-warning',
                failed: 'bg-danger',
            };
            return classes[status] || 'bg-secondary';
        },
    },
};
</script>

<style scoped>
.payments {
    padding: 1rem;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
}

.table-responsive {
    max-height: 600px;
    overflow-y: auto;
}

.modal.show {
    display: block;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}
</style>

