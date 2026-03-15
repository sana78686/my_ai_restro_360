<template>
  <div class="Products-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <button class="btn btn-outline-secondary me-2" @click="showDeletedModal">
          <i class="fas fa-trash-restore-alt me-1"></i> Deleted Products
        </button>
      </div>
      <h4 class="mb-0">Manage Products</h4>
      <button class="btn btn-add-Product" @click="showCreateModal">
        <i class="fas fa-plus me-2"></i>Add Product
      </button>
    </div>

    <!-- Filters and Column Filter -->
    <div class="mb-4">
      <div class="filter-body">
        <div class="row g-3 align-items-end flex-nowrap">
          <div class="col-md-6">
            <label class="form-label mb-1">Search</label>
            <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input" placeholder="Search by name, category, SKU, etc.">
          </div>
          <div class="col-md-6 d-flex align-items-end justify-content-end">
            <!-- Column Filter Dropdown -->
            <div class="column-filter-dropdown ms-auto">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" @click="toggleColumnDropdown" ref="dropdownBtn">
                <i class="fas fa-filter me-1"></i> Columns
              </button>
              <div class="dropdown-menu p-3" :class="{ show: showColumnDropdown }" style="min-width: 260px; max-height: 320px; overflow-y: auto;">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="selectAllCols" v-model="selectAllChecked" @change="toggleSelectAll">
                  <label class="form-check-label" for="selectAllCols">(Select All)</label>
                </div>
                <div v-for="col in allColumns" :key="col.key" class="form-check mb-1">
                  <input class="form-check-input" type="checkbox" :id="'col-' + col.key" v-model="tempSelectedColumns" :value="col.key">
                  <label class="form-check-label" :for="'col-' + col.key">{{ col.label }}</label>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                  <button class="btn btn-sm btn-primary" @click="applyColumnSelection">OK</button>
                  <button class="btn btn-sm btn-secondary" @click="cancelColumnSelection">Cancel</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
        <div v-else-if="products.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-box-open fa-3x mb-3" style="color: #bdbdbd;"></i>
          <div class="h5 mb-2">No Products Found</div>
          <div>No products match your search criteria.<br>Try adjusting your filters.</div>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover table-sm products-table">
            <thead>
              <tr>
                <th style="width: 32px;"></th>
                <th v-for="col in visibleColumns" :key="col.key">{{ col.label }}</th>
                <th>Actions</th>
              </tr>
            </thead>
            <draggable v-model="products" @end="onSortEnd" tag="tbody" :animation="200" handle=".drag-handle">
              <template #item="{ element: product }">
                <tr :key="product.id" :class="['d-block d-md-table-row w-100 mb-2 mb-md-0', { 'table-danger': product.deleted_at }]">
                  <td>
                    <span class="drag-handle" style="cursor: grab;"><i class="fas fa-grip-vertical"></i></span>
                  </td>
                  <td v-for="col in visibleColumns" :key="col.key">
                    <template v-if="col.key === 'category'">
                      {{ product.category?.name || 'None' }}
                    </template>
                    <template v-else-if="col.key === 'is_active'">
                      <input type="checkbox" disabled :checked="product.is_active">
                    </template>
                    <template v-else-if="col.key === 'is_featured'">
                      <input type="checkbox" disabled :checked="product.is_featured">
                    </template>
                    <template v-else-if="col.key === 'created_at'">
                      {{ new Date(product.created_at).toLocaleDateString() }}
                    </template>
                    <template v-else-if="col.key === 'status'">
                      <span class="badge" :class="product.status === 'active' ? 'bg-success' : 'bg-danger'">
                        {{ product.status === 'active' ? 'Active' : 'Inactive' }}
                      </span>
                    </template>
                    <template v-else>
                      {{ product[col.key] }}
                    </template>
                  </td>
                  <td>
                    <div class="btn-group">
                      <button class="btn btn-sm btn-action btn-view me-1" @click="viewProduct(product)"><i class="fas fa-eye"></i></button>
                      <button class="btn btn-sm btn-action btn-edit me-1" @click="editProduct(product)" v-if="!product.deleted_at"><i class="fas fa-edit"></i></button>
                      <button class="btn btn-sm btn-action btn-delete me-1" @click="confirmDelete(product)" v-if="!product.deleted_at"><i class="fas fa-trash"></i></button>
                      <button class="btn btn-sm btn-action btn-restore me-1" @click="restoreProduct(product)" v-if="product.deleted_at"><i class="fas fa-undo"></i></button>
                      <button class="btn btn-sm btn-action btn-hard-delete" @click="confirmHardDelete(product)" v-if="product.deleted_at"><i class="fas fa-times"></i></button>
                    </div>
                  </td>
                </tr>
              </template>
            </draggable>
          </table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="ProductModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content Product-modal-content">
          <div class="modal-header Product-modal-header">
            <h5 class="modal-title Product-modal-title">{{ isEditing ? 'Edit Product' : 'Add Product' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body Product-modal-body">
            <form @submit.prevent="saveProduct">
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                    <input id="productName" type="text" class="form-control" v-model="form.name" required placeholder="Product Name">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                    <select id="productCategory" class="form-select" v-model="form.category_id" required>
                      <option value="">Select Category</option>
                      <option v-for="cat in categoriesList" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                      </option>
                    </select>
                    <button class="btn btn-outline-success ms-2" type="button" @click="showQuickCategoryModal" title="Add Category"><i class="fas fa-plus"></i></button>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                    <input id="productPrice" type="number" class="form-control" v-model="form.price" required min="0" step="0.01" placeholder="Price">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-coins"></i></span>
                    <input id="productCost" type="number" class="form-control" v-model="form.cost_price" required min="0" step="0.01" placeholder="Cost Price">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-boxes"></i></span>
                    <input id="productStock" type="number" class="form-control" v-model="form.stock_quantity" required min="0" placeholder="Stock Quantity">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-exclamation-triangle"></i></span>
                    <input id="productMinStock" type="number" class="form-control" v-model="form.min_stock_level" required min="0" placeholder="Min Stock Level">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                    <input id="productSKU" type="text" class="form-control" v-model="form.sku" placeholder="SKU">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                    <input id="productBarcode" type="text" class="form-control" v-model="form.barcode" placeholder="Barcode">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="productActive" v-model="form.is_active">
                    <label class="form-check-label" for="productActive">Active</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="productFeatured" v-model="form.is_featured">
                    <label class="form-check-label" for="productFeatured">Featured</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                    <textarea id="productDescription" class="form-control" v-model="form.description" rows="3" placeholder="Product Description"></textarea>
                  </div>
                </div>
                <div class="col-12">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-image"></i></span>
                    <input type="file" class="form-control" @change="onImageChange">
                  </div>
                  <div v-if="form.imagePreview" class="mt-2">
                    <img :src="form.imagePreview" class="img-thumbnail" style="max-width: 200px;">
                  </div>
                </div>
              </div>
              <div class="modal-footer Product-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-submit">{{ isEditing ? 'Update' : 'Create' }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- View Product Modal -->
    <div class="modal fade" id="viewProductModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Product Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="Product-details">
              <p><strong>Name:</strong> {{ selectedProduct?.name }}</p>
              <p><strong>Description:</strong> {{ selectedProduct?.description }}</p>
              <p><strong>Category:</strong> {{ selectedProduct?.category?.name || 'None' }}</p>
              <p><strong>Price:</strong> {{ selectedProduct?.price }}</p>
              <p><strong>Cost Price:</strong> {{ selectedProduct?.cost_price }}</p>
              <p><strong>Stock Quantity:</strong> {{ selectedProduct?.stock_quantity }}</p>
              <p><strong>Min Stock Level:</strong> {{ selectedProduct?.min_stock_level }}</p>
              <p><strong>Status:</strong> {{ selectedProduct?.status }}</p>
              <p><strong>Active:</strong> {{ selectedProduct?.is_active ? 'Yes' : 'No' }}</p>
              <p><strong>Featured:</strong> {{ selectedProduct?.is_featured ? 'Yes' : 'No' }}</p>
              <p><strong>SKU:</strong> {{ selectedProduct?.sku }}</p>
              <p><strong>Barcode:</strong> {{ selectedProduct?.barcode }}</p>
              <p v-if="selectedProduct?.image"><strong>Image:</strong> <img :src="getImageUrl(selectedProduct.image)" class="img-thumbnail mt-2" style="max-width: 200px;"></p>
              <p><strong>Sort Order:</strong> {{ selectedProduct?.sort_order }}</p>
              <p><strong>Created At:</strong> {{ new Date(selectedProduct?.created_at).toLocaleString() }}</p>
              <p v-if="selectedProduct?.deleted_at">
                <strong>Deleted At:</strong> {{ new Date(selectedProduct?.deleted_at).toLocaleString() }}
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Deleted Products Modal -->
    <div class="modal fade" id="deletedProductsModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Deleted Products</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="deletedProducts.length === 0" class="text-center py-4 text-muted">
              <i class="fas fa-box-open fa-2x mb-2"></i>
              <div>No deleted products found.</div>
            </div>
            <div v-else class="table-responsive">
              <table class="table table-hover table-sm products-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th class="d-none d-md-table-cell">Category</th>
                    <th class="d-none d-md-table-cell">Status</th>
                    <th class="d-none d-md-table-cell">Created At</th>
                    <th class="d-none d-md-table-cell">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="product in deletedProducts" :key="product.id">
                    <td>{{ product.name }}</td>
                    <td>{{ product.category?.name || 'None' }}</td>
                    <td>{{ product.status }}</td>
                    <td>{{ new Date(product.deleted_at).toLocaleString() }}</td>
                    <td>
                      <button class="btn btn-sm btn-action btn-restore" @click="restoreProduct(product)"><i class="fas fa-undo"></i> Restore</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Add Category Modal -->
    <div class="modal fade" id="QuickCategoryModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="quickAddCategory">
              <div class="mb-3">
                <label class="form-label">Category Name</label>
                <input v-model="quickCategoryForm.name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Description</label>
                <input v-model="quickCategoryForm.description" type="text" class="form-control">
              </div>
              <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="quickCategoryActive" v-model="quickCategoryForm.is_active">
                <label class="form-check-label" for="quickCategoryActive">Active</label>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Add</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed, watch } from 'vue'
import { Modal } from 'bootstrap'
import axios from 'axios'
import Swal from 'sweetalert2'
import draggable from 'vuedraggable'

export default {
  name: 'Products',
  components: { draggable },
  setup() {
    const products = ref([])
    const deletedProducts = ref([])
    const categoriesList = ref([])
    const loading = ref(false)
    const filters = ref({ search: '', category_id: '', status: '' })
    const isEditing = ref(false)
    const form = ref({
      id: null,
      name: '',
      description: '',
      category_id: '',
      price: '',
      cost_price: '',
      stock_quantity: '',
      min_stock_level: '',
      status: 'active',
      is_active: true,
      is_featured: false,
      sku: '',
      barcode: '',
      image: null,
      imagePreview: null
    })
    const selectedProduct = ref(null)
    const quickCategoryForm = ref({ name: '', description: '', is_active: true })
    let productModal = null
    let viewProductModal = null
    let deletedProductsModal = null
    let quickCategoryModal = null

    const allColumns = [
      { key: 'name', label: 'Name' },
      { key: 'category', label: 'Category' },
      { key: 'price', label: 'Price' },
      { key: 'stock_quantity', label: 'Stock Quantity' },
      { key: 'min_stock_level', label: 'Min Stock Level' },
      { key: 'sku', label: 'SKU' },
      { key: 'barcode', label: 'Barcode' },
      { key: 'cost_price', label: 'Cost Price' },
      { key: 'status', label: 'Status' },
      { key: 'is_active', label: 'Active' },
      { key: 'is_featured', label: 'Featured' },
      { key: 'sort_order', label: 'Sort Order' },
      { key: 'created_at', label: 'Created At' },
    ]
    const COLUMN_CACHE_KEY = 'products_selected_columns_v1';
    const selectedColumns = ref(['name', 'category', 'price', 'stock_quantity', 'status'])
    const visibleColumns = ref(allColumns.filter(col => selectedColumns.value.includes(col.key)))
    // Column filter logic
    const showColumnDropdown = ref(false)
    const tempSelectedColumns = ref([...selectedColumns.value])
    const selectAllChecked = ref(false)
    const dropdownBtn = ref(null)
    const toggleColumnDropdown = () => {
      showColumnDropdown.value = !showColumnDropdown.value
      tempSelectedColumns.value = [...selectedColumns.value]
      updateSelectAllChecked()
    }
    const applyColumnSelection = () => {
      selectedColumns.value = [...tempSelectedColumns.value]
      visibleColumns.value = allColumns.filter(col => selectedColumns.value.includes(col.key))
      showColumnDropdown.value = false
    }
    const cancelColumnSelection = () => {
      tempSelectedColumns.value = [...selectedColumns.value]
      showColumnDropdown.value = false
    }
    const toggleSelectAll = () => {
      if (selectAllChecked.value) {
        tempSelectedColumns.value = allColumns.map(col => col.key)
      } else {
        tempSelectedColumns.value = []
      }
    }
    const updateSelectAllChecked = () => {
      selectAllChecked.value = tempSelectedColumns.value.length === allColumns.length
    }
    watch(tempSelectedColumns, updateSelectAllChecked)

    const fetchProducts = async () => {
      loading.value = true
      try {
        const { data } = await axios.get('/tenant/products', { params: filters.value })
        products.value = data.data.data || []
      } catch (e) {
        products.value = []
      }
      loading.value = false
    }

    const fetchCategories = async () => {
      try {
        const { data } = await axios.get('/tenant/categories')
        categoriesList.value = data.data.data || []
      } catch (e) {
        categoriesList.value = []
      }
    }

    const fetchDeletedProducts = async () => {
      try {
        const { data } = await axios.get('/tenant/products', { params: { only_deleted: true } })
        deletedProducts.value = data.data.data || []
      } catch (e) {
        deletedProducts.value = []
      }
    }

    const applyFilters = () => {
      fetchProducts()
    }

    const showCreateModal = () => {
      isEditing.value = false
      Object.assign(form.value, {
        id: null,
        name: '',
        description: '',
        category_id: '',
        price: '',
        cost_price: '',
        stock_quantity: '',
        min_stock_level: '',
        status: 'active',
        is_active: true,
        is_featured: false,
        sku: '',
        barcode: '',
        image: null,
        imagePreview: null
      })
      productModal = new Modal(document.getElementById('ProductModal'))
      productModal.show()
    }

    const getImageUrl = (imagePath) => {
      if (!imagePath) return null;
      const cleanPath = imagePath.replace(/^public[\\/]/, '');
      return `/storage/${cleanPath}`;
    }

    const editProduct = (product) => {
      isEditing.value = true
      Object.assign(form.value, {
        id: product.id,
        name: product.name,
        description: product.description,
        category_id: product.category_id,
        price: product.price,
        cost_price: product.cost_price,
        stock_quantity: product.stock_quantity,
        min_stock_level: product.min_stock_level,
        status: product.status,
        is_active: product.is_active,
        is_featured: product.is_featured,
        sku: product.sku,
        barcode: product.barcode,
        image: null,
        imagePreview: product.image ? getImageUrl(product.image) : null
      })
      productModal = new Modal(document.getElementById('ProductModal'))
      productModal.show()
    }

    const saveProduct = async () => {
      const fd = new FormData()
      for (const key in form.value) {
        if (key === 'imagePreview') continue
        if (form.value[key] !== null && form.value[key] !== undefined) {
          fd.append(key, form.value[key])
        }
      }
      try {
        if (isEditing.value) {
          await axios.post(`/tenant/products/${form.value.id}?_method=PUT`, fd)
          Swal.fire({ icon: 'success', title: 'Product updated' })
        } else {
          await axios.post('/tenant/products', fd)
          Swal.fire({ icon: 'success', title: 'Product created' })
        }
        fetchProducts()
        productModal.hide()
      } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save product' })
      }
    }

    const confirmDelete = (product) => {
      Swal.fire({
        title: 'Are you sure?',
        text: 'This will move the product to deleted list.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
      }).then(async (result) => {
        if (result.isConfirmed) {
          await axios.delete(`/tenant/products/${product.id}`)
          fetchProducts()
        }
      })
    }

    const confirmHardDelete = (product) => {
      Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete the product.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete permanently!'
      }).then(async (result) => {
        if (result.isConfirmed) {
          await axios.post(`/tenant/products/${product.id}/force-delete`)
          fetchDeletedProducts()
        }
      })
    }

    const restoreProduct = async (product) => {
      await axios.post(`/tenant/products/${product.id}/restore`)
      fetchProducts()
      fetchDeletedProducts()
    }

    const viewProduct = (product) => {
      selectedProduct.value = product
      viewProductModal = new Modal(document.getElementById('viewProductModal'))
      viewProductModal.show()
    }

    const showDeletedModal = () => {
      fetchDeletedProducts()
      deletedProductsModal = new Modal(document.getElementById('deletedProductsModal'))
      deletedProductsModal.show()
    }

    const onSortEnd = async () => {
      try {
        const order = products.value.map((prod, idx) => ({ id: prod.id, sort_order: idx }))
        await axios.post('/tenant/products/reorder', { order })
        await fetchProducts()
        Swal.fire({ icon: 'success', title: 'Order updated' })
      } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update order' })
      }
    }

    const onImageChange = (e) => {
      const file = e.target.files[0]
      if (file) {
        form.value.image = file
        form.value.imagePreview = URL.createObjectURL(file)
      }
    }

    const showQuickCategoryModal = () => {
      quickCategoryForm.value = { name: '', description: '', is_active: true }
      quickCategoryModal = new Modal(document.getElementById('QuickCategoryModal'))
      quickCategoryModal.show()
    }

    const quickAddCategory = async () => {
      try {
        const { data } = await axios.post('/tenant/categories', quickCategoryForm.value)
        categoriesList.value.push(data.data)
        form.value.category_id = data.data.id
        quickCategoryModal.hide()
        Swal.fire({ icon: 'success', title: 'Category added' })
      } catch (e) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to add category' })
      }
    }

    onMounted(() => {
      fetchProducts()
      fetchCategories()
      const cached = localStorage.getItem(COLUMN_CACHE_KEY)
      if (cached) {
        try {
          const parsed = JSON.parse(cached)
          if (Array.isArray(parsed) && parsed.every(k => allColumns.some(c => c.key === k))) {
            selectedColumns.value = parsed
            visibleColumns.value = allColumns.filter(col => selectedColumns.value.includes(col.key))
          }
        } catch {}
      }
    })

    watch(selectedColumns, (val) => {
      localStorage.setItem(COLUMN_CACHE_KEY, JSON.stringify(val))
    })

    return {
      products,
      deletedProducts,
      categoriesList,
      filters,
      loading,
      isEditing,
      form,
      selectedProduct,
      showCreateModal,
      editProduct,
      saveProduct,
      confirmDelete,
      confirmHardDelete,
      restoreProduct,
      viewProduct,
      showDeletedModal,
      applyFilters,
      onSortEnd,
      onImageChange,
      getImageUrl,
      quickCategoryForm,
      showQuickCategoryModal,
      quickAddCategory,
      showColumnDropdown,
      dropdownBtn,
      selectAllChecked,
      tempSelectedColumns,
      visibleColumns,
      allColumns
    }
  }
}
</script>

