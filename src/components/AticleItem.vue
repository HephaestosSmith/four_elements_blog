<template>
  <div class="rounded text-wrap article text-white">
    {{ content }}
  </div>
</template>

<script>
export default {
  name: 'AticleItem',
  props: {
    content: String
  }
}
</script>