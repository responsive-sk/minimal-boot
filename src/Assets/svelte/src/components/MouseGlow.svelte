<script>
  import { onMount } from 'svelte'
  
  let mouseX = 0
  let mouseY = 0
  let mounted = false
  
  function handleMouseMove(event) {
    mouseX = event.clientX
    mouseY = event.clientY
  }
  
  onMount(() => {
    mounted = true
  })
</script>

<svelte:window on:mousemove={handleMouseMove} />

<!-- Mouse tracking glow effect -->
{#if mounted}
  <div class="fixed inset-0 pointer-events-none z-5">
    <div 
      class="absolute w-96 h-96 bg-gradient-to-r from-blue-500/30 to-purple-500/30 rounded-full blur-3xl transition-all duration-1000 ease-out"
      style="
        left: {mouseX / (typeof window !== 'undefined' ? window.innerWidth : 1920) * 100 - 12}%;
        top: {mouseY / (typeof window !== 'undefined' ? window.innerHeight : 1080) * 100 - 12}%;
      "
    ></div>
  </div>
{/if}