<style scoped>
.Products-page {
  max-width: 1200px;
  margin: 0 auto;
}
.Products-table th, .Products-table td {
  vertical-align: middle;
}
.btn-add-Product {
  background: #e91e63;
  color: #fff;
}
.btn-add-Product:hover {
  background: #c2185b;
  color: #fff;
}
.btn-action {
  background: #f5f5f5;
  color: #333;
  border: none;
}
.btn-action:hover {
  background: #e0e0e0;
  color: #e91e63;
}
</style>

<style scoped>
.Products-page {
  padding: 20px;
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
}

.filter-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(60,60,60,0.08);
  border: 1px solid #e0e0e0;
  padding: 1.5rem 2rem 1rem 2rem;
}
.filter-body {
  padding: 0;
}
.filter-input {
  border-radius: 8px;
  font-size: 15px;
  border: 1px solid #e0e0e0;
  background: #fafbfc;
}

.btn-add-Category {
  background: #1565c0;
  color: #fff;
  font-weight: 500;
  font-size: 15px;
  border-radius: 8px;
  padding: 0.6rem 1.6rem;
  box-shadow: 0 2px 8px rgba(21,101,192,0.08);
  border: none;
  transition: background 0.15s, box-shadow 0.15s;
}
.btn-add-Category:hover, .btn-add-Category:focus {
  background: #0d47a1;
  color: #fff;
  box-shadow: 0 4px 16px rgba(21,101,192,0.13);
}

