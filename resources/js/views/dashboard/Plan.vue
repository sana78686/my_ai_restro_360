<template>
  <div class="container py-5">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3">Plans</h1>
      <button @click="showCreateModal = true" class="btn btn-primary">
        Add Plan
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="d-flex justify-content-center align-items-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Plans Table -->
    <div v-else class="card shadow-sm">
      <div class="table-responsive">
        <table class="table mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Name</th>
              <th scope="col">Price</th>
              <th scope="col">Currency</th>
              <th scope="col">Interval</th>
              <th scope="col">Created At</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="plans.length === 0">
              <td colspan="6" class="text-center text-muted">
                No plans found
              </td>
            </tr>
            <tr v-for="plan in plans" :key="plan.id">
              <td>{{ plan.name }}</td>
              <td>{{ plan.price }}</td>
              <td>{{ plan.currency }}</td>
              <td>{{ plan.interval }}</td>
              <td>{{ formatDate(plan.created_at) }}</td>
              <td>
                <button @click="editPlan(plan)" class="btn btn-link p-0 me-2">Edit</button>
                <button @click="deletePlan(plan.id)" class="btn btn-link text-danger p-0">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Plan Modal -->
    <div class="modal" tabindex="-1" :class="{ 'show d-block': showCreateModal }" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Plan</h5>
            <button type="button" class="btn-close" @click="showCreateModal = false"></button>
          </div>
          <form @submit.prevent="createPlan">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" v-model="newPlan.name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" v-model="newPlan.price" class="form-control" min="0" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Currency</label>
                <input type="text" v-model="newPlan.currency" class="form-control" placeholder="aud" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Interval</label>
                <select v-model="newPlan.interval" class="form-select">
                  <option value="month">Month</option>
                  <option value="year">Year</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Features</label>
                <input type="text" v-model="newPlan.features" class="form-control" placeholder="Feature A, Feature B">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="isProcessing">
                {{ isProcessing ? 'Saving...' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Plan Modal -->
    <div class="modal" tabindex="-1" :class="{ 'show d-block': showEditModal }" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Plan</h5>
            <button type="button" class="btn-close" @click="showEditModal = false"></button>
          </div>
          <form @submit.prevent="updatePlan">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" v-model="editingPlan.name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" v-model="editingPlan.price" class="form-control" min="0" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Currency</label>
                <input type="text" v-model="editingPlan.currency" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Interval</label>
                <select v-model="editingPlan.interval" class="form-select">
                  <option value="month">Month</option>
                  <option value="year">Year</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Features</label>
                <input type="text" v-model="editingPlan.features" class="form-control" placeholder="Feature A, Feature B, Feature C">
                <small class="form-text text-muted">Separate features with commas. Current: {{ Array.isArray(editingPlan.features) ? editingPlan.features.join(', ') : (editingPlan.features || 'None') }}</small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="isProcessing">
                {{ isProcessing ? 'Updating...' : 'Update' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';

// Use window.axios to ensure we use the configured instance with baseURL
const axios = window.axios || require('axios');

export default {
  name: 'Plans',
  setup() {
    const plans = ref([]);
    const loading = ref(false);
    const isProcessing = ref(false);
    const showCreateModal = ref(false);
    const showEditModal = ref(false);

    const newPlan = ref({
      name: '',
      price: 0,
      currency: 'aud',
      interval: 'month',
      features: ''
    });

    const editingPlan = ref({
      id: null,
      name: '',
      price: 0,
      currency: 'aud',
      interval: 'month',
      features: ''
    });

    const fetchPlans = async () => {
      loading.value = true;
      try {
        const endpoint = '/dashboard/plansDashboard';
        const baseURL = axios.defaults?.baseURL || window.axios?.defaults?.baseURL || '/api';
        const fullUrl = baseURL + endpoint;
        console.log('📡 Fetching plans from:', endpoint, '(baseURL:', baseURL, 'full URL:', fullUrl + ')');
        const res = await axios.get(endpoint);
        console.log('✅ Plans response:', res.data);
        plans.value = res.data.plans || res.data.data || [];
      } catch (err) {
        console.error('❌ Error fetching plans:', err);
        console.error('❌ Request URL:', err.config?.url);
        console.error('❌ Response:', err.response);
        Swal.fire('Error', err.response?.data?.message || err.response?.data?.error || 'Failed to fetch plans', 'error');
      } finally {
        loading.value = false;
      }
    };

    const createPlan = async () => {
      isProcessing.value = true;
      try {
        const payload = {
          ...newPlan.value,
          features: newPlan.value.features ? newPlan.value.features.split(',').map(f => f.trim()).filter(f => f) : []
        };
        const endpoint = '/dashboard/plansDashboardStore';
        const baseURL = axios.defaults?.baseURL || window.axios?.defaults?.baseURL || '/api';
        const fullUrl = baseURL + endpoint;

        console.log('📡 Creating plan at:', endpoint);
        console.log('📡 BaseURL:', baseURL);
        console.log('📡 Full URL will be:', fullUrl);
        console.log('📦 Payload:', payload);

        // Safety check: ensure endpoint is correct
        if (!endpoint.startsWith('/dashboard')) {
          console.error('❌ CRITICAL ERROR: Endpoint must start with /dashboard!', endpoint);
          throw new Error('Invalid endpoint configuration');
        }

        // Make the request
        await axios.post(endpoint, payload);
        Swal.fire('Success', 'Plan created', 'success');
        showCreateModal.value = false;
        fetchPlans();
        newPlan.value = { name: '', price: 0, currency: 'aud', interval: 'month', features: '' };
      } catch (err) {
        console.error('❌ Error creating plan:', err);
        console.error('❌ Request URL:', err.config?.url);
        console.error('❌ Response:', err.response);
        Swal.fire('Error', err.response?.data?.message || err.response?.data?.error || 'Failed to create plan', 'error');
      } finally {
        isProcessing.value = false;
      }
    };

    const editPlan = (plan) => {
      editingPlan.value = {
        ...plan,
        features: Array.isArray(plan.features) ? plan.features.join(', ') : (plan.features || '')
      };
      showEditModal.value = true;
    };

    const updatePlan = async () => {
      isProcessing.value = true;
      try {
        const payload = {
          name: editingPlan.value.name,
          price: editingPlan.value.price,
          currency: editingPlan.value.currency,
          interval: editingPlan.value.interval,
          features: editingPlan.value.features ? (typeof editingPlan.value.features === 'string'
            ? editingPlan.value.features.split(',').map(f => f.trim()).filter(f => f)
            : editingPlan.value.features) : []
        };
        const endpoint = `/dashboard/plansDashboard/${editingPlan.value.id}`;
        const baseURL = axios.defaults?.baseURL || window.axios?.defaults?.baseURL || '/api';
        const fullUrl = baseURL + endpoint;
        console.log('📡 Updating plan at:', endpoint, '(baseURL:', baseURL, 'full URL:', fullUrl + ')');
        console.log('📦 Payload:', payload);

        // Double-check: if endpoint doesn't start with /dashboard, force it
        if (!endpoint.startsWith('/dashboard')) {
          console.error('❌ ERROR: Endpoint should start with /dashboard!', endpoint);
        }

        await axios.put(endpoint, payload);
        Swal.fire('Success', 'Plan updated', 'success');
        showEditModal.value = false;
        fetchPlans();
      } catch (err) {
        console.error('❌ Error updating plan:', err);
        console.error('❌ Request URL:', err.config?.url);
        console.error('❌ Response:', err.response);
        Swal.fire('Error', err.response?.data?.message || err.response?.data?.error || 'Failed to update plan', 'error');
      } finally {
        isProcessing.value = false;
      }
    };

    const deletePlan = async (id) => {
      const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      });
      if (result.isConfirmed) {
        try {
          const endpoint = `/dashboard/plansDashboard/${id}`;
          const baseURL = axios.defaults?.baseURL || window.axios?.defaults?.baseURL || '/api';
          const fullUrl = baseURL + endpoint;
          console.log('📡 Deleting plan at:', endpoint, '(baseURL:', baseURL, 'full URL:', fullUrl + ')');

          // Double-check: if endpoint doesn't start with /dashboard, force it
          if (!endpoint.startsWith('/dashboard')) {
            console.error('❌ ERROR: Endpoint should start with /dashboard!', endpoint);
          }

          await axios.delete(endpoint);
          Swal.fire('Deleted!', 'Plan has been deleted.', 'success');
          fetchPlans();
        } catch (err) {
          console.error('❌ Error deleting plan:', err);
          console.error('❌ Request URL:', err.config?.url);
          console.error('❌ Response:', err.response);
          Swal.fire('Error', err.response?.data?.message || err.response?.data?.error || 'Failed to delete plan', 'error');
        }
      }
    };

    const formatDate = (date) => new Date(date).toLocaleDateString();

    onMounted(fetchPlans);

    return {
      plans, loading, isProcessing,
      showCreateModal, showEditModal,
      newPlan, editingPlan,
      fetchPlans, createPlan, editPlan, updatePlan, deletePlan, formatDate
    };
  }
};
</script>

<style scoped>
/* Optional: add your custom styles here */
</style>
