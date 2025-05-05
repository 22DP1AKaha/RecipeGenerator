<template>
  <ProfileCard
    :user="user"
    :dietas="dietas"
    :alergijas="alergijas"
  />
</template>

<script setup>
import ProfileCard from '@/Components/ProfileCard.vue';

defineProps({
  user: Object,
  dietas: Array,
  alergijas: Array,
});
</script>