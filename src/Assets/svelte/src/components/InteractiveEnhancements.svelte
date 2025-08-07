<script>
  import { onMount } from 'svelte'
  
  let mounted = false
  
  function enhanceButtons() {
    // Add enhanced hover effects to buttons
    const buttons = document.querySelectorAll('.btn-primary, .btn-secondary')
    buttons.forEach(button => {
      button.addEventListener('mouseenter', () => {
        button.style.transform = 'scale(1.05)'
        button.style.boxShadow = '0 10px 25px rgba(0,0,0,0.3)'
      })
      
      button.addEventListener('mouseleave', () => {
        button.style.transform = 'scale(1)'
        button.style.boxShadow = 'none'
      })
    })
  }
  
  function enhanceCards() {
    // Add enhanced hover effects to feature cards
    const cards = document.querySelectorAll('.feature-card')
    cards.forEach(card => {
      card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-5px) scale(1.02)'
        card.style.boxShadow = '0 20px 40px rgba(0,0,0,0.3)'
      })
      
      card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0) scale(1)'
        card.style.boxShadow = 'none'
      })
    })
  }
  
  function enhanceMobileMenu() {
    // Add mobile menu functionality
    const menuButton = document.querySelector('#mobile-menu-button')
    const mobileMenu = document.querySelector('#mobile-menu')
    
    if (menuButton && mobileMenu) {
      menuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden')
      })
    }
  }
  
  onMount(() => {
    mounted = true
    
    // Apply all enhancements
    enhanceButtons()
    enhanceCards()
    enhanceMobileMenu()
    
    console.log('Interactive enhancements applied to static HTML')
  })
</script>

<!-- Interactive enhancements are applied via DOM manipulation -->
