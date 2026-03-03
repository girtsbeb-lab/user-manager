<template>
  <AppLayout :title="`Edit ${user.first_name} ${user.last_name}`">
    <v-breadcrumbs :items="[
      { title: 'Users', href: route('users.index') },
      { title: `${user.first_name} ${user.last_name}`, href: route('users.show', user.id) },
      { title: 'Edit', disabled: true }
    ]" class="pa-0 mb-4" />
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card variant="outlined">
          <v-card-title class="pa-6 pb-0">
            <v-icon start>mdi-account-edit</v-icon>Edit User
          </v-card-title>
          <v-card-text class="pa-6">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.first_name" label="First Name *" variant="outlined" density="compact"
                  :error-messages="errors.first_name" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.last_name" label="Last Name *" variant="outlined" density="compact"
                  :error-messages="errors.last_name" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.email" label="Email *" type="email" variant="outlined" density="compact"
                  :error-messages="errors.email" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.username" label="Username *" variant="outlined" density="compact"
                  :error-messages="errors.username" />
              </v-col>
              <v-col cols="12" md="6">
                <v-select v-model="form.gender" :items="['male', 'female', 'other']" label="Gender *"
                  variant="outlined" density="compact" :error-messages="errors.gender" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.nationality" label="Nationality" variant="outlined" density="compact"
                  :error-messages="errors.nationality" maxlength="10" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.date_of_birth" label="Date of Birth" type="date" variant="outlined"
                  density="compact" :error-messages="errors.date_of_birth" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model.number="form.age" label="Age" type="number" variant="outlined" density="compact"
                  :error-messages="errors.age" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.avatar" label="Avatar URL" variant="outlined" density="compact"
                  :error-messages="errors.avatar" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="form.thumbnail" label="Thumbnail URL" variant="outlined" density="compact"
                  :error-messages="errors.thumbnail" />
              </v-col>
            </v-row>
          </v-card-text>
          <v-card-actions class="pa-6 pt-0">
            <v-btn variant="text" :href="route('users.show', user.id)">Cancel</v-btn>
            <v-spacer />
            <v-btn color="primary" :loading="loading" @click="submit">Save Changes</v-btn>
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

const props = defineProps({ user: Object })

const errors  = reactive({})
const loading = ref(false)

const form = reactive({
  first_name:    props.user.first_name,
  last_name:     props.user.last_name,
  email:         props.user.email,
  username:      props.user.username,
  gender:        props.user.gender,
  nationality:   props.user.nationality || '',
  date_of_birth: props.user.date_of_birth || '',
  age:           props.user.age,
  avatar:        props.user.avatar || '',
  thumbnail:     props.user.thumbnail || '',
})

const submit = () => {
  loading.value = true
  router.put(route('users.update', props.user.id), form, {
    onError: (e) => { Object.assign(errors, e) },
    onFinish: () => { loading.value = false },
  })
}
</script>
