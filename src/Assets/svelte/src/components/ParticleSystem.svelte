<script>
  import { onMount } from 'svelte'
  
  let particles = []
  let mounted = false
  
  onMount(() => {
    mounted = true
    // Generate particles
    particles = Array.from({ length: 20 }, (_, i) => ({
      id: i,
      x: Math.random() * 100,
      y: Math.random() * 100,
      delay: Math.random() * 6,
      duration: 6 + Math.random() * 4
    }))
  })
</script>

<!-- Floating particles overlay -->
{#if mounted}
  <div class="fixed inset-0 pointer-events-none z-10">
    {#each particles as particle (particle.id)}
      <div 
        class="absolute w-2 h-2 bg-white/20 rounded-full animate-float"
        style="
          left: {particle.x}%;
          top: {particle.y}%;
          animation-delay: {particle.delay}s;
          animation-duration: {particle.duration}s;
        "
      ></div>
    {/each}
  </div>
{/if}
