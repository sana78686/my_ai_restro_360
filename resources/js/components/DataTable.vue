<template>
  <div class="data-table-container">
    <!-- Filters -->
    <div class="filters">
      <div class="search-filter">
        <i class="fas fa-search"></i>
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search..."
          @input="handleSearch"
        />
      </div>
      <div class="filter-options">
        <select v-model="selectedFilter" @change="handleFilterChange">
          <option value="">All</option>
          <option v-for="option in filterOptions" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
        <button class="reset-btn" @click="resetFilters">
          <i class="fas fa-undo"></i> Reset
        </button>
      </div>
    </div>

    <!-- Data Table -->
    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              @click="sortBy(column.key)"
              :class="{ sortable: column.sortable }"
            >
              {{ column.label }}
              <i
                v-if="column.sortable"
                :class="[
                  'fas',
                  sortKey === column.key
                    ? sortOrder === 1
                      ? 'fa-sort-up'
                      : 'fa-sort-down'
                    : 'fa-sort'
                ]"
              ></i>
            </th>
            <th v-if="actions.length">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in paginatedData" :key="item.id">
            <td v-for="column in columns" :key="column.key">
              <slot :name="column.key" :item="item">
                {{ item[column.key] }}
              </slot>
            </td>
            <td v-if="actions.length" class="actions">
              <button
                v-for="action in actions"
                :key="action.name"
                :class="action.class"
                @click="$emit(action.name, item)"
              >
                <i :class="action.icon"></i>
                <span>{{ action.label }}</span>
              </button>
            </td>
          </tr>
          <tr v-if="paginatedData.length === 0">
            <td :colspan="columns.length + (actions.length ? 1 : 0)" class="no-data">
              No data found
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <div class="pagination-info">
        Showing {{ startIndex + 1 }} to {{ endIndex }} of {{ filteredData.length }} entries
      </div>
      <div class="pagination-controls">
        <button
          :disabled="currentPage === 1"
          @click="currentPage--"
          class="pagination-btn"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <span class="page-info">
          Page {{ currentPage }} of {{ totalPages }}
        </span>
        <button
          :disabled="currentPage === totalPages"
          @click="currentPage++"
          class="pagination-btn"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DataTable',
  props: {
    data: {
      type: Array,
      required: true
    },
    columns: {
      type: Array,
      required: true
    },
    actions: {
      type: Array,
      default: () => []
    },
    filterOptions: {
      type: Array,
      default: () => []
    },
    itemsPerPage: {
      type: Number,
      default: 10
    }
  },
  data() {
    return {
      searchQuery: '',
      selectedFilter: '',
      currentPage: 1,
      sortKey: '',
      sortOrder: 1 // 1 for ascending, -1 for descending
    }
  },
  computed: {
    filteredData() {
      let result = [...this.data]

      // Apply search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        result = result.filter(item => {
          return this.columns.some(column => {
            const value = item[column.key]
            return value && value.toString().toLowerCase().includes(query)
          })
        })
      }

      // Apply selected filter
      if (this.selectedFilter) {
        result = result.filter(item => item.status === this.selectedFilter)
      }

      // Apply sorting
      if (this.sortKey) {
        result.sort((a, b) => {
          const aValue = a[this.sortKey]
          const bValue = b[this.sortKey]
          if (typeof aValue === 'string') {
            return aValue.localeCompare(bValue) * this.sortOrder
          }
          return (aValue - bValue) * this.sortOrder
        })
      }

      return result
    },
    paginatedData() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.filteredData.slice(start, end)
    },
    totalPages() {
      return Math.ceil(this.filteredData.length / this.itemsPerPage)
    },
    startIndex() {
      return (this.currentPage - 1) * this.itemsPerPage
    },
    endIndex() {
      const end = this.startIndex + this.itemsPerPage
      return end > this.filteredData.length ? this.filteredData.length : end
    }
  },
  methods: {
    handleSearch() {
      this.currentPage = 1
    },
    handleFilterChange() {
      this.currentPage = 1
    },
    resetFilters() {
      this.searchQuery = ''
      this.selectedFilter = ''
      this.currentPage = 1
    },
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortOrder *= -1
      } else {
        this.sortKey = key
        this.sortOrder = 1
      }
    }
  },
  watch: {
    data() {
      this.currentPage = 1
    }
  }
}
</script>

<style scoped>
.data-table-container {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 20px;
}

.filters {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  gap: 20px;
}

.search-filter {
  display: flex;
  align-items: center;
  background-color: #f5f6fa;
  padding: 8px 15px;
  border-radius: 20px;
  width: 300px;
}

.search-filter input {
  border: none;
  background: none;
  margin-left: 10px;
  width: 100%;
  outline: none;
}

.filter-options {
  display: flex;
  align-items: center;
  gap: 15px;
}

.filter-options select {
  padding: 8px 15px;
  border: 1px solid #ddd;
  border-radius: 4px;
  outline: none;
}

.reset-btn {
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 5px;
}

.reset-btn:hover {
  color: #333;
}

.table-responsive {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th,
.data-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.data-table th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #333;
}

.data-table th.sortable {
  cursor: pointer;
  user-select: none;
}

.data-table th.sortable:hover {
  background-color: #f0f0f0;
}

.data-table tbody tr:hover {
  background-color: #f8f9fa;
}

.actions {
  display: flex;
  gap: 10px;
}

.actions button {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.actions button.edit {
  background-color: #3498db;
  color: white;
}

.actions button.delete {
  background-color: #e74c3c;
  color: white;
}

.actions button.view {
  background-color: #2ecc71;
  color: white;
}

.no-data {
  text-align: center;
  color: #666;
  padding: 20px;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.pagination-info {
  color: #666;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 15px;
}

.pagination-btn {
  background: none;
  border: 1px solid #ddd;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pagination-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: #666;
}

@media (max-width: 768px) {
  .filters {
    flex-direction: column;
    align-items: stretch;
  }

  .search-filter {
    width: 100%;
  }

  .filter-options {
    justify-content: space-between;
  }

  .pagination {
    flex-direction: column;
    gap: 15px;
  }
}
</style> 