<template>
  <section class="relative h-screen flex items-center justify-center overflow-hidden">
    <!-- Holographic Particles -->
    <div class="absolute inset-0">
      <div 
        v-for="particle in particles" 
        :key="particle.id"
        class="absolute w-1 h-1 rounded-full animate-pulse"
        :style="{
          left: particle.x + '%',
          top: particle.y + '%',
          backgroundColor: particle.color,
          boxShadow: `0 0 10px ${particle.color}`,
          animationDelay: particle.delay + 's',
          animationDuration: particle.duration + 's'
        }"
      ></div>
    </div>
    
    <!-- Interactive Glow Effect -->
    <div 
      class="absolute w-96 h-96 rounded-full blur-3xl transition-all duration-1000 ease-out pointer-events-none"
      :style="{
        left: mouseX + '%',
        top: mouseY + '%',
        background: `radial-gradient(circle, ${currentGlowColor}40 0%, transparent 70%)`
      }"
    ></div>
    
    <!-- Content -->
    <div class="relative z-10 max-w-6xl mx-auto px-6 text-center">
      <!-- Typing Animation Title -->
      <div class="mb-8">
        <h1 class="text-6xl md:text-8xl font-black mb-6">
          <span class="hologram-text block">{{ displayedTitle }}</span>
          <span v-if="showCursor" class="animate-pulse text-cyber-cyan">|</span>
        </h1>
        
        <div class="glitch-text text-2xl md:text-4xl cyber-glow mb-8" :data-text="subtitle">
          {{ subtitle }}
        </div>
      </div>
      
      <!-- Cyberpunk Feature Cards -->
      <div class="grid md:grid-cols-3 gap-6 mb-12">
        <div 
          v-for="(feature, index) in features" 
          :key="feature.title"
          class="feature-card neon-box p-6 rounded-lg transition-all duration-500 hover:scale-105 cursor-pointer"
          :style="{ animationDelay: (index * 0.2) + 's' }"
          @mouseenter="feature.isHovered = true"
          @mouseleave="feature.isHovered = false"
          @click="activateFeature(feature)"
        >
          <div class="text-4xl mb-4" :class="feature.iconClass">
            {{ feature.icon }}
          </div>
          <h3 class="cyber-glow font-bold mb-2 uppercase tracking-wider">{{ feature.title }}</h3>
          <p class="text-cyber-cyan/80 text-sm">{{ feature.description }}</p>
          
          <!-- Hover Effect -->
          <div 
            v-if="feature.isHovered"
            class="absolute inset-0 bg-gradient-to-r from-cyber-pink/20 to-cyber-cyan/20 rounded-lg animate-pulse"
          ></div>
        </div>
      </div>
      
      <!-- CTA Buttons -->
      <div class="flex flex-col sm:flex-row gap-6 justify-center">
        <button 
          class="cyber-button text-lg px-8 py-4"
          @click="enterMatrix"
        >
          ENTER MATRIX
        </button>
        <button 
          class="cyber-button border-cyber-cyan text-cyber-cyan hover:bg-cyber-cyan hover:text-cyber-dark text-lg px-8 py-4"
          @click="viewDemo"
        >
          VIEW DEMO
        </button>
      </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div 
      class="absolute bottom-8 left-1/2 transform -translate-x-1/2 cursor-pointer animate-bounce"
      @click="scrollToContent"
    >
      <div class="w-8 h-12 border-2 border-cyber-cyan rounded-full flex justify-center">
        <div class="w-1 h-3 bg-cyber-cyan rounded-full mt-2 animate-pulse"></div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useMouse } from '@vueuse/core'

const { x: mouseX, y: mouseY } = useMouse()
const particles = ref([])
const displayedTitle = ref('')
const showCursor = ref(true)
const currentGlowColor = ref('#00ffff')

const title = 'CYBERPUNK WEB'
const subtitle = 'NEURAL NETWORK INTERFACE'

const features = ref([
  {
    icon: '🧠',
    iconClass: 'animate-hologram',
    title: 'NEURAL AI',
    description: 'Advanced artificial intelligence with quantum processing capabilities',
    isHovered: false
  },
  {
    icon: '⚡',
    iconClass: 'animate-neon-glow text-cyber-yellow',
    title: 'QUANTUM SPEED',
    description: 'Lightning-fast performance with zero-latency neural pathways',
    isHovered: false
  },
  {
    icon: '🛡️',
    iconClass: 'animate-glow-pulse text-cyber-green',
    title: 'CYBER SECURITY',
    description: 'Military-grade encryption with biometric authentication protocols',
    isHovered: false
  }
])

const mouseXPercent = computed(() => (mouseX.value / window.innerWidth) * 100 - 12)
const mouseYPercent = computed(() => (mouseY.value / window.innerHeight) * 100 - 12)

function generateParticles() {
  const colors = ['#00ffff', '#ff0080', '#8000ff', '#00ff41', '#ffff00']
  
  for (let i = 0; i < 50; i++) {
    particles.value.push({
      id: i,
      x: Math.random() * 100,
      y: Math.random() * 100,
      color: colors[Math.floor(Math.random() * colors.length)],
      delay: Math.random() * 3,
      duration: 2 + Math.random() * 4
    })
  }
}

function typeTitle() {
  let index = 0
  const interval = setInterval(() => {
    if (index <= title.length) {
      displayedTitle.value = title.slice(0, index)
      index++
    } else {
      clearInterval(interval)
      showCursor.value = false
    }
  }, 150)
}

function activateFeature(feature) {
  console.log(`🚀 Activating ${feature.title}...`)
  // Add some cyberpunk effect
  currentGlowColor.value = feature.title.includes('NEURAL') ? '#ff0080' : 
                          feature.title.includes('QUANTUM') ? '#ffff00' : '#00ff41'
}

function enterMatrix() {
  alert('🌈 INITIALIZING NEURAL LINK...\n🚀 UPLOADING CONSCIOUSNESS...\n💀 WELCOME TO THE MATRIX!')
}

function viewDemo() {
  window.open('/demo', '_blank')
}

function scrollToContent() {
  window.scrollTo({
    top: window.innerHeight,
    behavior: 'smooth'
  })
}

onMounted(() => {
  generateParticles()
  typeTitle()
  
  // Change glow color periodically
  setInterval(() => {
    const colors = ['#00ffff', '#ff0080', '#8000ff', '#00ff41']
    currentGlowColor.value = colors[Math.floor(Math.random() * colors.length)]
  }, 3000)
  
  console.log('🌈 Neon Hero initialized!')
})
</script>

<style scoped>
.feature-card {
  position: relative;
  overflow: hidden;
}

.feature-card:hover {
  transform: scale(1.05) rotateY(5deg);
  box-shadow: 
    0 0 30px theme('colors.cyber.cyan'),
    inset 0 0 30px theme('colors.cyber.cyan/10');
}
</style>
