<template>
  <div class="min-h-screen bg-cyber-darker relative overflow-hidden">
    <!-- Matrix Rain Background -->
    <MatrixRain />
    
    <!-- Cyberpunk Grid Overlay -->
    <div class="fixed inset-0 opacity-10 pointer-events-none">
      <div class="w-full h-full" 
           style="background-image: linear-gradient(rgba(0,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;">
      </div>
    </div>
    
    <!-- Cyberpunk Navigation -->
    <CyberpunkNav />
    
    <!-- Neon Hero Section -->
    <NeonHero />
    
    <!-- Holographic Footer -->
    <HolographicFooter />
    
    <!-- Glitch Effects Overlay -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-50">
      <div class="glitch-overlay"></div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import MatrixRain from './components/MatrixRain.vue'
import CyberpunkNav from './components/CyberpunkNav.vue'
import NeonHero from './components/NeonHero.vue'
import HolographicFooter from './components/HolographicFooter.vue'

onMounted(() => {
  console.log('🌈 Vue Cyberpunk components loaded!')
})
</script>

<style scoped>
.glitch-overlay {
  background: linear-gradient(
    90deg,
    transparent 98%,
    rgba(0, 255, 255, 0.03) 100%
  );
  animation: glitch-scan 3s linear infinite;
}

@keyframes glitch-scan {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100vw); }
}
</style>
