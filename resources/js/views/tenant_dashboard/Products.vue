<template>
    <div class="Products-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <button
                    class="btn btn-outline-secondary me-2"
                    @click="showDeletedModal"
                >
                    <i class="fas fa-trash-restore-alt me-1"></i> {{ $t('tenant_dashboard.products.actions.deletedItems') }}
                </button>
            </div>
            <h4 class="mb-0">{{ $t('tenant_dashboard.products.title') }}</h4>
            <button class="btn btn-add-Product" @click="showCreateModal">
                <i class="fas fa-plus me-2"></i>{{ $t('tenant_dashboard.products.actions.addItem') }}
            </button>
        </div>

        <!-- Filters and Column Filter -->
        <div class="mb-4">
            <div class="filter-body">
                <div class="row g-3 align-items-end flex-nowrap">
                    <div class="col-md-6">
                        <label class="form-label mb-1">{{ $t('tenant_dashboard.products.search.label') }}</label>
                        <input
                            v-model="filters.search"
                            @input="applyFilters"
                            type="text"
                            class="form-control filter-input"
                            :placeholder="$t('tenant_dashboard.products.search.placeholder')"
                        />
                    </div>
                    <div
                        class="col-md-6 d-flex align-items-end justify-content-end"
                    >
                        <!-- Column Filter Dropdown -->
                        <div class="dropdown column-filter-dropdown ms-auto">
                            <button
                                class="btn btn-outline-secondary dropdown-toggle"
                                type="button"
                                @click="toggleColumnDropdown"
                                ref="dropdownBtn"
                            >
                                <i class="fas fa-filter me-1"></i> {{ $t('tenant_dashboard.products.columns.title') }}
                            </button>
                            <div
                                class="dropdown-menu p-3"
                                :class="{ show: showColumnDropdown }"
                                style="
                                    min-width: 260px;
                                    max-height: 320px;
                                    overflow-y: auto;
                                "
                            >
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="selectAllCols"
                                        v-model="selectAllChecked"
                                        @change="toggleSelectAll"
                                    />
                                    <label
                                        class="form-check-label"
                                        for="selectAllCols"
                                        >{{ $t('tenant_dashboard.products.columns.selectAll') }}</label
                                    >
                                </div>
                                <div
                                    v-for="col in allColumns"
                                    :key="col.key"
                                    class="form-check mb-1"
                                >
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        :id="'col-' + col.key"
                                        v-model="tempSelectedColumns"
                                        :value="col.key"
                                    />
                                    <label
                                        class="form-check-label"
                                        :for="'col-' + col.key"
                                        >{{ $t(`tenant_dashboard.products.columns.${col.key}`) }}</label
                                    >
                                </div>
                                <div
                                    class="d-flex justify-content-end gap-2 mt-3"
                                >
                                    <button
                                        class="btn btn-sm btn-primary"
                                        @click="applyColumnSelection"
                                    >
                                        {{ $t('tenant_dashboard.dashboard_common.ok') }}
                                    </button>
                                    <button
                                        class="btn btn-sm btn-secondary"
                                        @click="cancelColumnSelection"
                                    >
                                        {{ $t('tenant_dashboard.dashboard_common.cancel') }}
                                    </button>
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
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">{{ $t('tenant_dashboard.dashboard_common.loading') }}</span>
                    </div>
                </div>
                <div
                    v-else-if="products.length === 0"
                    class="text-center py-5 text-muted"
                >
                    <i
                        class="fas fa-box-open fa-3x mb-3"
                        style="color: #bdbdbd"
                    ></i>
                    <div class="h5 mb-2">{{ $t('tenant_dashboard.products.empty.title') }}</div>
                    <div>
                        {{ $t('tenant_dashboard.products.empty.message') }}
                    </div>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-hover table-sm products-table">
                        <thead>
                            <tr>
                                <th style="width: 32px"></th>
                                <th>{{ $t('tenant_dashboard.products.columns.name') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.products.columns.category') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.products.columns.price') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.products.columns.cost_price') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.products.columns.stock_quantity') }}</th>
                                <th class="d-none d-md-table-cell">
                                    {{ $t('tenant_dashboard.products.columns.min_stock_level') }}
                                </th>
                                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.products.columns.status') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.products.columns.is_active') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.products.columns.is_featured') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.products.columns.sku') }}</th>
                                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.products.columns.barcode') }}</th>
                                <th class="d-none d-md-table-cell">
                                    {{ $t('tenant_dashboard.products.columns.sort_order') }}
                                </th>
                                <th class="d-none d-md-table-cell">
                                    {{ $t('tenant_dashboard.products.columns.created_at') }}
                                </th>
                                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.products.columns.actions') }}</th>
                            </tr>
                        </thead>
                        <draggable
                            v-model="products"
                            @end="onSortEnd"
                            tag="tbody"
                            :animation="200"
                            handle=".drag-handle"
                        >
                            <template #item="{ element: product }">
                                <tr
                                    :key="product.id"
                                    :class="[
                                        'd-block d-md-table-row w-100 mb-2 mb-md-0',
                                        { 'table-danger': product.deleted_at },
                                    ]"
                                >
                                    <td>
                                        <span
                                            class="drag-handle"
                                            style="cursor: grab"
                                            ><i class="fas fa-grip-vertical"></i
                                        ></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">
                                            {{ product.name }}
                                        </div>
                                        <!-- Product Image in List -->
                                        <div
                                            v-if="
                                                product.images &&
                                                product.images.length
                                            "
                                            class="mt-1"
                                        >
                                            <img
                                                :src="product.images[0].url"
                                                class="img-thumbnail"
                                                style="
                                                    max-width: 50px;
                                                    max-height: 50px;
                                                "
                                                :alt="product.name"
                                            />
                                        </div>
                                        <div class="small text-muted">
                                            {{ product.description }}
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        {{ product.category?.name || $t('tenant_dashboard.products.common.none') }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        {{ product.price }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        {{ product.cost_price }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        {{ product.stock_quantity }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        {{ product.min_stock_level }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span
                                            class="badge"
                                            :class="
                                                product.status === 'active'
                                                    ? 'bg-success'
                                                    : 'bg-danger'
                                            "
                                        >
                                            {{
                                                product.status === "active"
                                                    ? $t('tenant_dashboard.products.status.active')
                                                    : $t('tenant_dashboard.products.status.inactive')
                                            }}
                                        </span>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <input
                                            type="checkbox"
                                            disabled
                                            :checked="product.is_active"
                                        />
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <input
                                            type="checkbox"
                                            disabled
                                            :checked="product.is_featured"
                                        />
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        {{ product.sku }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        {{ product.barcode }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        {{ product.sort_order }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        {{
                                            new Date(
                                                product.created_at
                                            ).toLocaleDateString()
                                        }}
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <div class="btn-group">
                                            <button
                                                class="btn btn-sm btn-action btn-view me-1"
                                                @click="viewProduct(product)"
                                                :title="$t('tenant_dashboard.products.actions.view')"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-action btn-edit me-1"
                                                @click="editProduct(product)"
                                                v-if="!product.deleted_at"
                                                :title="$t('tenant_dashboard.products.actions.edit')"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                           <button
                                                class="btn btn-sm btn-action btn-delete me-1"
                                                @click="confirmDelete(product)"
                                                v-if="!product.deleted_at"
                                                :title="$t('products.actions.delete')"
                                                :disabled="deletingProduct"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-action btn-restore me-1"
                                                @click="restoreProduct(product)"
                                                v-if="product.deleted_at"
                                                :title="$t('products.actions.restore')"
                                                :disabled="restoringProduct"
                                            >
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-action btn-hard-delete"
                                                @click="confirmHardDelete(product)"
                                                v-if="product.deleted_at"
                                                :title="$t('products.actions.hardDelete')"
                                                :disabled="hardDeletingProduct"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </draggable>
                    </table>
                </div>
                <!-- Pagination -->
                <div
                    v-if="pagination.total > 0"
                    class="d-flex justify-content-between align-items-center mt-3"
                >
                    <div class="text-muted">
                        {{ $t('tenant_dashboard.products.pagination.showing', { from: pagination.from, to: pagination.to, total: pagination.total }) }}
                    </div>
                    <nav v-if="pagination.last_page > 1">
                        <ul class="pagination mb-0">
                            <li
                                class="page-item"
                                :class="{
                                    disabled: pagination.current_page === 1,
                                }"
                            >
                                <button
                                    class="page-link"
                                    @click="
                                        goToPage(pagination.current_page - 1)
                                    "
                                    :disabled="pagination.current_page === 1"
                                >
                                    &laquo;
                                </button>
                            </li>
                            <li
                                v-for="page in paginationWindow"
                                :key="page"
                                class="page-item"
                                :class="{
                                    active: page === pagination.current_page,
                                }"
                            >
                                <button
                                    class="page-link"
                                    @click="goToPage(page)"
                                >
                                    {{ page }}
                                </button>
                            </li>
                            <li
                                class="page-item"
                                :class="{
                                    disabled:
                                        pagination.current_page ===
                                        pagination.last_page,
                                }"
                            >
                                <button
                                    class="page-link"
                                    @click="
                                        goToPage(pagination.current_page + 1)
                                    "
                                    :disabled="
                                        pagination.current_page ===
                                        pagination.last_page
                                    "
                                >
                                    &raquo;
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div class="modal fade" id="ProductModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content Product-modal-content">
                    <div class="modal-header Product-modal-header">
                        <h5 class="modal-title Product-modal-title">
                            {{ isEditing ? $t('tenant_dashboard.products.modal.editTitle') : $t('tenant_dashboard.products.modal.createTitle') }}
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            @click="resetForm"
                        ></button>
                    </div>
                    <div class="modal-body Product-modal-body">
                        <form @submit.prevent="saveProduct">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="fas fa-tag"></i
                                        ></span>
                                        <input
                                            id="productName"
                                            type="text"
                                            class="form-control"
                                            v-model="form.name"
                                            required
                                            :placeholder="$t('tenant_dashboard.products.form.name.placeholder')"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="fas fa-layer-group"></i
                                        ></span>
                                        <select
                                            id="productCategory"
                                            class="form-select"
                                            v-model="form.category_id"
                                            required
                                        >
                                            <option value="">
                                                {{ $t('tenant_dashboard.products.form.category.placeholder') }}
                                            </option>
                                            <option
                                                v-for="cat in categoriesList"
                                                :key="cat.id"
                                                :value="cat.id"
                                            >
                                                {{ cat.name }}
                                            </option>
                                        </select>
                                        <button
                                            class="btn btn-outline-success ms-2"
                                            type="button"
                                            @click="showQuickCategoryModal"
                                            :title="$t('tenant_dashboard.products.form.category.addTitle')"
                                        >
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="fas fa-dollar-sign"></i
                                        ></span>
                                        <input
                                            id="productPrice"
                                            type="number"
                                            class="form-control"
                                            v-model="form.price"
                                            required
                                            min="0"
                                            step="0.01"
                                            :placeholder="$t('tenant_dashboard.products.form.price.placeholder')"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="fas fa-coins"></i
                                        ></span>
                                        <input
                                            id="productCost"
                                            type="number"
                                            class="form-control"
                                            v-model="form.cost_price"
                                            required
                                            min="0"
                                            step="0.01"
                                            :placeholder="$t('tenant_dashboard.products.form.cost_price.placeholder')"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="fas fa-boxes"></i
                                        ></span>
                                        <input
                                            id="productStock"
                                            type="number"
                                            class="form-control"
                                            v-model="form.stock_quantity"
                                            required
                                            min="0"
                                            :placeholder="$t('tenant_dashboard.products.form.stock_quantity.placeholder')"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i
                                                class="fas fa-exclamation-triangle"
                                            ></i
                                        ></span>
                                        <input
                                            id="productMinStock"
                                            type="number"
                                            class="form-control"
                                            v-model="form.min_stock_level"
                                            required
                                            min="0"
                                            :placeholder="$t('tenant_dashboard.products.form.min_stock_level.placeholder')"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="fas fa-barcode"></i
                                        ></span>
                                        <input
                                            id="productSKU"
                                            type="text"
                                            class="form-control"
                                            v-model="form.sku"
                                            :placeholder="$t('tenant_dashboard.products.form.sku.placeholder')"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="fas fa-barcode"></i
                                        ></span>
                                        <input
                                            id="productBarcode"
                                            type="text"
                                            class="form-control"
                                            v-model="form.barcode"
                                            :placeholder="$t('tenant_dashboard.products.form.barcode.placeholder')"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mt-2">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="productActive"
                                            v-model="form.is_active"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="productActive"
                                            >{{ $t('tenant_dashboard.products.form.is_active.label') }}</label
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mt-2">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="productFeatured"
                                            v-model="form.is_featured"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="productFeatured"
                                            >{{ $t('tenant_dashboard.products.form.is_featured.label') }}</label
                                        >
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="fas fa-align-left"></i
                                        ></span>
                                        <textarea
                                            id="productDescription"
                                            class="form-control"
                                            v-model="form.description"
                                            rows="3"
                                            :placeholder="$t('tenant_dashboard.products.form.description.placeholder')"
                                        ></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text"
                                            ><i class="fas fa-image"></i
                                        ></span>
                                        <input
                                            type="file"
                                            class="form-control"
                                            @change="onImagesChange"
                                            multiple
                                            accept="image/*"
                                            ref="fileInput"
                                        />
                                    </div>
                                    <!-- Selected new image previews -->
                                    <div
                                        v-if="
                                            form.imagePreviews &&
                                            form.imagePreviews.length
                                        "
                                        class="mt-2 d-flex gap-2 flex-wrap"
                                    >
                                        <div
                                            v-for="(
                                                p, idx
                                            ) in form.imagePreviews"
                                            :key="idx"
                                            class="position-relative"
                                        >
                                            <img
                                                :src="p"
                                                class="img-thumbnail"
                                                style="max-width: 120px"
                                            />
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger position-absolute"
                                                style="top: 4px; right: 4px"
                                                @click="removeNewImage(idx)"
                                                :title="$t('tenant_dashboard.products.actions.removeImage')"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Existing images (when editing) -->
                                    <div
                                        v-if="
                                            form.existingImages &&
                                            form.existingImages.length
                                        "
                                        class="mt-2 d-flex gap-2 flex-wrap"
                                    >
                                        <div
                                            v-for="(
                                                imgObj, idx
                                            ) in form.existingImages"
                                            :key="
                                                imgObj.id || imgObj.url || idx
                                            "
                                            class="position-relative"
                                        >
                                            <img
                                                :src="imgObj.url || imgObj"
                                                class="img-thumbnail"
                                                style="max-width: 120px"
                                            />
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-warning position-absolute"
                                                style="top: 4px; right: 4px"
                                                @click="markRemoveExisting(idx)"
                                                :title="$t('tenant_dashboard.products.actions.removeImage')"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer Product-modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                    @click="resetForm"
                                >
                                    {{ $t('tenant_dashboard.dashboard_common.cancel') }}
                                </button>
                                <button 
                                    type="submit" 
                                    class="btn btn-submit" 
                                    :disabled="savingProduct"
                                >
                                    <span v-if="savingProduct">
                                        <i class="fas fa-spinner fa-spin me-1"></i>
                                        {{ isEditing ? $t('tenant_dashboard.products.common.saving') : $t('tenant_dashboard.products.common.creating') }}
                                    </span>
                                    <span v-else>
                                        {{ isEditing ? $t('tenant_dashboard.dashboard_common.update') : $t('tenant_dashboard.dashboard_common.create') }}
                                    </span>
                                </button>
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
                        <h5 class="modal-title">{{ $t('tenant_dashboard.products.modal.viewTitle') }}</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="Product-details">
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.name.label') }}:</strong>
                                {{ selectedProduct?.name }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.description.label') }}:</strong>
                                {{ selectedProduct?.description }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.category.label') }}:</strong>
                                {{ selectedProduct?.category?.name || $t('tenant_dashboard.dashboard_common.none') }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.price.label') }}:</strong>
                                {{ selectedProduct?.price }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.cost_price.label') }}:</strong>
                                {{ selectedProduct?.cost_price }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.stock_quantity.label') }}:</strong>
                                {{ selectedProduct?.stock_quantity }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.min_stock_level.label') }}:</strong>
                                {{ selectedProduct?.min_stock_level }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.status.label') }}:</strong>
                                {{ selectedProduct?.status }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.is_active.label') }}:</strong>
                                {{ selectedProduct?.is_active ? $t('tenant_dashboard.dashboard_common.yes') : $t('tenant_dashboard.dashboard_common.no') }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.is_featured.label') }}:</strong>
                                {{
                                    selectedProduct?.is_featured ? $t('tenant_dashboard.dashboard_common.yes') : $t('tenant_dashboard.dashboard_common.no')
                                }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.sku.label') }}:</strong> {{ selectedProduct?.sku }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.barcode.label') }}:</strong>
                                {{ selectedProduct?.barcode }}
                            </p>
                            <div
                                v-if="
                                    selectedProduct?.images &&
                                    selectedProduct.images.length
                                "
                            >
                                <p><strong>{{ $t('tenant_dashboard.products.form.images.label') }}:</strong></p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <img
                                        v-for="img in selectedProduct.images"
                                        :key="img.id"
                                        :src="img.url"
                                        class="img-thumbnail mt-2"
                                        style="max-width: 120px"
                                    />
                                </div>
                            </div>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.form.sort_order.label') }}:</strong>
                                {{ selectedProduct?.sort_order }}
                            </p>
                            <p>
                                <strong>{{ $t('tenant_dashboard.products.common.createdAt') }}:</strong>
                                {{
                                    new Date(
                                        selectedProduct?.created_at
                                    ).toLocaleString()
                                }}
                            </p>
                            <p v-if="selectedProduct?.deleted_at">
                                <strong>{{ $t('tenant_dashboard.products.common.deletedAt') }}:</strong>
                                {{
                                    new Date(
                                        selectedProduct?.deleted_at
                                    ).toLocaleString()
                                }}
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            {{ $t('tenant_dashboard.dashboard_common.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deleted Products Modal -->
        <div class="modal fade" id="deletedProductsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $t('tenant_dashboard.products.modal.deletedTitle') }}</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div
                            v-if="deletedProducts.length === 0"
                            class="text-center py-4 text-muted"
                        >
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <div>{{ $t('tenant_dashboard.products.empty.deleted') }}</div>
                        </div>
                        <div v-else class="table-responsive">
                            <table
                                class="table table-hover table-sm products-table"
                            >
                                <thead>
                                    <tr>
                                        <th>{{ $t('tenant_dashboard.products.columns.name') }}</th>
                                        <th class="d-none d-md-table-cell">
                                            {{ $t('tenant_dashboard.products.columns.category') }}
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            {{ $t('tenant_dashboard.products.columns.status') }}
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            {{ $t('tenant_dashboard.products.common.deletedAt') }}
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            {{ $t('tenant_dashboard.products.columns.actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="product in deletedProducts"
                                        :key="product.id"
                                    >
                                        <td>{{ product.name }}</td>
                                        <td>
                                            {{
                                                product.category?.name || $t('tenant_dashboard.dashboard_common.none')
                                            }}
                                        </td>
                                        <td>{{ product.status }}</td>
                                        <td>
                                            {{
                                                new Date(
                                                    product.deleted_at
                                                ).toLocaleString()
                                            }}
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-action btn-restore"
                                                @click="restoreProduct(product)"
                                            >
                                                <i class="fas fa-undo"></i>
                                                {{ $t('tenant_dashboard.products.actions.restore') }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            {{ $t('tenant_dashboard.dashboard_common.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Add Category Modal -->
        <div class="modal fade" id="QuickCategoryModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $t('tenant_dashboard.products.modal.quickCategoryTitle') }}</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="quickAddCategory">
                            <div class="mb-3">
                                <label class="form-label">{{ $t('tenant_dashboard.products.form.category.name') }}</label>
                                <input
                                    v-model="quickCategoryForm.name"
                                    type="text"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ $t('tenant_dashboard.products.form.category.description') }}</label>
                                <input
                                    v-model="quickCategoryForm.description"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="quickCategoryActive"
                                    v-model="quickCategoryForm.is_active"
                                />
                                <label
                                    class="form-check-label"
                                    for="quickCategoryActive"
                                    >{{ $t('tenant_dashboard.products.form.is_active.label') }}</label
                                >
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                >
                                    {{ $t('tenant_dashboard.dashboard_common.cancel') }}
                                </button>
                                <button type="submit" class="btn btn-success">
                                    {{ $t('tenant_dashboard.dashboard_common.add') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- <script>
import { ref, onMounted, computed, watch } from "vue";
import { Modal } from "bootstrap";
import axios from "axios";
import Swal from "sweetalert2";
import draggable from "vuedraggable";

export default {
    name: "Products",
    components: { draggable },
    setup() {
        const products = ref([]);
        const deletedProducts = ref([]);
        const categoriesList = ref([]);
        const loading = ref(false);
        const filters = ref({ search: "", category_id: "", status: "" });
        const isEditing = ref(false);
        const fileInput = ref(null);

        // Default form state
        const defaultForm = {
            id: null,
            name: "",
            description: "",
            category_id: "",
            price: "",
            cost_price: "",
            stock_quantity: "",
            min_stock_level: "",
            status: "active",
            is_active: true,
            is_featured: false,
            sku: "",
            barcode: "",
            // image handling
            newImages: [], // File objects selected this session
            imagePreviews: [], // object URLs for previews of newImages
            existingImages: [], // array of urls from server
            existingImageIds: [], // corresponding media ids
            remove_media_ids: [], // media ids to remove on update
        };

        const form = ref({ ...defaultForm });
        const selectedProduct = ref(null);
        const quickCategoryForm = ref({
            name: "",
            description: "",
            is_active: true,
        });
        let productModal = null;
        let viewProductModal = null;
        let deletedProductsModal = null;
        let quickCategoryModal = null;
        const pagination = ref({
            current_page: 1,
            last_page: 1,
            per_page: 5,
            total: 0,
            from: 0,
            to: 0,
        });

        const allColumns = [
            { key: "name", label: "Name" },
            { key: "category", label: "Category" },
            { key: "price", label: "Price" },
            { key: "stock_quantity", label: "Stock Quantity" },
            { key: "min_stock_level", label: "Min Stock Level" },
            { key: "sku", label: "SKU" },
            { key: "barcode", label: "Barcode" },
            { key: "cost_price", label: "Cost Price" },
            { key: "is_active", label: "Active" },
            { key: "is_featured", label: "Featured" },
            { key: "sort_order", label: "Sort Order" },
            { key: "created_at", label: "Created At" },
        ];
        const COLUMN_CACHE_KEY = "products_selected_columns_v1";
        const selectedColumns = ref([
            "name",
            "category",
            "price",
            "stock_quantity",
        ]);
        const visibleColumns = ref(
            allColumns.filter((col) => selectedColumns.value.includes(col.key))
        );
        // Column filter logic
        const showColumnDropdown = ref(false);
        const tempSelectedColumns = ref([...selectedColumns.value]);
        const selectAllChecked = ref(false);
        const dropdownBtn = ref(null);
        const toggleColumnDropdown = () => {
            showColumnDropdown.value = !showColumnDropdown.value;
            tempSelectedColumns.value = [...selectedColumns.value];
            updateSelectAllChecked();
        };
        const applyColumnSelection = () => {
            selectedColumns.value = [...tempSelectedColumns.value];
            visibleColumns.value = allColumns.filter((col) =>
                selectedColumns.value.includes(col.key)
            );
            showColumnDropdown.value = false;
        };
        const cancelColumnSelection = () => {
            tempSelectedColumns.value = [...selectedColumns.value];
            showColumnDropdown.value = false;
        };
        const toggleSelectAll = () => {
            if (selectAllChecked.value) {
                tempSelectedColumns.value = allColumns.map((col) => col.key);
            } else {
                tempSelectedColumns.value = [];
            }
        };
        const updateSelectAllChecked = () => {
            selectAllChecked.value =
                tempSelectedColumns.value.length === allColumns.length;
        };
        watch(tempSelectedColumns, updateSelectAllChecked);

        // Reset form function
        const resetForm = () => {
            // Revoke all object URLs for image previews to prevent memory leaks
            form.value.imagePreviews.forEach((url) => {
                try {
                    URL.revokeObjectURL(url);
                } catch (e) {
                    // Ignore errors
                }
            });

            // Reset form to default state
            form.value = { ...defaultForm };

            // Reset file input
            if (fileInput.value) {
                fileInput.value.value = "";
            }

            isEditing.value = false;
        };

        const fetchProducts = async (page = 1) => {
            loading.value = true;
            try {
                const params = {
                    page,
                    per_page: pagination.value.per_page,
                    ...filters.value,
                };
                const { data } = await axios.get("/tenant/products", {
                    params,
                });
                const d = data.data;
                products.value = d.data || [];
                pagination.value = {
                    current_page: d.current_page,
                    last_page: d.last_page,
                    per_page: d.per_page,
                    total: d.total,
                    from: d.from,
                    to: d.to,
                };
            } catch (e) {
                products.value = [];
                pagination.value = {
                    current_page: 1,
                    last_page: 1,
                    per_page: 5,
                    total: 0,
                    from: 0,
                    to: 0,
                };
            }
            loading.value = false;
        };

        const fetchCategories = async () => {
            try {
                const { data } = await axios.get("/tenant/categories");
                categoriesList.value = data.data.data || [];
            } catch (e) {
                categoriesList.value = [];
            }
        };

        const fetchDeletedProducts = async () => {
            try {
                const { data } = await axios.get("/tenant/products", {
                    params: { only_deleted: true },
                });
                deletedProducts.value = data.data.data || [];
            } catch (e) {
                deletedProducts.value = [];
            }
        };

        const applyFilters = () => {
            fetchProducts(1);
        };

        const showCreateModal = () => {
            resetForm(); // Reset form when creating new product
            productModal = new Modal(document.getElementById("ProductModal"));
            productModal.show();
        };

        const editProduct = (product) => {
            isEditing.value = true;
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
                newImages: [],
                imagePreviews: [],
                // This should now receive the images array from backend with proper URLs
                existingImages: product.images ? product.images : [],
                existingImageIds: product.images
                    ? product.images.map((i) => i.id)
                    : [],
                remove_media_ids: [],
            });
            productModal = new Modal(document.getElementById("ProductModal"));
            productModal.show();
        };

        const saveProduct = async () => {
            const fd = new FormData();

            // append scalar fields
            const skip = [
                "imagePreviews",
                "newImages",
                "existingImages",
                "existingImageIds",
                "remove_media_ids",
            ];
            for (const key in form.value) {
                if (skip.includes(key)) continue;
                const val = form.value[key];
                if (val !== null && val !== undefined && val !== "") {
                    // booleans need to be converted
                    if (typeof val === "boolean") {
                        fd.append(key, val ? "1" : "0");
                    } else {
                        fd.append(key, val);
                    }
                }
            }

            // append new images
            if (form.value.newImages && form.value.newImages.length) {
                form.value.newImages.forEach((f) => fd.append("images[]", f));
            }

            // append media ids to remove on update
            if (
                form.value.remove_media_ids &&
                form.value.remove_media_ids.length
            ) {
                form.value.remove_media_ids.forEach((id) =>
                    fd.append("remove_media_ids[]", id)
                );
            }

            try {
                if (isEditing.value) {
                    await axios.post(
                        `/tenant/products/${form.value.id}?_method=PUT`,
                        fd,
                        { headers: { "Content-Type": "multipart/form-data" } }
                    );
                    Swal.fire({ 
                        icon: "success", 
                        title: this.$t('tenant_dashboard.products.alerts.updated') 
                    });
                } else {
                    await axios.post("/tenant/products", fd, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    Swal.fire({ 
                        icon: "success", 
                        title: this.$t('tenant_dashboard.products.alerts.created') 
                    });
                }
                fetchProducts();
                productModal.hide();
                resetForm(); // Reset form after successful save
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: this.$t('tenant_dashboard.dashboard_common.error'),
                    text: this.$t('tenant_dashboard.products.alerts.saveFailed'),
                });
            }
        };

        const confirmDelete = (product) => {
            Swal.fire({
                title: this.$t('tenant_dashboard.products.confirm.delete.title'),
                text: this.$t('tenant_dashboard.products.confirm.delete.message'),
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: this.$t('tenant_dashboard.products.confirm.delete.confirm'),
                cancelButtonText: this.$t('tenant_dashboard.dashboard_common.cancel')
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await axios.delete(`/tenant/products/${product.id}`);
                        fetchProducts();
                        Swal.fire({
                            icon: "success",
                            title: this.$t('tenant_dashboard.products.alerts.deleted')
                        });
                    } catch (e) {
                        Swal.fire({
                            icon: "error",
                            title: this.$t('tenant_dashboard.dashboard_common.error'),
                            text: this.$t('tenant_dashboard.products.alerts.deleteFailed')
                        });
                    }
                }
            });
        };

        const confirmHardDelete = (product) => {
            Swal.fire({
                title: this.$t('tenant_dashboard.products.confirm.hardDelete.title'),
                text: this.$t('tenant_dashboard.products.confirm.hardDelete.message'),
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: this.$t('tenant_dashboard.products.confirm.hardDelete.confirm'),
                cancelButtonText: this.$t('tenant_dashboard.dashboard_common.cancel')
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await axios.post(
                            `/tenant/products/${product.id}/force-delete`
                        );
                        fetchDeletedProducts();
                        Swal.fire({
                            icon: "success",
                            title: this.$t('tenant_dashboard.products.alerts.hardDeleted')
                        });
                    } catch (e) {
                        Swal.fire({
                            icon: "error",
                            title: this.$t('tenant_dashboard.dashboard_common.error'),
                            text: this.$t('tenant_dashboard.products.alerts.hardDeleteFailed')
                        });
                    }
                }
            });
        };

        const restoreProduct = async (product) => {
            try {
                await axios.post(`/tenant/products/${product.id}/restore`);
                fetchProducts();
                fetchDeletedProducts();
                Swal.fire({
                    icon: "success",
                    title: this.$t('tenant_dashboard.products.alerts.restored')
                });
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: this.$t('tenant_dashboard.dashboard_common.error'),
                    text: this.$t('tenant_dashboard.products.alerts.restoreFailed')
                });
            }
        };

        const viewProduct = (product) => {
            selectedProduct.value = product;
            viewProductModal = new Modal(
                document.getElementById("viewProductModal")
            );
            viewProductModal.show();
        };

        const showDeletedModal = () => {
            fetchDeletedProducts();
            deletedProductsModal = new Modal(
                document.getElementById("deletedProductsModal")
            );
            deletedProductsModal.show();
        };

        const onSortEnd = async () => {
            try {
                const order = products.value.map((prod, idx) => ({
                    id: prod.id,
                    sort_order: idx,
                }));
                await axios.post("/tenant/products/reorder", { order });
                await fetchProducts();
                Swal.fire({ 
                    icon: "success", 
                    title: this.$t('tenant_dashboard.products.alerts.orderUpdated') 
                });
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: this.$t('tenant_dashboard.dashboard_common.error'),
                    text: this.$t('tenant_dashboard.products.alerts.orderUpdateFailed'),
                });
            }
        };

        const onImagesChange = (e) => {
            const files = Array.from(e.target.files || []);
            files.forEach((file) => {
                form.value.newImages.push(file);
                form.value.imagePreviews.push(URL.createObjectURL(file));
            });
        };

        const removeNewImage = (idx) => {
            const url = form.value.imagePreviews[idx];
            // revoke object URL
            try {
                URL.revokeObjectURL(url);
            } catch (e) {}
            form.value.imagePreviews.splice(idx, 1);
            form.value.newImages.splice(idx, 1);
        };

        const markRemoveExisting = (idx) => {
            const id = form.value.existingImageIds[idx];
            if (id) {
                form.value.remove_media_ids.push(id);
            }
            form.value.existingImages.splice(idx, 1);
            form.value.existingImageIds.splice(idx, 1);
        };

        const showQuickCategoryModal = () => {
            quickCategoryForm.value = {
                name: "",
                description: "",
                is_active: true,
            };
            quickCategoryModal = new Modal(
                document.getElementById("QuickCategoryModal")
            );
            quickCategoryModal.show();
        };

        const quickAddCategory = async () => {
            try {
                const { data } = await axios.post(
                    "/tenant/categories",
                    quickCategoryForm.value
                );
                categoriesList.value.push(data.data);
                form.value.category_id = data.data.id;
                quickCategoryModal.hide();
                Swal.fire({ 
                    icon: "success", 
                    title: this.$t('tenant_dashboard.products.alerts.categoryAdded') 
                });
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: this.$t('tenant_dashboard.dashboard_common.error'),
                    text: this.$t('tenant_dashboard.products.alerts.categoryAddFailed'),
                });
            }
        };

        const goToPage = (page) => {
            if (page < 1 || page > pagination.value.last_page) return;
            fetchProducts(page);
        };

        const paginationWindow = computed(() => {
            const total = pagination.value.last_page;
            const current = pagination.value.current_page;
            const windowSize = 5;
            let start = Math.max(1, current - 2);
            let end = Math.min(total, current + 2);
            if (end - start < windowSize - 1) {
                if (start === 1) {
                    end = Math.min(total, start + windowSize - 1);
                } else if (end === total) {
                    start = Math.max(1, end - windowSize + 1);
                }
            }
            const pages = [];
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        });

        onMounted(() => {
            fetchProducts();
            fetchCategories();
            const cached = localStorage.getItem(COLUMN_CACHE_KEY);
            if (cached) {
                try {
                    const parsed = JSON.parse(cached);
                    if (
                        Array.isArray(parsed) &&
                        parsed.every((k) => allColumns.some((c) => c.key === k))
                    ) {
                        selectedColumns.value = parsed;
                        visibleColumns.value = allColumns.filter((col) =>
                            selectedColumns.value.includes(col.key)
                        );
                    }
                } catch {}
            }
        });

        watch(selectedColumns, (val) => {
            localStorage.setItem(COLUMN_CACHE_KEY, JSON.stringify(val));
        });

        return {
            products,
            deletedProducts,
            categoriesList,
            filters,
            loading,
            isEditing,
            form,
            selectedProduct,
            fileInput,
            showCreateModal,
            editProduct,
            saveProduct,
            resetForm,
            confirmDelete,
            confirmHardDelete,
            restoreProduct,
            viewProduct,
            showDeletedModal,
            applyFilters,
            onSortEnd,
            onImagesChange,
            removeNewImage,
            markRemoveExisting,
            quickCategoryForm,
            showQuickCategoryModal,
            quickAddCategory,
            showColumnDropdown,
            dropdownBtn,
            selectAllChecked,
            tempSelectedColumns,
            visibleColumns,
            allColumns,
            pagination,
            goToPage,
            paginationWindow,
        };
    },
};
</script> -->
<script>
import { ref, onMounted, computed, watch, getCurrentInstance } from "vue";
import { Modal } from "bootstrap";
import axios from "axios";
import Swal from "sweetalert2";
import draggable from "vuedraggable";

export default {
    name: "Products",
    components: { draggable },
    setup() {
        const { proxy } = getCurrentInstance();
        
        const products = ref([]);
        const deletedProducts = ref([]);
        const categoriesList = ref([]);
        const loading = ref(false);
        const filters = ref({ search: "", category_id: "", status: "" });
        const isEditing = ref(false);
        const fileInput = ref(null);
        
        // New loading states for specific operations
        const savingProduct = ref(false);
        const deletingProduct = ref(false);
        const restoringProduct = ref(false);
        const hardDeletingProduct = ref(false);
        const reorderingProducts = ref(false);
        const addingCategory = ref(false);

        // Default form state
        const defaultForm = {
            id: null,
            name: "",
            description: "",
            category_id: "",
            price: "",
            cost_price: "",
            stock_quantity: "",
            min_stock_level: "",
            status: "active",
            is_active: true,
            is_featured: false,
            sku: "",
            barcode: "",
            // image handling
            newImages: [], // File objects selected this session
            imagePreviews: [], // object URLs for previews of newImages
            existingImages: [], // array of urls from server
            existingImageIds: [], // corresponding media ids
            remove_media_ids: [], // media ids to remove on update
        };

        const form = ref({ ...defaultForm });
        const selectedProduct = ref(null);
        const quickCategoryForm = ref({
            name: "",
            description: "",
            is_active: true,
        });
        let productModal = null;
        let viewProductModal = null;
        let deletedProductsModal = null;
        let quickCategoryModal = null;
        const pagination = ref({
            current_page: 1,
            last_page: 1,
            per_page: 5,
            total: 0,
            from: 0,
            to: 0,
        });

        const allColumns = [
            { key: "name", label: "Name" },
            { key: "category", label: "Category" },
            { key: "price", label: "Price" },
            { key: "stock_quantity", label: "Stock Quantity" },
            { key: "min_stock_level", label: "Min Stock Level" },
            { key: "sku", label: "SKU" },
            { key: "barcode", label: "Barcode" },
            { key: "cost_price", label: "Cost Price" },
            { key: "is_active", label: "Active" },
            { key: "is_featured", label: "Featured" },
            { key: "sort_order", label: "Sort Order" },
            { key: "created_at", label: "Created At" },
        ];
        const COLUMN_CACHE_KEY = "products_selected_columns_v1";
        const selectedColumns = ref([
            "name",
            "category",
            "price",
            "stock_quantity",
        ]);
        const visibleColumns = ref(
            allColumns.filter((col) => selectedColumns.value.includes(col.key))
        );
        // Column filter logic
        const showColumnDropdown = ref(false);
        const tempSelectedColumns = ref([...selectedColumns.value]);
        const selectAllChecked = ref(false);
        const dropdownBtn = ref(null);
        const toggleColumnDropdown = () => {
            showColumnDropdown.value = !showColumnDropdown.value;
            tempSelectedColumns.value = [...selectedColumns.value];
            updateSelectAllChecked();
        };
        const applyColumnSelection = () => {
            selectedColumns.value = [...tempSelectedColumns.value];
            visibleColumns.value = allColumns.filter((col) =>
                selectedColumns.value.includes(col.key)
            );
            showColumnDropdown.value = false;
        };
        const cancelColumnSelection = () => {
            tempSelectedColumns.value = [...selectedColumns.value];
            showColumnDropdown.value = false;
        };
        const toggleSelectAll = () => {
            if (selectAllChecked.value) {
                tempSelectedColumns.value = allColumns.map((col) => col.key);
            } else {
                tempSelectedColumns.value = [];
            }
        };
        const updateSelectAllChecked = () => {
            selectAllChecked.value =
                tempSelectedColumns.value.length === allColumns.length;
        };
        watch(tempSelectedColumns, updateSelectAllChecked);

        // Reset form function
        const resetForm = () => {
            // Revoke all object URLs for image previews to prevent memory leaks
            form.value.imagePreviews.forEach((url) => {
                try {
                    URL.revokeObjectURL(url);
                } catch (e) {
                    // Ignore errors
                }
            });

            // Reset form to default state
            form.value = { ...defaultForm };

            // Reset file input
            if (fileInput.value) {
                fileInput.value.value = "";
            }

            isEditing.value = false;
            savingProduct.value = false;
        };

        const fetchProducts = async (page = 1) => {
            loading.value = true;
            try {
                const params = {
                    page,
                    per_page: pagination.value.per_page,
                    ...filters.value,
                };
                const { data } = await axios.get("/tenant/products", {
                    params,
                });
                const d = data.data;
                products.value = d.data || [];
                pagination.value = {
                    current_page: d.current_page,
                    last_page: d.last_page,
                    per_page: d.per_page,
                    total: d.total,
                    from: d.from,
                    to: d.to,
                };
            } catch (e) {
                products.value = [];
                pagination.value = {
                    current_page: 1,
                    last_page: 1,
                    per_page: 5,
                    total: 0,
                    from: 0,
                    to: 0,
                };
            }
            loading.value = false;
        };

        const fetchCategories = async () => {
            try {
                const { data } = await axios.get("/tenant/categories");
                categoriesList.value = data.data.data || [];
            } catch (e) {
                categoriesList.value = [];
            }
        };

        const fetchDeletedProducts = async () => {
            try {
                const { data } = await axios.get("/tenant/products", {
                    params: { only_deleted: true },
                });
                deletedProducts.value = data.data.data || [];
            } catch (e) {
                deletedProducts.value = [];
            }
        };

        const applyFilters = () => {
            fetchProducts(1);
        };

        const showCreateModal = () => {
            resetForm(); // Reset form when creating new product
            productModal = new Modal(document.getElementById("ProductModal"));
            productModal.show();
        };

        const editProduct = (product) => {
            isEditing.value = true;
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
                newImages: [],
                imagePreviews: [],
                // This should now receive the images array from backend with proper URLs
                existingImages: product.images ? product.images : [],
                existingImageIds: product.images
                    ? product.images.map((i) => i.id)
                    : [],
                remove_media_ids: [],
            });
            productModal = new Modal(document.getElementById("ProductModal"));
            productModal.show();
        };

        const saveProduct = async () => {
            if (savingProduct.value) return; // Prevent multiple submissions
            
            savingProduct.value = true;
            
            const fd = new FormData();

            // append scalar fields
            const skip = [
                "imagePreviews",
                "newImages",
                "existingImages",
                "existingImageIds",
                "remove_media_ids",
            ];
            for (const key in form.value) {
                if (skip.includes(key)) continue;
                const val = form.value[key];
                if (val !== null && val !== undefined && val !== "") {
                    // booleans need to be converted
                    if (typeof val === "boolean") {
                        fd.append(key, val ? "1" : "0");
                    } else {
                        fd.append(key, val);
                    }
                }
            }

            // append new images
            if (form.value.newImages && form.value.newImages.length) {
                form.value.newImages.forEach((f) => fd.append("images[]", f));
            }

            // append media ids to remove on update
            if (
                form.value.remove_media_ids &&
                form.value.remove_media_ids.length
            ) {
                form.value.remove_media_ids.forEach((id) =>
                    fd.append("remove_media_ids[]", id)
                );
            }

            try {
                if (isEditing.value) {
                    await axios.post(
                        `/tenant/products/${form.value.id}?_method=PUT`,
                        fd,
                        { headers: { "Content-Type": "multipart/form-data" } }
                    );
                    Swal.fire({ 
                        icon: "success", 
                        title: proxy.$t('tenant_dashboard.products.alerts.updated') 
                    });
                } else {
                    await axios.post("/tenant/products", fd, {
                        headers: { "Content-Type": "multipart/form-data" },
                    });
                    Swal.fire({ 
                        icon: "success", 
                        title: proxy.$t('tenant_dashboard.products.alerts.created') 
                    });
                }
                await fetchProducts();
                productModal.hide();
                resetForm(); // Reset form after successful save
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: proxy.$t('tenant_dashboard.dashboard_common.error'),
                    text: e.response?.data?.message || proxy.$t('tenant_dashboard.products.alerts.saveFailed'),
                });
            } finally {
                savingProduct.value = false;
            }
        };

        const confirmDelete = (product) => {
            Swal.fire({
                title: proxy.$t('tenant_dashboard.products.confirm.delete.title'),
                text: proxy.$t('tenant_dashboard.products.confirm.delete.message'),
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: proxy.$t('tenant_dashboard.products.confirm.delete.confirm'),
                cancelButtonText: proxy.$t('tenant_dashboard.dashboard_common.cancel'),
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    deletingProduct.value = true;
                    try {
                        await axios.delete(`/tenant/products/${product.id}`);
                        await fetchProducts();
                        return true;
                    } catch (e) {
                        throw new Error(e.response?.data?.message || proxy.$t('tenant_dashboard.products.alerts.deleteFailed'));
                    } finally {
                        deletingProduct.value = false;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "success",
                        title: proxy.$t('tenant_dashboard.products.alerts.deleted')
                    });
                }
            }).catch((error) => {
                Swal.fire({
                    icon: "error",
                    title: proxy.$t('tenant_dashboard.dashboard_common.error'),
                    text: error.message
                });
            });
        };

        const confirmHardDelete = (product) => {
            Swal.fire({
                title: proxy.$t('tenant_dashboard.products.confirm.hardDelete.title'),
                text: proxy.$t('tenant_dashboard.products.confirm.hardDelete.message'),
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: proxy.$t('tenant_dashboard.products.confirm.hardDelete.confirm'),
                cancelButtonText: proxy.$t('tenant_dashboard.dashboard_common.cancel'),
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    hardDeletingProduct.value = true;
                    try {
                        await axios.post(`/tenant/products/${product.id}/force-delete`);
                        await fetchDeletedProducts();
                        return true;
                    } catch (e) {
                        throw new Error(e.response?.data?.message || proxy.$t('tenant_dashboard.products.alerts.hardDeleteFailed'));
                    } finally {
                        hardDeletingProduct.value = false;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "success",
                        title: proxy.$t('tenant_dashboard.products.alerts.hardDeleted')
                    });
                }
            }).catch((error) => {
                Swal.fire({
                    icon: "error",
                    title: proxy.$t('tenant_dashboard.dashboard_common.error'),
                    text: error.message
                });
            });
        };

        const restoreProduct = async (product) => {
            if (restoringProduct.value) return;
            
            restoringProduct.value = true;
            try {
                await axios.post(`/tenant/products/${product.id}/restore`);
                await fetchProducts();
                await fetchDeletedProducts();
                Swal.fire({
                    icon: "success",
                    title: proxy.$t('tenant_dashboard.products.alerts.restored')
                });
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: proxy.$t('tenant_dashboard.dashboard_common.error'),
                    text: e.response?.data?.message || proxy.$t('tenant_dashboard.products.alerts.restoreFailed')
                });
            } finally {
                restoringProduct.value = false;
            }
        };

        const viewProduct = (product) => {
            selectedProduct.value = product;
            viewProductModal = new Modal(
                document.getElementById("viewProductModal")
            );
            viewProductModal.show();
        };

        const showDeletedModal = () => {
            fetchDeletedProducts();
            deletedProductsModal = new Modal(
                document.getElementById("deletedProductsModal")
            );
            deletedProductsModal.show();
        };

        const onSortEnd = async () => {
            if (reorderingProducts.value) return;
            
            reorderingProducts.value = true;
            try {
                const order = products.value.map((prod, idx) => ({
                    id: prod.id,
                    sort_order: idx,
                }));
                await axios.post("/tenant/products/reorder", { order });
                await fetchProducts();
                Swal.fire({ 
                    icon: "success", 
                    title: proxy.$t('tenant_dashboard.products.alerts.orderUpdated') 
                });
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: proxy.$t('tenant_dashboard.dashboard_common.error'),
                    text: e.response?.data?.message || proxy.$t('tenant_dashboard.products.alerts.orderUpdateFailed'),
                });
            } finally {
                reorderingProducts.value = false;
            }
        };

        const onImagesChange = (e) => {
            const files = Array.from(e.target.files || []);
            files.forEach((file) => {
                form.value.newImages.push(file);
                form.value.imagePreviews.push(URL.createObjectURL(file));
            });
        };

        const removeNewImage = (idx) => {
            const url = form.value.imagePreviews[idx];
            // revoke object URL
            try {
                URL.revokeObjectURL(url);
            } catch (e) {}
            form.value.imagePreviews.splice(idx, 1);
            form.value.newImages.splice(idx, 1);
        };

        const markRemoveExisting = (idx) => {
            const id = form.value.existingImageIds[idx];
            if (id) {
                form.value.remove_media_ids.push(id);
            }
            form.value.existingImages.splice(idx, 1);
            form.value.existingImageIds.splice(idx, 1);
        };

        const showQuickCategoryModal = () => {
            quickCategoryForm.value = {
                name: "",
                description: "",
                is_active: true,
            };
            quickCategoryModal = new Modal(
                document.getElementById("QuickCategoryModal")
            );
            quickCategoryModal.show();
        };

        const quickAddCategory = async () => {
            if (addingCategory.value) return;
            
            addingCategory.value = true;
            try {
                const { data } = await axios.post(
                    "/tenant/categories",
                    quickCategoryForm.value
                );
                categoriesList.value.push(data.data);
                form.value.category_id = data.data.id;
                quickCategoryModal.hide();
                Swal.fire({ 
                    icon: "success", 
                    title: proxy.$t('tenant_dashboard.products.alerts.categoryAdded') 
                });
            } catch (e) {
                Swal.fire({
                    icon: "error",
                    title: proxy.$t('tenant_dashboard.dashboard_common.error'),
                    text: e.response?.data?.message || proxy.$t('tenant_dashboard.products.alerts.categoryAddFailed'),
                });
            } finally {
                addingCategory.value = false;
            }
        };

        const goToPage = (page) => {
            if (page < 1 || page > pagination.value.last_page) return;
            fetchProducts(page);
        };

        const paginationWindow = computed(() => {
            const total = pagination.value.last_page;
            const current = pagination.value.current_page;
            const windowSize = 5;
            let start = Math.max(1, current - 2);
            let end = Math.min(total, current + 2);
            if (end - start < windowSize - 1) {
                if (start === 1) {
                    end = Math.min(total, start + windowSize - 1);
                } else if (end === total) {
                    start = Math.max(1, end - windowSize + 1);
                }
            }
            const pages = [];
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        });

        onMounted(() => {
            fetchProducts();
            fetchCategories();
            const cached = localStorage.getItem(COLUMN_CACHE_KEY);
            if (cached) {
                try {
                    const parsed = JSON.parse(cached);
                    if (
                        Array.isArray(parsed) &&
                        parsed.every((k) => allColumns.some((c) => c.key === k))
                    ) {
                        selectedColumns.value = parsed;
                        visibleColumns.value = allColumns.filter((col) =>
                            selectedColumns.value.includes(col.key)
                        );
                    }
                } catch {}
            }
        });

        watch(selectedColumns, (val) => {
            localStorage.setItem(COLUMN_CACHE_KEY, JSON.stringify(val));
        });

        return {
            products,
            deletedProducts,
            categoriesList,
            filters,
            loading,
            isEditing,
            form,
            selectedProduct,
            fileInput,
            // Loading states
            savingProduct,
            deletingProduct,
            restoringProduct,
            hardDeletingProduct,
            reorderingProducts,
            addingCategory,
            // Methods
            showCreateModal,
            editProduct,
            saveProduct,
            resetForm,
            confirmDelete,
            confirmHardDelete,
            restoreProduct,
            viewProduct,
            showDeletedModal,
            applyFilters,
            onSortEnd,
            onImagesChange,
            removeNewImage,
            markRemoveExisting,
            quickCategoryForm,
            showQuickCategoryModal,
            quickAddCategory,
            showColumnDropdown,
            dropdownBtn,
            selectAllChecked,
            tempSelectedColumns,
            visibleColumns,
            allColumns,
            pagination,
            goToPage,
            paginationWindow,
        };
    },
};
</script>
<style scoped>
/* Your existing CSS remains the same */
.Products-page {
    max-width: 1200px;
    margin: 0 auto;
}
.Products-table th,
.Products-table td {
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
    font-family: "Inter", "Roboto", "Segoe UI", Arial, sans-serif;
    font-size: 16px;
}

