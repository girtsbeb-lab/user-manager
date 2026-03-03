<template>
  <AppLayout :title="`Edit Contact — ${user.first_name} ${user.last_name}`">
    <v-breadcrumbs :items="[
      { title: 'Users', href: route('users.index') },
      { title: `${user.first_name} ${user.last_name}`, href: route('users.show', user.id) },
      { title: 'Edit Contact', disabled: true }
    ]" class="pa-0 mb-4" />
    <v-row justify="center">
      <v-col cols="12" md="6">
        <v-card variant="outlined">
          <v-card-title class="pa-6 pb-0">
            <v-icon start>mdi-phone-edit</v-icon>Contact Information
          </v-card-title>
          <v-card-text class="pa-6">
            <v-text-field v-model="form.phone" label="Phone" variant="outlined" density="compact"
              prepend-inner-icon="mdi-phone" :error-messages="errors.phone" class="mb-2" />
            <v-text-field v-model="form.cell" label="Cell" variant="outlined" density="compact"
              prepend-inner-icon="mdi-cellphone" :error-messages="errors.cell" class="mb-2" />
            <v-text-field v-model="form.email" label="Email" type="email" variant="outlined" density="compact"
              prepend-inner-icon="mdi-email" :error-messages="errors.email" />
          </v-card-text>
          <v-card-actions class="pa-6 pt-0">
            <v-btn variant="text" :href="route('users.show', user.id)">Cancel</v-btn>
            <v-spacer />
            <v-btn color="primary" :loading="loading" @click="submit">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </AppLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ user: Object, contact: Object })

const errors  = reactive({})
const loading = ref(false)

const form = reactive({
  phone: props.contact?.phone || '',
  cell:  props.contact?.cell  || '',
  email: props.contact?.email || '',
})

const submit = () => {
  loading.value = true
  router.put(route('users.contact.update', props.user.id), form, {
    onError: (e) => { Object.assign(errors, e) },
    onFinish: () => { loading.value = false },
  })
}
</script>
