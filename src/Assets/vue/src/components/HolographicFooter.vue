<template>
  <footer class="relative neon-box border-t-2 border-cyber-cyan">
    <div class="max-w-7xl mx-auto px-6 py-12">
      <div class="grid md:grid-cols-4 gap-8">
        <!-- Brand -->
        <div class="md:col-span-2">
          <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-8 bg-gradient-to-r from-cyber-cyan to-cyber-pink rounded flex items-center justify-center font-bold text-xs text-cyber-dark animate-hologram">
              RESP
            </div>
            <span class="cyber-glow font-bold text-xl animate-neon-glow">
              CYBER.SOLUTIONS
            </span>
          </div>
          <p class="text-cyber-cyan/80 mb-6 max-w-md leading-relaxed">
            Advanced neural network solutions for the digital frontier. 
            Connecting minds across the cybernetic landscape with quantum-encrypted protocols.
          </p>
          <div class="flex space-x-4">
            <a 
              v-for="social in socialLinks" 
              :key="social.name"
              :href="social.href" 
              class="cyber-social-link"
              :aria-label="social.name"
              @mouseenter="social.isHovered = true"
              @mouseleave="social.isHovered = false"
            >
              <div class="w-10 h-10 cyber-border rounded-lg flex items-center justify-center transition-all duration-300 hover:animate-glow-pulse">
                <span class="text-lg">{{ social.icon }}</span>
              </div>
            </a>
          </div>
        </div>
        
        <!-- Neural Links -->
        <div>
          <h3 class="cyber-glow font-bold mb-4 uppercase tracking-wider">Neural Links</h3>
          <ul class="space-y-3">
            <li v-for="link in neuralLinks" :key="link.name">
              <a 
                :href="link.href" 
                class="cyber-link"
                @mouseenter="link.isHovered = true"
                @mouseleave="link.isHovered = false"
              >
                <span class="mr-2">{{ link.icon }}</span>
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>
        
        <!-- Quantum Resources -->
        <div>
          <h3 class="cyber-glow font-bold mb-4 uppercase tracking-wider">Quantum Resources</h3>
          <ul class="space-y-3">
            <li v-for="resource in quantumResources" :key="resource.name">
              <a 
                :href="resource.href" 
                class="cyber-link"
                @mouseenter="resource.isHovered = true"
                @mouseleave="resource.isHovered = false"
              >
                <span class="mr-2">{{ resource.icon }}</span>
                {{ resource.name }}
              </a>
            </li>
          </ul>
        </div>
      </div>
      
      <!-- Bottom Bar -->
      <div class="border-t border-cyber-cyan/30 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
        <p class="text-cyber-cyan/60 text-sm font-matrix">
          © {{ currentYear }} CYBER.SOLUTIONS // NEURAL NETWORK ACTIVATED // 
          <span class="animate-cyber-flicker">QUANTUM_STATE: ONLINE</span>
        </p>
        <div class="flex space-x-6 mt-4 md:mt-0">
          <a 
            v-for="legal in legalLinks" 
            :key="legal.name"
            :href="legal.href" 
            class="text-cyber-cyan/60 hover:text-cyber-pink text-sm transition-colors duration-300 uppercase tracking-wider"
          >
            {{ legal.name }}
          </a>
        </div>
      </div>
      
      <!-- Matrix Code Animation -->
      <div class="absolute top-0 right-0 w-32 h-32 opacity-20 overflow-hidden">
        <div 
          v-for="(line, index) in matrixCode" 
          :key="index"
          class="matrix-char text-xs leading-tight animate-matrix-rain"
          :style="{ animationDelay: (index * 0.1) + 's' }"
        >
          {{ line }}
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const currentYear = new Date().getFullYear()

const socialLinks = ref([
  { name: 'GitHub', href: 'https://github.com', icon: '⚡', isHovered: false },
  { name: 'Neural Net', href: 'https://twitter.com', icon: '🧠', isHovered: false },
  { name: 'Quantum Link', href: 'https://linkedin.com', icon: '🌀', isHovered: false },
])

const neuralLinks = ref([
  { name: 'Home Matrix', href: '/', icon: '🏠', isHovered: false },
  { name: 'Neural Core', href: '/about', icon: '🧠', isHovered: false },
  { name: 'Contact Node', href: '/contact', icon: '📡', isHovered: false },
  { name: 'Demo Portal', href: '/demo', icon: '🚀', isHovered: false },
])

const quantumResources = ref([
  { name: 'Documentation', href: '/docs', icon: '📚', isHovered: false },
  { name: 'API Gateway', href: '/api', icon: '🔌', isHovered: false },
  { name: 'Code Samples', href: '/examples', icon: '💻', isHovered: false },
  { name: 'Support Hub', href: '/support', icon: '🛠️', isHovered: false },
])

const legalLinks = ref([
  { name: 'Privacy Protocol', href: '/privacy' },
  { name: 'Terms Matrix', href: '/terms' },
  { name: 'Cookie Neural', href: '/cookies' },
])

const matrixCode = ref([])

function generateMatrixCode() {
  const chars = '01アイウエオカキクケコ'
  for (let i = 0; i < 10; i++) {
    let line = ''
    for (let j = 0; j < 8; j++) {
      line += chars[Math.floor(Math.random() * chars.length)]
    }
    matrixCode.value.push(line)
  }
}

onMounted(() => {
  generateMatrixCode()
  console.log('🌈 Holographic Footer materialized!')
})
</script>

<style scoped>
.cyber-link {
  @apply text-cyber-cyan/80 hover:text-cyber-pink transition-all duration-300 uppercase tracking-wide text-sm;
  text-shadow: 0 0 5px currentColor;
}

.cyber-link:hover {
  text-shadow: 0 0 10px currentColor;
  transform: translateX(5px);
}

.cyber-social-link:hover .cyber-border {
  box-shadow: 
    0 0 20px theme('colors.cyber.pink'),
    inset 0 0 20px theme('colors.cyber.pink/20');
}
</style>