.filter-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(60, 60, 60, 0.08);
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
    box-shadow: 0 2px 8px rgba(21, 101, 192, 0.08);
    border: none;
    transition: background 0.15s, box-shadow 0.15s;
}
.btn-add-Category:hover,
.btn-add-Category:focus {
    background: #0d47a1;
    color: #fff;
    box-shadow: 0 4px 16px rgba(21, 101, 192, 0.13);
}

.products-table {
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 8px rgba(60, 60, 60, 0.06);
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
.btn-view {
    background: #e3f2fd;
    color: #1565c0;
}
.btn-view:hover {
    background: #bbdefb;
    color: #0d47a1;
}
.btn-edit {
    background: #e8f5e9;
    color: #388e3c;
}
.btn-edit:hover {
    background: #c8e6c9;
    color: #1b5e20;
}
.btn-delete {
    background: #ffebee;
    color: #c62828;
}
.btn-delete:hover {
    background: #ffcdd2;
    color: #b71c1c;
}
.btn-restore {
    background: #fffde7;
    color: #fbc02d;
}
.btn-restore:hover {
    background: #fff9c4;
    color: #f9a825;
}
.btn-hard-delete {
    background: #fbe9e7;
    color: #d84315;
}
.btn-hard-delete:hover {
    background: #ffccbc;
    color: #bf360c;
}

.Category-details p {
    margin-bottom: 0.5rem;
}

.table td {
    vertical-align: middle;
}

.Category-modal-content {
    font-family: "Inter", "Roboto", "Segoe UI", Arial, sans-serif;
    font-size: 16px;
    background: #fff;
    border-radius: 0 0 12px 12px;
    border: 1px solid #e0e0e0;
    box-shadow: 0 8px 32px rgba(60, 60, 60, 0.13);
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
    box-shadow: 0 2px 6px rgba(198, 40, 40, 0.08);
    padding: 0.7rem 2.2rem;
    letter-spacing: 1px;
    transition: background 0.15s, box-shadow 0.15s;
}
.btn-submit:hover,
.btn-submit:focus {
    background: #b71c1c;
    color: #fff;
    box-shadow: 0 4px 12px rgba(198, 40, 40, 0.13);
}

.drag-handle {
    vertical-align: middle;
}
</style>