.products-table {
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(60,60,60,0.06);
  font-size: 15px;
  border-radius: 8px;
  overflow: hidden;
}
.products-table thead th {
  background: #fafbfc;
  position: sticky;
  top: 0;
  z-index: 2;
  font-weight: 500;
  color: #b71c1c;
}
.products-table tbody tr:nth-child(even) {
  background: #f7f7f7;
}
.products-table tbody tr:hover {
  background: #fff3f3;
}

.btn-group .btn-action {
  padding: 0.25rem 0.5rem;
  font-size: 0.95rem;
  border-radius: 6px;
  border: none;
  transition: background 0.13s;
}
.btn-view { background: #e3f2fd; color: #1565c0; }
.btn-view:hover { background: #bbdefb; color: #0d47a1; }
.btn-edit { background: #e8f5e9; color: #388e3c; }
.btn-edit:hover { background: #c8e6c9; color: #1b5e20; }
.btn-delete { background: #ffebee; color: #c62828; }
.btn-delete:hover { background: #ffcdd2; color: #b71c1c; }
.btn-restore { background: #fffde7; color: #fbc02d; }
.btn-restore:hover { background: #fff9c4; color: #f9a825; }
.btn-hard-delete { background: #fbe9e7; color: #d84315; }
.btn-hard-delete:hover { background: #ffccbc; color: #bf360c; }

.Category-details p {
  margin-bottom: 0.5rem;
}

.table td {
  vertical-align: middle;
}

.Category-modal-content {
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
  background: #fff;
  border-radius: 0 0 12px 12px;
  border: 1px solid #e0e0e0;
  box-shadow: 0 8px 32px rgba(60,60,60,0.13);
  max-width: 650px;
  margin: auto;
  padding: 0;
}
.Category-modal-header {
  background: #c62828;
  color: #fff;
  border-radius: 0;
  border: none;
  padding: 1.2rem 2rem;
  position: relative;
}
.Category-modal-title {
  font-size: 1.25rem;
  font-weight: 400;
  letter-spacing: 0.01em;
}
.Category-modal-header .btn-close {
  filter: invert(1) grayscale(1) brightness(2);
  opacity: 1;
}
.Category-modal-body {
  background: #fafbfc;
  padding: 2rem 2rem 1.5rem 2rem;
}
.Category-modal-footer {
  border-top: 1px solid #ececec;
  padding: 1.2rem 2rem;
  background: #fafbfc;
  border-radius: 0 0 12px 12px;
  display: flex;
  justify-content: center;
}
.input-group-text {
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  color: #b71c1c;
  font-size: 1.1rem;
}
.btn-submit {
  background: #c62828;
  color: #fff;
  text-transform: uppercase;
  font-weight: 400;
  font-size: 15px;
  border: none;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(198,40,40,0.08);
  padding: 0.7rem 2.2rem;
  letter-spacing: 1px;
  transition: background 0.15s, box-shadow 0.15s;
}
.btn-submit:hover, .btn-submit:focus {
  background: #b71c1c;
  color: #fff;
  box-shadow: 0 4px 12px rgba(198,40,40,0.13);
}

.drag-handle {
  vertical-align: middle;
}

.column-filter-dropdown {
  position: relative;
}
.column-filter-dropdown .dropdown-menu {
  left: auto;
  right: 0;
  min-width: 260px;
  box-shadow: 0 4px 24px rgba(60,60,60,0.13);
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  z-index: 1052;
}
.column-filter-dropdown .form-check {
  user-select: none;
}
</style>