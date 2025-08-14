<script setup>
import { onMounted } from 'vue';
import { useProfile } from '../composables/useProfile';
import { useAuth } from '../composables/useAuth';

const { user } = useAuth();
const { me, loading, error, load } = useProfile();


if (user.value && !me.value) {
    me.value = user.value;
}
console.log(user.value);
onMounted(load);
</script>

<template>
    <h1>{{ user.name }}</h1>
    <p>{{ user.email }}</p>
</template>