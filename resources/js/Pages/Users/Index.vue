<template>
  <AppLayout title="Users">
    <!-- Header -->
    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <h1 class="text-h4 font-weight-bold">Users</h1>
        <p class="text-body-2 text-medium-emphasis mt-1">
          {{ users.total }} users in database
        </p>
      </div>
      <div class="d-flex gap-2">
        <v-btn color="secondary" variant="tonal" prepend-icon="mdi-download-circle" @click="importDialog = true">
          Import from API
        </v-btn>
        <v-btn color="primary" prepend-icon="mdi-plus" :href="route('users.create')">
          Add User
        </v-btn>
      </div>
    </div>

    <!-- Filters -->
    <v-card class="mb-4" variant="outlined">
      <v-card-text>
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="filters.search"
              label="Search by name, email or username"
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              @update:model-value="debouncedSearch"
            />
          </v-col>
          <v-col cols="6" md="3">
            <v-select
              v-model="filters.gender"
              :items="['male', 'female', 'other']"
              label="Gender"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              @update:model-value="applyFilters"
            />
          </v-col>
          <v-col cols="6" md="3">
            <v-select
              v-model="filters.nationality"
              :items="nationalities"
              label="Nationality"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              @update:model-value="applyFilters"
            />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Users Table -->
    <v-card variant="outlined">
      <v-data-table-server
        :headers="headers"
        :items="users.data"
        :items-length="users.total"
        :loading="loading"
        :items-per-page="users.per_page"
        item-value="id"
        @update:page="changePage"
      >
        <template #item.avatar="{ item }">
          <v-avatar size="36" class="my-1">
            <v-img v-if="item.thumbnail" :src="item.thumbnail" :alt="item.first_name" />
            <v-icon v-else>mdi-account</v-icon>
          </v-avatar>
        </template>

        <template #item.full_name="{ item }">
          <div class="font-weight-medium">{{ item.first_name }} {{ item.last_name }}</div>
          <div class="text-caption text-medium-emphasis">@{{ item.username }}</div>
        </template>

        <template #item.gender="{ item }">
          <v-chip
            :color="item.gender === 'male' ? 'blue' : item.gender === 'female' ? 'pink' : 'purple'"
            size="small"
            variant="tonal"
          >
            {{ item.gender }}
          </v-chip>
        </template>

        <template #item.nationality="{ item }">
          <v-chip size="small" variant="tonal">{{ item.nationality }}</v-chip>
        </template>

        <template #item.contact="{ item }">
          <span class="text-caption">{{ item.contact?.phone || '—' }}</span>
        </template>

        <template #item.city="{ item }">
          <span class="text-caption">{{ item.address?.city || '—' }}</span>
        </template>

        <template #item.actions="{ item }">
          <v-btn icon="mdi-eye" size="small" variant="text" color="primary" :href="route('users.show', item.id)" />
          <v-btn icon="mdi-pencil" size="small" variant="text" color="warning" :href="route('users.edit', item.id)" />
          <v-btn icon="mdi-delete" size="small" variant="text" color="error" @click="confirmDelete(item)" />
        </template>
      </v-data-table-server>
    </v-card>

    <!-- Import Dialog -->
    <v-dialog v-model="importDialog" max-width="500">
      <v-card>
        <v-card-title class="d-flex align-center">
          <v-icon start>mdi-download-circle</v-icon>
          Import Users from API
        </v-card-title>
        <v-card-text>
          <p class="text-body-2 mb-4 text-medium-emphasis">
            Fetch users from <strong>randomuser.me</strong> API and save them to database.
          </p>
          <v-text-field
            v-model.number="importForm.results"
            label="Number of users to import"
            type="number"
            min="1"
            max="5000"
            variant="outlined"
            density="compact"
          />
          <v-checkbox
            v-model="importForm.truncate"
            label="Delete all existing users before import"
            color="error"
            density="compact"
          />
          <v-alert v-if="importForm.truncate" type="warning" variant="tonal" density="compact">
            This will permanently delete all existing user data!
          </v-alert>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="importDialog = false">Cancel</v-btn>
          <v-btn color="primary" :loading="importLoading" @click="doImport">Import</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Confirm Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title>Delete User</v-card-title>
        <v-card-text>
          Are you sure you want to delete <strong>{{ deleteTarget?.first_name }} {{ deleteTarget?.last_name }}</strong>?
          This will also delete their contact info and address.
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="deleteDialog = false">Cancel</v-btn>
          <v-btn color="error" @click="doDelete">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  users:         Object,
  filters:       Object,
  nationalities: Array,
})

const loading  = ref(false)
const filters  = reactive({ ...props.filters })

// Debounce search
let searchTimer = null
const debouncedSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(applyFilters, 400)
}

const applyFilters = () => {
  loading.value = true
  router.get(route('users.index'), filters, {
    preserveState: true,
    replace: true,
    onFinish: () => { loading.value = false },
  })
}

const changePage = (page) => {
  loading.value = true
  router.get(route('users.index'), { ...filters, page }, {
    preserveState: true,
    onFinish: () => { loading.value = false },
  })
}

const headers = [
  { title: '', key: 'avatar', sortable: false, width: '60px' },
  { title: 'Name', key: 'full_name', sortable: false },
  { title: 'Email', key: 'email' },
  { title: 'Gender', key: 'gender' },
  { title: 'Nationality', key: 'nationality' },
  { title: 'Phone', key: 'contact', sortable: false },
  { title: 'City', key: 'city', sortable: false },
  { title: 'Actions', key: 'actions', sortable: false, align: 'end' },
]

// Import
const importDialog  = ref(false)
const importLoading = ref(false)
const importForm    = reactive({ results: 50, truncate: false })

const doImport = () => {
  importLoading.value = true
  router.post(route('users.import'), importForm, {
    onSuccess: () => { importDialog.value = false },
    onFinish: () => { importLoading.value = false },
  })
}

// Delete
const deleteDialog = ref(false)
const deleteTarget = ref(null)

const confirmDelete = (user) => {
  deleteTarget.value = user
  deleteDialog.value = true
}

const doDelete = () => {
  router.delete(route('users.destroy', deleteTarget.value.id), {
    onSuccess: () => { deleteDialog.value = false },
  })
}
</script>
