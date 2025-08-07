<script>
  import { onMount } from 'svelte'
  import ParticleSystem from './components/ParticleSystem.svelte'
  import MouseGlow from './components/MouseGlow.svelte'
  import ScrollEffects from './components/ScrollEffects.svelte'
  import InteractiveEnhancements from './components/InteractiveEnhancements.svelte'

  let mounted = false

  onMount(() => {
    mounted = true
    console.log('Svelte enhancements loaded - SEO content preserved!')
  })
</script>

<!-- Svelte enhancements overlay - does not replace HTML content -->
{#if mounted}
  <ParticleSystem />
  <MouseGlow />
  <ScrollEffects />
  <InteractiveEnhancements />
{/if}
