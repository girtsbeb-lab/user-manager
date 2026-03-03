<template>
  <AppLayout :title="`${user.first_name} ${user.last_name}`">
    <!-- Breadcrumb -->
    <v-breadcrumbs :items="breadcrumbs" class="pa-0 mb-4" />

    <v-row>
      <!-- User Card -->
      <v-col cols="12" md="4">
        <v-card variant="outlined">
          <v-card-text class="text-center pa-6">
            <v-avatar size="100" class="mb-4" color="primary">
              <v-img v-if="user.avatar" :src="user.avatar" />
              <span v-else class="text-h4 text-white">{{ initials }}</span>
            </v-avatar>
            <div class="text-h5 font-weight-bold">{{ user.first_name }} {{ user.last_name }}</div>
            <div class="text-body-2 text-medium-emphasis">@{{ user.username }}</div>
            <div class="mt-2">
              <v-chip :color="user.gender === 'male' ? 'blue' : 'pink'" size="small" variant="tonal" class="mr-1">
                {{ user.gender }}
              </v-chip>
              <v-chip size="small" variant="tonal">{{ user.nationality }}</v-chip>
            </div>
          </v-card-text>
          <v-divider />
          <v-list density="compact">
            <v-list-item prepend-icon="mdi-email" :subtitle="user.email" title="Email" />
            <v-list-item prepend-icon="mdi-cake" :subtitle="user.date_of_birth ? `${user.date_of_birth} (${user.age} yrs)` : '—'" title="Date of Birth" />
          </v-list>
          <v-card-actions>
            <v-btn block color="primary" variant="tonal" prepend-icon="mdi-pencil" :href="route('users.edit', user.id)">
              Edit User
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <v-col cols="12" md="8">
        <!-- Contact Info -->
        <v-card variant="outlined" class="mb-4">
          <v-card-title class="d-flex align-center justify-space-between">
            <span><v-icon start>mdi-phone</v-icon>Contact Information</span>
            <div>
              <v-btn
                v-if="user.contact"
                icon="mdi-delete"
                size="small"
                variant="text"
                color="error"
                @click="confirmDeleteContact"
              />
              <v-btn
                icon="mdi-pencil"
                size="small"
                variant="text"
                color="primary"
                :href="route('users.contact.edit', user.id)"
              />
            </div>
          </v-card-title>
          <v-card-text>
            <div v-if="user.contact">
              <v-row>
                <v-col cols="4"><span class="text-medium-emphasis">Phone</span></v-col>
                <v-col cols="8">{{ user.contact.phone || '—' }}</v-col>
                <v-col cols="4"><span class="text-medium-emphasis">Cell</span></v-col>
                <v-col cols="8">{{ user.contact.cell || '—' }}</v-col>
                <v-col cols="4"><span class="text-medium-emphasis">Email</span></v-col>
                <v-col cols="8">{{ user.contact.email || '—' }}</v-col>
              </v-row>
            </div>
            <div v-else class="text-center py-4 text-medium-emphasis">
              <v-icon size="32" class="mb-2">mdi-phone-off</v-icon>
              <div>No contact information</div>
              <v-btn class="mt-2" size="small" color="primary" :href="route('users.contact.edit', user.id)">Add Contact</v-btn>
            </div>
          </v-card-text>
        </v-card>

        <!-- Address -->
        <v-card variant="outlined">
          <v-card-title class="d-flex align-center justify-space-between">
            <span><v-icon start>mdi-map-marker</v-icon>Address</span>
            <div>
              <v-btn
                v-if="user.address"
                icon="mdi-delete"
                size="small"
                variant="text"
                color="error"
                @click="confirmDeleteAddress"
              />
              <v-btn
                icon="mdi-pencil"
                size="small"
                variant="text"
                color="primary"
                :href="route('users.address.edit', user.id)"
              />
            </div>
          </v-card-title>
          <v-card-text>
            <div v-if="user.address">
              <v-row>
                <v-col cols="4"><span class="text-medium-emphasis">Street</span></v-col>
                <v-col cols="8">{{ user.address.street_number }} {{ user.address.street_name }}</v-col>
                <v-col cols="4"><span class="text-medium-emphasis">City</span></v-col>
                <v-col cols="8">{{ user.address.city }}</v-col>
                <v-col cols="4"><span class="text-medium-emphasis">State</span></v-col>
                <v-col cols="8">{{ user.address.state }}</v-col>
                <v-col cols="4"><span class="text-medium-emphasis">Country</span></v-col>
                <v-col cols="8">{{ user.address.country }}</v-col>
                <v-col cols="4"><span class="text-medium-emphasis">Postcode</span></v-col>
                <v-col cols="8">{{ user.address.postcode }}</v-col>
                <v-col cols="4"><span class="text-medium-emphasis">Timezone</span></v-col>
                <v-col cols="8">{{ user.address.timezone_offset }} — {{ user.address.timezone_description }}</v-col>
                <v-col cols="4"><span class="text-medium-emphasis">Coordinates</span></v-col>
                <v-col cols="8">{{ user.address.latitude }}, {{ user.address.longitude }}</v-col>
              </v-row>
            </div>
            <div v-else class="text-center py-4 text-medium-emphasis">
              <v-icon size="32" class="mb-2">mdi-map-marker-off</v-icon>
              <div>No address information</div>
              <v-btn class="mt-2" size="small" color="primary" :href="route('users.address.edit', user.id)">Add Address</v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Confirm dialogs -->
    <v-dialog v-model="contactDeleteDialog" max-width="400">
      <v-card>
        <v-card-title>Delete Contact</v-card-title>
        <v-card-text>Are you sure you want to delete this contact information?</v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="contactDeleteDialog = false">Cancel</v-btn>
          <v-btn color="error" @click="doDeleteContact">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="addressDeleteDialog" max-width="400">
      <v-card>
        <v-card-title>Delete Address</v-card-title>
        <v-card-text>Are you sure you want to delete this address?</v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="addressDeleteDialog = false">Cancel</v-btn>
          <v-btn color="error" @click="doDeleteAddress">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ user: Object })

const initials = computed(() =>
  `${props.user.first_name[0]}${props.user.last_name[0]}`.toUpperCase()
)

const breadcrumbs = [
  { title: 'Users', href: route('users.index') },
  { title: `${props.user.first_name} ${props.user.last_name}`, disabled: true },
]

const contactDeleteDialog = ref(false)
const addressDeleteDialog = ref(false)

const confirmDeleteContact = () => { contactDeleteDialog.value = true }
const confirmDeleteAddress = () => { addressDeleteDialog.value = true }

const doDeleteContact = () => {
  router.delete(route('users.contact.destroy', props.user.id), {
    onSuccess: () => { contactDeleteDialog.value = false }
  })
}
const doDeleteAddress = () => {
  router.delete(route('users.address.destroy', props.user.id), {
    onSuccess: () => { addressDeleteDialog.value = false }
  })
}
</script>
