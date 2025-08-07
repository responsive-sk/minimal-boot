<script>
  import { onMount } from 'svelte'
  
  let scrolled = false
  let mounted = false
  
  function handleScroll() {
    const scrollY = window.scrollY
    scrolled = scrollY > 50

    // Update navigation glassmorphism with smooth transitions
    const nav = document.querySelector('nav')
    if (nav) {
      // Calculate opacity based on scroll position (0-100px range)
      const opacity = Math.min(scrollY / 100, 1)
      const borderOpacity = Math.min(scrollY / 150, 0.2)

      if (scrolled) {
        nav.style.background = `rgba(255, 255, 255, ${opacity * 0.1})`
        nav.style.backdropFilter = `blur(${opacity * 12}px)`
        nav.style.borderBottom = `1px solid rgba(255, 255, 255, ${borderOpacity})`
        nav.classList.add('transition-all', 'duration-300')
      } else {
        nav.style.background = 'transparent'
        nav.style.backdropFilter = 'none'
        nav.style.borderBottom = 'none'
        nav.classList.add('transition-all', 'duration-300')
      }
    }
  }
  
  onMount(() => {
    mounted = true
  })
</script>

<svelte:window on:scroll={handleScroll} />

<!-- Scroll effects are applied via DOM manipulation -->
