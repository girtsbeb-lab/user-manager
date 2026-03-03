<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" :rail="rail" permanent>
      <v-list-item
        prepend-icon="mdi-account-group"
        title="User Manager"
        nav
      >
        <template #append>
          <v-btn :icon="rail ? 'mdi-chevron-right' : 'mdi-chevron-left'" variant="text" @click="rail = !rail" />
        </template>
      </v-list-item>
      <v-divider />
      <v-list density="compact" nav>
        <v-list-item
          prepend-icon="mdi-account-multiple"
          title="Users"
          :href="route('users.index')"
          :active="$page.url.startsWith('/users')"
          active-color="primary"
        />
      </v-list>
    </v-navigation-drawer>

    <v-app-bar elevation="1">
      <v-app-bar-title>{{ title }}</v-app-bar-title>
      <template #append>
        <v-chip class="mr-3" color="primary" variant="tonal" size="small" prepend-icon="mdi-database">
          PostgreSQL
        </v-chip>
      </template>
    </v-app-bar>

    <v-main>
      <v-container fluid class="pa-6">
        <!-- Flash messages -->
        <v-alert
          v-if="$page.props.flash?.success"
          type="success"
          variant="tonal"
          closable
          class="mb-4"
        >{{ $page.props.flash.success }}</v-alert>
        <v-alert
          v-if="$page.props.flash?.error"
          type="error"
          variant="tonal"
          closable
          class="mb-4"
        >{{ $page.props.flash.error }}</v-alert>

        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue'

defineProps({ title: String })

const drawer = ref(true)
const rail   = ref(false)
</script>
