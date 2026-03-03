<template>
  <AppLayout :title="`Edit Address — ${user.first_name} ${user.last_name}`">
    <v-breadcrumbs :items="[
      { title: 'Users', href: route('users.index') },
      { title: `${user.first_name} ${user.last_name}`, href: route('users.show', user.id) },
      { title: 'Edit Address', disabled: true }
    ]" class="pa-0 mb-4" />
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card variant="outlined">
          <v-card-title class="pa-6 pb-0">
            <v-icon start>mdi-map-marker-edit</v-icon>Address
          </v-card-title>
          <v-card-text class="pa-6">
            <v-row>
              <v-col cols="4">
                <v-text-field v-model="form.street_number" label="Street Number" variant="outlined" density="compact"
                  :error-messages="errors.street_number" />
              </v-col>
              <v-col cols="8">
                <v-text-field v-model="form.street_name" label="Street Name" variant="outlined" density="compact"
                  :error-messages="errors.street_name" />
              </v-col>
              <v-col cols="6">
                <v-text-field v-model="form.city" label="City" variant="outlined" density="compact"
                  :error-messages="errors.city" />
              </v-col>
              <v-col cols="6">
                <v-text-field v-model="form.state" label="State" variant="outlined" density="compact"
                  :error-messages="errors.state" />
              </v-col>
              <v-col cols="6">
                <v-text-field v-model="form.country" label="Country" variant="outlined" density="compact"
                  :error-messages="errors.country" />
              </v-col>
              <v-col cols="6">
                <v-text-field v-model="form.postcode" label="Postcode" variant="outlined" density="compact"
                  :error-messages="errors.postcode" />
              </v-col>
              <v-col cols="6">
                <v-text-field v-model="form.latitude" label="Latitude" type="number" variant="outlined" density="compact"
                  :error-messages="errors.latitude" />
              </v-col>
              <v-col cols="6">
                <v-text-field v-model="form.longitude" label="Longitude" type="number" variant="outlined" density="compact"
                  :error-messages="errors.longitude" />
              </v-col>
              <v-col cols="6">
                <v-text-field v-model="form.timezone_offset" label="Timezone Offset (e.g. +2:00)" variant="outlined"
                  density="compact" :error-messages="errors.timezone_offset" />
              </v-col>
              <v-col cols="6">
                <v-text-field v-model="form.timezone_description" label="Timezone Description" variant="outlined"
                  density="compact" :error-messages="errors.timezone_description" />
              </v-col>
            </v-row>
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

const props = defineProps({ user: Object, address: Object })

const errors  = reactive({})
const loading = ref(false)

const form = reactive({
  street_number:        props.address?.street_number        || '',
  street_name:          props.address?.street_name          || '',
  city:                 props.address?.city                 || '',
  state:                props.address?.state                || '',
  country:              props.address?.country              || '',
  postcode:             props.address?.postcode             || '',
  latitude:             props.address?.latitude             || '',
  longitude:            props.address?.longitude            || '',
  timezone_offset:      props.address?.timezone_offset      || '',
  timezone_description: props.address?.timezone_description || '',
})

const submit = () => {
  loading.value = true
  router.put(route('users.address.update', props.user.id), form, {
    onError: (e) => { Object.assign(errors, e) },
    onFinish: () => { loading.value = false },
  })
}
</script>
