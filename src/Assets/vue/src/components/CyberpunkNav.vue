<template>
  <nav class="fixed w-full top-0 z-50 transition-all duration-500" :class="navClasses">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Cyberpunk Logo -->
        <div class="flex items-center">
          <a href="/" class="flex items-center space-x-3 group" @click="logoGlitch">
            <div class="relative">
              <div class="w-10 h-8 bg-gradient-to-r from-cyber-cyan to-cyber-pink rounded flex items-center justify-center font-bold text-xs text-cyber-dark">
                RESP
              </div>
              <!-- Glitch overlay -->
              <div v-if="isGlitching" class="absolute inset-0 bg-cyber-pink opacity-20 animate-pulse"></div>
            </div>
            <span class="cyber-glow font-bold text-xl hidden sm:block animate-neon-glow">
              CYBER.SOLUTIONS
            </span>
          </a>
        </div>
        
        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center space-x-8">
          <a 
            v-for="link in navLinks" 
            :key="link.name"
            :href="link.href" 
            class="nav-link relative overflow-hidden"
            @mouseenter="link.isHovered = true"
            @mouseleave="link.isHovered = false"
          >
            <span class="relative z-10">{{ link.name }}</span>
            <div 
              class="absolute inset-0 bg-gradient-to-r from-cyber-cyan to-cyber-pink opacity-0 transition-opacity duration-300"
              :class="{ 'opacity-20': link.isHovered }"
            ></div>
          </a>
          <button class="cyber-button" @click="hackTheMatrix">
            JACK IN
          </button>
        </div>
        
        <!-- Mobile Menu Button -->
        <div class="md:hidden">
          <button 
            @click="toggleMobileMenu" 
            class="cyber-border p-2 rounded transition-all duration-300 hover:animate-glow-pulse"
          >
            <div class="w-6 h-6 relative">
              <span 
                v-for="(line, index) in 3" 
                :key="index"
                class="block absolute h-0.5 w-6 bg-cyber-cyan transition-all duration-300"
                :class="mobileMenuClasses[index]"
                :style="{ top: (index * 8) + 'px' }"
              ></span>
            </div>
          </button>
        </div>
      </div>
      
      <!-- Mobile Menu -->
      <transition name="cyber-slide">
        <div v-if="mobileMenuOpen" class="md:hidden neon-box rounded-lg mt-2 p-4 space-y-4">
          <a 
            v-for="link in navLinks" 
            :key="link.name + '-mobile'"
            :href="link.href" 
            class="block cyber-glow hover:text-cyber-pink transition-colors duration-300"
          >
            {{ link.name }}
          </a>
          <button class="cyber-button w-full" @click="hackTheMatrix">
            JACK IN
          </button>
        </div>
      </transition>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useScroll } from '@vueuse/core'

const { y: scrollY } = useScroll(window)
const mobileMenuOpen = ref(false)
const isGlitching = ref(false)

const navLinks = ref([
  { name: 'HOME', href: '/', isHovered: false },
  { name: 'MATRIX', href: '/about', isHovered: false },
  { name: 'NEURAL', href: '/contact', isHovered: false },
])

const navClasses = computed(() => ({
  'neon-box': scrollY.value > 50,
  'bg-transparent': scrollY.value <= 50
}))

const logoStyle = computed(() => ({
  filter: `hue-rotate(${scrollY.value * 0.5}deg) brightness(0) invert(1)`,
  textShadow: '0 0 10px #00ffff'
}))

const mobileMenuClasses = computed(() => {
  if (!mobileMenuOpen.value) {
    return ['', '', '']
  }
  return [
    'rotate-45 translate-y-2',
    'opacity-0',
    '-rotate-45 -translate-y-2'
  ]
})

function toggleMobileMenu() {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

function logoGlitch() {
  isGlitching.value = true
  setTimeout(() => {
    isGlitching.value = false
  }, 300)
}

function hackTheMatrix() {
  alert('🚀 ACCESSING MAINFRAME...\n💀 FIREWALL BYPASSED\n🌈 WELCOME TO THE MATRIX!')
}

onMounted(() => {
  console.log('🤖 Cyberpunk Navigation online!')
})
</script>

<style scoped>
.nav-link {
  @apply text-cyber-cyan font-bold uppercase tracking-wider transition-all duration-300 hover:text-cyber-pink;
  text-shadow: 0 0 5px currentColor;
}

.cyber-slide-enter-active,
.cyber-slide-leave-active {
  transition: all 0.3s ease;
}

.cyber-slide-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.cyber-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
